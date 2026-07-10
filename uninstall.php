<?php
/**
 * Désinstallation : supprime toujours les options du plugin.
 * Le journal de preuve (table) n'est supprimé que si l'utilisateur l'a demandé —
 * par défaut on le préserve (obligation d'auditabilité RGPD).
 *
 * @package FreeCookie
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'freecookie_settings', array() );
$purge    = is_array( $settings ) && ! empty( $settings['purge_on_uninstall'] );

// Options : toujours nettoyées (pas de données orphelines).
delete_option( 'freecookie_settings' );
delete_option( 'freecookie_visits' );
delete_option( 'freecookie_db_version' );
delete_option( 'freecookie_colors_detected' );
delete_option( 'freecookie_scan' );

if ( $purge ) {
	global $wpdb;
	$table = $wpdb->prefix . 'freecookie_log';
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore
}
