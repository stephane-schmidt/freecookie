=== FreeCookie — Cookie Consent RGPD/CNIL ===
Contributors: stephaneschmidt
Tags: cookie consent, gdpr, rgpd, cnil, consent mode
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.7.1
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

= 0.7.1 =
* Fiches par cookie entièrement traduites dans les 7 langues (descriptions + durées, base de cookies neutre en jetons). Auparavant, ces textes restaient en français.
* Liste publique [freecookie_cookies] : en-têtes de colonnes ajoutés en espagnol, néerlandais et portugais.

= 0.7.0 =
* Détails par cookie dans la bannière : sous chaque traceur, une fiche dépliable liste ses cookies (nom, durée, description), traduite automatiquement.

= 0.6.0 =
* Scan automatique planifié (quotidien / hebdomadaire / manuel, au choix dans les options) : le site s'analyse en tâche de fond, premier scan lancé automatiquement à l'activation.
* Les résultats du scan (service, catégorie, risque, finalité) s'affichent directement sous le bouton de scan dans l'administration.

= 0.5.0 =
* Fenêtre principale : les traceurs détectés, leur finalité et leur niveau de risque (faible/moyen/élevé) sont visibles directement, avec un interrupteur par traceur — plus d'étape « Personnaliser ».
* Éthique/conformité : volet « À propos » désactivé par défaut (opt-in strict), consentement 90 jours par défaut (reco EDPB/CNIL), niveaux de risque factuels au lieu de notes chiffrées, limites du blocage documentées, avertissements admin (juridique, mineurs, GD).
* Accessibilité : piège de focus sur le bandeau, aria-modal correct, annonces aria-live, focus restauré à la fermeture, badge avec aria-expanded.
* Sécurité : motif de neutralisation durci (attributs piégés), requêtes locales via wp_safe_remote_get, anti-abus sur l'endpoint de journalisation, désinstallation nettoyée.
* Admin : avis de soutien désactivable.

= 0.4.2 =
* Boutons homogènes : « Tout accepter » et « Tout refuser » identiques (parité), « Personnaliser » en contour de même forme ; boutons protégés du style du thème. Améliorations responsive (mobile) : boutons empilés, marges ajustées.

= 0.4.1 =
* Badge : un peu plus petit, s'estompe après 10 s d'inactivité et redevient pleinement visible quand la souris s'en approche (100 px), au survol ou au focus clavier.

= 0.4.0 =
* Centre de préférences transparent : chaque traceur détecté affiche sa finalité, une note de respect de la vie privée sur 10 (pastille verte/orange/rouge) et un interrupteur individuel pour l'activer ou non.

= 0.3.0 =
* 20 formes de cookie au choix pour le badge, sélecteur visuel dans l'administration.

= 0.2.0 =
* Volet « À propos » : lien discret sur la bannière ouvrant un volet concis (réseaux, bouton « Offrez-moi un café », mention du palier gratuit), entièrement traduit. Configurable dans l'admin.

= 0.1.9 =
* Lien « À propos » sur la bannière : ouvre un volet avec vos références et vos réseaux sociaux (libellés traduits automatiquement). Configurable dans l'admin.

= 0.1.8 =
* Couleur adaptative par page : en mode auto, le badge prend la couleur dominante de la page affichée. Une couleur fixée dans les réglages reste prioritaire.

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
Non. Tout est traité et stocké sur votre propre site. Le scanner et la détection de couleur n'appellent que votre propre site (boucle locale).

= Quelles sont les limites du blocage ? =
FreeCookie neutralise les scripts et iframes tiers connus avant consentement. Il ne bloque pas le localStorage/sessionStorage ni le fingerprinting réalisés par des scripts qu'il n'a pas neutralisés : vérifiez les traceurs de votre site (bouton « Lancer un scan ») et déclarez manuellement les scripts personnalisés si besoin. L'activation du plugin ne suffit pas à elle seule à rendre un site conforme.

= Y a-t-il des prérequis techniques ? =
La bibliothèque PHP GD est recommandée (détection de couleur depuis un logo PNG/JPG) ; sans elle, les autres sources de détection restent actives. Le blocage a priori réécrit le HTML des pages à la volée : coût mesuré inférieur à 1 ms par page.
