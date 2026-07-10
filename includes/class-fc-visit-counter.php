<?php
/**
 * Compteur de visites LOCAL (honor system) — sans traceur, sans appel tiers.
 *
 * Compte des « visites » (≈ sessions) et non des pages vues : un cookie court
 * évite d'incrémenter à chaque page. Sert uniquement à afficher, au-delà d'un
 * seuil, un avis DISCRET côté administration invitant à soutenir le projet.
 * Rien n'est jamais bloqué, rien n'est envoyé nulle part.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Visit_Counter {

	const OPTION      = 'freecookie_visits';
	const SEEN_COOKIE = 'fc_v';

	/**
	 * Incrémente au plus une fois par visite (≈30 min).
	 * Hooké sur « init » côté front, avant émission du HTML.
	 */
	public function maybe_count() {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( ! empty( $_COOKIE[ self::SEEN_COOKIE ] ) ) {
			return;
		}
		// L'aperçu d'observation du scan (admin) n'est pas une visite.
		if ( FC_Scanner::is_sniff_request() ) {
			return;
		}
		// Ne pas compter les robots évidents.
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';
		if ( $ua && preg_match( '/bot|crawl|spider|slurp|preview|headless|lighthouse|freecookie-scanner/', $ua ) ) {
			return;
		}

		$month  = gmdate( 'Y-m' );
		$counts = get_option( self::OPTION, array() );
		if ( ! is_array( $counts ) ) {
			$counts = array();
		}
		$counts[ $month ] = isset( $counts[ $month ] ) ? (int) $counts[ $month ] + 1 : 1;

		// Ne garde que 12 mois glissants.
		if ( count( $counts ) > 12 ) {
			ksort( $counts );
			$counts = array_slice( $counts, -12, null, true );
		}
		update_option( self::OPTION, $counts, false );

		if ( ! headers_sent() ) {
			setcookie(
				self::SEEN_COOKIE,
				'1',
				array(
					'expires'  => time() + 1800,
					'path'     => defined( 'COOKIEPATH' ) ? COOKIEPATH : '/',
					'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
					'samesite' => 'Lax',
					'secure'   => is_ssl(),
					'httponly' => true,
				)
			);
		}
	}

	/**
	 * Nombre de visites du mois en cours.
	 *
	 * @return int
	 */
	public static function current_month() {
		$counts = get_option( self::OPTION, array() );
		$month  = gmdate( 'Y-m' );
		return is_array( $counts ) && isset( $counts[ $month ] ) ? (int) $counts[ $month ] : 0;
	}
}
