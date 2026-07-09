<?php
/**
 * Désinstallation : ne supprime les données que si l'utilisateur l'a demandé.
 * Par défaut on préserve le journal de preuve (obligation d'auditabilité).
 *
 * @package FreeCookie
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'freecookie_settings', array() );
$purge    = is_array( $settings ) && ! empty( $settings['purge_on_uninstall'] );

if ( $purge ) {
	global $wpdb;
	$table = $wpdb->prefix . 'freecookie_log';
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore
	delete_option( 'freecookie_settings' );
	delete_option( 'freecookie_visits' );
	delete_option( 'freecookie_db_version' );
}
