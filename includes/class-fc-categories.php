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
			'google-analytics' => array( 'purpose' => array( 'fr' => 'Statistiques de visite (Google Analytics) : pages vues, comportement.', 'en' => 'Visit statistics (Google Analytics): page views, behaviour.', 'de' => 'Besuchsstatistik (Google Analytics): Seitenaufrufe, Verhalten.', 'it' => 'Statistiche di visita (Google Analytics): pagine viste, comportamento.', 'es' => 'Estadísticas de visita (Google Analytics): páginas vistas, comportamiento.', 'nl' => 'Bezoekstatistieken (Google Analytics): paginaweergaven, gedrag.', 'pt' => 'Estatísticas de visita (Google Analytics): páginas vistas, comportamento.' ), 'score' => 4 ),
			'google-tag-manager' => array( 'purpose' => array( 'fr' => 'Gestionnaire de balises Google : charge d’autres traceurs.', 'en' => 'Google Tag Manager: loads other trackers.', 'de' => 'Google Tag Manager: lädt weitere Tracker.', 'it' => 'Google Tag Manager: carica altri tracker.', 'es' => 'Google Tag Manager: carga otros rastreadores.', 'nl' => 'Google Tag Manager: laadt andere trackers.', 'pt' => 'Google Tag Manager: carrega outros rastreadores.' ), 'score' => 4 ),
			'google-ads' => array( 'purpose' => array( 'fr' => 'Publicité et remarketing Google.', 'en' => 'Google advertising and remarketing.', 'de' => 'Google-Werbung und Remarketing.', 'it' => 'Pubblicità e remarketing di Google.', 'es' => 'Publicidad y remarketing de Google.', 'nl' => 'Google-advertenties en remarketing.', 'pt' => 'Publicidade e remarketing da Google.' ), 'score' => 1 ),
			'google-funding-choices' => array( 'purpose' => array( 'fr' => 'Fenêtre de consentement publicitaire de Google (Funding Choices) : charge des scripts Google et peut afficher une seconde bannière.', 'en' => 'Google ad-consent window (Funding Choices): loads Google scripts and may display a second banner.', 'de' => 'Google-Werbeeinwilligungsfenster (Funding Choices): lädt Google-Skripte und kann ein zweites Banner anzeigen.', 'it' => 'Finestra di consenso pubblicitario di Google (Funding Choices): carica script di Google e può mostrare un secondo banner.', 'es' => 'Ventana de consentimiento publicitario de Google (Funding Choices): carga scripts de Google y puede mostrar un segundo banner.', 'nl' => 'Google-advertentietoestemmingsvenster (Funding Choices): laadt Google-scripts en kan een tweede banner tonen.', 'pt' => 'Janela de consentimento publicitário da Google (Funding Choices): carrega scripts da Google e pode exibir um segundo banner.' ), 'score' => 1 ),
			'google-signin' => array( 'purpose' => array( 'fr' => 'Bouton « Se connecter avec Google » : charge un script Google et dépose un cookie d\'état de connexion.', 'en' => '“Sign in with Google” button: loads a Google script and sets a sign-in state cookie.', 'de' => 'Schaltfläche „Über Google anmelden“: lädt ein Google-Skript und setzt ein Anmeldestatus-Cookie.', 'it' => 'Pulsante «Accedi con Google»: carica uno script di Google e imposta un cookie di stato di accesso.', 'es' => 'Botón «Iniciar sesión con Google»: carga un script de Google y coloca una cookie de estado de sesión.', 'nl' => 'Knop “Inloggen met Google”: laadt een Google-script en plaatst een inlogstatus-cookie.', 'pt' => 'Botão «Iniciar sessão com o Google»: carrega um script da Google e coloca um cookie de estado de sessão.' ), 'score' => 5 ),
			'meta-pixel' => array( 'purpose' => array( 'fr' => 'Suivi publicitaire de Meta (Facebook / Instagram).', 'en' => 'Meta advertising tracking (Facebook / Instagram).', 'de' => 'Werbe-Tracking von Meta (Facebook / Instagram).', 'it' => 'Tracciamento pubblicitario di Meta (Facebook / Instagram).', 'es' => 'Seguimiento publicitario de Meta (Facebook / Instagram).', 'nl' => 'Advertentietracking van Meta (Facebook / Instagram).', 'pt' => 'Rastreamento publicitário da Meta (Facebook / Instagram).' ), 'score' => 1 ),
			'youtube' => array( 'purpose' => array( 'fr' => 'Lecteur vidéo YouTube (dépose des cookies de suivi Google).', 'en' => 'YouTube video player (sets Google tracking cookies).', 'de' => 'YouTube-Videoplayer (setzt Google-Tracking-Cookies).', 'it' => 'Lettore video YouTube (imposta cookie di tracciamento Google).', 'es' => 'Reproductor de vídeo de YouTube (coloca cookies de seguimiento de Google).', 'nl' => 'YouTube-videospeler (plaatst Google-trackingcookies).', 'pt' => 'Leitor de vídeo do YouTube (coloca cookies de rastreamento da Google).' ), 'score' => 4 ),
			'vimeo' => array( 'purpose' => array( 'fr' => 'Lecteur vidéo Vimeo intégré.', 'en' => 'Embedded Vimeo video player.', 'de' => 'Eingebetteter Vimeo-Videoplayer.', 'it' => 'Lettore video Vimeo incorporato.', 'es' => 'Reproductor de vídeo de Vimeo integrado.', 'nl' => 'Ingesloten Vimeo-videospeler.', 'pt' => 'Leitor de vídeo Vimeo incorporado.' ), 'score' => 7 ),
			'google-maps' => array( 'purpose' => array( 'fr' => 'Cartes Google (transmet votre adresse IP à Google).', 'en' => 'Google Maps (shares your IP address with Google).', 'de' => 'Google Maps (übermittelt Ihre IP-Adresse an Google).', 'it' => 'Google Maps (trasmette il tuo indirizzo IP a Google).', 'es' => 'Google Maps (transmite tu dirección IP a Google).', 'nl' => 'Google Maps (deelt uw IP-adres met Google).', 'pt' => 'Google Maps (transmite o seu endereço IP à Google).' ), 'score' => 6 ),
			'hotjar' => array( 'purpose' => array( 'fr' => 'Enregistrement de session et cartes de chaleur (Hotjar).', 'en' => 'Session recording and heatmaps (Hotjar).', 'de' => 'Sitzungsaufzeichnung und Heatmaps (Hotjar).', 'it' => 'Registrazione delle sessioni e mappe di calore (Hotjar).', 'es' => 'Grabación de sesiones y mapas de calor (Hotjar).', 'nl' => 'Sessieopname en heatmaps (Hotjar).', 'pt' => 'Gravação de sessões e mapas de calor (Hotjar).' ), 'score' => 3 ),
			'linkedin' => array( 'purpose' => array( 'fr' => 'Suivi publicitaire LinkedIn.', 'en' => 'LinkedIn advertising tracking.', 'de' => 'LinkedIn-Werbe-Tracking.', 'it' => 'Tracciamento pubblicitario LinkedIn.', 'es' => 'Seguimiento publicitario de LinkedIn.', 'nl' => 'LinkedIn-advertentietracking.', 'pt' => 'Rastreamento publicitário do LinkedIn.' ), 'score' => 2 ),
			'twitter' => array( 'purpose' => array( 'fr' => 'Widgets et suivi X (Twitter).', 'en' => 'X (Twitter) widgets and tracking.', 'de' => 'X-(Twitter-)Widgets und Tracking.', 'it' => 'Widget e tracciamento X (Twitter).', 'es' => 'Widgets y seguimiento de X (Twitter).', 'nl' => 'X-(Twitter-)widgets en tracking.', 'pt' => 'Widgets e rastreamento X (Twitter).' ), 'score' => 2 ),
			'tiktok' => array( 'purpose' => array( 'fr' => 'Suivi publicitaire TikTok.', 'en' => 'TikTok advertising tracking.', 'de' => 'TikTok-Werbe-Tracking.', 'it' => 'Tracciamento pubblicitario TikTok.', 'es' => 'Seguimiento publicitario de TikTok.', 'nl' => 'TikTok-advertentietracking.', 'pt' => 'Rastreamento publicitário do TikTok.' ), 'score' => 1 ),
			'instagram' => array( 'purpose' => array( 'fr' => 'Contenu intégré Instagram (Meta).', 'en' => 'Embedded Instagram content (Meta).', 'de' => 'Eingebettete Instagram-Inhalte (Meta).', 'it' => 'Contenuti Instagram incorporati (Meta).', 'es' => 'Contenido de Instagram integrado (Meta).', 'nl' => 'Ingesloten Instagram-inhoud (Meta).', 'pt' => 'Conteúdo do Instagram incorporado (Meta).' ), 'score' => 5 ),
			'snapchat' => array( 'purpose' => array( 'fr' => 'Suivi publicitaire Snapchat.', 'en' => 'Snapchat advertising tracking.', 'de' => 'Snapchat-Werbe-Tracking.', 'it' => 'Tracciamento pubblicitario Snapchat.', 'es' => 'Seguimiento publicitario de Snapchat.', 'nl' => 'Snapchat-advertentietracking.', 'pt' => 'Rastreamento publicitário do Snapchat.' ), 'score' => 3 ),
			'spotify' => array( 'purpose' => array( 'fr' => 'Lecteur Spotify intégré.', 'en' => 'Embedded Spotify player.', 'de' => 'Eingebetteter Spotify-Player.', 'it' => 'Lettore Spotify incorporato.', 'es' => 'Reproductor de Spotify integrado.', 'nl' => 'Ingesloten Spotify-speler.', 'pt' => 'Leitor Spotify incorporado.' ), 'score' => 7 ),
			'twitch' => array( 'purpose' => array( 'fr' => 'Lecteur Twitch intégré.', 'en' => 'Embedded Twitch player.', 'de' => 'Eingebetteter Twitch-Player.', 'it' => 'Lettore Twitch incorporato.', 'es' => 'Reproductor de Twitch integrado.', 'nl' => 'Ingesloten Twitch-speler.', 'pt' => 'Leitor Twitch incorporado.' ), 'score' => 5 ),
			'dailymotion' => array( 'purpose' => array( 'fr' => 'Lecteur vidéo Dailymotion.', 'en' => 'Dailymotion video player.', 'de' => 'Dailymotion-Videoplayer.', 'it' => 'Lettore video Dailymotion.', 'es' => 'Reproductor de vídeo de Dailymotion.', 'nl' => 'Dailymotion-videospeler.', 'pt' => 'Leitor de vídeo Dailymotion.' ), 'score' => 5 ),
			'wistia' => array( 'purpose' => array( 'fr' => 'Lecteur vidéo Wistia (statistiques de lecture).', 'en' => 'Wistia video player (playback analytics).', 'de' => 'Wistia-Videoplayer (Wiedergabestatistik).', 'it' => 'Lettore video Wistia (statistiche di riproduzione).', 'es' => 'Reproductor de vídeo de Wistia (estadísticas de reproducción).', 'nl' => 'Wistia-videospeler (afspeelstatistieken).', 'pt' => 'Leitor de vídeo Wistia (estatísticas de reprodução).' ), 'score' => 6 ),
			'disqus' => array( 'purpose' => array( 'fr' => 'Système de commentaires Disqus.', 'en' => 'Disqus commenting system.', 'de' => 'Disqus-Kommentarsystem.', 'it' => 'Sistema di commenti Disqus.', 'es' => 'Sistema de comentarios Disqus.', 'nl' => 'Disqus-reactiesysteem.', 'pt' => 'Sistema de comentários Disqus.' ), 'score' => 6 ),
			'calendly' => array( 'purpose' => array( 'fr' => 'Prise de rendez-vous Calendly.', 'en' => 'Calendly appointment booking.', 'de' => 'Terminbuchung mit Calendly.', 'it' => 'Prenotazione appuntamenti Calendly.', 'es' => 'Reserva de citas con Calendly.', 'nl' => 'Afspraken plannen met Calendly.', 'pt' => 'Marcação de reuniões Calendly.' ), 'score' => 7 ),
			'typeform' => array( 'purpose' => array( 'fr' => 'Formulaires Typeform intégrés.', 'en' => 'Embedded Typeform forms.', 'de' => 'Eingebettete Typeform-Formulare.', 'it' => 'Moduli Typeform incorporati.', 'es' => 'Formularios de Typeform integrados.', 'nl' => 'Ingesloten Typeform-formulieren.', 'pt' => 'Formulários Typeform incorporados.' ), 'score' => 7 ),
			'pinterest' => array( 'purpose' => array( 'fr' => 'Suivi publicitaire Pinterest.', 'en' => 'Pinterest advertising tracking.', 'de' => 'Pinterest-Werbe-Tracking.', 'it' => 'Tracciamento pubblicitario Pinterest.', 'es' => 'Seguimiento publicitario de Pinterest.', 'nl' => 'Pinterest-advertentietracking.', 'pt' => 'Rastreamento publicitário do Pinterest.' ), 'score' => 2 ),
			'microsoft-clarity' => array( 'purpose' => array( 'fr' => 'Enregistrement de session (Microsoft Clarity).', 'en' => 'Session recording (Microsoft Clarity).', 'de' => 'Sitzungsaufzeichnung (Microsoft Clarity).', 'it' => 'Registrazione delle sessioni (Microsoft Clarity).', 'es' => 'Grabación de sesiones (Microsoft Clarity).', 'nl' => 'Sessieopname (Microsoft Clarity).', 'pt' => 'Gravação de sessões (Microsoft Clarity).' ), 'score' => 3 ),
			'soundcloud' => array( 'purpose' => array( 'fr' => 'Lecteur audio SoundCloud intégré.', 'en' => 'Embedded SoundCloud audio player.', 'de' => 'Eingebetteter SoundCloud-Audioplayer.', 'it' => 'Lettore audio SoundCloud incorporato.', 'es' => 'Reproductor de audio de SoundCloud integrado.', 'nl' => 'Ingesloten SoundCloud-audiospeler.', 'pt' => 'Leitor de áudio SoundCloud incorporado.' ), 'score' => 7 ),
			'google-fonts' => array( 'purpose' => array( 'fr' => 'Polices Google (transmet votre adresse IP à Google).', 'en' => 'Google Fonts (shares your IP address with Google).', 'de' => 'Google Fonts (übermittelt Ihre IP-Adresse an Google).', 'it' => 'Google Fonts (trasmette il tuo indirizzo IP a Google).', 'es' => 'Google Fonts (transmite tu dirección IP a Google).', 'nl' => 'Google Fonts (deelt uw IP-adres met Google).', 'pt' => 'Google Fonts (transmite o seu endereço IP à Google).' ), 'score' => 6 ),
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
	 * Couleur du niveau : green (score >=7), orange (4-6), red (<=3).
	 *
	 * @param int $score Score interne.
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
	 * Niveau de risque FACTUEL affiché au visiteur (pas de note chiffrée,
	 * plus défendable et plus lisible) : low / medium / high.
	 *
	 * @param int $score Score interne.
	 * @return string
	 */
	public static function risk_key( $score ) {
		if ( $score >= 7 ) {
			return 'low';
		}
		if ( $score >= 4 ) {
			return 'medium';
		}
		return 'high';
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
			'google-ads' => 'Google Ads', 'google-funding-choices' => 'Google Funding Choices',
			'google-signin' => 'Google Sign-In',
			'meta-pixel' => 'Meta Pixel', 'youtube' => 'YouTube',
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
			'google-funding-choices' => array(
				'patterns'  => array( 'fundingchoicesmessages.google.com' ),
				'category'  => 'marketing',
			),
			'google-signin' => array(
				'patterns'  => array( 'accounts.google.com/gsi/client' ),
				'category'  => 'preferences',
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
