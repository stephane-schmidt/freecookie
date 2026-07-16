<?php
/**
 * Géo-ciblage LOCAL, sans base tierce.
 *
 * Lit le pays fourni par l'hébergeur/CDN (en-têtes Cloudflare, variables GeoIP
 * du serveur) s'il existe. À défaut, on ne devine pas : on applique le régime le
 * plus protecteur (consentement requis) — montrer la bannière n'est jamais une
 * faute. Aucun appel à un service de géolocalisation externe.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freecookie_Geo {

	/** Pays de l'EEE (UE + Islande, Liechtenstein, Norvège). */
	const EEA = array(
		'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU',
		'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES',
		'SE', 'IS', 'LI', 'NO',
	);

	/**
	 * Code pays ISO du visiteur, si l'infrastructure le fournit.
	 *
	 * @return string Code à 2 lettres en majuscules, ou '' si inconnu.
	 */
	public static function country() {
		$candidates = array();

		if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
			$candidates[] = wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] );
		}
		if ( ! empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) {
			$candidates[] = wp_unslash( $_SERVER['GEOIP_COUNTRY_CODE'] );
		}
		if ( ! empty( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) {
			$candidates[] = wp_unslash( $_SERVER['HTTP_X_COUNTRY_CODE'] );
		}

		foreach ( $candidates as $c ) {
			$c = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', (string) $c ), 0, 2 ) );
			if ( 2 === strlen( $c ) && 'XX' !== $c ) {
				return $c;
			}
		}
		return (string) apply_filters( 'freecookie_country', '' );
	}

	/**
	 * Région normalisée : EU, CH, UK, OTHER, ou '' (inconnu → traité comme EU).
	 *
	 * @return string
	 */
	public static function region() {
		$country = self::country();
		if ( '' === $country ) {
			return '';
		}
		if ( in_array( $country, self::EEA, true ) ) {
			return 'EU';
		}
		if ( 'CH' === $country ) {
			return 'CH';
		}
		if ( 'GB' === $country ) {
			return 'UK';
		}
		return 'OTHER';
	}

	/**
	 * Le consentement préalable est-il exigé pour cette région ?
	 * Inconnu ('') → oui (prudence). EU/CH/UK → oui. Autres → non.
	 *
	 * @return bool
	 */
	public static function requires_consent() {
		$region = self::region();
		return in_array( $region, array( '', 'EU', 'CH', 'UK' ), true );
	}
}
