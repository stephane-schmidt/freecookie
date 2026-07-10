<?php
/**
 * Cookies internes (première partie) connus : WordPress, extensions courantes…
 * Sert à classer les cookies réellement observés pendant le scan
 * (en-têtes Set-Cookie côté serveur + document.cookie côté navigateur).
 *
 * 'match' accepte le joker « * ». 'duration' = jeton FC_I18n::duration_label().
 * 'desc' = tableau multilingue résolu par FC_I18n::pick().
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	array(
		'match'    => 'wordpress_test_cookie',
		'cat'      => 'necessary',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Vérifie que le navigateur accepte les cookies (WordPress).', 'en' => 'Checks that the browser accepts cookies (WordPress).', 'de' => 'Prüft, ob der Browser Cookies akzeptiert (WordPress).', 'it' => 'Verifica che il browser accetti i cookie (WordPress).', 'es' => 'Comprueba que el navegador acepta cookies (WordPress).', 'nl' => 'Controleert of de browser cookies accepteert (WordPress).', 'pt' => 'Verifica se o navegador aceita cookies (WordPress).' ),
	),
	array(
		'match'    => 'wordpress_logged_in_*',
		'cat'      => 'necessary',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Session de connexion WordPress (administration).', 'en' => 'WordPress login session (administration).', 'de' => 'WordPress-Anmeldesitzung (Verwaltung).', 'it' => 'Sessione di accesso WordPress (amministrazione).', 'es' => 'Sesión de inicio de sesión de WordPress (administración).', 'nl' => 'WordPress-aanmeldsessie (beheer).', 'pt' => 'Sessão de início de sessão WordPress (administração).' ),
	),
	array(
		'match'    => 'wordpress_sec_*',
		'cat'      => 'necessary',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Session de connexion WordPress (administration).', 'en' => 'WordPress login session (administration).', 'de' => 'WordPress-Anmeldesitzung (Verwaltung).', 'it' => 'Sessione di accesso WordPress (amministrazione).', 'es' => 'Sesión de inicio de sesión de WordPress (administración).', 'nl' => 'WordPress-aanmeldsessie (beheer).', 'pt' => 'Sessão de início de sessão WordPress (administração).' ),
	),
	array(
		'match'    => 'wp-settings-*',
		'cat'      => 'preferences',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Préférences d’interface WordPress.', 'en' => 'WordPress interface preferences.', 'de' => 'WordPress-Oberflächeneinstellungen.', 'it' => 'Preferenze dell’interfaccia WordPress.', 'es' => 'Preferencias de la interfaz de WordPress.', 'nl' => 'WordPress-interfacevoorkeuren.', 'pt' => 'Preferências da interface WordPress.' ),
	),
	array(
		'match'    => 'PHPSESSID',
		'cat'      => 'necessary',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Session technique du site.', 'en' => 'Technical site session.', 'de' => 'Technische Sitzung der Website.', 'it' => 'Sessione tecnica del sito.', 'es' => 'Sesión técnica del sitio.', 'nl' => 'Technische sessie van de site.', 'pt' => 'Sessão técnica do site.' ),
	),
	array(
		'match'    => 'comment_author_*',
		'cat'      => 'preferences',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Mémorise vos informations de commentaire.', 'en' => 'Remembers your comment details.', 'de' => 'Speichert Ihre Kommentardaten.', 'it' => 'Memorizza i dati dei commenti.', 'es' => 'Recuerda sus datos de comentario.', 'nl' => 'Onthoudt uw reactiegegevens.', 'pt' => 'Memoriza os seus dados de comentário.' ),
	),
	array(
		'match'    => 'freecookie_consent',
		'cat'      => 'necessary',
		'duration' => '3mo',
		'desc'     => array( 'fr' => 'Mémorise vos choix de consentement (FreeCookie).', 'en' => 'Stores your consent choices (FreeCookie).', 'de' => 'Speichert Ihre Einwilligungsentscheidungen (FreeCookie).', 'it' => 'Memorizza le sue scelte di consenso (FreeCookie).', 'es' => 'Guarda sus preferencias de consentimiento (FreeCookie).', 'nl' => 'Bewaart uw toestemmingskeuzes (FreeCookie).', 'pt' => 'Guarda as suas escolhas de consentimento (FreeCookie).' ),
	),
	array(
		'match'    => 'cookieyes-consent',
		'cat'      => 'necessary',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Choix de consentement (outil de gestion des cookies).', 'en' => 'Consent choices (cookie management tool).', 'de' => 'Einwilligungsauswahl (Cookie-Verwaltung).', 'it' => 'Scelte di consenso (strumento di gestione dei cookie).', 'es' => 'Preferencias de consentimiento (gestor de cookies).', 'nl' => 'Toestemmingskeuzes (cookiebeheer).', 'pt' => 'Escolhas de consentimento (gestor de cookies).' ),
	),
	array(
		'match'    => 'cmplz_*',
		'cat'      => 'necessary',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Choix de consentement (outil de gestion des cookies).', 'en' => 'Consent choices (cookie management tool).', 'de' => 'Einwilligungsauswahl (Cookie-Verwaltung).', 'it' => 'Scelte di consenso (strumento di gestione dei cookie).', 'es' => 'Preferencias de consentimiento (gestor de cookies).', 'nl' => 'Toestemmingskeuzes (cookiebeheer).', 'pt' => 'Escolhas de consentimento (gestor de cookies).' ),
	),
	array(
		'match'    => 'moove_gdpr_popup',
		'cat'      => 'necessary',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Choix de consentement (outil de gestion des cookies).', 'en' => 'Consent choices (cookie management tool).', 'de' => 'Einwilligungsauswahl (Cookie-Verwaltung).', 'it' => 'Scelte di consenso (strumento di gestione dei cookie).', 'es' => 'Preferencias de consentimiento (gestor de cookies).', 'nl' => 'Toestemmingskeuzes (cookiebeheer).', 'pt' => 'Escolhas de consentimento (gestor de cookies).' ),
	),
	array(
		'match'    => 'pll_language',
		'cat'      => 'preferences',
		'duration' => '1y',
		'desc'     => array( 'fr' => 'Langue choisie (Polylang).', 'en' => 'Selected language (Polylang).', 'de' => 'Gewählte Sprache (Polylang).', 'it' => 'Lingua selezionata (Polylang).', 'es' => 'Idioma seleccionado (Polylang).', 'nl' => 'Gekozen taal (Polylang).', 'pt' => 'Idioma selecionado (Polylang).' ),
	),
	array(
		'match'    => 'wp-wpml_current_language',
		'cat'      => 'preferences',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Langue choisie (WPML).', 'en' => 'Selected language (WPML).', 'de' => 'Gewählte Sprache (WPML).', 'it' => 'Lingua selezionata (WPML).', 'es' => 'Idioma seleccionado (WPML).', 'nl' => 'Gekozen taal (WPML).', 'pt' => 'Idioma selecionado (WPML).' ),
	),
	array(
		'match'    => 'woocommerce_*',
		'cat'      => 'necessary',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Panier et session boutique (WooCommerce).', 'en' => 'Cart and shop session (WooCommerce).', 'de' => 'Warenkorb und Shop-Sitzung (WooCommerce).', 'it' => 'Carrello e sessione negozio (WooCommerce).', 'es' => 'Carrito y sesión de tienda (WooCommerce).', 'nl' => 'Winkelwagen en winkelsessie (WooCommerce).', 'pt' => 'Carrinho e sessão da loja (WooCommerce).' ),
	),
	array(
		'match'    => 'wp_woocommerce_session_*',
		'cat'      => 'necessary',
		'duration' => '1d',
		'desc'     => array( 'fr' => 'Panier et session boutique (WooCommerce).', 'en' => 'Cart and shop session (WooCommerce).', 'de' => 'Warenkorb und Shop-Sitzung (WooCommerce).', 'it' => 'Carrello e sessione negozio (WooCommerce).', 'es' => 'Carrito y sesión de tienda (WooCommerce).', 'nl' => 'Winkelwagen en winkelsessie (WooCommerce).', 'pt' => 'Carrinho e sessão da loja (WooCommerce).' ),
	),
	array(
		'match'    => 'tk_ai',
		'cat'      => 'statistics',
		'duration' => 'session',
		'desc'     => array( 'fr' => 'Mesure d’audience (Jetpack).', 'en' => 'Audience measurement (Jetpack).', 'de' => 'Reichweitenmessung (Jetpack).', 'it' => 'Misurazione del pubblico (Jetpack).', 'es' => 'Medición de audiencia (Jetpack).', 'nl' => 'Publieksmeting (Jetpack).', 'pt' => 'Medição de audiência (Jetpack).' ),
	),
);
