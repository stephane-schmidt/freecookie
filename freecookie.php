<?php
/**
 * Plugin Name:       FreeCookie — Cookie Consent RGPD/CNIL
 * Plugin URI:        https://github.com/stephane-schmidt/freecookie
 * Description:       Bandeau de consentement cookies 100 % local, conforme RGPD / ePrivacy / CNIL / nLPD. Blocage réel des traceurs avant consentement, journal de preuve dans votre base, Google Consent Mode v2, multilingue automatique. Aucun appel réseau tiers.
 * Version:           0.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stéphane Schmidt
 * Author URI:        https://alveo.design
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       freecookie
 * Domain Path:       /languages
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FREECOOKIE_VERSION', '0.2.0' );
define( 'FREECOOKIE_FILE', __FILE__ );
define( 'FREECOOKIE_DIR', plugin_dir_path( __FILE__ ) );
define( 'FREECOOKIE_URL', plugin_dir_url( __FILE__ ) );
define( 'FREECOOKIE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Nom du cookie de premier niveau où l'on stocke le choix du visiteur.
 * C'est un cookie strictement nécessaire (mémorise le consentement) : exempté.
 */
define( 'FREECOOKIE_COOKIE', 'freecookie_consent' );

require_once FREECOOKIE_DIR . 'includes/class-fc-categories.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-colors.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-color-detector.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-consent-store.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-i18n.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-script-blocker.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-consent-mode.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-visit-counter.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-geo.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-scanner.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-cookie-list.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-frontend.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-rest.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-admin.php';
require_once FREECOOKIE_DIR . 'includes/class-fc-plugin.php';

/**
 * Activation : crée la table du journal de preuve et pose les options par défaut.
 */
function freecookie_activate() {
	FC_Consent_Store::install();

	if ( false === get_option( 'freecookie_settings' ) ) {
		add_option( 'freecookie_settings', FC_Plugin::default_settings() );
	}
	if ( false === get_option( 'freecookie_db_version' ) ) {
		add_option( 'freecookie_db_version', FREECOOKIE_VERSION );
	}

	// Première détection des couleurs du site (sources structurées, sans réseau).
	FC_Color_Detector::detect( false );
}
register_activation_hook( __FILE__, 'freecookie_activate' );

/**
 * Démarrage.
 */
function freecookie_boot() {
	FC_Plugin::instance()->run();
}
add_action( 'plugins_loaded', 'freecookie_boot' );
