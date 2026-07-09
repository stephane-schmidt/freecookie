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
			'consent_days'     => 180,
			'visit_threshold'  => 10000,
			'position'         => 'bottom',
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
				'enabled' => true,
				'name'    => 'FreeCookie',
				'tagline' => '',
				'website' => '',
				'email'   => '',
				'donate'  => 'https://revolut.me/stphanjt11',
				'social'  => array(
					'facebook'  => 'https://www.facebook.com/free.stephane',
					'instagram' => 'https://www.instagram.com/free.stephane',
					'tiktok'    => 'https://www.tiktok.com/@freestephane',
					'github'    => 'https://github.com/stephane-schmidt',
				),
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

		// Front uniquement au-delà d'ici.
		if ( ! is_admin() ) {
			$counter = new FC_Visit_Counter();
			add_action( 'init', array( $counter, 'maybe_count' ) );

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
		$threshold = (int) $this->settings['visit_threshold'];
		$visits    = FC_Visit_Counter::current_month();
		if ( $visits <= $threshold ) {
			return;
		}
		echo '<div class="notice notice-info is-dismissible"><p>';
		printf(
			/* translators: 1: visits this month, 2: threshold. */
			esc_html__( 'FreeCookie : ce site a dépassé %1$s visites ce mois-ci (seuil gratuit : %2$s). Le plugin reste entièrement fonctionnel — si FreeCookie vous est utile, vous pouvez soutenir le projet (10 $/an ou 45 $ à vie). Merci !', 'freecookie' ),
			esc_html( number_format_i18n( $visits ) ),
			esc_html( number_format_i18n( $threshold ) )
		);
		echo '</p></div>';
	}
}
