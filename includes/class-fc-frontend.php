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
			)
		);
	}

	/**
	 * Rend le bandeau dans le pied de page.
	 */
	public function render_banner() {
		$lang    = FC_I18n::detect( ! empty( $this->settings['detect_browser'] ) );
		$strings = $this->strings( $lang );
		$cats    = FC_Categories::all();
		include FREECOOKIE_DIR . 'public/partials/banner.php';
	}
}
