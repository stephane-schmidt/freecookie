<?php
/**
 * FreeCookie Pro — validation de la clé de licence, en SYSTÈME DE CONFIANCE.
 *
 * Aucune vérification serveur, aucun appel réseau, aucun bridage caché : la
 * clé transmise après l'achat (10 $/an ou 45 $ à vie) suffit. Cette clé n'est
 * utilisée QUE par l'extension compagnon « FreeCookie Pro » (installée
 * séparément) : aucune fonctionnalité de ce plugin gratuit n'en dépend — la
 * conformité reste toujours gratuite et complète.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Pro {

	/**
	 * Boutique Pro (Polar, merchant of record — TVA/taxes gérées) : l'achat
	 * génère et envoie AUTOMATIQUEMENT une clé de licence par e-mail
	 * (préfixe FCPRO). Affiché côté administration uniquement.
	 */
	const BUY_URL = 'https://polar.sh/freeeconcept';

	/** Lien de don « Offrez-moi un café » (distinct de l'achat Pro). */
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
