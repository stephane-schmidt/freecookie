<?php
/**
 * FreeCookie Pro — activation par clé, en SYSTÈME DE CONFIANCE.
 *
 * Aucune vérification serveur, aucun appel réseau, aucun bridage caché :
 * la clé transmise après le soutien (10 $/an ou 45 $ à vie) suffit. La
 * conformité de base reste toujours gratuite ; Pro n'ajoute que du confort
 * (familles de formes supplémentaires, et plus à venir).
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Pro {

	/** Lien de soutien (affiché côté administration uniquement). */
	const SUPPORT_URL = 'https://revolut.me/stphanjt11';

	/**
	 * Pro est-il actif pour ces réglages ?
	 *
	 * @param array $settings Réglages du plugin.
	 * @return bool
	 */
	public static function active( $settings ) {
		$key = isset( $settings['license_key'] ) ? trim( (string) $settings['license_key'] ) : '';
		return strlen( $key ) >= 8;
	}
}
