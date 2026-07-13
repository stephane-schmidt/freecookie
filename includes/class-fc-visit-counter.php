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

	const OPTION        = 'freecookie_visits';
	const SEEN_COOKIE   = 'fc_v';
	const STATE_PENDING = 'pending';
	const STATE_COUNTED = 'counted';

	/**
	 * Incrémente au plus une fois par session (≈30 min), via « cookie-echo ».
	 * Hooké sur « init » côté front, avant émission du HTML.
	 *
	 * On ne compte QUE les clients qui nous re-présentent une sonde posée au hit
	 * précédent : un vrai navigateur (qui garde les cookies) le fait au 2e
	 * chargement, mais curl, scrapers à UA de navigateur, moniteurs et le
	 * loopback wp-cron — qui n'ont pas de jar de cookies — ne comptent JAMAIS.
	 * (Léger sous-comptage des visites 1-page : assumé, très préférable à la
	 * surestimation ×250 de l'ancien « pas de cookie = +1 à chaque hit ».)
	 */
	public function maybe_count() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
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

		$state = isset( $_COOKIE[ self::SEEN_COOKIE ] )
			? sanitize_text_field( wp_unslash( $_COOKIE[ self::SEEN_COOKIE ] ) )
			: '';

		// Déjà compté pendant la fenêtre de 30 min : ne rien faire.
		if ( self::STATE_COUNTED === $state ) {
			return;
		}

		// Premier contact (aucun cookie, ou valeur héritée d'une version
		// antérieure) : on pose une sonde `pending` SANS compter. Seul un client
		// avec jar de cookies nous la renverra au hit suivant.
		if ( self::STATE_PENDING !== $state ) {
			$this->set_seen_cookie( self::STATE_PENDING );
			return;
		}

		// $state === 'pending' : la sonde nous revient → client réel. On compte
		// une fois, puis on bascule sur `counted` pour ne pas recompter chaque
		// page de la session (re-comptable après expiration du cookie).
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

		$this->set_seen_cookie( self::STATE_COUNTED );
	}

	/**
	 * Pose le cookie de sonde (`pending` puis `counted`), durée 30 min, et le
	 * reflète tout de suite dans $_COOKIE pour la cohérence intra-requête.
	 *
	 * @param string $value Nouvel état (self::STATE_PENDING|STATE_COUNTED).
	 */
	private function set_seen_cookie( $value ) {
		if ( ! headers_sent() ) {
			setcookie(
				self::SEEN_COOKIE,
				$value,
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
		$_COOKIE[ self::SEEN_COOKIE ] = $value;
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
