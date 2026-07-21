<?php
/**
 * Plugin Name:       FreeCookie Pro
 * Plugin URI:        https://polar.sh/freeeconcept
 * Description:       Extension compagnon de FreeCookie : 12 familles de formes supplémentaires (260 badges) pour le cookie flottant. Nécessite le plugin FreeCookie.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  freecookie
 * Author:            Stéphane Schmidt
 * Author URI:        https://alveo.design
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       freecookie-pro
 * Domain Path:       /languages
 *
 * @package FreeCookie_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FCPRO_VERSION', '1.0.0' );
define( 'FCPRO_FILE', __FILE__ );
define( 'FCPRO_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Démarrage. L'extension ne fait rien tant que FreeCookie n'est pas actif ;
 * ensuite elle enregistre ses familles de formes via le filtre public
 * `freecookie_shape_families` dès que la clé de licence saisie dans
 * FreeCookie ▸ FreeCookie Pro est active (système de confiance : validation
 * locale par FC_Pro, aucun appel réseau).
 */
function fcpro_boot() {
	load_plugin_textdomain( 'freecookie-pro', false, dirname( plugin_basename( FCPRO_FILE ) ) . '/languages' );

	// FreeCookie absent (ou version sans bibliothèque de formes) : avis, rien d'autre.
	if ( ! class_exists( 'FC_Shapes' ) ) {
		add_action( 'admin_notices', 'fcpro_notice_missing_freecookie' );
		return;
	}

	require_once FCPRO_DIR . 'includes/class-fcpro-shapes.php';

	// Clé de licence : vérifiée par FC_Pro du plugin principal si présent.
	if ( class_exists( 'FC_Pro' ) && ! FC_Pro::active( (array) get_option( 'freecookie_settings', array() ) ) ) {
		add_action( 'admin_notices', 'fcpro_notice_missing_key' );
		return;
	}

	add_filter( 'freecookie_shape_families', array( 'FCPro_Shapes', 'register' ) );
}
add_action( 'plugins_loaded', 'fcpro_boot', 20 );

/**
 * Avis d'administration : FreeCookie est requis.
 */
function fcpro_notice_missing_freecookie() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>'
		. esc_html__( 'FreeCookie Pro nécessite le plugin FreeCookie : installez-le et activez-le pour profiter des familles de formes supplémentaires.', 'freecookie-pro' )
		. '</p></div>';
}

/**
 * Avis d'administration : clé de licence absente ou incomplète.
 */
function fcpro_notice_missing_key() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>'
		. esc_html__( 'FreeCookie Pro est installé : saisissez la clé de licence reçue par e-mail (FCPRO-…) dans FreeCookie ▸ FreeCookie Pro pour activer les familles de formes supplémentaires.', 'freecookie-pro' )
		. '</p></div>';
}
