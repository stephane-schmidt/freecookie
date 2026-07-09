<?php
/**
 * Moteur de blocage a priori — 100 % local.
 *
 * Réécrit, dans un tampon de sortie, les balises tierces connues
 * (<script src>, scripts inline signés, <iframe>) en version neutralisée
 * AVANT le consentement. Le déblocage se fait ensuite côté JS selon le cookie
 * de choix — le HLTM mis en cache est donc identique pour tous les visiteurs
 * (compatible WP Super Cache / LiteSpeed).
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Script_Blocker {

	/**
	 * Signatures de scripts INLINE à bloquer (regex sans délimiteurs), par finalité.
	 *
	 * @return array<string,string>
	 */
	protected function inline_signatures() {
		return array(
			'statistics' => '(gtag\(\s*[\'"]config[\'"]\s*,\s*[\'"]G-|ga\(\s*[\'"]create[\'"]|_gaq\.push|googletagmanager)',
			'marketing'  => '(fbq\(\s*[\'"]init[\'"]|connect\.facebook\.net|_linkedin_partner_id|ttq\.load)',
		);
	}

	/**
	 * Démarre le tampon de sortie sur le front.
	 */
	public function start_buffer() {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) {
			return;
		}
		if ( is_embed() ) {
			return;
		}
		ob_start( array( $this, 'process_html' ) );
	}

	/**
	 * Callback du tampon : réécrit le HTML.
	 *
	 * @param string $html Contenu tamponné.
	 * @return string
	 */
	public function process_html( $html ) {
		if ( '' === trim( (string) $html ) ) {
			return $html;
		}
		// On ne touche qu'à des documents HTML.
		if ( stripos( $html, '</head>' ) === false && stripos( $html, '<body' ) === false ) {
			return $html;
		}

		$html = $this->rewrite_scripts( $html );
		$html = $this->rewrite_iframes( $html );
		return $html;
	}

	/**
	 * Réécrit les balises <script>.
	 *
	 * @param string $html HTML complet.
	 * @return string
	 */
	protected function rewrite_scripts( $html ) {
		return (string) preg_replace_callback(
			'#<script\b([^>]*)>(.*?)</script>#is',
			array( $this, 'cb_script' ),
			$html
		);
	}

	/**
	 * Callback par balise <script>.
	 *
	 * @param array $m Groupes : [0]=tout, [1]=attributs, [2]=contenu inline.
	 * @return string
	 */
	protected function cb_script( $m ) {
		$attrs = $m[1];
		$inner = $m[2];

		// Déjà neutralisé (pré-taggué par le site ou par nous) → on laisse.
		if ( false !== stripos( $attrs, 'data-fc-category' ) ) {
			return $m[0];
		}
		// On ne bloque que le JS exécutable (pas les <script type="application/ld+json">, etc.).
		if ( preg_match( '#type\s*=\s*[\'"]([^\'"]+)[\'"]#i', $attrs, $tm ) ) {
			$type = strtolower( trim( $tm[1] ) );
			if ( ! in_array( $type, array( 'text/javascript', 'application/javascript', 'module' ), true ) ) {
				return $m[0];
			}
		}

		$category = '';
		$service  = '';

		// Par URL (src).
		if ( preg_match( '#\bsrc\s*=\s*[\'"]([^\'"]+)[\'"]#i', $attrs, $sm ) ) {
			$category = $this->match_url( $sm[1] );
			if ( '' !== $category ) {
				$service = FC_Categories::match_service( $sm[1] );
			}
		}

		// Par signature inline.
		if ( '' === $category && '' !== trim( $inner ) ) {
			foreach ( $this->inline_signatures() as $cat => $rx ) {
				if ( preg_match( '#' . $rx . '#i', $inner ) ) {
					$category = $cat;
					break;
				}
			}
		}

		if ( '' === $category ) {
			return $m[0];
		}

		return $this->neutralize_script( $attrs, $inner, $category, $service );
	}

	/**
	 * Neutralise une balise <script> : type=text/plain + data-* pour le déblocage JS.
	 *
	 * @param string $attrs    Attributs d'origine.
	 * @param string $inner    Contenu inline.
	 * @param string $category Finalité.
	 * @return string
	 */
	protected function neutralize_script( $attrs, $inner, $category, $service = '' ) {
		// Retire un éventuel type existant, on force text/plain.
		$attrs = preg_replace( '#\s*type\s*=\s*[\'"][^\'"]*[\'"]#i', '', $attrs );
		// Déplace src → data-fc-src pour empêcher tout préchargement.
		$attrs = preg_replace( '#\bsrc\s*=\s*([\'"])([^\'"]+)\1#i', 'data-fc-src=$1$2$1', $attrs );

		$attrs = trim( $attrs );
		$attrs = '' !== $attrs ? ' ' . $attrs : '';
		$svc   = $service ? ' data-fc-service="' . esc_attr( $service ) . '"' : '';
		return '<script type="text/plain" data-fc-category="' . esc_attr( $category ) . '"' . $svc
			. $attrs . '>' . $inner . '</script>';
	}

	/**
	 * Réécrit les balises <iframe> tierces connues.
	 *
	 * @param string $html HTML complet.
	 * @return string
	 */
	protected function rewrite_iframes( $html ) {
		return (string) preg_replace_callback(
			'#<iframe\b([^>]*)>#is',
			array( $this, 'cb_iframe' ),
			$html
		);
	}

	/**
	 * Callback par balise <iframe>.
	 *
	 * @param array $m Groupes : [0]=balise, [1]=attributs.
	 * @return string
	 */
	protected function cb_iframe( $m ) {
		$attrs = $m[1];

		if ( false !== stripos( $attrs, 'data-fc-category' ) ) {
			return $m[0];
		}
		if ( ! preg_match( '#\bsrc\s*=\s*[\'"]([^\'"]+)[\'"]#i', $attrs, $sm ) ) {
			return $m[0];
		}
		$category = $this->match_url( $sm[1] );
		if ( '' === $category ) {
			return $m[0];
		}
		$service = FC_Categories::match_service( $sm[1] );
		$svc     = $service ? ' data-fc-service="' . esc_attr( $service ) . '"' : '';

		// src → data-fc-src pour empêcher le chargement, + classe pour le placeholder CSS.
		$new = preg_replace( '#\bsrc\s*=\s*([\'"])([^\'"]+)\1#i', 'data-fc-src=$1$2$1', $attrs );
		$new = $this->add_class( $new, 'fc-blocked-embed' );
		return '<iframe' . $new . ' data-fc-category="' . esc_attr( $category ) . '"' . $svc . '>';
	}

	/**
	 * Confronte une URL aux services connus.
	 *
	 * @param string $url URL de la balise.
	 * @return string Finalité, ou '' si non reconnue.
	 */
	protected function match_url( $url ) {
		// URLs internes (même hôte) : jamais bloquées.
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( $host && $host === wp_parse_url( home_url(), PHP_URL_HOST ) ) {
			return '';
		}
		foreach ( FC_Categories::known_services() as $svc ) {
			foreach ( $svc['patterns'] as $needle ) {
				if ( false !== stripos( $url, $needle ) ) {
					return $svc['category'];
				}
			}
		}
		return '';
	}

	/**
	 * Ajoute une classe à une chaîne d'attributs.
	 *
	 * @param string $attrs Attributs.
	 * @param string $class Classe à ajouter.
	 * @return string
	 */
	protected function add_class( $attrs, $class ) {
		if ( preg_match( '#\bclass\s*=\s*([\'"])(.*?)\1#i', $attrs, $cm ) ) {
			return str_replace( $cm[0], 'class=' . $cm[1] . trim( $cm[2] . ' ' . $class ) . $cm[1], $attrs );
		}
		return $attrs . ' class="' . esc_attr( $class ) . '"';
	}
}
