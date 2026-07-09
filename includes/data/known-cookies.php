<?php
/**
 * Base de cookies connus, livrée avec le plugin (aucune requête tierce).
 * service => liste de cookies typiques déposés par ce service.
 *
 * Sert à composer la liste de cookies affichée au visiteur à partir des
 * services détectés par le scanner. Durées et finalités indicatives.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'google-analytics' => array(
		array( 'name' => '_ga', 'duration' => '2 ans', 'desc' => 'Identifiant de visiteur (Google Analytics).' ),
		array( 'name' => '_ga_*', 'duration' => '2 ans', 'desc' => 'État de session GA4.' ),
		array( 'name' => '_gid', 'duration' => '24 heures', 'desc' => 'Identifiant de visiteur (Google Analytics).' ),
		array( 'name' => '_gat', 'duration' => '1 minute', 'desc' => 'Limitation du débit de requêtes.' ),
	),
	'google-tag-manager' => array(
		array( 'name' => '_dc_gtm_*', 'duration' => '1 minute', 'desc' => 'Google Tag Manager.' ),
	),
	'google-ads' => array(
		array( 'name' => '_gcl_au', 'duration' => '3 mois', 'desc' => 'Attribution de conversion (Google Ads).' ),
		array( 'name' => 'IDE', 'duration' => '13 mois', 'desc' => 'Publicité et mesure (DoubleClick).' ),
		array( 'name' => 'test_cookie', 'duration' => '15 minutes', 'desc' => 'Vérifie la prise en charge des cookies.' ),
	),
	'meta-pixel' => array(
		array( 'name' => '_fbp', 'duration' => '3 mois', 'desc' => 'Suivi publicitaire Meta (Facebook).' ),
		array( 'name' => 'fr', 'duration' => '3 mois', 'desc' => 'Publicité ciblée Meta.' ),
	),
	'youtube' => array(
		array( 'name' => 'VISITOR_INFO1_LIVE', 'duration' => '6 mois', 'desc' => 'Préférences du lecteur YouTube, estimation de bande passante.' ),
		array( 'name' => 'YSC', 'duration' => 'Session', 'desc' => 'Statistiques des vidéos vues (YouTube).' ),
	),
	'vimeo' => array(
		array( 'name' => 'vuid', 'duration' => '2 ans', 'desc' => 'Statistiques de lecture (Vimeo).' ),
	),
	'google-maps' => array(
		array( 'name' => 'NID', 'duration' => '6 mois', 'desc' => 'Préférences Google (Maps).' ),
	),
	'hotjar' => array(
		array( 'name' => '_hjSessionUser_*', 'duration' => '1 an', 'desc' => 'Session utilisateur Hotjar.' ),
		array( 'name' => '_hjSession_*', 'duration' => '30 minutes', 'desc' => 'Session Hotjar.' ),
	),
	'linkedin' => array(
		array( 'name' => 'li_sugr', 'duration' => '3 mois', 'desc' => 'Correspondance de navigateur (LinkedIn).' ),
		array( 'name' => 'bcookie', 'duration' => '1 an', 'desc' => 'Identifiant d’appareil (LinkedIn).' ),
	),
	'twitter' => array(
		array( 'name' => 'personalization_id', 'duration' => '2 ans', 'desc' => 'Publicité et personnalisation (X/Twitter).' ),
	),
	'tiktok' => array(
		array( 'name' => '_ttp', 'duration' => '13 mois', 'desc' => 'Suivi publicitaire (TikTok).' ),
	),
	'soundcloud' => array(
		array( 'name' => 'sc_anonymous_id', 'duration' => '10 ans', 'desc' => 'Identifiant du lecteur SoundCloud.' ),
	),
	'matomo' => array(
		array( 'name' => '_pk_id.*', 'duration' => '13 mois', 'desc' => 'Identifiant de visiteur (Matomo).' ),
		array( 'name' => '_pk_ses.*', 'duration' => '30 minutes', 'desc' => 'Session de visite (Matomo).' ),
	),
	'microsoft-clarity' => array(
		array( 'name' => '_clck', 'duration' => '1 an', 'desc' => 'Identifiant de visiteur (Microsoft Clarity).' ),
		array( 'name' => '_clsk', 'duration' => '1 jour', 'desc' => 'Session et pages vues (Microsoft Clarity).' ),
	),
	'pinterest' => array(
		array( 'name' => '_pinterest_ct_ua', 'duration' => '1 an', 'desc' => 'Suivi de conversion (Pinterest).' ),
		array( 'name' => '_pin_unauth', 'duration' => '1 an', 'desc' => 'Regroupement d’audience (Pinterest).' ),
	),
	'snapchat' => array(
		array( 'name' => '_scid', 'duration' => '13 mois', 'desc' => 'Identifiant publicitaire (Snapchat).' ),
	),
	'wistia' => array(
		array( 'name' => 'wistia', 'duration' => 'Persistant', 'desc' => 'Statistiques de lecture vidéo (Wistia).' ),
	),
	'google-fonts' => array(),
);
