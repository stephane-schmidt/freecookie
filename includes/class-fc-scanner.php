<?php
/**
 * Scanner de cookies LOCAL.
 *
 * Récupère quelques pages du site LUI-MÊME (boucle locale, pas un service tiers),
 * y détecte les services connus, capture les cookies réellement posés
 * (en-têtes Set-Cookie côté serveur, document.cookie côté navigateur pendant le
 * scan interactif), puis compose la liste affichée au visiteur.
 * Aucune donnée ne quitte le serveur.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Scanner {

	const OPTION   = 'freecookie_scan';
	const RUN_TR   = 'fc_scan_run'; // accumulation d'un scan interactif en cours.

	/**
	 * Requête d'aperçu d'observation ? (iframe du scan interactif, admin seulement).
	 *
	 * Pendant cet aperçu, le blocage et la bannière sont désactivés pour que les
	 * scripts s'exécutent réellement et posent leurs cookies dans le navigateur
	 * de l'administrateur — c'est ce qui permet de les observer.
	 *
	 * @return bool
	 */
	public static function is_sniff_request() {
		if ( empty( $_GET['fc_sniff'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}
		$nonce = isset( $_GET['fc_sniff'] ) ? sanitize_text_field( wp_unslash( $_GET['fc_sniff'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'fc_sniff' ) || ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		nocache_headers();
		return true;
	}

	/**
	 * Détecte les services connus dans un HTML.
	 *
	 * @param string $html HTML d'une page.
	 * @return string[] Clés de services détectés.
	 */
	public static function detect_services( $html ) {
		$found = array();
		foreach ( FC_Categories::known_services() as $key => $svc ) {
			foreach ( $svc['patterns'] as $needle ) {
				if ( false !== stripos( $html, $needle ) ) {
					$found[ $key ] = true;
					break;
				}
			}
		}
		return array_keys( $found );
	}

	/**
	 * URLs du site à examiner : accueil + quelques contenus publiés.
	 *
	 * @param int $max Nombre max d'URLs.
	 * @return string[]
	 */
	public static function gather_urls( $max = 0 ) {
		if ( $max <= 0 ) {
			// Réglage « Pages analysées » (10/25/50/100). Inutile de crawler TOUT
			// le site : les traceurs sont posés par le thème/les extensions et se
			// retrouvent partout — un échantillon représentatif suffit.
			$s   = wp_parse_args( get_option( 'freecookie_settings', array() ), FC_Plugin::default_settings() );
			$max = max( 5, min( 100, (int) ( $s['scan_pages'] ?? 10 ) ) );
		}
		$urls = array( home_url( '/' ) );

		// Tous les types de contenus publics (articles, pages, produits, CPT…).
		$types = get_post_types( array( 'public' => true ) );
		unset( $types['attachment'] );

		$posts = get_posts(
			array(
				'post_type'      => array_values( $types ),
				'post_status'    => 'publish',
				'posts_per_page' => $max - 1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);
		foreach ( $posts as $id ) {
			$link = get_permalink( $id );
			if ( $link ) {
				$urls[] = $link;
			}
		}
		return array_values( array_unique( $urls ) );
	}

	/**
	 * Analyse UNE URL : services connus dans le HTML + cookies posés en HTTP.
	 *
	 * @param string $url URL à examiner.
	 * @return array{ok:bool,services:string[],cookies:array<string,array>}
	 */
	public static function scan_url( $url ) {
		$resp = wp_safe_remote_get(
			$url,
			array(
				'timeout'     => 8,
				'redirection' => 2,
				'sslverify'   => false, // boucle locale : le certificat local peut être auto-signé.
				'user-agent'  => 'FreeCookie-Scanner/' . FREECOOKIE_VERSION,
			)
		);
		if ( is_wp_error( $resp ) ) {
			return array( 'ok' => false, 'services' => array(), 'cookies' => array() );
		}

		$body    = (string) wp_remote_retrieve_body( $resp );
		$cookies = array();
		foreach ( wp_remote_retrieve_cookies( $resp ) as $ck ) {
			if ( $ck instanceof WP_Http_Cookie && '' !== $ck->name ) {
				$cookies[ $ck->name ] = self::classify_cookie( $ck->name, 'http' );
			}
		}

		return array(
			'ok'       => '' !== $body,
			'services' => '' !== $body ? self::detect_services( $body ) : array(),
			'cookies'  => $cookies,
		);
	}

	/**
	 * Observation Set-Cookie BEST-EFFORT (jamais bloquante).
	 *
	 * Contrairement à scan_url(), cette sonde ne conditionne pas la réussite d'une
	 * étape : elle tente une requête locale courte uniquement pour capter les
	 * cookies posés côté serveur (souvent HttpOnly, invisibles à document.cookie).
	 * Elle est SAUTÉE sous le serveur PHP intégré (mono-processus : le site ne
	 * peut pas se répondre à lui-même pendant la requête → blocage garanti).
	 *
	 * @param string $url URL locale à sonder.
	 * @return array<string,array> name => classification (vide si sonde impossible).
	 */
	public static function probe_set_cookie( $url ) {
		if ( 'cli-server' === php_sapi_name() ) {
			return array();
		}
		$resp = wp_safe_remote_get(
			$url,
			array(
				'timeout'     => 3,
				'redirection' => 1,
				'sslverify'   => false,
				'user-agent'  => 'FreeCookie-Scanner/' . FREECOOKIE_VERSION,
			)
		);
		if ( is_wp_error( $resp ) ) {
			return array();
		}
		$cookies = array();
		foreach ( wp_remote_retrieve_cookies( $resp ) as $ck ) {
			if ( $ck instanceof WP_Http_Cookie && '' !== $ck->name ) {
				$cookies[ $ck->name ] = self::classify_cookie( $ck->name, 'http' );
			}
		}
		return $cookies;
	}

	/**
	 * Classe un cookie observé : service tiers connu, cookie interne connu, ou inconnu.
	 *
	 * @param string $name Nom du cookie.
	 * @param string $src  Source d'observation : http | js.
	 * @return array{src:string,cat:string,service:string,duration:string,desc:string|array}
	 */
	public static function classify_cookie( $name, $src = 'http' ) {
		// 1) Cookie d'un service tiers connu (base livrée) → révèle aussi le service.
		$svc = self::service_for_cookie( $name );
		if ( $svc ) {
			$map = FC_Categories::known_services();
			return array(
				'src'      => $src,
				'cat'      => isset( $map[ $svc['service'] ] ) ? $map[ $svc['service'] ]['category'] : 'marketing',
				'service'  => $svc['service'],
				'duration' => $svc['duration'],
				'desc'     => $svc['desc'],
			);
		}

		// 2) Cookie interne connu (WordPress, extensions courantes).
		foreach ( self::first_party_db() as $row ) {
			if ( self::name_matches( $name, $row['match'] ) ) {
				return array(
					'src'      => $src,
					'cat'      => $row['cat'],
					'service'  => '',
					'duration' => $row['duration'],
					'desc'     => $row['desc'],
				);
			}
		}

		// 3) Inconnu : cookie interne générique.
		return array(
			'src'      => $src,
			'cat'      => 'necessary',
			'service'  => '',
			'duration' => '',
			'desc'     => array( 'fr' => 'Cookie interne du site.', 'en' => 'Internal site cookie.', 'de' => 'Interner Website-Cookie.', 'it' => 'Cookie interno del sito.', 'es' => 'Cookie interno del sitio.', 'nl' => 'Interne cookie van de site.', 'pt' => 'Cookie interno do site.' ),
		);
	}

	/**
	 * Retrouve le service tiers correspondant à un nom de cookie observé
	 * (carte inverse de la base known-cookies : « _ga » → google-analytics).
	 *
	 * @param string $name Nom du cookie.
	 * @return array{service:string,duration:string,desc:string|array}|null
	 */
	public static function service_for_cookie( $name ) {
		static $db = null;
		if ( null === $db ) {
			$db = include FREECOOKIE_DIR . 'includes/data/known-cookies.php';
		}
		foreach ( $db as $svc => $cookies ) {
			foreach ( $cookies as $ck ) {
				// « fr » (Meta) est trop court/ambigu pour une détection par nom seul.
				if ( 'fr' === $ck['name'] ) {
					continue;
				}
				if ( self::name_matches( $name, $ck['name'] ) ) {
					return array( 'service' => $svc, 'duration' => $ck['duration'], 'desc' => $ck['desc'] );
				}
			}
		}
		return null;
	}

	/**
	 * Compare un nom de cookie à un motif avec joker « * » (« .* » toléré).
	 *
	 * @param string $name    Nom observé.
	 * @param string $pattern Motif (ex. « _ga_* », « _pk_id.* »).
	 * @return bool
	 */
	protected static function name_matches( $name, $pattern ) {
		if ( $name === $pattern ) {
			return true;
		}
		if ( false === strpos( $pattern, '*' ) ) {
			return false;
		}
		$re = '/^' . str_replace( array( '\.\*', '\*' ), '.*', preg_quote( $pattern, '/' ) ) . '$/i';
		return (bool) preg_match( $re, $name );
	}

	/** Base des cookies internes connus. */
	protected static function first_party_db() {
		static $db = null;
		if ( null === $db ) {
			$db = include FREECOOKIE_DIR . 'includes/data/known-first-party.php';
			/**
			 * Permet à un thème/site de déclarer SES propres cookies first-party
			 * (préférences, langue…) sans modifier le plugin — indispensable pour
			 * un plugin distribué. Chaque entrée : match, cat, duration, desc[].
			 *
			 * @param array $db Liste des cookies internes connus.
			 */
			$db = apply_filters( 'freecookie_known_first_party', $db );
			if ( ! is_array( $db ) ) {
				$db = array();
			}
		}
		return $db;
	}

	/**
	 * Scan complet en une passe (WP-Cron / fallback sans JavaScript).
	 *
	 * @return array{time:int,services:string[],cookies:array,scanned:int}
	 */
	public static function scan() {
		$urls     = self::gather_urls();
		$services = array();
		$cookies  = array();
		$scanned  = 0;
		$t0       = microtime( true );

		foreach ( $urls as $url ) {
			// Garde-fou pour le scan planifié côté serveur : arrêt propre après
			// 20 s (hébergements à temps d'exécution limité), en sauvegardant
			// ce qui a déjà été trouvé.
			if ( microtime( true ) - $t0 > 20 ) {
				break;
			}
			$r = self::scan_url( $url );
			if ( ! $r['ok'] ) {
				continue;
			}
			$scanned++;
			$services = array_merge( $services, $r['services'] );
			$cookies  = array_merge( $cookies, $r['cookies'] );
		}

		// Garde-fou anti-écrasement : si AUCUNE page n'a pu être récupérée
		// (serveur mono-worker incapable de se requêter, loopback bloqué par
		// l'hébergeur, panne réseau transitoire…), le résultat est ININTERPRÉTABLE.
		// On NE remplace PAS un scan précédent valide par ce vide trompeur — sinon
		// la bannière afficherait « aucun traceur » à tort. Le scan interactif
		// (HTML fourni par le navigateur de l'admin) reste la voie fiable.
		if ( 0 === $scanned ) {
			$previous = self::last();
			if ( is_array( $previous ) ) {
				return $previous;
			}
		}

		return self::save( $services, $cookies, $scanned );
	}

	/**
	 * Enregistre un résultat de scan consolidé.
	 *
	 * @param string[] $services Clés de services.
	 * @param array    $cookies  name => classification.
	 * @param int      $scanned  Pages analysées.
	 * @return array Résultat sauvegardé.
	 */
	public static function save( $services, $cookies, $scanned ) {
		// Les cookies de services tiers révèlent aussi le service correspondant.
		foreach ( $cookies as $meta ) {
			if ( ! empty( $meta['service'] ) ) {
				$services[] = $meta['service'];
			}
		}
		ksort( $cookies );
		$result = array(
			'time'     => time(),
			'services' => array_values( array_unique( $services ) ),
			'cookies'  => $cookies,
			'scanned'  => (int) $scanned,
		);
		update_option( self::OPTION, $result, false );
		return $result;
	}

	/* ---- Scan interactif (progressif, piloté par l'admin en AJAX) ---- */

	/** Démarre une accumulation de scan interactif. */
	public static function run_start() {
		set_transient( self::RUN_TR, array( 'services' => array(), 'cookies' => array(), 'scanned' => 0 ), 15 * MINUTE_IN_SECONDS );
	}

	/**
	 * Fusionne le résultat d'une étape dans l'accumulation en cours.
	 *
	 * @param string[] $services Services trouvés à cette étape.
	 * @param array    $cookies  Cookies trouvés à cette étape.
	 * @param int      $scanned_inc +1 si une page a été analysée.
	 */
	public static function run_merge( $services, $cookies, $scanned_inc = 0 ) {
		$run = get_transient( self::RUN_TR );
		if ( ! is_array( $run ) ) {
			$run = array( 'services' => array(), 'cookies' => array(), 'scanned' => 0 );
		}
		$run['services'] = array_values( array_unique( array_merge( $run['services'], $services ) ) );
		$run['cookies']  = array_merge( $run['cookies'], $cookies );
		$run['scanned'] += (int) $scanned_inc;
		set_transient( self::RUN_TR, $run, 15 * MINUTE_IN_SECONDS );
	}

	/**
	 * Termine le scan interactif : sauvegarde l'accumulation comme résultat.
	 *
	 * @return array Résultat sauvegardé.
	 */
	public static function run_finish() {
		$run = get_transient( self::RUN_TR );
		delete_transient( self::RUN_TR );
		if ( ! is_array( $run ) ) {
			$run = array( 'services' => array(), 'cookies' => array(), 'scanned' => 0 );
		}
		return self::save( $run['services'], $run['cookies'], $run['scanned'] );
	}

	/**
	 * Dernier résultat de scan.
	 *
	 * @return array|null
	 */
	public static function last() {
		$r = get_option( self::OPTION, null );
		return is_array( $r ) ? $r : null;
	}

	/**
	 * Construit la liste de cookies par finalité (services détectés + base connue
	 * + cookies internes réellement observés).
	 * S'il n'y a pas encore de scan, part de tous les services connus.
	 *
	 * @param string $lang Langue des durées + descriptions.
	 * @return array<string,array<int,array<string,string>>> category => cookies[]
	 */
	public static function report( $lang = 'en' ) {
		$db       = include FREECOOKIE_DIR . 'includes/data/known-cookies.php';
		$scan     = self::last();
		$services = ( $scan && ! empty( $scan['services'] ) ) ? $scan['services'] : array_keys( FC_Categories::known_services() );
		$map      = FC_Categories::known_services();

		$out = array();
		foreach ( $services as $svc ) {
			if ( empty( $map[ $svc ] ) || empty( $db[ $svc ] ) ) {
				continue;
			}
			$cat = $map[ $svc ]['category'];
			foreach ( $db[ $svc ] as $cookie ) {
				$out[ $cat ][] = array(
					'service'  => $svc,
					'name'     => $cookie['name'],
					'duration' => FC_I18n::duration_label( $cookie['duration'], $lang ),
					'desc'     => FC_I18n::pick( $cookie['desc'], $lang ),
				);
			}
		}

		// Cookies internes observés pendant le scan (hors services tiers, déjà listés).
		if ( $scan && ! empty( $scan['cookies'] ) && is_array( $scan['cookies'] ) ) {
			foreach ( $scan['cookies'] as $name => $meta ) {
				if ( ! empty( $meta['service'] ) ) {
					continue;
				}
				$out[ $meta['cat'] ][] = array(
					'service'  => '',
					'name'     => $name,
					'duration' => ! empty( $meta['duration'] ) ? FC_I18n::duration_label( $meta['duration'], $lang ) : '—',
					'desc'     => FC_I18n::pick( $meta['desc'], $lang ),
				);
			}
		}
		return $out;
	}
}
