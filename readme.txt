=== FreeCookie — Cookie Consent RGPD/CNIL ===
Contributors: stephaneschmidt
Tags: cookie consent, gdpr, rgpd, cnil, consent mode
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.1.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Bandeau de consentement cookies 100 % local, conforme RGPD / ePrivacy / CNIL / nLPD. Blocage réel avant consentement, journal de preuve dans votre base, Consent Mode v2.

== Description ==

FreeCookie affiche un bandeau de consentement conforme et **bloque réellement** les traceurs tiers (Google Analytics, YouTube, Meta Pixel, Maps…) tant que le visiteur n'a pas donné son accord — sans dépendre d'aucun service externe.

* 100 % local : bannière, blocage et journal servis par votre WordPress. Zéro appel réseau tiers, zéro compte, zéro CDN.
* Conforme par défaut : « Tout refuser » aussi accessible que « Tout accepter », aucune case pré-cochée, blocage a priori.
* Journal de preuve dans votre base de données, exportable.
* Google Consent Mode v2 natif.
* Multilingue automatique (chaînes livrées avec le plugin, sélection selon la langue du visiteur).
* Léger, compatible cache de page.

Gratuit jusqu'à 10 000 visites/mois. Au-delà, un soutien est proposé (10 $/an ou 45 $ à vie) — le plugin reste entièrement fonctionnel dans tous les cas.

== Changelog ==

= 0.1.7 =
* Badge : cookie croqué sur le côté (mordu latéral).

= 0.1.6 =
* Nouveau badge : cookie croqué minimal avec marques de dents (monochrome, à la couleur du site).

= 0.1.5 =
* Titres du bandeau protégés du style du thème ; le titre des préférences prend la couleur dominante.

= 0.1.4 =
* Détection de couleur plus fiable : lit les logos SVG, détection profonde automatique à l'ouverture, sources de thèmes supplémentaires. Menu FreeCookie dédié dans l'administration.

= 0.1.3 =
* Détection de couleur : échantillonne aussi le logo du site (signal de marque le plus fiable). Badge un peu plus petit.

= 0.1.2 =
* Détection automatique des couleurs dominantes du site (kit Elementor, theme.json, réglages, analyse de fréquence) ; pastilles cliquables dans l'admin.

= 0.1.1 =
* Badge : neutralise les bordures/outline que certains thèmes appliquent aux boutons, sans casser le focus clavier.

= 0.1.0 =
* Version initiale de développement : moteur de blocage a priori, bandeau accessible, Consent Mode v2, journal de preuve, multilingue FR/EN/DE/IT, compteur honor system.

== Frequently Asked Questions ==

= FreeCookie envoie-t-il des données à un serveur externe ? =
Non. Tout est traité et stocké sur votre propre site.
