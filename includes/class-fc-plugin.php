<?php
/**
 * Orchestrateur : câble tous les modules aux hooks WordPress.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freecookie_Plugin {

	/** @var Freecookie_Plugin|null */
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
			// 0.16.1 : services CONNUS (clés de Freecookie_Categories::known_services)
			// EXEMPTÉS du blocage a priori — chargés sans consentement, par décision de
			// l'éditeur du site, quand un service EST le site (un lecteur YouTube sur un
			// annuaire de chaînes n'est pas un traceur qu'on glisse, c'est la page). Vide
			// par défaut : tout reste bloqué. Le bandeau affiche ces services « Toujours
			// actif » ; le filtre `freecookie_exempt_services` complète la liste.
			'exempt_services'  => array(),
			// Détection de la langue du navigateur : ACTIVE par défaut depuis 0.15.4
			// (décision Stéphane 24/08 : un bandeau de consentement doit être compris
			// par le visiteur, pas par le site). Détection côté client (cache-safe,
			// voir 0.13.9), repli sur la langue du site ; Polylang/WPML priment.
			// Les réglages déjà enregistrés gardent leur choix (wp_parse_args).
			'detect_browser'   => true,
			// Comptes exemptés : pour eux, FreeCookie s'efface entièrement côté
			// front (ni bandeau, ni badge, ni blocage, ni comptage). L'équipe
			// connectée n'est pas un visiteur à faire consentir ; les visiteurs
			// anonymes — y compris via le cache de pages, qui ne sert jamais les
			// connectés — restent bloqués a priori.
			'hide_for'         => 'logged', // none | admins | logged.
			'consent_days'     => 90, // Reco EDPB/CNIL : re-demander régulièrement (90 j par défaut).
			'visit_threshold'  => 10000,
			'hide_honor_notice' => false,
			'scan_frequency'   => 'weekly', // never | daily | weekly — scan automatique des traceurs.
			'scan_pages'       => 10, // pages échantillonnées par scan : 10, 25, 50 ou 100.
			'purge_on_uninstall' => false, // false = le journal de preuve survit à la désinstallation (auditabilité).
			'position'         => 'bottom',
			// 0.16.0 : auto = suit prefers-color-scheme du navigateur ; light | dark = force.
			'theme'            => 'auto',
			// 0.15.0 : présentation du premier contact. `full` = le panneau complet en
			// dialogue (comportement historique). `mini` = une barre discrète en bas de
			// page (OK / Refuser / Plus d'infos) ; le panneau complet ne s'ouvre qu'à la
			// demande, EN FLUX dans la page (jamais en surcouche) — inséré après
			// l'élément désigné par `mini_anchor` (sélecteur CSS, ex. `footer .foot-row`),
			// ou juste au-dessus de la barre si l'ancre est introuvable.
			// 0.16.0 : `trait` = une ligne de 3 px au bord bas ; un toucher ouvre une rangée
			// de 40 px (Réglages / Refuser / Accepter) ; les détails se déplient en flux comme
			// en mode barre. Demande Stéphane 04/09 pour terondo.
			'layout'           => 'full',
			'mini_anchor'      => '',
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
			// Couleurs du mode SOMBRE (vide = palette de la maquette : carte #1c1c1a, encre #f2f1ec).
			'colors_dark'      => array(
				'bg'   => '',
				'text' => '',
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
	 * @return Freecookie_Plugin
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
		// WP ≥ 6.7 : charger le textdomain avant `init` déclenche un _doing_it_wrong
		// (« translation loading triggered too early ») — run() tourne sur plugins_loaded.
		add_action( 'init', function () {
			load_plugin_textdomain( 'freecookie', false, dirname( FREECOOKIE_BASENAME ) . '/languages' );
		} );

		// REST : journal de preuve.
		$rest = new Freecookie_Rest( $this->settings );
		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );

		// Géo-ciblage : alimente le filtre de région (défaut '' = régime protecteur).
		add_filter( 'freecookie_region', array( 'Freecookie_Geo', 'region' ) );

		// Liste de cookies publique.
		add_shortcode( 'freecookie_cookies', array( 'Freecookie_Cookie_List', 'render' ) );

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

		// Front uniquement au-delà d'ici. L'utilisateur courant n'est fiable
		// qu'à partir de `init` : tout le câblage front se décide là, pour
		// pouvoir exempter les comptes connectés (réglage « hide_for »).
		if ( ! is_admin() ) {
			add_action( 'init', array( $this, 'setup_front' ) );
		}

		// Administration : écran de réglages (apparence, textes, options, scan).
		if ( is_admin() ) {
			$admin = new Freecookie_Admin();
			$admin->register();
		}

		// Avis honor system (administration).
		add_action( 'admin_notices', array( $this, 'honor_notice' ) );
	}

	/**
	 * Câble le front : compteur de visites, blocage a priori, Consent Mode,
	 * bandeau. Sur `init`, une fois l'utilisateur courant connu — les comptes
	 * exemptés naviguent comme si le plugin n'était pas là.
	 */
	public function setup_front() {
		if ( self::hidden_for_user( $this->settings ) ) {
			return;
		}

		$counter = new Freecookie_Visit_Counter();
		$counter->maybe_count();

		if ( ! empty( $this->settings['blocking_enabled'] ) ) {
			$blocker = new Freecookie_Script_Blocker( Freecookie_Categories::exempt_services( $this->settings ) );
			add_action( 'template_redirect', array( $blocker, 'start_buffer' ), 0 );

			$mode = new Freecookie_Consent_Mode();
			add_action( 'wp_head', array( $mode, 'print_default' ), 0 );
		}

		$front = new Freecookie_Frontend( $this->settings );
		add_action( 'wp_enqueue_scripts', array( $front, 'enqueue' ) );
		add_action( 'wp_footer', array( $front, 'render_banner' ), 20 );
	}

	/**
	 * L'utilisateur courant est-il exempté de FreeCookie (réglage « hide_for ») ?
	 *
	 * @param array $settings Réglages du plugin.
	 * @return bool
	 */
	public static function hidden_for_user( $settings ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$mode = isset( $settings['hide_for'] ) ? $settings['hide_for'] : 'logged';
		if ( 'logged' === $mode ) {
			return true;
		}
		return 'admins' === $mode && current_user_can( 'manage_options' );
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
		Freecookie_Scanner::scan();
		Freecookie_Color_Detector::detect( true );
	}

	/**
	 * Traite une demande de scan (déclenchée depuis l'administration).
	 */
	public function handle_scan() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'freecookie' ) );
		}
		check_admin_referer( 'freecookie_scan' );

		$result = Freecookie_Scanner::scan();
		Freecookie_Color_Detector::detect( true ); // détection profonde des couleurs (fréquence).

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
		$visits    = Freecookie_Visit_Counter::current_month();
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
		echo ' <a href="' . esc_url( Freecookie_Pro::BUY_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Soutenir (clé envoyée automatiquement par e-mail)', 'freecookie' ) . '</a>';
		echo ' · <a href="https://github.com/stephane-schmidt/freecookie" target="_blank" rel="noopener">' . esc_html__( 'En savoir plus', 'freecookie' ) . '</a>';
		echo ' — <a href="' . esc_url( admin_url( 'admin.php?page=freecookie' ) ) . '">' . esc_html__( 'masquer cet avis dans les réglages', 'freecookie' ) . '</a>.';
		echo '</p></div>';
	}
}
