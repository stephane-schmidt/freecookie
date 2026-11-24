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

class Freecookie_Script_Blocker {

	/**
	 * 0.16.1 — Clés de services (Freecookie_Categories::known_services) que ce site
	 * charge SANS consentement : leurs balises sont laissées telles quelles, ni
	 * `data-fc-src` ni `type=text/plain`. Vide = comportement historique, tout bloqué.
	 *
	 * @var string[]
	 */
	protected $exempt = array();

	/**
	 * @param string[] $exempt Services exemptés (déjà normalisés par Freecookie_Categories::exempt_services).
	 */
	public function __construct( $exempt = array() ) {
		$this->exempt = array_values( array_filter( array_map( 'strval', (array) $exempt ) ) );
	}

	/**
	 * Un service est-il exempté du blocage sur ce site ?
	 *
	 * @param string $service Clé de service ('' si inconnue).
	 * @return bool
	 */
	protected function is_exempt( $service ) {
		return '' !== $service && in_array( $service, $this->exempt, true );
	}

	/**
	 * Signatures de scripts INLINE à bloquer (regex sans délimiteurs), par finalité.
	 *
	 * @return array<string,string>
	 */
	protected function inline_signatures() {
		return array(
			'statistics' => '(gtag\(\s*[\'"]config[\'"]\s*,\s*[\'"]G-|ga\(\s*[\'"]create[\'"]|_gaq\.push|googletagmanager)',
			'marketing'  => '(fbq\(\s*[\'"]init[\'"]|connect\.facebook\.net|_linkedin_partner_id|ttq\.load|fundingchoicesmessages\.google\.com|googlefc\.callbackQueue|signalGooglefcPresent)',
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
		// Aperçu d'observation du scan (admin seulement) : laisser tout s'exécuter.
		if ( Freecookie_Scanner::is_sniff_request() ) {
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
	 * 0.16.3 — `preg_replace_callback` renvoie NULL quand PCRE abandonne (limite de retours en
	 * arrière ou pile JIT atteinte sur un très gros document, typiquement un énorme script en
	 * ligne). L'ancien transtypage `(string)` en faisait une chaîne vide : la page ENTIÈRE partait
	 * blanche, en 200, sans une ligne au journal. Désormais l'échec est signalé, puis la même
	 * réécriture est refaite par un analyseur linéaire (scan_tags) qui n'applique aucune
	 * expression régulière au document : ni page vide, ni traceur laissé passer avant le
	 * consentement.
	 *
	 * @param string $html HTML complet.
	 * @return string
	 */
	protected function rewrite_scripts( $html ) {
		// Le motif d'attributs accepte les valeurs entre guillemets contenant « > »
		// (sinon une balise piégée pourrait échapper à la neutralisation).
		$out = preg_replace_callback(
			'#<script\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>(.*?)</script>#is',
			array( $this, 'cb_script' ),
			$html
		);
		if ( null !== $out ) {
			return $out;
		}
		$this->report_pcre_failure( 'script', preg_last_error(), strlen( $html ) );
		return $this->scan_tags( $html, 'script', true, 'cb_script' );
	}

	/**
	 * 0.16.3 — Signale un abandon de PCRE sur le document : une ligne au journal d'erreurs PHP
	 * et l'action `freecookie_blocker_pcre_failure`, pour qu'un site puisse le surveiller.
	 * La page, elle, est tout de même servie réécrite (voir rewrite_scripts).
	 *
	 * @param string $tag    Balise en cours de réécriture : 'script' ou 'iframe'.
	 * @param int    $code   Code renvoyé par preg_last_error() juste après l'échec.
	 * @param int    $length Taille du document, en octets.
	 */
	protected function report_pcre_failure( $tag, $code, $length ) {
		$names = array(
			PREG_INTERNAL_ERROR        => 'erreur interne',
			PREG_BACKTRACK_LIMIT_ERROR => 'pcre.backtrack_limit atteinte',
			PREG_RECURSION_LIMIT_ERROR => 'pcre.recursion_limit atteinte',
			PREG_BAD_UTF8_ERROR        => 'UTF-8 invalide',
			PREG_BAD_UTF8_OFFSET_ERROR => 'décalage UTF-8 invalide',
			PREG_JIT_STACKLIMIT_ERROR  => 'pile JIT épuisée',
		);
		$reason = isset( $names[ $code ] ) ? $names[ $code ] : 'code ' . (int) $code;

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- chemin d'échec, à tracer.
		error_log(
			sprintf(
				'FreeCookie : PCRE a abandonné la réécriture des <%1$s> (%2$s) sur un document de %3$d octets ; page réécrite par l\'analyseur de secours.',
				$tag,
				$reason,
				$length
			)
		);

		/**
		 * PCRE a abandonné sur le document ; l'analyseur de secours a pris le relais.
		 *
		 * @param string $tag    'script' ou 'iframe'.
		 * @param int    $code   Code preg_last_error().
		 * @param int    $length Taille du document, en octets.
		 */
		do_action( 'freecookie_blocker_pcre_failure', $tag, (int) $code, (int) $length );
	}

	/**
	 * 0.16.3 — Analyseur de secours : parcourt le document à la recherche des balises `<$name …>`
	 * et applique le même rappel que l'expression régulière, avec les mêmes groupes. Uniquement
	 * des recherches de chaînes (strpos, strcspn) : le coût est linéaire et rien ne peut
	 * « abandonner », quelle que soit la taille du document.
	 *
	 * La grammaire reproduit celle des motifs : nom de balise insensible à la casse et suivi
	 * d'une frontière de mot ; attributs jusqu'au premier « > » situé hors guillemets ; pour une
	 * balise appariée, contenu jusqu'à la première fermeture `</$name>`.
	 *
	 * @param string $html     HTML complet.
	 * @param string $name     Nom de balise en minuscules ('script', 'iframe').
	 * @param bool   $paired   true : capturer aussi le contenu jusqu'à `</$name>`.
	 * @param string $callback Méthode de rappel (cb_script, cb_iframe).
	 * @return string
	 */
	protected function scan_tags( $html, $name, $paired, $callback ) {
		$low   = strtolower( $html ); // Une seule copie en minuscules : positions identiques.
		$open  = '<' . $name;
		$close = '</' . $name . '>';
		$olen  = strlen( $open );
		$clen  = strlen( $close );
		$out   = '';
		$pos   = 0;

		while ( false !== ( $start = strpos( $low, $open, $pos ) ) ) {
			$after = $start + $olen;
			$end   = $this->is_word_char( $html, $after ) ? false : $this->tag_end( $html, $after );
			if ( false === $end ) {
				// `<scripts`, balise jamais refermée, guillemet orphelin : pas une balise pour
				// le motif non plus. On la laisse et on reprend juste après.
				$out .= substr( $html, $pos, $after - $pos );
				$pos  = $after;
				continue;
			}

			$attrs = substr( $html, $after, $end - $after );
			if ( ! $paired ) {
				$stop = $end + 1;
				$m    = array( substr( $html, $start, $stop - $start ), $attrs );
			} else {
				$shut = strpos( $low, $close, $end + 1 );
				if ( false === $shut ) {
					break; // Plus aucune fermeture dans le document : le reste part tel quel.
				}
				$stop = $shut + $clen;
				$m    = array(
					substr( $html, $start, $stop - $start ),
					$attrs,
					substr( $html, $end + 1, $shut - $end - 1 ),
				);
			}

			$out .= substr( $html, $pos, $start - $pos ) . $this->$callback( $m );
			$pos  = $stop;
		}

		return $out . substr( $html, $pos );
	}

	/**
	 * Position du « > » qui ferme la balise ouvrante, en sautant les valeurs entre guillemets
	 * (elles peuvent contenir « > »). false si la balise ne se referme pas, ou si un guillemet
	 * reste ouvert jusqu'à la fin du document.
	 *
	 * @param string $html HTML complet.
	 * @param int    $from Position juste après le nom de balise.
	 * @return int|false
	 */
	protected function tag_end( $html, $from ) {
		$len = strlen( $html );
		$i   = $from;
		while ( $i < $len ) {
			$i += strcspn( $html, '>"\'', $i );
			if ( $i >= $len ) {
				return false;
			}
			if ( '>' === $html[ $i ] ) {
				return $i;
			}
			$quote = strpos( $html, $html[ $i ], $i + 1 );
			if ( false === $quote ) {
				return false;
			}
			$i = $quote + 1;
		}
		return false;
	}

	/**
	 * L'octet à cette position est-il un caractère de mot (au sens de `\w` sans le drapeau u) ?
	 * Sert de frontière de mot après le nom de balise : `<script>` oui, `<scripts>` non.
	 *
	 * @param string $html HTML complet.
	 * @param int    $at   Position à tester.
	 * @return bool
	 */
	protected function is_word_char( $html, $at ) {
		return isset( $html[ $at ] )
			&& false !== strpos( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_', $html[ $at ] );
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
				$service = Freecookie_Categories::match_service( $sm[1] );
				// 0.16.1 : service exempté sur ce site → la balise part intacte.
				if ( $this->is_exempt( $service ) ) {
					return $m[0];
				}
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
		$out = preg_replace_callback(
			'#<iframe\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>#is',
			array( $this, 'cb_iframe' ),
			$html
		);
		if ( null !== $out ) {
			return $out;
		}
		// 0.16.3 : même filet que rewrite_scripts, jamais de chaîne vide.
		$this->report_pcre_failure( 'iframe', preg_last_error(), strlen( $html ) );
		return $this->scan_tags( $html, 'iframe', false, 'cb_iframe' );
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
		$service = Freecookie_Categories::match_service( $sm[1] );
		// 0.16.1 : service exempté sur ce site (ex. YouTube sur un annuaire de chaînes) → l'iframe
		// garde son `src` et se charge comme avant le plugin ; aucune façade ne la recouvre.
		if ( $this->is_exempt( $service ) ) {
			return $m[0];
		}
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
		foreach ( Freecookie_Categories::known_services() as $svc ) {
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
