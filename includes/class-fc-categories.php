<?php
/**
 * Finalités (catégories de consentement) et carte des services connus.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Categories {

	/**
	 * Les finalités, dans l'ordre d'affichage.
	 * « necessary » est toujours actif et verrouillé (exempté de consentement).
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function all() {
		return array(
			'necessary'   => array(
				'locked'  => true,
				'default' => true,
			),
			'preferences' => array(
				'locked'  => false,
				'default' => false,
			),
			'statistics'  => array(
				'locked'  => false,
				'default' => false,
			),
			'marketing'   => array(
				'locked'  => false,
				'default' => false,
			),
		);
	}

	/**
	 * Clés des finalités non verrouillées (celles que le visiteur choisit).
	 *
	 * @return string[]
	 */
	public static function optional_keys() {
		$keys = array();
		foreach ( self::all() as $key => $def ) {
			if ( empty( $def['locked'] ) ) {
				$keys[] = $key;
			}
		}
		return $keys;
	}

	/**
	 * Correspondance finalité → signaux Google Consent Mode v2.
	 *
	 * @return array<string,string[]>
	 */
	public static function consent_mode_map() {
		return array(
			'statistics' => array( 'analytics_storage' ),
			'marketing'  => array( 'ad_storage', 'ad_user_data', 'ad_personalization' ),
			'preferences' => array( 'functionality_storage', 'personalization_storage' ),
		);
	}

	/**
	 * Services tiers connus détectés par le moteur d'auto-blocage.
	 * Chaque service = liste de fragments d'URL (hôtes) + finalité.
	 *
	 * @return array<string,array{patterns:string[],category:string}>
	 */
	public static function known_services() {
		return array(
			'google-analytics' => array(
				'patterns'  => array( 'google-analytics.com', 'googletagmanager.com/gtag/js', 'analytics.google.com' ),
				'category'  => 'statistics',
			),
			'google-tag-manager' => array(
				'patterns'  => array( 'googletagmanager.com/gtm.js', 'googletagmanager.com/gtm.' ),
				'category'  => 'statistics',
			),
			'google-ads' => array(
				'patterns'  => array( 'googleadservices.com', 'googlesyndication.com', 'doubleclick.net' ),
				'category'  => 'marketing',
			),
			'meta-pixel' => array(
				'patterns'  => array( 'connect.facebook.net', 'facebook.com/tr' ),
				'category'  => 'marketing',
			),
			'youtube' => array(
				'patterns'  => array( 'youtube.com/embed', 'youtube-nocookie.com/embed', 'youtube.com/iframe_api' ),
				'category'  => 'marketing',
			),
			'vimeo' => array(
				'patterns'  => array( 'player.vimeo.com' ),
				'category'  => 'marketing',
			),
			'google-maps' => array(
				'patterns'  => array( 'maps.google.com/maps', 'google.com/maps/embed', 'maps.googleapis.com' ),
				'category'  => 'preferences',
			),
			'hotjar' => array(
				'patterns'  => array( 'static.hotjar.com', 'script.hotjar.com' ),
				'category'  => 'statistics',
			),
			'linkedin' => array(
				'patterns'  => array( 'snap.licdn.com', 'platform.linkedin.com' ),
				'category'  => 'marketing',
			),
			'twitter' => array(
				'patterns'  => array( 'platform.twitter.com', 'ads-twitter.com' ),
				'category'  => 'marketing',
			),
			'tiktok' => array(
				'patterns'  => array( 'analytics.tiktok.com' ),
				'category'  => 'marketing',
			),
			'instagram' => array(
				'patterns'  => array( 'instagram.com/embed' ),
				'category'  => 'marketing',
			),
			'soundcloud' => array(
				'patterns'  => array( 'w.soundcloud.com/player' ),
				'category'  => 'preferences',
			),
			'matomo' => array(
				'patterns'  => array( 'matomo.js', 'piwik.js', 'matomo.php' ),
				'category'  => 'statistics',
			),
			'microsoft-clarity' => array(
				'patterns'  => array( 'clarity.ms', '.clarity.ms/tag' ),
				'category'  => 'statistics',
			),
			'pinterest' => array(
				'patterns'  => array( 's.pinimg.com', 'ct.pinterest.com', 'assets.pinterest.com' ),
				'category'  => 'marketing',
			),
			'snapchat' => array(
				'patterns'  => array( 'sc-static.net', 'tr.snapchat.com' ),
				'category'  => 'marketing',
			),
			'spotify' => array(
				'patterns'  => array( 'open.spotify.com/embed', 'spotify.com/embed' ),
				'category'  => 'preferences',
			),
			'twitch' => array(
				'patterns'  => array( 'player.twitch.tv', 'embed.twitch.tv' ),
				'category'  => 'marketing',
			),
			'dailymotion' => array(
				'patterns'  => array( 'dailymotion.com/embed', 'geo.dailymotion.com/player' ),
				'category'  => 'marketing',
			),
			'wistia' => array(
				'patterns'  => array( 'fast.wistia.com', 'fast.wistia.net' ),
				'category'  => 'statistics',
			),
			'disqus' => array(
				'patterns'  => array( 'disqus.com/embed', '.disqus.com/count' ),
				'category'  => 'preferences',
			),
			'calendly' => array(
				'patterns'  => array( 'assets.calendly.com', 'calendly.com/assets' ),
				'category'  => 'preferences',
			),
			'typeform' => array(
				'patterns'  => array( 'embed.typeform.com' ),
				'category'  => 'preferences',
			),
			'google-fonts' => array(
				'patterns'  => array( 'fonts.googleapis.com', 'fonts.gstatic.com' ),
				'category'  => 'preferences',
			),
		);
	}
}
