<?php
/**
 * Compteur de visites LOCAL (honor system) — sans traceur, sans appel tiers.
 *
 * Compte des « visites » (≈ sessions) et non des pages vues : un cookie court
 * évite d'incrémenter à chaque page. Sert uniquement à afficher, au-delà d'un
 * seuil, un avis DISCRET côté administration invitant à soutenir le projet.
 * Rien n'est jamais bloqué, rien n'est envoyé nulle part.
 *
 * Depuis la 0.14.0, le « cookie-echo » vit CÔTÉ CLIENT : la page n'émet plus
 * aucun Set-Cookie (une page HTML avec Set-Cookie est inéligible au cache CDN
 * — Cloudflare & co refusent de la garder en bord de réseau). Une mini-sonde
 * JS pose `fc_v=pending` au premier chargement, puis au suivant signale la
 * visite via un POST REST (non cacheable) et bascule le cookie sur `counted`.
 * Même philosophie qu'avant : seuls les clients qui exécutent le JS ET
 * conservent les cookies comptent — curl, scrapers et moniteurs ne comptent
 * jamais. Le HTML devient identique pour tous → cache pleine page possible.
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
	const REST_NS       = 'freecookie/v1';

	/**
	 * Route REST de signalement : POST /wp-json/freecookie/v1/visit.
	 * Réponse jamais mise en cache (REST), aucun corps requis ni renvoyé.
	 */
	public function register_rest() {
		register_rest_route(
			self::REST_NS,
			'/visit',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'count_visit' ),
			)
		);
	}

	/**
	 * Incrémente au plus une fois par session (≈30 min) — appelé par la sonde
	 * JS quand elle re-présente `fc_v=pending` (voir print_probe()).
	 *
	 * @return WP_REST_Response
	 */
	public function count_visit() {
		// Mêmes gardes que l'ancien comptage serveur : robots évidents exclus.
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';
		if ( ! $ua || preg_match( '/bot|crawl|spider|slurp|preview|headless|lighthouse|freecookie-scanner/', $ua ) ) {
			return new WP_REST_Response( null, 204 );
		}
		if ( class_exists( 'FC_Scanner' ) && FC_Scanner::is_sniff_request() ) {
			return new WP_REST_Response( null, 204 );
		}
		// Le cookie doit être en `pending` : sans lui (ou déjà `counted`), on ignore
		// — un POST forgé sans jar de cookies ne compte pas.
		$state = isset( $_COOKIE[ self::SEEN_COOKIE ] )
			? sanitize_text_field( wp_unslash( $_COOKIE[ self::SEEN_COOKIE ] ) )
			: '';
		if ( self::STATE_PENDING !== $state ) {
			return new WP_REST_Response( null, 204 );
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

		return new WP_REST_Response( null, 204 );
	}

	/**
	 * Sonde JS (footer front) : gère le cookie-echo côté client.
	 * `pending` posé au 1er chargement (sans compter) ; au chargement suivant,
	 * beacon vers la route REST puis bascule sur `counted` (30 min).
	 */
	public function print_probe() {
		if ( is_admin() ) {
			return;
		}
		$url = esc_url_raw( rest_url( self::REST_NS . '/visit' ) );
		?>
<script id="fc-visit-probe">(function(){try{
var m=document.cookie.match(/(?:^|; )fc_v=([^;]*)/),v=m?m[1]:'';
if(v==='<?php echo esc_js( self::STATE_COUNTED ); ?>'){return;}
function put(x){var d=new Date(Date.now()+18e5);document.cookie='fc_v='+x+'; expires='+d.toUTCString()+'; path=/; SameSite=Lax'+('https:'===location.protocol?'; Secure':'');}
if(v!=='<?php echo esc_js( self::STATE_PENDING ); ?>'){put('<?php echo esc_js( self::STATE_PENDING ); ?>');return;}
put('<?php echo esc_js( self::STATE_COUNTED ); ?>');
var u=<?php echo wp_json_encode( $url ); ?>;
if(navigator.sendBeacon){navigator.sendBeacon(u);}
else{var x=new XMLHttpRequest();x.open('POST',u,true);x.send();}
}catch(e){}})();</script>
		<?php
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
