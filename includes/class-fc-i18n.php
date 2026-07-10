<?php
/**
 * Détection de langue + chaînes du bandeau, livrées avec le plugin (100 % local).
 *
 * « Multilingue auto » = jeux de chaînes pré-traduits + auto-sélection selon la
 * langue du visiteur. Aucune traduction en direct via une API tierce.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_I18n {

	/**
	 * Détermine la langue à servir (cascade).
	 *
	 * 1) langue active WPML/Polylang (langue réelle de la PAGE affichée)
	 * → 2) (option) langue du NAVIGATEUR du visiteur, si prise en charge
	 * → 3) locale du site → 4) anglais.
	 *
	 * @param bool $use_browser La langue du navigateur prime sur celle du site.
	 * @return string Code court : fr, en, de, it…
	 */
	public static function detect( $use_browser = false ) {
		// Polylang.
		if ( function_exists( 'pll_current_language' ) ) {
			$pll = pll_current_language( 'slug' );
			if ( $pll ) {
				return self::normalize( $pll );
			}
		}
		// WPML.
		if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE ) {
			return self::normalize( ICL_LANGUAGE_CODE );
		}
		// Navigateur du visiteur : première langue de sa liste que l'on parle.
		if ( $use_browser ) {
			$bl = self::browser_lang();
			if ( '' !== $bl ) {
				return $bl;
			}
		}
		// Locale du site.
		$locale = get_locale();
		if ( $locale ) {
			return self::normalize( $locale );
		}
		return 'en';
	}

	/**
	 * Première langue PRISE EN CHARGE de la liste Accept-Language du visiteur
	 * (parcourue dans l'ordre de préférence), ou '' si aucune.
	 *
	 * @return string Code court ou ''.
	 */
	public static function browser_lang() {
		if ( empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			return '';
		}
		$al    = strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) );
		$avail = array_keys( self::strings() );
		foreach ( explode( ',', $al ) as $part ) {
			$code  = trim( explode( ';', $part )[0] );
			$short = substr( str_replace( '_', '-', $code ), 0, 2 );
			if ( in_array( $short, $avail, true ) ) {
				return $short;
			}
		}
		return '';
	}

	/**
	 * Réduit « fr_FR », « fr-CH », « FR » → « fr ».
	 *
	 * @param string $code Code de langue.
	 * @return string
	 */
	public static function normalize( $code ) {
		$code = strtolower( (string) $code );
		$code = str_replace( '_', '-', $code );
		$short = substr( $code, 0, 2 );
		$avail = array_keys( self::strings() );
		return in_array( $short, $avail, true ) ? $short : 'en';
	}

	/**
	 * Renvoie les chaînes du bandeau pour une langue (repli anglais).
	 *
	 * @param string $lang Code court.
	 * @return array<string,string>
	 */
	public static function get( $lang ) {
		$all = self::strings();
		return isset( $all[ $lang ] ) ? $all[ $lang ] : $all['en'];
	}

	/**
	 * Sélectionne la variante d'une valeur multilingue (repli anglais puis première).
	 *
	 * @param string|array<string,string> $value Chaîne unique ou tableau lang => texte.
	 * @param string                      $lang  Code court.
	 * @return string
	 */
	public static function pick( $value, $lang ) {
		if ( ! is_array( $value ) ) {
			return (string) $value;
		}
		if ( isset( $value[ $lang ] ) ) {
			return $value[ $lang ];
		}
		if ( isset( $value['en'] ) ) {
			return $value['en'];
		}
		$first = reset( $value );
		return is_string( $first ) ? $first : '';
	}

	/**
	 * Libellé traduit d'une durée de cookie à partir d'un jeton neutre.
	 * Jetons : session, persistent, 1min, 15min, 30min, 24h, 1d, 3mo, 6mo, 13mo, 1y, 2y, 10y.
	 *
	 * @param string $token Jeton de durée.
	 * @param string $lang  Code court.
	 * @return string
	 */
	public static function duration_label( $token, $lang ) {
		$map = array(
			'session'    => array( 'fr' => 'Session', 'en' => 'Session', 'de' => 'Sitzung', 'it' => 'Sessione', 'es' => 'Sesión', 'nl' => 'Sessie', 'pt' => 'Sessão' ),
			'persistent' => array( 'fr' => 'Persistant', 'en' => 'Persistent', 'de' => 'Dauerhaft', 'it' => 'Persistente', 'es' => 'Persistente', 'nl' => 'Permanent', 'pt' => 'Persistente' ),
			'1min'       => array( 'fr' => '1 minute', 'en' => '1 minute', 'de' => '1 Minute', 'it' => '1 minuto', 'es' => '1 minuto', 'nl' => '1 minuut', 'pt' => '1 minuto' ),
			'15min'      => array( 'fr' => '15 minutes', 'en' => '15 minutes', 'de' => '15 Minuten', 'it' => '15 minuti', 'es' => '15 minutos', 'nl' => '15 minuten', 'pt' => '15 minutos' ),
			'30min'      => array( 'fr' => '30 minutes', 'en' => '30 minutes', 'de' => '30 Minuten', 'it' => '30 minuti', 'es' => '30 minutos', 'nl' => '30 minuten', 'pt' => '30 minutos' ),
			'24h'        => array( 'fr' => '24 heures', 'en' => '24 hours', 'de' => '24 Stunden', 'it' => '24 ore', 'es' => '24 horas', 'nl' => '24 uur', 'pt' => '24 horas' ),
			'1d'         => array( 'fr' => '1 jour', 'en' => '1 day', 'de' => '1 Tag', 'it' => '1 giorno', 'es' => '1 día', 'nl' => '1 dag', 'pt' => '1 dia' ),
			'3mo'        => array( 'fr' => '3 mois', 'en' => '3 months', 'de' => '3 Monate', 'it' => '3 mesi', 'es' => '3 meses', 'nl' => '3 maanden', 'pt' => '3 meses' ),
			'6mo'        => array( 'fr' => '6 mois', 'en' => '6 months', 'de' => '6 Monate', 'it' => '6 mesi', 'es' => '6 meses', 'nl' => '6 maanden', 'pt' => '6 meses' ),
			'13mo'       => array( 'fr' => '13 mois', 'en' => '13 months', 'de' => '13 Monate', 'it' => '13 mesi', 'es' => '13 meses', 'nl' => '13 maanden', 'pt' => '13 meses' ),
			'1y'         => array( 'fr' => '1 an', 'en' => '1 year', 'de' => '1 Jahr', 'it' => '1 anno', 'es' => '1 año', 'nl' => '1 jaar', 'pt' => '1 ano' ),
			'2y'         => array( 'fr' => '2 ans', 'en' => '2 years', 'de' => '2 Jahre', 'it' => '2 anni', 'es' => '2 años', 'nl' => '2 jaar', 'pt' => '2 anos' ),
			'10y'        => array( 'fr' => '10 ans', 'en' => '10 years', 'de' => '10 Jahre', 'it' => '10 anni', 'es' => '10 años', 'nl' => '10 jaar', 'pt' => '10 anos' ),
		);
		if ( ! isset( $map[ $token ] ) ) {
			return (string) $token;
		}
		return self::pick( $map[ $token ], $lang );
	}

	/**
	 * Toutes les traductions livrées. À étendre langue par langue.
	 *
	 * @return array<string,array<string,string>>
	 */
	public static function strings() {
		return array(
			'fr' => array(
				'title'       => 'Nous respectons votre vie privée',
				'edu_open'    => 'À quoi servent les cookies ?',
				'edu_title'   => 'Comprendre les cookies',
				'edu_intro'   => 'Un cookie est un petit fichier déposé sur votre appareil. Beaucoup sont utiles ; d’autres servent surtout à vous suivre. Voici comment les distinguer.',
				'edu_useful_t'=> 'Utiles',
				'edu_useful_d'=> 'Garder votre panier, votre langue ou votre session connectée. Sans eux, le site fonctionne mal.',
				'edu_mixed_t' => 'À nuancer',
				'edu_mixed_d' => 'La mesure d’audience aide le site à s’améliorer, mais recueille des données sur votre navigation.',
				'edu_danger_t'=> 'À surveiller',
				'edu_danger_d'=> 'Les cookies publicitaires vous suivent d’un site à l’autre pour cibler des annonces. Vous pouvez les refuser sans rien perdre.',
				'edu_back'    => 'Retour',
				'body'        => 'Ce site dépose des cookies pour la mesure d’audience et, avec votre accord, d’autres finalités. Vous choisissez ce que vous acceptez. Aucun traceur n’est activé sans votre consentement.',
				'accept_all'  => 'Tout accepter',
				'reject_all'  => 'Tout refuser',
				'customize'   => 'Personnaliser',
				'save'        => 'Enregistrer mes choix',
				'prefs_title' => 'Vos préférences de cookies',
				'necessary'   => 'Strictement nécessaires',
				'preferences' => 'Préférences',
				'statistics'  => 'Mesure d’audience',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Indispensables au fonctionnement du site. Toujours actifs.',
				'preferences_d' => 'Mémorisent vos choix (langue, affichage) pour améliorer votre expérience.',
				'statistics_d' => 'Nous aident à comprendre l’usage du site de façon agrégée.',
				'marketing_d' => 'Servent à la publicité et au suivi entre sites.',
				'always_on'   => 'Toujours actif',
				'manage'      => 'Gérer mes cookies',
				'risk_low'    => 'Utile',
				'risk_medium' => 'À nuancer',
				'risk_high'   => 'À surveiller',
				'ck_details'  => 'Détails des cookies',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Durée',
				'ck_desc'     => 'Description',
			),
			'en' => array(
				'title'       => 'We value your privacy',
				'edu_open'    => 'What are cookies for?',
				'edu_title'   => 'Understanding cookies',
				'edu_intro'   => 'A cookie is a small file stored on your device. Many are helpful; others mainly track you. Here is how to tell them apart.',
				'edu_useful_t'=> 'Helpful',
				'edu_useful_d'=> 'Keeping your cart, language or login session. Without them the site works poorly.',
				'edu_mixed_t' => 'Mixed',
				'edu_mixed_d' => 'Audience measurement helps improve the site, but collects data about your browsing.',
				'edu_danger_t'=> 'Watch out',
				'edu_danger_d'=> 'Advertising cookies track you across sites to target ads. You can refuse them and lose nothing.',
				'edu_back'    => 'Back',
				'body'        => 'This site uses cookies for audience measurement and, with your consent, other purposes. You choose what you accept. No tracker runs without your consent.',
				'accept_all'  => 'Accept all',
				'reject_all'  => 'Reject all',
				'customize'   => 'Customize',
				'save'        => 'Save my choices',
				'prefs_title' => 'Your cookie preferences',
				'necessary'   => 'Strictly necessary',
				'preferences' => 'Preferences',
				'statistics'  => 'Statistics',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Essential for the site to work. Always on.',
				'preferences_d' => 'Remember your choices (language, display) to improve your experience.',
				'statistics_d' => 'Help us understand site usage in aggregate.',
				'marketing_d' => 'Used for advertising and cross-site tracking.',
				'always_on'   => 'Always on',
				'manage'      => 'Manage cookies',
				'risk_low'    => 'Helpful',
				'risk_medium' => 'Mixed',
				'risk_high'   => 'Watch out',
				'ck_details'  => 'Cookie details',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Duration',
				'ck_desc'     => 'Description',
			),
			'de' => array(
				'title'       => 'Ihre Privatsphäre ist uns wichtig',
				'edu_open'    => 'Wozu dienen Cookies?',
				'edu_title'   => 'Cookies verstehen',
				'edu_intro'   => 'Ein Cookie ist eine kleine Datei auf Ihrem Gerät. Viele sind nützlich, andere dienen vor allem der Nachverfolgung. So unterscheiden Sie sie.',
				'edu_useful_t'=> 'Nützlich',
				'edu_useful_d'=> 'Warenkorb, Sprache oder angemeldete Sitzung behalten. Ohne sie funktioniert die Website schlecht.',
				'edu_mixed_t' => 'Abwägen',
				'edu_mixed_d' => 'Reichweitenmessung hilft, die Website zu verbessern, sammelt aber Daten über Ihr Surfverhalten.',
				'edu_danger_t'=> 'Aufpassen',
				'edu_danger_d'=> 'Werbe-Cookies verfolgen Sie über Websites hinweg für gezielte Werbung. Sie können sie ablehnen, ohne etwas zu verlieren.',
				'edu_back'    => 'Zurück',
				'body'        => 'Diese Website verwendet Cookies zur Reichweitenmessung und – mit Ihrer Einwilligung – für weitere Zwecke. Sie entscheiden, was Sie zulassen. Ohne Ihre Einwilligung wird kein Tracker aktiviert.',
				'accept_all'  => 'Alle akzeptieren',
				'reject_all'  => 'Alle ablehnen',
				'customize'   => 'Anpassen',
				'save'        => 'Auswahl speichern',
				'prefs_title' => 'Ihre Cookie-Einstellungen',
				'necessary'   => 'Unbedingt erforderlich',
				'preferences' => 'Präferenzen',
				'statistics'  => 'Statistik',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Für den Betrieb der Website unerlässlich. Immer aktiv.',
				'preferences_d' => 'Speichern Ihre Auswahl (Sprache, Anzeige) für ein besseres Erlebnis.',
				'statistics_d' => 'Helfen uns, die Nutzung der Website aggregiert zu verstehen.',
				'marketing_d' => 'Dienen der Werbung und dem seitenübergreifenden Tracking.',
				'always_on'   => 'Immer aktiv',
				'manage'      => 'Cookies verwalten',
				'risk_low'    => 'Nützlich',
				'risk_medium' => 'Abwägen',
				'risk_high'   => 'Aufpassen',
				'ck_details'  => 'Cookie-Details',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Dauer',
				'ck_desc'     => 'Beschreibung',
			),
			'it' => array(
				'title'       => 'Teniamo alla tua privacy',
				'edu_open'    => 'A cosa servono i cookie?',
				'edu_title'   => 'Capire i cookie',
				'edu_intro'   => 'Un cookie è un piccolo file salvato sul tuo dispositivo. Molti sono utili; altri servono soprattutto a tracciarti. Ecco come distinguerli.',
				'edu_useful_t'=> 'Utili',
				'edu_useful_d'=> 'Conservano carrello, lingua o sessione di accesso. Senza di essi il sito funziona male.',
				'edu_mixed_t' => 'Da valutare',
				'edu_mixed_d' => 'La misurazione del pubblico aiuta a migliorare il sito, ma raccoglie dati sulla tua navigazione.',
				'edu_danger_t'=> 'Attenzione',
				'edu_danger_d'=> 'I cookie pubblicitari ti tracciano tra i siti per mostrarti annunci mirati. Puoi rifiutarli senza perdere nulla.',
				'edu_back'    => 'Indietro',
				'body'        => 'Questo sito utilizza cookie per la misurazione del pubblico e, con il tuo consenso, per altre finalità. Scegli tu cosa accettare. Nessun tracker viene attivato senza il tuo consenso.',
				'accept_all'  => 'Accetta tutto',
				'reject_all'  => 'Rifiuta tutto',
				'customize'   => 'Personalizza',
				'save'        => 'Salva le scelte',
				'prefs_title' => 'Le tue preferenze sui cookie',
				'necessary'   => 'Strettamente necessari',
				'preferences' => 'Preferenze',
				'statistics'  => 'Statistiche',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Indispensabili al funzionamento del sito. Sempre attivi.',
				'preferences_d' => 'Memorizzano le tue scelte (lingua, visualizzazione) per migliorare l’esperienza.',
				'statistics_d' => 'Ci aiutano a capire l’uso del sito in forma aggregata.',
				'marketing_d' => 'Utilizzati per pubblicità e tracciamento tra siti.',
				'always_on'   => 'Sempre attivo',
				'manage'      => 'Gestisci i cookie',
				'risk_low'    => 'Utile',
				'risk_medium' => 'Da valutare',
				'risk_high'   => 'Attenzione',
				'ck_details'  => 'Dettagli dei cookie',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Durata',
				'ck_desc'     => 'Descrizione',
			),
			'es' => array(
				'title'       => 'Respetamos tu privacidad',
				'edu_open'    => '¿Para qué sirven las cookies?',
				'edu_title'   => 'Entender las cookies',
				'edu_intro'   => 'Una cookie es un pequeño archivo guardado en tu dispositivo. Muchas son útiles; otras sirven sobre todo para rastrearte. Así las distingues.',
				'edu_useful_t'=> 'Útiles',
				'edu_useful_d'=> 'Guardan tu carrito, idioma o sesión iniciada. Sin ellas el sitio funciona mal.',
				'edu_mixed_t' => 'Matizables',
				'edu_mixed_d' => 'La medición de audiencia ayuda a mejorar el sitio, pero recoge datos sobre tu navegación.',
				'edu_danger_t'=> 'Ojo',
				'edu_danger_d'=> 'Las cookies publicitarias te rastrean entre sitios para dirigir anuncios. Puedes rechazarlas sin perder nada.',
				'edu_back'    => 'Volver',
				'body'        => 'Este sitio utiliza cookies para la medición de audiencia y, con tu consentimiento, otras finalidades. Tú eliges qué aceptar. Ningún rastreador se activa sin tu consentimiento.',
				'accept_all'  => 'Aceptar todo',
				'reject_all'  => 'Rechazar todo',
				'customize'   => 'Personalizar',
				'save'        => 'Guardar mis opciones',
				'prefs_title' => 'Tus preferencias de cookies',
				'necessary'   => 'Estrictamente necesarias',
				'preferences' => 'Preferencias',
				'statistics'  => 'Estadísticas',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Imprescindibles para el funcionamiento del sitio. Siempre activas.',
				'preferences_d' => 'Recuerdan tus opciones (idioma, visualización) para mejorar tu experiencia.',
				'statistics_d' => 'Nos ayudan a entender el uso del sitio de forma agregada.',
				'marketing_d' => 'Se usan para publicidad y seguimiento entre sitios.',
				'always_on'   => 'Siempre activo',
				'manage'      => 'Gestionar cookies',
				'risk_low'    => 'Útil',
				'risk_medium' => 'Matizable',
				'risk_high'   => 'Ojo',
				'ck_details'  => 'Detalles de las cookies',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Duración',
				'ck_desc'     => 'Descripción',
			),
			'nl' => array(
				'title'       => 'Wij respecteren uw privacy',
				'edu_open'    => 'Waarvoor dienen cookies?',
				'edu_title'   => 'Cookies begrijpen',
				'edu_intro'   => 'Een cookie is een klein bestand op uw apparaat. Veel zijn nuttig; andere dienen vooral om u te volgen. Zo herkent u ze.',
				'edu_useful_t'=> 'Nuttig',
				'edu_useful_d'=> 'Bewaren uw winkelwagen, taal of ingelogde sessie. Zonder deze werkt de site slecht.',
				'edu_mixed_t' => 'Genuanceerd',
				'edu_mixed_d' => 'Publieksmeting helpt de site te verbeteren, maar verzamelt gegevens over uw surfgedrag.',
				'edu_danger_t'=> 'Opletten',
				'edu_danger_d'=> 'Advertentiecookies volgen u over sites heen voor gerichte advertenties. U kunt ze weigeren zonder iets te verliezen.',
				'edu_back'    => 'Terug',
				'body'        => 'Deze site gebruikt cookies voor publieksmeting en, met uw toestemming, andere doeleinden. U kiest wat u accepteert. Zonder uw toestemming wordt geen enkele tracker geactiveerd.',
				'accept_all'  => 'Alles accepteren',
				'reject_all'  => 'Alles weigeren',
				'customize'   => 'Aanpassen',
				'save'        => 'Mijn keuzes opslaan',
				'prefs_title' => 'Uw cookievoorkeuren',
				'necessary'   => 'Strikt noodzakelijk',
				'preferences' => 'Voorkeuren',
				'statistics'  => 'Statistieken',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Essentieel voor de werking van de site. Altijd aan.',
				'preferences_d' => 'Onthouden uw keuzes (taal, weergave) om uw ervaring te verbeteren.',
				'statistics_d' => 'Helpen ons het gebruik van de site geaggregeerd te begrijpen.',
				'marketing_d' => 'Worden gebruikt voor advertenties en tracking tussen sites.',
				'always_on'   => 'Altijd aan',
				'manage'      => 'Cookies beheren',
				'risk_low'    => 'Nuttig',
				'risk_medium' => 'Genuanceerd',
				'risk_high'   => 'Opletten',
				'ck_details'  => 'Cookiedetails',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Duur',
				'ck_desc'     => 'Beschrijving',
			),
			'pt' => array(
				'title'       => 'Respeitamos a sua privacidade',
				'edu_open'    => 'Para que servem os cookies?',
				'edu_title'   => 'Compreender os cookies',
				'edu_intro'   => 'Um cookie é um pequeno ficheiro guardado no seu dispositivo. Muitos são úteis; outros servem sobretudo para o seguir. Veja como distingui-los.',
				'edu_useful_t'=> 'Úteis',
				'edu_useful_d'=> 'Guardam o seu carrinho, idioma ou sessão iniciada. Sem eles o site funciona mal.',
				'edu_mixed_t' => 'A ponderar',
				'edu_mixed_d' => 'A medição de audiência ajuda a melhorar o site, mas recolhe dados sobre a sua navegação.',
				'edu_danger_t'=> 'Atenção',
				'edu_danger_d'=> 'Os cookies publicitários seguem-no entre sites para direcionar anúncios. Pode recusá-los sem perder nada.',
				'edu_back'    => 'Voltar',
				'body'        => 'Este site utiliza cookies para medição de audiência e, com o seu consentimento, outras finalidades. Você escolhe o que aceita. Nenhum rastreador é ativado sem o seu consentimento.',
				'accept_all'  => 'Aceitar tudo',
				'reject_all'  => 'Rejeitar tudo',
				'customize'   => 'Personalizar',
				'save'        => 'Guardar as minhas escolhas',
				'prefs_title' => 'As suas preferências de cookies',
				'necessary'   => 'Estritamente necessários',
				'preferences' => 'Preferências',
				'statistics'  => 'Estatísticas',
				'marketing'   => 'Marketing',
				'necessary_d' => 'Indispensáveis ao funcionamento do site. Sempre ativos.',
				'preferences_d' => 'Memorizam as suas escolhas (idioma, exibição) para melhorar a sua experiência.',
				'statistics_d' => 'Ajudam-nos a compreender o uso do site de forma agregada.',
				'marketing_d' => 'Servem para publicidade e rastreamento entre sites.',
				'always_on'   => 'Sempre ativo',
				'manage'      => 'Gerir cookies',
				'risk_low'    => 'Útil',
				'risk_medium' => 'A ponderar',
				'risk_high'   => 'Atenção',
				'ck_details'  => 'Detalhes dos cookies',
				'ck_cookie'   => 'Cookie',
				'ck_duration' => 'Duração',
				'ck_desc'     => 'Descrição',
			),
		);
	}

	/**
	 * Libellés du volet « À propos » (auto-traduits selon la langue du visiteur).
	 *
	 * @param string $lang Code court.
	 * @return array<string,string>
	 */
	public static function about_labels( $lang ) {
		$m = array(
			'fr' => array( 'about' => 'À propos', 'back' => 'Retour', 'coffee' => 'Offrez-moi un café', 'promo' => 'Bandeau de consentement libre et léger — gratuit jusqu’à 10 000 visites par mois. Découvrez le projet et suivez-nous :' ),
			'en' => array( 'about' => 'About', 'back' => 'Back', 'coffee' => 'Buy me a coffee', 'promo' => 'A free, lightweight consent banner — free up to 10,000 visits per month. Discover the project and follow us:' ),
			'de' => array( 'about' => 'Info', 'back' => 'Zurück', 'coffee' => 'Spendier mir einen Kaffee', 'promo' => 'Ein freies, leichtes Consent-Banner — kostenlos bis 10.000 Besuche pro Monat. Entdecken Sie das Projekt und folgen Sie uns:' ),
			'it' => array( 'about' => 'Info', 'back' => 'Indietro', 'coffee' => 'Offrimi un caffè', 'promo' => 'Un banner di consenso libero e leggero — gratuito fino a 10.000 visite al mese. Scopri il progetto e seguici:' ),
			'es' => array( 'about' => 'Acerca de', 'back' => 'Volver', 'coffee' => 'Invítame a un café', 'promo' => 'Un banner de consentimiento libre y ligero — gratis hasta 10 000 visitas al mes. Descubre el proyecto y síguenos:' ),
			'nl' => array( 'about' => 'Info', 'back' => 'Terug', 'coffee' => 'Trakteer me op een koffie', 'promo' => 'Een vrije, lichte consent-banner — gratis tot 10.000 bezoeken per maand. Ontdek het project en volg ons:' ),
			'pt' => array( 'about' => 'Sobre', 'back' => 'Voltar', 'coffee' => 'Pague-me um café', 'promo' => 'Um banner de consentimento livre e leve — gratuito até 10 000 visitas por mês. Descubra o projeto e siga-nos:' ),
		);
		return isset( $m[ $lang ] ) ? $m[ $lang ] : $m['en'];
	}
}
