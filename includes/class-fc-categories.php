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
	 * Métadonnées d'affichage par service : finalité + note de respect de la
	 * vie privée (0-10 ; 10 = inoffensif/vert, bas = intrusif/rouge).
	 *
	 * @return array<string,array{purpose:string,score:int}>
	 */
	public static function services_meta() {
		return array(
			'google-analytics'   => array( 'purpose' => __( 'Statistiques de visite (Google Analytics) : pages vues, comportement.', 'freecookie' ), 'score' => 4 ),
			'google-tag-manager' => array( 'purpose' => __( 'Gestionnaire de balises Google : charge d’autres traceurs.', 'freecookie' ), 'score' => 4 ),
			'google-ads'         => array( 'purpose' => __( 'Publicité et remarketing Google.', 'freecookie' ), 'score' => 1 ),
			'meta-pixel'         => array( 'purpose' => __( 'Suivi publicitaire de Meta (Facebook / Instagram).', 'freecookie' ), 'score' => 1 ),
			'youtube'            => array( 'purpose' => __( 'Lecteur vidéo YouTube (dépose des cookies de suivi Google).', 'freecookie' ), 'score' => 4 ),
			'vimeo'              => array( 'purpose' => __( 'Lecteur vidéo Vimeo intégré.', 'freecookie' ), 'score' => 7 ),
			'google-maps'        => array( 'purpose' => __( 'Cartes Google (transmet votre adresse IP à Google).', 'freecookie' ), 'score' => 6 ),
			'hotjar'             => array( 'purpose' => __( 'Enregistrement de session et cartes de chaleur (Hotjar).', 'freecookie' ), 'score' => 3 ),
			'linkedin'           => array( 'purpose' => __( 'Suivi publicitaire LinkedIn.', 'freecookie' ), 'score' => 2 ),
			'twitter'            => array( 'purpose' => __( 'Widgets et suivi X (Twitter).', 'freecookie' ), 'score' => 2 ),
			'tiktok'             => array( 'purpose' => __( 'Suivi publicitaire TikTok.', 'freecookie' ), 'score' => 1 ),
			'instagram'          => array( 'purpose' => __( 'Contenu intégré Instagram (Meta).', 'freecookie' ), 'score' => 5 ),
			'snapchat'           => array( 'purpose' => __( 'Suivi publicitaire Snapchat.', 'freecookie' ), 'score' => 3 ),
			'spotify'            => array( 'purpose' => __( 'Lecteur Spotify intégré.', 'freecookie' ), 'score' => 7 ),
			'twitch'             => array( 'purpose' => __( 'Lecteur Twitch intégré.', 'freecookie' ), 'score' => 5 ),
			'dailymotion'        => array( 'purpose' => __( 'Lecteur vidéo Dailymotion.', 'freecookie' ), 'score' => 5 ),
			'wistia'             => array( 'purpose' => __( 'Lecteur vidéo Wistia (statistiques de lecture).', 'freecookie' ), 'score' => 6 ),
			'disqus'             => array( 'purpose' => __( 'Système de commentaires Disqus.', 'freecookie' ), 'score' => 6 ),
			'calendly'           => array( 'purpose' => __( 'Prise de rendez-vous Calendly.', 'freecookie' ), 'score' => 7 ),
			'typeform'           => array( 'purpose' => __( 'Formulaires Typeform intégrés.', 'freecookie' ), 'score' => 7 ),
			'pinterest'          => array( 'purpose' => __( 'Suivi publicitaire Pinterest.', 'freecookie' ), 'score' => 2 ),
			'microsoft-clarity'  => array( 'purpose' => __( 'Enregistrement de session (Microsoft Clarity).', 'freecookie' ), 'score' => 3 ),
			'soundcloud'         => array( 'purpose' => __( 'Lecteur audio SoundCloud intégré.', 'freecookie' ), 'score' => 7 ),
			'google-fonts'       => array( 'purpose' => __( 'Polices Google (transmet votre adresse IP à Google).', 'freecookie' ), 'score' => 6 ),
		);
	}

	/**
	 * Finalité + note d'un service (repli par catégorie si inconnu).
	 *
	 * @param string $key Clé de service.
	 * @return array{purpose:string,score:int,category:string}
	 */
	public static function meta( $key ) {
		$meta     = self::services_meta();
		$services = self::known_services();
		$category = isset( $services[ $key ] ) ? $services[ $key ]['category'] : 'marketing';
		if ( isset( $meta[ $key ] ) ) {
			return array(
				'purpose'  => $meta[ $key ]['purpose'],
				'score'    => (int) $meta[ $key ]['score'],
				'category' => $category,
			);
		}
		$fallback = array( 'necessary' => 10, 'preferences' => 6, 'statistics' => 4, 'marketing' => 2 );
		return array(
			'purpose'  => '',
			'score'    => isset( $fallback[ $category ] ) ? $fallback[ $category ] : 4,
			'category' => $category,
		);
	}

	/**
	 * Couleur de la note : green (>=7), orange (4-6), red (<=3).
	 *
	 * @param int $score Note.
	 * @return string
	 */
	public static function score_color( $score ) {
		if ( $score >= 7 ) {
			return 'green';
		}
		if ( $score >= 4 ) {
			return 'orange';
		}
		return 'red';
	}

	/**
	 * Libellé lisible d'un service.
	 *
	 * @param string $key Clé.
	 * @return string
	 */
	public static function service_label( $key ) {
		$labels = array(
			'google-analytics' => 'Google Analytics', 'google-tag-manager' => 'Google Tag Manager',
			'google-ads' => 'Google Ads', 'meta-pixel' => 'Meta Pixel', 'youtube' => 'YouTube',
			'vimeo' => 'Vimeo', 'google-maps' => 'Google Maps', 'hotjar' => 'Hotjar',
			'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)', 'tiktok' => 'TikTok',
			'instagram' => 'Instagram', 'snapchat' => 'Snapchat', 'spotify' => 'Spotify',
			'twitch' => 'Twitch', 'dailymotion' => 'Dailymotion', 'wistia' => 'Wistia',
			'disqus' => 'Disqus', 'calendly' => 'Calendly', 'typeform' => 'Typeform',
			'pinterest' => 'Pinterest', 'microsoft-clarity' => 'Microsoft Clarity',
			'soundcloud' => 'SoundCloud', 'google-fonts' => 'Google Fonts',
		);
		return isset( $labels[ $key ] ) ? $labels[ $key ] : ucwords( str_replace( '-', ' ', $key ) );
	}

	/**
	 * Clé de service correspondant à une URL (ou '' si inconnue).
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function match_service( $url ) {
		foreach ( self::known_services() as $key => $svc ) {
			foreach ( $svc['patterns'] as $needle ) {
				if ( false !== stripos( $url, $needle ) ) {
					return $key;
				}
			}
		}
		return '';
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
