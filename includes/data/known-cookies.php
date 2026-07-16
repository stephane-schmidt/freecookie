<?php
/**
 * Base de cookies connus, livrée avec le plugin (aucune requête tierce).
 * service => liste de cookies typiques déposés par ce service.
 *
 * Sert à composer la liste de cookies affichée au visiteur à partir des
 * services détectés par le scanner. Durées et finalités indicatives.
 *
 * Format neutre : 'duration' = jeton traduit par Freecookie_I18n::duration_label(),
 * 'desc' = tableau multilingue résolu par Freecookie_I18n::pick().
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fc_d = array(
	'ga_visitor'      => array( 'fr' => 'Identifiant de visiteur (Google Analytics).', 'en' => 'Visitor identifier (Google Analytics).', 'de' => 'Besucher-Kennung (Google Analytics).', 'it' => 'Identificativo del visitatore (Google Analytics).', 'es' => 'Identificador de visitante (Google Analytics).', 'nl' => 'Bezoekers-ID (Google Analytics).', 'pt' => 'Identificador de visitante (Google Analytics).' ),
	'ga4_state'       => array( 'fr' => 'État de session GA4.', 'en' => 'GA4 session state.', 'de' => 'GA4-Sitzungsstatus.', 'it' => 'Stato della sessione GA4.', 'es' => 'Estado de la sesión GA4.', 'nl' => 'GA4-sessiestatus.', 'pt' => 'Estado da sessão GA4.' ),
	'rate_limit'      => array( 'fr' => 'Limitation du débit de requêtes.', 'en' => 'Request rate limiting.', 'de' => 'Begrenzung der Anfragerate.', 'it' => 'Limitazione della frequenza delle richieste.', 'es' => 'Limitación de la frecuencia de solicitudes.', 'nl' => 'Beperking van het aantal verzoeken.', 'pt' => 'Limitação da taxa de pedidos.' ),
	'gtm'             => 'Google Tag Manager.',
	'ads_attribution' => array( 'fr' => 'Attribution de conversion (Google Ads).', 'en' => 'Conversion attribution (Google Ads).', 'de' => 'Conversion-Zuordnung (Google Ads).', 'it' => 'Attribuzione delle conversioni (Google Ads).', 'es' => 'Atribución de conversiones (Google Ads).', 'nl' => 'Conversietoewijzing (Google Ads).', 'pt' => 'Atribuição de conversões (Google Ads).' ),
	'doubleclick'     => array( 'fr' => 'Publicité et mesure (DoubleClick).', 'en' => 'Advertising and measurement (DoubleClick).', 'de' => 'Werbung und Messung (DoubleClick).', 'it' => 'Pubblicità e misurazione (DoubleClick).', 'es' => 'Publicidad y medición (DoubleClick).', 'nl' => 'Advertenties en meting (DoubleClick).', 'pt' => 'Publicidade e medição (DoubleClick).' ),
	'test_cookie'     => array( 'fr' => 'Vérifie la prise en charge des cookies.', 'en' => 'Checks whether cookies are supported.', 'de' => 'Prüft, ob Cookies unterstützt werden.', 'it' => 'Verifica il supporto dei cookie.', 'es' => 'Comprueba si las cookies son compatibles.', 'nl' => 'Controleert of cookies worden ondersteund.', 'pt' => 'Verifica se os cookies são suportados.' ),
	'meta_tracking'   => array( 'fr' => 'Suivi publicitaire Meta (Facebook).', 'en' => 'Meta (Facebook) advertising tracking.', 'de' => 'Werbe-Tracking von Meta (Facebook).', 'it' => 'Tracciamento pubblicitario Meta (Facebook).', 'es' => 'Seguimiento publicitario de Meta (Facebook).', 'nl' => 'Advertentietracking van Meta (Facebook).', 'pt' => 'Rastreamento publicitário da Meta (Facebook).' ),
	'meta_targeted'   => array( 'fr' => 'Publicité ciblée Meta.', 'en' => 'Targeted advertising (Meta).', 'de' => 'Gezielte Werbung (Meta).', 'it' => 'Pubblicità mirata (Meta).', 'es' => 'Publicidad personalizada (Meta).', 'nl' => 'Gerichte advertenties (Meta).', 'pt' => 'Publicidade direcionada (Meta).' ),
	'yt_prefs'        => array( 'fr' => 'Préférences du lecteur YouTube, estimation de bande passante.', 'en' => 'YouTube player preferences, bandwidth estimation.', 'de' => 'Einstellungen des YouTube-Players, Bandbreitenschätzung.', 'it' => 'Preferenze del lettore YouTube, stima della banda.', 'es' => 'Preferencias del reproductor de YouTube, estimación del ancho de banda.', 'nl' => 'Voorkeuren van de YouTube-speler, bandbreedteschatting.', 'pt' => 'Preferências do leitor YouTube, estimativa de largura de banda.' ),
	'yt_stats'        => array( 'fr' => 'Statistiques des vidéos vues (YouTube).', 'en' => 'Statistics on videos watched (YouTube).', 'de' => 'Statistiken zu angesehenen Videos (YouTube).', 'it' => 'Statistiche sui video guardati (YouTube).', 'es' => 'Estadísticas de vídeos vistos (YouTube).', 'nl' => 'Statistieken van bekeken video’s (YouTube).', 'pt' => 'Estatísticas de vídeos vistos (YouTube).' ),
	'vimeo_stats'     => array( 'fr' => 'Statistiques de lecture (Vimeo).', 'en' => 'Playback statistics (Vimeo).', 'de' => 'Wiedergabestatistiken (Vimeo).', 'it' => 'Statistiche di riproduzione (Vimeo).', 'es' => 'Estadísticas de reproducción (Vimeo).', 'nl' => 'Afspeelstatistieken (Vimeo).', 'pt' => 'Estatísticas de reprodução (Vimeo).' ),
	'google_prefs'    => array( 'fr' => 'Préférences Google (Maps).', 'en' => 'Google preferences (Maps).', 'de' => 'Google-Einstellungen (Maps).', 'it' => 'Preferenze Google (Maps).', 'es' => 'Preferencias de Google (Maps).', 'nl' => 'Google-voorkeuren (Maps).', 'pt' => 'Preferências Google (Maps).' ),
	'hotjar_user'     => array( 'fr' => 'Session utilisateur Hotjar.', 'en' => 'Hotjar user session.', 'de' => 'Hotjar-Benutzersitzung.', 'it' => 'Sessione utente Hotjar.', 'es' => 'Sesión de usuario de Hotjar.', 'nl' => 'Hotjar-gebruikerssessie.', 'pt' => 'Sessão de utilizador Hotjar.' ),
	'hotjar_session'  => array( 'fr' => 'Session Hotjar.', 'en' => 'Hotjar session.', 'de' => 'Hotjar-Sitzung.', 'it' => 'Sessione Hotjar.', 'es' => 'Sesión de Hotjar.', 'nl' => 'Hotjar-sessie.', 'pt' => 'Sessão Hotjar.' ),
	'li_match'        => array( 'fr' => 'Correspondance de navigateur (LinkedIn).', 'en' => 'Browser matching (LinkedIn).', 'de' => 'Browser-Zuordnung (LinkedIn).', 'it' => 'Abbinamento del browser (LinkedIn).', 'es' => 'Correspondencia de navegador (LinkedIn).', 'nl' => 'Browserkoppeling (LinkedIn).', 'pt' => 'Correspondência de navegador (LinkedIn).' ),
	'li_device'       => array( 'fr' => 'Identifiant d’appareil (LinkedIn).', 'en' => 'Device identifier (LinkedIn).', 'de' => 'Geräte-Kennung (LinkedIn).', 'it' => 'Identificativo del dispositivo (LinkedIn).', 'es' => 'Identificador de dispositivo (LinkedIn).', 'nl' => 'Apparaat-ID (LinkedIn).', 'pt' => 'Identificador do dispositivo (LinkedIn).' ),
	'x_ads'           => array( 'fr' => 'Publicité et personnalisation (X/Twitter).', 'en' => 'Advertising and personalisation (X/Twitter).', 'de' => 'Werbung und Personalisierung (X/Twitter).', 'it' => 'Pubblicità e personalizzazione (X/Twitter).', 'es' => 'Publicidad y personalización (X/Twitter).', 'nl' => 'Advertenties en personalisatie (X/Twitter).', 'pt' => 'Publicidade e personalização (X/Twitter).' ),
	'tiktok_ads'      => array( 'fr' => 'Suivi publicitaire (TikTok).', 'en' => 'Advertising tracking (TikTok).', 'de' => 'Werbe-Tracking (TikTok).', 'it' => 'Tracciamento pubblicitario (TikTok).', 'es' => 'Seguimiento publicitario (TikTok).', 'nl' => 'Advertentietracking (TikTok).', 'pt' => 'Rastreamento publicitário (TikTok).' ),
	'sc_player'       => array( 'fr' => 'Identifiant du lecteur SoundCloud.', 'en' => 'SoundCloud player identifier.', 'de' => 'Kennung des SoundCloud-Players.', 'it' => 'Identificativo del lettore SoundCloud.', 'es' => 'Identificador del reproductor de SoundCloud.', 'nl' => 'ID van de SoundCloud-speler.', 'pt' => 'Identificador do leitor SoundCloud.' ),
	'matomo_visitor'  => array( 'fr' => 'Identifiant de visiteur (Matomo).', 'en' => 'Visitor identifier (Matomo).', 'de' => 'Besucher-Kennung (Matomo).', 'it' => 'Identificativo del visitatore (Matomo).', 'es' => 'Identificador de visitante (Matomo).', 'nl' => 'Bezoekers-ID (Matomo).', 'pt' => 'Identificador de visitante (Matomo).' ),
	'matomo_session'  => array( 'fr' => 'Session de visite (Matomo).', 'en' => 'Visit session (Matomo).', 'de' => 'Besuchssitzung (Matomo).', 'it' => 'Sessione di visita (Matomo).', 'es' => 'Sesión de visita (Matomo).', 'nl' => 'Bezoeksessie (Matomo).', 'pt' => 'Sessão de visita (Matomo).' ),
	'clarity_visitor' => array( 'fr' => 'Identifiant de visiteur (Microsoft Clarity).', 'en' => 'Visitor identifier (Microsoft Clarity).', 'de' => 'Besucher-Kennung (Microsoft Clarity).', 'it' => 'Identificativo del visitatore (Microsoft Clarity).', 'es' => 'Identificador de visitante (Microsoft Clarity).', 'nl' => 'Bezoekers-ID (Microsoft Clarity).', 'pt' => 'Identificador de visitante (Microsoft Clarity).' ),
	'clarity_session' => array( 'fr' => 'Session et pages vues (Microsoft Clarity).', 'en' => 'Session and page views (Microsoft Clarity).', 'de' => 'Sitzung und Seitenaufrufe (Microsoft Clarity).', 'it' => 'Sessione e pagine viste (Microsoft Clarity).', 'es' => 'Sesión y páginas vistas (Microsoft Clarity).', 'nl' => 'Sessie en paginaweergaven (Microsoft Clarity).', 'pt' => 'Sessão e páginas vistas (Microsoft Clarity).' ),
	'pin_conversion'  => array( 'fr' => 'Suivi de conversion (Pinterest).', 'en' => 'Conversion tracking (Pinterest).', 'de' => 'Conversion-Tracking (Pinterest).', 'it' => 'Monitoraggio delle conversioni (Pinterest).', 'es' => 'Seguimiento de conversiones (Pinterest).', 'nl' => 'Conversietracking (Pinterest).', 'pt' => 'Rastreamento de conversões (Pinterest).' ),
	'pin_audience'    => array( 'fr' => 'Regroupement d’audience (Pinterest).', 'en' => 'Audience grouping (Pinterest).', 'de' => 'Zielgruppen-Gruppierung (Pinterest).', 'it' => 'Raggruppamento del pubblico (Pinterest).', 'es' => 'Agrupación de audiencias (Pinterest).', 'nl' => 'Doelgroepgroepering (Pinterest).', 'pt' => 'Agrupamento de audiências (Pinterest).' ),
	'snap_ads'        => array( 'fr' => 'Identifiant publicitaire (Snapchat).', 'en' => 'Advertising identifier (Snapchat).', 'de' => 'Werbe-Kennung (Snapchat).', 'it' => 'Identificativo pubblicitario (Snapchat).', 'es' => 'Identificador publicitario (Snapchat).', 'nl' => 'Advertentie-ID (Snapchat).', 'pt' => 'Identificador publicitário (Snapchat).' ),
	'wistia_stats'    => array( 'fr' => 'Statistiques de lecture vidéo (Wistia).', 'en' => 'Video playback statistics (Wistia).', 'de' => 'Videowiedergabe-Statistiken (Wistia).', 'it' => 'Statistiche di riproduzione video (Wistia).', 'es' => 'Estadísticas de reproducción de vídeo (Wistia).', 'nl' => 'Videoafspeelstatistieken (Wistia).', 'pt' => 'Estatísticas de reprodução de vídeo (Wistia).' ),
);

return array(
	'google-analytics' => array(
		array( 'name' => '_ga', 'duration' => '2y', 'desc' => $fc_d['ga_visitor'] ),
		array( 'name' => '_ga_*', 'duration' => '2y', 'desc' => $fc_d['ga4_state'] ),
		array( 'name' => '_gid', 'duration' => '24h', 'desc' => $fc_d['ga_visitor'] ),
		array( 'name' => '_gat', 'duration' => '1min', 'desc' => $fc_d['rate_limit'] ),
	),
	'google-tag-manager' => array(
		array( 'name' => '_dc_gtm_*', 'duration' => '1min', 'desc' => $fc_d['gtm'] ),
	),
	'google-ads' => array(
		array( 'name' => '_gcl_au', 'duration' => '3mo', 'desc' => $fc_d['ads_attribution'] ),
		array( 'name' => 'IDE', 'duration' => '13mo', 'desc' => $fc_d['doubleclick'] ),
		array( 'name' => 'test_cookie', 'duration' => '15min', 'desc' => $fc_d['test_cookie'] ),
	),
	'meta-pixel' => array(
		array( 'name' => '_fbp', 'duration' => '3mo', 'desc' => $fc_d['meta_tracking'] ),
		array( 'name' => 'fr', 'duration' => '3mo', 'desc' => $fc_d['meta_targeted'] ),
	),
	'youtube' => array(
		array( 'name' => 'VISITOR_INFO1_LIVE', 'duration' => '6mo', 'desc' => $fc_d['yt_prefs'] ),
		array( 'name' => 'YSC', 'duration' => 'session', 'desc' => $fc_d['yt_stats'] ),
	),
	'vimeo' => array(
		array( 'name' => 'vuid', 'duration' => '2y', 'desc' => $fc_d['vimeo_stats'] ),
	),
	'google-maps' => array(
		array( 'name' => 'NID', 'duration' => '6mo', 'desc' => $fc_d['google_prefs'] ),
	),
	'hotjar' => array(
		array( 'name' => '_hjSessionUser_*', 'duration' => '1y', 'desc' => $fc_d['hotjar_user'] ),
		array( 'name' => '_hjSession_*', 'duration' => '30min', 'desc' => $fc_d['hotjar_session'] ),
	),
	'linkedin' => array(
		array( 'name' => 'li_sugr', 'duration' => '3mo', 'desc' => $fc_d['li_match'] ),
		array( 'name' => 'bcookie', 'duration' => '1y', 'desc' => $fc_d['li_device'] ),
	),
	'twitter' => array(
		array( 'name' => 'personalization_id', 'duration' => '2y', 'desc' => $fc_d['x_ads'] ),
	),
	'tiktok' => array(
		array( 'name' => '_ttp', 'duration' => '13mo', 'desc' => $fc_d['tiktok_ads'] ),
	),
	'soundcloud' => array(
		array( 'name' => 'sc_anonymous_id', 'duration' => '10y', 'desc' => $fc_d['sc_player'] ),
	),
	'matomo' => array(
		array( 'name' => '_pk_id.*', 'duration' => '13mo', 'desc' => $fc_d['matomo_visitor'] ),
		array( 'name' => '_pk_ses.*', 'duration' => '30min', 'desc' => $fc_d['matomo_session'] ),
	),
	'microsoft-clarity' => array(
		array( 'name' => '_clck', 'duration' => '1y', 'desc' => $fc_d['clarity_visitor'] ),
		array( 'name' => '_clsk', 'duration' => '1d', 'desc' => $fc_d['clarity_session'] ),
	),
	'pinterest' => array(
		array( 'name' => '_pinterest_ct_ua', 'duration' => '1y', 'desc' => $fc_d['pin_conversion'] ),
		array( 'name' => '_pin_unauth', 'duration' => '1y', 'desc' => $fc_d['pin_audience'] ),
	),
	'snapchat' => array(
		array( 'name' => '_scid', 'duration' => '13mo', 'desc' => $fc_d['snap_ads'] ),
	),
	'wistia' => array(
		array( 'name' => 'wistia', 'duration' => 'persistent', 'desc' => $fc_d['wistia_stats'] ),
	),
	'google-signin' => array(
		array( 'name' => 'g_state', 'duration' => '6mo', 'desc' => array( 'fr' => 'État de l\'invite de connexion Google.', 'en' => 'Google sign-in prompt state.', 'de' => 'Status der Google-Anmeldeaufforderung.', 'it' => 'Stato del prompt di accesso Google.', 'es' => 'Estado del aviso de inicio de sesión de Google.', 'nl' => 'Status van de Google-inlogprompt.', 'pt' => 'Estado do aviso de início de sessão Google.' ) ),
	),
	'google-fonts' => array(),
);
