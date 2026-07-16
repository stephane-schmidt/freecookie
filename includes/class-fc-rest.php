<?php
/**
 * Endpoint REST : enregistre la preuve de consentement dans la base locale.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freecookie_Rest {

	/**
	 * Déclare la route.
	 */
	public function register_routes() {
		register_rest_route(
			'freecookie/v1',
			'/consent',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'log_consent' ),
				'permission_callback' => '__return_true', // Public : consentement d'un visiteur anonyme.
				'args'                => array(
					'consent_id' => array( 'type' => 'string', 'required' => true ),
					'categories' => array( 'type' => 'string', 'required' => true ),
					'action'     => array( 'type' => 'string', 'required' => false ),
					'version'    => array( 'type' => 'string', 'required' => false ),
					'lang'       => array( 'type' => 'string', 'required' => false ),
					'region'     => array( 'type' => 'string', 'required' => false ),
				),
			)
		);

		// Scan interactif (administration uniquement).
		$admin_only = function () {
			return current_user_can( 'manage_options' );
		};
		register_rest_route(
			'freecookie/v1',
			'/scan/start',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'scan_start' ),
				'permission_callback' => $admin_only,
			)
		);
		register_rest_route(
			'freecookie/v1',
			'/scan/step',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'scan_step' ),
				'permission_callback' => $admin_only,
				'args'                => array(
					'url'   => array( 'type' => 'string', 'required' => true ),
					'html'  => array( 'type' => 'string', 'required' => false ),
					'probe' => array( 'type' => 'string', 'required' => false ),
				),
			)
		);
		register_rest_route(
			'freecookie/v1',
			'/scan/client-cookies',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'scan_client_cookies' ),
				'permission_callback' => $admin_only,
				'args'                => array(
					'names' => array( 'type' => 'string', 'required' => true ),
				),
			)
		);
		register_rest_route(
			'freecookie/v1',
			'/scan/finish',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'scan_finish' ),
				'permission_callback' => $admin_only,
			)
		);
	}

	/**
	 * Démarre un scan interactif : renvoie la liste d'URLs à examiner.
	 *
	 * @return WP_REST_Response
	 */
	public function scan_start() {
		Freecookie_Scanner::run_start();
		return new WP_REST_Response( array( 'ok' => true, 'urls' => Freecookie_Scanner::gather_urls() ), 200 );
	}

	/**
	 * Analyse une URL du site et renvoie ce qui a été trouvé (pour l'affichage en direct).
	 *
	 * @param WP_REST_Request $req Requête.
	 * @return WP_REST_Response
	 */
	public function scan_step( WP_REST_Request $req ) {
		$url  = esc_url_raw( (string) $req->get_param( 'url' ) );
		$home = home_url();
		if ( '' === $url || 0 !== strpos( $url, $home ) ) {
			return new WP_REST_Response( array( 'ok' => false ), 400 );
		}

		// Chemin principal : le HTML est fourni par le NAVIGATEUR de l'admin
		// (fetch même origine dans admin.js). Aucune requête du site vers
		// lui-même → fonctionne sur serveur mono-processus (wp server) et sur
		// les hébergements qui bloquent le loopback. Le HTML n'est jamais
		// stocké ni affiché : uniquement balayé par stripos() puis jeté.
		$html = (string) $req->get_param( 'html' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- analysé, jamais persisté/rendu.
		if ( '' !== $html ) {
			$services = Freecookie_Scanner::detect_services( substr( $html, 0, 2 * MB_IN_BYTES ) );
			// Sonde Set-Cookie serveur : une seule fois par scan (1re page), jamais bloquante.
			$cookies = $req->get_param( 'probe' ) ? Freecookie_Scanner::probe_set_cookie( $url ) : array();
			Freecookie_Scanner::run_merge( $services, $cookies, 1 );

			return new WP_REST_Response(
				array(
					'ok'       => true,
					'services' => $this->describe_services( $services ),
					'cookies'  => $this->describe_cookies( $cookies ),
				),
				200
			);
		}

		// Repli historique (sans HTML) : le serveur va chercher la page lui-même.
		$r = Freecookie_Scanner::scan_url( $url );
		Freecookie_Scanner::run_merge( $r['services'], $r['cookies'], $r['ok'] ? 1 : 0 );

		return new WP_REST_Response(
			array(
				'ok'       => $r['ok'],
				'services' => $this->describe_services( $r['services'] ),
				'cookies'  => $this->describe_cookies( $r['cookies'] ),
			),
			200
		);
	}

	/**
	 * Reçoit les cookies observés dans le navigateur (document.cookie de l'aperçu).
	 *
	 * @param WP_REST_Request $req Requête.
	 * @return WP_REST_Response
	 */
	public function scan_client_cookies( WP_REST_Request $req ) {
		$raw     = (string) $req->get_param( 'names' );
		$names   = array_filter( array_map( 'trim', explode( ',', $raw ) ) );
		$cookies = array();
		foreach ( array_slice( $names, 0, 100 ) as $name ) {
			$name = preg_replace( '/[^A-Za-z0-9_\-\.%]/', '', $name );
			// Cookies de session de l'ADMINISTRATEUR connecté : jamais posés aux
			// visiteurs anonymes → on ne pollue pas la liste avec.
			if ( '' === $name || preg_match( '/^(wordpress_|wp-settings-|wp_postpass_)/i', $name ) ) {
				continue;
			}
			$cookies[ $name ] = Freecookie_Scanner::classify_cookie( $name, 'js' );
		}
		Freecookie_Scanner::run_merge( array(), $cookies, 0 );

		return new WP_REST_Response( array( 'ok' => true, 'cookies' => $this->describe_cookies( $cookies ) ), 200 );
	}

	/**
	 * Termine le scan interactif : consolide, sauvegarde, rafraîchit les couleurs.
	 *
	 * @return WP_REST_Response
	 */
	public function scan_finish() {
		$result = Freecookie_Scanner::run_finish();
		if ( class_exists( 'Freecookie_Color_Detector' ) ) {
			Freecookie_Color_Detector::detect( true );
		}
		return new WP_REST_Response(
			array(
				'ok'       => true,
				'scanned'  => $result['scanned'],
				'services' => count( $result['services'] ),
				'cookies'  => count( $result['cookies'] ),
			),
			200
		);
	}

	/** Étiquettes lisibles des services, pour le journal en direct. */
	protected function describe_services( $services ) {
		$out = array();
		foreach ( $services as $key ) {
			$out[] = array( 'key' => $key, 'label' => Freecookie_Categories::service_label( $key ) );
		}
		return $out;
	}

	/** Étiquettes lisibles des cookies, pour le journal en direct. */
	protected function describe_cookies( $cookies ) {
		$lang = Freecookie_I18n::detect( false );
		$out  = array();
		foreach ( $cookies as $name => $meta ) {
			$out[] = array(
				'name'    => $name,
				'service' => ! empty( $meta['service'] ) ? Freecookie_Categories::service_label( $meta['service'] ) : '',
				'desc'    => Freecookie_I18n::pick( $meta['desc'], $lang ),
				'src'     => $meta['src'],
			);
		}
		return $out;
	}

	/**
	 * Traite la requête.
	 *
	 * @param WP_REST_Request $req Requête.
	 * @return WP_REST_Response
	 */
	public function log_consent( WP_REST_Request $req ) {
		// Le nonce protège d'un abus trivial (le endpoint reste public).
		// Réponse générique : ne pas révéler la raison du refus.
		$nonce = $req->get_header( 'X-WP-Nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_REST_Response( array( 'ok' => false ), 403 );
		}

		// Anti-abus : au plus 10 enregistrements par minute et par IP
		// (protège la table de journal contre un remplissage malveillant).
		$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$rlkey = 'freecookie_rl_' . md5( $ip );
		$hits  = (int) get_transient( $rlkey );
		if ( $hits >= 10 ) {
			return new WP_REST_Response( array( 'ok' => false ), 429 );
		}
		set_transient( $rlkey, $hits + 1, MINUTE_IN_SECONDS );

		$id = Freecookie_Consent_Store::record(
			array(
				'consent_id'     => preg_replace( '/[^a-z0-9\-]/i', '', (string) $req->get_param( 'consent_id' ) ),
				'categories'     => sanitize_text_field( (string) $req->get_param( 'categories' ) ),
				'action'         => sanitize_text_field( (string) $req->get_param( 'action' ) ),
				'banner_version' => sanitize_text_field( (string) $req->get_param( 'version' ) ),
				'lang'           => sanitize_text_field( (string) $req->get_param( 'lang' ) ),
				'region'         => sanitize_text_field( (string) $req->get_param( 'region' ) ),
				'ua'             => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
				'url'            => esc_url_raw( (string) $req->get_header( 'Referer' ) ),
			)
		);

		return new WP_REST_Response( array( 'ok' => (bool) $id ), $id ? 200 : 500 );
	}
}
