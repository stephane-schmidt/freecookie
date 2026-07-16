<?php
/**
 * Journal de preuve de consentement — stocké dans la base WordPress.
 *
 * Table locale : chaque enregistrement documente un acte de consentement,
 * exportable pour une demande CNIL. Aucune donnée n'est envoyée à un tiers.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freecookie_Consent_Store {

	/**
	 * Nom complet de la table (avec préfixe du site).
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'freecookie_log';
	}

	/**
	 * Crée / met à jour la table via dbDelta.
	 */
	public static function install() {
		global $wpdb;
		$table           = self::table();
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			created_at DATETIME NOT NULL,
			consent_id CHAR(36) NOT NULL,
			categories VARCHAR(255) NOT NULL,
			action VARCHAR(20) NOT NULL,
			banner_version VARCHAR(40) NOT NULL,
			policy_version VARCHAR(40) NOT NULL DEFAULT '',
			lang VARCHAR(12) NOT NULL DEFAULT '',
			region VARCHAR(12) NOT NULL DEFAULT '',
			ip_hash CHAR(64) NOT NULL DEFAULT '',
			ua VARCHAR(255) NOT NULL DEFAULT '',
			url VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			KEY consent_id (consent_id),
			KEY created_at (created_at)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Enregistre un acte de consentement.
	 *
	 * @param array $data Données déjà validées côté REST.
	 * @return int|false ID inséré ou false.
	 */
	public static function record( array $data ) {
		global $wpdb;

		$row = array(
			'created_at'     => current_time( 'mysql', true ),
			'consent_id'     => substr( (string) ( $data['consent_id'] ?? '' ), 0, 36 ),
			'categories'     => substr( (string) ( $data['categories'] ?? '' ), 0, 255 ),
			'action'         => substr( (string) ( $data['action'] ?? 'save' ), 0, 20 ),
			'banner_version' => substr( (string) ( $data['banner_version'] ?? '' ), 0, 40 ),
			'policy_version' => substr( (string) ( $data['policy_version'] ?? '' ), 0, 40 ),
			'lang'           => substr( (string) ( $data['lang'] ?? '' ), 0, 12 ),
			'region'         => substr( (string) ( $data['region'] ?? '' ), 0, 12 ),
			'ip_hash'        => self::hash_ip(),
			'ua'             => substr( (string) ( $data['ua'] ?? '' ), 0, 255 ),
			'url'            => substr( (string) ( $data['url'] ?? '' ), 0, 255 ),
		);

		$ok = $wpdb->insert( self::table(), $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $ok ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Hash irréversible et tronqué de l'IP (minimisation RGPD : on prouve
	 * l'origine sans conserver l'IP en clair). Salé par les clés WP du site.
	 *
	 * @return string
	 */
	protected static function hash_ip() {
		$ip = '';
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		if ( '' === $ip ) {
			return '';
		}
		// On masque le dernier octet avant de hasher (double minimisation).
		$ip   = preg_replace( '/\.\d+$/', '.0', $ip );
		$salt = defined( 'AUTH_SALT' ) ? AUTH_SALT : 'freecookie';
		return hash( 'sha256', $ip . '|' . $salt );
	}

	/**
	 * Compte total d'enregistrements.
	 *
	 * @return int
	 */
	public static function count() {
		global $wpdb;
		$table = self::table();
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore
	}
}
