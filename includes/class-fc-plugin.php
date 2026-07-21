<?php
/**
 * Orchestrateur : câble tous les modules aux hooks WordPress.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Plugin {

	/** @var FC_Plugin|null */
	protected static $instance = null;

	/** @var array */
	protected $settings;

	/**
	 * Réglages par défaut (posés à l'activation).
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'blocking_enabled' => true,
			'detect_browser'   => true,
			'consent_days'     => 90, // Reco EDPB/CNIL : re-demander régulièrement (90 j par défaut).
			'visit_threshold'  => 10000,
			'hide_honor_notice' => false,
			'scan_frequency'   => 'weekly', // never | daily | weekly — scan automatique des traceurs.
			'scan_pages'       => 10, // pages échantillonnées par scan : 10, 25, 50 ou 100.
			'position'         => 'bottom',
			'badge_shape'      => 'croque-lateral',
			'license_key'      => '', // FreeCookie Pro (système de confiance).
			'colors'           => array(
				'accent'         => '', // vide = couleur principale du site (auto).
				'accent_text'    => '',
				'bg'             => '',
				'text'           => '',
				'secondary_bg'   => '',
				'secondary_text' => '',
				'badge'          => '',
			),
			'text_overrides'   => array(), // [langue][clé] => texte
			'about'            => array(
				// Opt-in strict : rien n'est affiché aux visiteurs tant que
				// l'administrateur du site n'active pas le volet et ne remplit
				// pas SES propres informations.
				'enabled' => false,
				'name'    => '',
				'tagline' => '',
				'website' => '',
				'email'   => '',
				'donate'  => '',
				'social'  => array(),
			),
		);
	}

	/**
	 * @return FC_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function __construct() {
		$saved          = get_option( 'freecookie_settings', array() );
		$this->settings = wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	/**
	 * Enregistre les hooks.
	 */
	public function run() {
		load_plugin_textdomain( 'freecookie', false, dirname( FREECOOKIE_BASENAME ) . '/languages' );

		// REST : journal de preuve.
		$rest = new FC_Rest();
		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );

		// Géo-ciblage : alimente le filtre de région (défaut '' = régime protecteur).
		add_filter( 'freecookie_region', array( 'FC_Geo', 'region' ) );

		// Liste de cookies publique.
		add_shortcode( 'freecookie_cookies', array( 'FC_Cookie_List', 'render' ) );

		// Déclencheur de scan (bouton fourni par l'écran d'admin — couche C).
		add_action( 'admin_post_freecookie_scan', array( $this, 'handle_scan' ) );

		// Scan automatique planifié (WP-Cron) + auto-réparation du planning
		// (une mise à jour du plugin ne repasse pas par l'activation).
		add_action( 'freecookie_scan_event', array( __CLASS__, 'cron_scan' ) );
		$freq = isset( $this->settings['scan_frequency'] ) ? $this->settings['scan_frequency'] : 'weekly';
		if ( in_array( $freq, array( 'daily', 'weekly' ), true ) && ! wp_next_scheduled( 'freecookie_scan_event' ) ) {
			self::sync_schedule( $freq );
		} elseif ( 'never' === $freq && wp_next_scheduled( 'freecookie_scan_event' ) ) {
			self::sync_schedule( 'never' );
		}

		// Front uniquement au-delà d'ici.
		if ( ! is_admin() ) {
			$counter = new FC_Visit_Counter();
			// 0.14.0 : plus de Set-Cookie serveur (page cacheable CDN) — sonde JS
			// dans le footer + signalement via la route REST (jamais cachée).
			add_action( 'wp_footer', array( $counter, 'print_probe' ), 99 );
			add_action( 'rest_api_init', array( $counter, 'register_rest' ) );

			if ( ! empty( $this->settings['blocking_enabled'] ) ) {
				$blocker = new FC_Script_Blocker();
				add_action( 'template_redirect', array( $blocker, 'start_buffer' ), 0 );

				$mode = new FC_Consent_Mode();
				add_action( 'wp_head', array( $mode, 'print_default' ), 0 );
			}

			$front = new FC_Frontend( $this->settings );
			add_action( 'wp_enqueue_scripts', array( $front, 'enqueue' ) );
			add_action( 'wp_footer', array( $front, 'render_banner' ), 20 );
		}

		// Administration : écran de réglages (apparence, textes, options, scan).
		if ( is_admin() ) {
			$admin = new FC_Admin();
			$admin->register();
		}

		// Avis honor system (administration).
		add_action( 'admin_notices', array( $this, 'honor_notice' ) );
	}

	/**
	 * (Re)programme le scan automatique selon la fréquence choisie.
	 *
	 * @param string $frequency never | daily | weekly.
	 */
	public static function sync_schedule( $frequency ) {
		wp_clear_scheduled_hook( 'freecookie_scan_event' );
		if ( in_array( $frequency, array( 'daily', 'weekly' ), true ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, $frequency, 'freecookie_scan_event' );
		}
	}

	/**
	 * Tâche planifiée : scan des traceurs + rafraîchissement des couleurs.
	 */
	public static function cron_scan() {
		FC_Scanner::scan();
		FC_Color_Detector::detect( true );
	}

	/**
	 * Traite une demande de scan (déclenchée depuis l'administration).
	 */
	public function handle_scan() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'freecookie' ) );
		}
		check_admin_referer( 'freecookie_scan' );

		$result = FC_Scanner::scan();
		FC_Color_Detector::detect( true ); // détection profonde des couleurs (fréquence).

		$back = add_query_arg(
			array(
				'page'        => 'freecookie',
				'fc_scanned'  => (int) $result['scanned'],
				'fc_services' => count( $result['services'] ),
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $back );
		exit;
	}

	/**
	 * Avis DISCRET, non bloquant, au-delà du seuil de visites.
	 */
	public function honor_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! empty( $this->settings['hide_honor_notice'] ) ) {
			return; // L'admin a choisi de masquer l'avis : on respecte.
		}
		$threshold = (int) $this->settings['visit_threshold'];
		$visits    = FC_Visit_Counter::current_month();
		if ( $visits <= $threshold ) {
			return;
		}
		echo '<div class="notice notice-info is-dismissible"><p>';
		printf(
			/* translators: 1: browsing sessions this month, 2: threshold. */
			esc_html__( 'FreeCookie : ce site a dépassé %1$s sessions de navigation ce mois-ci (approximation locale, sans traceur ; seuil gratuit : %2$s). Le plugin reste entièrement fonctionnel — si FreeCookie vous est utile, vous pouvez soutenir le projet (10 $/an ou 45 $ à vie). Merci !', 'freecookie' ),
			esc_html( number_format_i18n( $visits ) ),
			esc_html( number_format_i18n( $threshold ) )
		);
		echo ' <a href="' . esc_url( FC_Pro::BUY_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Soutenir (clé envoyée automatiquement par e-mail)', 'freecookie' ) . '</a>';
		echo ' · <a href="https://github.com/stephane-schmidt/freecookie" target="_blank" rel="noopener">' . esc_html__( 'En savoir plus', 'freecookie' ) . '</a>';
		echo ' — <a href="' . esc_url( admin_url( 'admin.php?page=freecookie' ) ) . '">' . esc_html__( 'masquer cet avis dans les réglages', 'freecookie' ) . '</a>.';
		echo '</p></div>';
	}
}
