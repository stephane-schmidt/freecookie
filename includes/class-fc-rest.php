<?php
/**
 * Endpoint REST : enregistre la preuve de consentement dans la base locale.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Rest {

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
	}

	/**
	 * Traite la requête.
	 *
	 * @param WP_REST_Request $req Requête.
	 * @return WP_REST_Response
	 */
	public function log_consent( WP_REST_Request $req ) {
		// Le nonce protège d'un abus trivial (le endpoint reste public).
		$nonce = $req->get_header( 'X-WP-Nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_REST_Response( array( 'ok' => false, 'error' => 'bad_nonce' ), 403 );
		}

		$id = FC_Consent_Store::record(
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
