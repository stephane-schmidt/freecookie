<?php
/**
 * Rendu front : styles, script, bandeau, configuration passée au JS.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Frontend {

	/** @var array Réglages du plugin. */
	protected $settings;

	/**
	 * @param array $settings Réglages.
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Version du bandeau : tout changement invalide les consentements passés.
	 *
	 * @return string
	 */
	public function banner_version() {
		return FREECOOKIE_VERSION . '-' . substr( md5( wp_json_encode( $this->settings ) ), 0, 8 );
	}

	/**
	 * Chaînes de la langue + surcharges de texte de l'administration.
	 *
	 * @param string $lang Langue.
	 * @return array<string,string>
	 */
	protected function strings( $lang ) {
		$s  = FC_I18n::get( $lang );
		$ov = isset( $this->settings['text_overrides'][ $lang ] ) && is_array( $this->settings['text_overrides'][ $lang ] )
			? $this->settings['text_overrides'][ $lang ]
			: array();
		foreach ( $ov as $key => $value ) {
			if ( '' !== trim( (string) $value ) ) {
				$s[ $key ] = (string) $value;
			}
		}
		return $s;
	}

	/**
	 * Enfile CSS + JS et transmet la configuration.
	 */
	public function enqueue() {
		wp_enqueue_style( 'freecookie', FREECOOKIE_URL . 'public/css/freecookie.css', array(), FREECOOKIE_VERSION );
		wp_add_inline_style( 'freecookie', FC_Colors::inline_css( $this->settings ) );
		wp_enqueue_script( 'freecookie', FREECOOKIE_URL . 'public/js/freecookie.js', array(), FREECOOKIE_VERSION, true );

		$lang    = FC_I18n::detect( ! empty( $this->settings['detect_browser'] ) );
		$strings = $this->strings( $lang );

		$cats = array();
		foreach ( FC_Categories::all() as $key => $def ) {
			$cats[] = array(
				'key'     => $key,
				'locked'  => (bool) $def['locked'],
				'default' => (bool) $def['default'],
				'label'   => isset( $strings[ $key ] ) ? $strings[ $key ] : $key,
				'desc'    => isset( $strings[ $key . '_d' ] ) ? $strings[ $key . '_d' ] : '',
			);
		}

		wp_localize_script(
			'freecookie',
			'FreeCookieData',
			array(
				'cookie'         => FREECOOKIE_COOKIE,
				'version'        => $this->banner_version(),
				'lang'           => $lang,
				'region'         => (string) apply_filters( 'freecookie_region', '' ),
				'consentExpiry'  => (int) $this->settings['consent_days'],
				'categories'     => $cats,
				'consentModeMap' => FC_Categories::consent_mode_map(),
				'restUrl'        => esc_url_raw( rest_url( 'freecookie/v1/consent' ) ),
				'nonce'          => wp_create_nonce( 'wp_rest' ),
				'strings'        => $strings,
				// Mode auto : aucune couleur principale fixée dans les réglages
				// → le badge/bannière suit la couleur dominante de CHAQUE page.
				'autoColor'      => ( '' === FC_Colors::sanitize( isset( $this->settings['colors']['accent'] ) ? $this->settings['colors']['accent'] : '' ) ),
			)
		);
	}

	/**
	 * Rend le bandeau dans le pied de page.
	 */
	/**
	 * Services tiers détectés sur le site, groupés par catégorie, avec finalité + note.
	 *
	 * @param string $lang Langue des fiches cookies (durées + descriptions).
	 * @return array<string,array<int,array<string,mixed>>>
	 */
	protected function detected_services( $lang = 'en' ) {
		$scan = FC_Scanner::last();
		$keys = ( $scan && ! empty( $scan['services'] ) ) ? $scan['services'] : array();
		$out  = array();
		$db   = include FREECOOKIE_DIR . 'includes/data/known-cookies.php';
		foreach ( $keys as $key ) {
			$meta = FC_Categories::meta( $key );
			if ( 'necessary' === $meta['category'] ) {
				continue;
			}
			$cookies = array();
			if ( ! empty( $db[ $key ] ) ) {
				foreach ( $db[ $key ] as $ck ) {
					$cookies[] = array(
						'name'     => $ck['name'],
						'duration' => FC_I18n::duration_label( $ck['duration'], $lang ),
						'desc'     => FC_I18n::pick( $ck['desc'], $lang ),
					);
				}
			}
			$out[ $meta['category'] ][] = array(
				'key'     => $key,
				'label'   => FC_Categories::service_label( $key ),
				'purpose' => $meta['purpose'],
				'score'   => $meta['score'],
				'risk'    => FC_Categories::risk_key( $meta['score'] ),
				'color'   => FC_Categories::score_color( $meta['score'] ),
				'cookies' => $cookies,
			);
		}
		return $out;
	}

	public function render_banner() {
		// Aperçu d'observation du scan : pas de bannière dans l'iframe.
		if ( FC_Scanner::is_sniff_request() ) {
			return;
		}
		$lang     = FC_I18n::detect( ! empty( $this->settings['detect_browser'] ) );
		$strings  = $this->strings( $lang );
		$cats     = FC_Categories::all();
		$services = $this->detected_services( $lang );
		$defaults = FC_Plugin::default_settings();
		$about    = isset( $this->settings['about'] ) && is_array( $this->settings['about'] )
			? wp_parse_args( $this->settings['about'], $defaults['about'] )
			: $defaults['about'];
		$alabels  = FC_I18n::about_labels( $lang );
		$shape    = FC_Shapes::valid( isset( $this->settings['badge_shape'] ) ? $this->settings['badge_shape'] : '' );
		if ( FC_Shapes::is_pro( $shape ) && ! FC_Pro::active( $this->settings ) ) {
			$shape = FC_Shapes::DEFAULT_ID; // forme Pro sans clé : repli sur la forme libre.
		}
		include FREECOOKIE_DIR . 'public/partials/banner.php';
	}
}
