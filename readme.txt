=== FreeCookie — Cookie Consent RGPD/CNIL ===
Contributors: stephaneschmidt
Tags: cookie consent, gdpr, rgpd, cnil, consent mode
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.13.7
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

Gratuit jusqu'à 10 000 visites/mois. Au-delà, un soutien est proposé (10 $/an ou 45 $ à vie, sur polar.sh/freeeconcept — la clé Pro est envoyée automatiquement par e-mail) — le plugin reste entièrement fonctionnel dans tous les cas.

== Screenshots ==

1. Le bandeau de consentement (bureau) : catégories détaillées, cases décochées, « Tout refuser » à parité stricte avec « Tout accepter ».
2. Le bandeau sur mobile (375 px) : modale compacte centrée, boutons empilés pleine largeur, liste des catégories défilable.
3. Le volet pédagogique « Comprendre les cookies » : trois niveaux de lecture (Utile / À nuancer / À surveiller).
4. Le bandeau en arabe : prise en charge RTL complète, l'une des 26 langues livrées.

(Les images correspondantes sont dans `docs/img/` du dépôt : `screenshot-1.png` à `screenshot-4.png`.)

== Changelog ==

= 0.13.7 =
* Indice de défilement de la modale : quand une partie du contenu reste sous le pli (~20 % invisibles sur certains téléphones sans aucun signe), un fondu dégradé apparaît en bas du bandeau tant qu'il reste du contenu à voir, et au premier affichage la liste fait une petite « respiration » (18 px aller-retour, douce) qui montre le geste sans l'expliquer. Discret par conception : le fondu s'efface en fin de course, la respiration ne joue qu'une fois et respecte `prefers-reduced-motion`.

= 0.13.6 =
* Bandeau mobile plus compact : espacements et boutons resserrés sur les écrans étroits (padding et gaps réduits), et surtout `margin: 0` imposé aux boutons du bandeau — certains thèmes hôtes (Elementor observé) posaient leurs propres marges sur les `<button>`, ce qui écartelait verticalement la pile Enregistrer / Tout refuser / Tout accepter sur téléphone. La modale reste centrée avec des marges extérieures égales.

= 0.13.5 =
* Compteur de visites plus honnête : il ne comptait plus seulement les vrais navigateurs mais aussi chaque requête sans cookie (curl, robots à identité de navigateur, moniteurs de disponibilité, tâches planifiées internes), ce qui gonflait énormément le total. Désormais une visite n'est comptée que si le navigateur renvoie une sonde posée au chargement précédent (méthode « cookie-echo »), les tâches planifiées WordPress sont exclues, et l'avis d'administration parle de « sessions de navigation (approximation locale, sans traceur) » plutôt que de « visites ». Aucun traceur, toujours 100 % local.

= 0.13.4 =
* Bandeau responsive sur mobile et écrans étroits : la modale s'élargit à 86 % de la largeur (plafonnée à 380 px) au lieu de l'ancien 70 % « timbre-poste », reste centrée et défile en interne si elle est trop haute. Les boutons Accepter/Refuser passent en pleine largeur sans déborder, même quand le thème du site pose une marge sur les boutons. Prise en charge des écrans de couverture des pliables (Honor Magic V2, Galaxy Z Fold ≈ 280–380 px) : padding réduit, plus aucun débordement de contenu (les longs noms de cookies et domaines se coupent, la grille explicative passe sur une colonne).

= 0.13.3 =
* Nouvelle famille de formes de badge « Verre » (Pro) : 40 cookies translucides façon verre dépoli — corps transparent, reflet lumineux, bulles à éclat. Comme toutes les formes, elles suivent automatiquement la couleur principale du site.

= 0.13.2 =
* Correctifs d'affichage du bandeau (signalés en production) : le lien « À quoi servent les cookies ? » est désormais un lien discret aligné à gauche (avant : pastille flottant à droite, à cheval sur le séparateur) ; l'ouverture du volet « Comprendre les cookies » remplace proprement le contenu (fini le chevauchement) ; les boutons n'affichent plus de double bordure quand le thème du site ajoute son propre contour (l'anneau de focus n'apparaît qu'au clavier).

= 0.13.1 =
* Le bandeau visiteur parle désormais 26 langues (parité avec l'admin) : ajout de l'arabe, hébreu, tchèque, danois, grec, finnois, hongrois, indonésien, japonais, coréen, norvégien, polonais, portugais (Portugal ET Brésil, distincts), roumain, russe, suédois, turc, ukrainien, chinois simplifié ET traditionnel. Détection région-consciente pour le portugais et le chinois (variantes non interchangeables).
* Prise en charge des langues écrites de droite à gauche (arabe, hébreu) : le bandeau bascule automatiquement en RTL.
* Nouveau filtre `freecookie_known_first_party` : un thème/site peut déclarer ses propres cookies internes (préférences, langue…) sans modifier le plugin.

= 0.13.0 =
* Interface d'administration désormais traduisible et livrée traduite dans 26 langues (les mêmes que SwitchMyBar), plus l'anglais : fichiers gettext `.pot` / `.po` / `.mo` dans `/languages/` (source française). Jusqu'ici l'UI d'admin n'avait aucun fichier de langue.
* Rappel : le bandeau visiteur détecte déjà automatiquement la langue du navigateur (option « Détection de langue », activée par défaut) et reste servi depuis les jeux de chaînes intégrés (FC_I18n). L'extension du bandeau visiteur à ces 26 langues suit dans une prochaine version.
* Les traductions non latines (arabe, hébreu, japonais, coréen, chinois) sont fournies en qualité machine et méritent une relecture par un locuteur natif.

= 0.12.12 =
* Nouveau service reconnu et bloqué a priori : « Google Sign-In » (`accounts.google.com/gsi/client`, souvent injecté par Google Site Kit). Le bouton « Se connecter avec Google » ne charge qu'après consentement de la famille Préférences ; cookie `g_state` documenté dans les fiches (7 langues).

= 0.12.11 =
* Bannière plus discrète : jamais plus de 70 % de la largeur ni 60 % de la hauteur de l'écran (elle occupait quasi tout l'écran sur mobile). Planchers d'accessibilité conservés : 280 px de large minimum (interrupteurs et boutons utilisables), 340 px de haut minimum (paysage mobile). Desktop inchangé (680 px).

= 0.12.10 =
* Façade des embeds bloqués : dimensionnement fiabilisé. Le voile suit désormais la taille réelle du lecteur via ResizeObserver (iframe + parent) au lieu d'une mesure unique au chargement — corrige la petite boîte rognée quand le lecteur est layouté tardivement (onglet, aspect-ratio, polices). Les embeds cachés au chargement (onglet masqué) reçoivent aussi leur façade, dimensionnée à l'apparition.

= 0.12.9 =
* Un lecteur bloqué n'est plus jamais un cul-de-sac : chaque embed neutralisé (YouTube, Vimeo…) affiche une façade « Charger le lecteur » qui accepte UNIQUEMENT le service concerné (consentement granulaire, la bannière ne se rouvre pas). Nouvelle allow-list par service dans le cookie de consentement (`on`), reflétée dans le panneau : la case d'un service reste utilisable même quand sa famille est refusée. Textes traduits en 7 langues, boutons aux couleurs du site.

= 0.12.8 =
* Blocage a priori de Google Funding Choices (fenêtre de consentement publicitaire de Google) : le script `fundingchoicesmessages.google.com` et ses amorces inline (`googlefc`) sont neutralisés avant consentement, comme les autres traceurs marketing. Évite la double bannière quand un site colle le tag Funding Choices en dur. Nouveau service « Google Funding Choices » dans le scan, décrit en 7 langues.

= 0.12.7 =
* Reconnaissance des cookies fonctionnels du chat auto-hébergé Wise Chat (session de pseudo, préférences d'affichage) : classés strictement nécessaires, décrits en 7 langues. Le scan les montre honnêtement comme fonctionnels, pas comme traceurs.

= 0.12.6 =
* Descriptions des services entièrement traduites dans les 7 langues : la finalité de chaque traceur (Google Analytics, AdSense, YouTube, Meta, Maps…) s'affiche désormais dans la langue du visiteur, au lieu du français. La bannière est enfin 100 % cohérente sur les sites non francophones.

= 0.12.5 =
* Fiabilité du scan : un scan automatique qui ne parvient à récupérer aucune page (serveur mono-processus, loopback bloqué par l'hébergeur, panne réseau passagère) ne remplace plus un résultat valide précédent — fini le « aucun traceur détecté » affiché à tort après un scan planifié qui a échoué. Le scan interactif (HTML fourni par le navigateur de l'administrateur) reste la voie la plus fiable.

= 0.12.4 =
* Prise en charge de GTranslate : le cookie fonctionnel « googtrans » (langue d'affichage choisie) est reconnu et classé comme strictement nécessaire, décrit en 7 langues. Le sélecteur de langue reste fonctionnel — ses scripts ne sont pas neutralisés avant consentement, car changer de langue est une action explicite du visiteur.

= 0.12.3 =
* Correctif du « cookie au milieu de l'écran » : les styles globaux de boutons de certains thèmes/kits (largeur ou hauteur minimales, width 100 %) gonflaient le bouton du badge en un grand rectangle invisible — le cookie semblait flotter au tiers de l'écran. Les dimensions du badge sont désormais blindées (35×35, insensible aux kits).
* Mode ?fcdebug=1 : la taille réelle du badge est affichée (c'est elle qui a révélé le coupable).

= 0.12.2 =
* Le badge et la bannière se re-parentent automatiquement dans <body> : certains gabarits (pieds de page Elementor à effets, conteneurs avec transform/filter/backdrop-filter/contain) capturent les éléments « fixed » et les font dériver — plus possible désormais.
* Mode diagnostic (?fcdebug=1) enrichi : contour rouge sur le badge FreeCookie, repère vert au coin attendu, liste des ancêtres à effets pièges — une capture d'écran suffit à identifier n'importe quel élément intrus.

= 0.12.1 =
* La bannière s'adapte à la HAUTEUR de l'écran : sur les petits portables, la typographie et les espacements se compactent (deux paliers, 860 px et 700 px), et si la liste des catégories déborde malgré tout, c'est elle qui défile — les boutons Accepter/Refuser/Enregistrer restent toujours visibles sans défilement.

= 0.12.0 =
* Le résultat du scan est toujours visible côté visiteurs : quand aucun traceur tiers n'est détecté, la bannière l'affiche fièrement (« Bonne nouvelle : aucun traceur tiers n'a été détecté sur ce site ») au lieu de rester muette — traduit dans les 7 langues.
* Transparence totale : la catégorie « Strictement nécessaires » liste désormais ses cookies réels en fiches dépliables — le cookie de consentement de FreeCookie lui-même (avec sa durée réglée) et les cookies internes observés par le scan.

= 0.11.1 =
* Les pastilles des traceurs parlent le même langage que le volet pédagogique : « Utile / À nuancer / À surveiller » (au lieu de « Risque faible/moyen/élevé », jugé inquiétant), dans les 7 langues.
* La pastille est cliquable : elle ouvre « Comprendre les cookies » pour expliquer le code couleur.

= 0.11.0 =
* Achat Pro en ligne : bouton « Passer à Pro » vers la boutique (polar.sh/freeeconcept, 10 $/an ou 45 $ à vie) — la clé de licence est générée et envoyée AUTOMATIQUEMENT par e-mail à l'achat. Collez-la dans le champ Clé Pro, c'est tout : fidèle au « 100 % local », aucune vérification en ligne.
* L'avis de soutien (au-delà du seuil de visites) pointe vers la boutique ; le don « Offrez-moi un café » reste distinct.

= 0.10.3 =
* Correctif iOS : l'ombre du badge est portée par le SVG interne et non plus par le bouton flottant — sur iOS, un filtre CSS posé sur un élément position:fixed casse son ancrage (bug WebKit) et faisait dériver le badge au milieu de l'écran.
* Mode diagnostic embarqué : ajoutez ?fcdebug=1 à l'URL du site pour afficher les mesures en direct (viewport, zoom, position du badge) — utile pour le support mobile.

= 0.10.2 =
* Badge et bannière restent collés au bon coin sur Safari iOS même quand la page est zoomée (pincement, zoom de page, ou dézoom automatique d'une mise en page trop large) : ré-épinglage au viewport visuel, sans aucun effet quand le zoom est à 100 %.

= 0.10.1 =
* Correctif : plus d'anneau de focus (« cadre rouge ») autour du badge après un clic — certains thèmes dessinent un anneau sur les boutons focalisés. Le focus n'est rendu au badge que pour la navigation au clavier (accessibilité préservée), et les anneaux du thème (outline, box-shadow, bordure) sont neutralisés sur le badge.

= 0.10.0 =
* Nouveau bouton « À quoi servent les cookies ? » dans la bannière : un volet pédagogique explique qu’un cookie n’est pas mauvais en soi, avec trois exemples classés par couleur — utiles (vert), à nuancer (orange), à surveiller (rouge). Traduit dans les 7 langues.
* FreeCookie Pro : deux nouvelles familles de formes ABSTRAITES (non-gâteau) pour le badge — « Consentement » (coche d’approbation + élan) et « Réglages » (engrenage, curseurs, interrupteur, molette). 240 formes au total sur 12 familles.

= 0.9.1 =
* Nombre de pages analysées par scan au choix : 10 (recommandé), 25, 50 ou 100 — les traceurs étant posés par le thème et les extensions, un échantillon représentatif suffit ; crawler tout le site n'apporte rien de plus.
* Le scan échantillonne désormais tous les types de contenus publics (articles, pages, produits, contenus personnalisés…).
* Le scan planifié s'interrompt proprement après 20 secondes sur les hébergements limités, en conservant ce qui a déjà été trouvé.
* Le scan n'affiche plus les cookies de session de l'administrateur (wordpress_*, wp-settings-*) : ils n'existent jamais pour les visiteurs.

= 0.9.0 =
* FreeCookie Pro (système de confiance, aucune vérification en ligne) : 9 familles de formes supplémentaires pour le badge — 180 formes générées — dont deux familles EN COULEURS : « Pastilles du site » (pastilles aux couleurs détectées de votre site) et « Gourmandes » (couleurs naturelles de cookies : pâtes dorées, tout chocolat, trempé au chocolat blanc, caramel, marbré). Plus Cartoon, Fournée, Croqués & miettes, Nappés, Fêtes, Duo graphique et Emporte-pièce. Déverrouillées par la clé reçue après votre soutien ; la conformité de base reste toujours gratuite et complète.
* Administration : sélecteur de formes regroupé par familles avec verrous PRO, section « FreeCookie Pro » (clé, statut, lien de soutien).
* Scan fiable partout : le navigateur de l'administrateur fournit lui-même le HTML des pages à analyser — plus d'auto-requête du serveur, fonctionne en local et chez les hébergeurs qui bloquent le loopback (repli serveur conservé).
* Langue : quand l'option est activée, la langue du navigateur du visiteur prime réellement sur celle du site.

= 0.8.0 =
* Le scan détecte maintenant les COOKIES RÉELS, pas seulement les scripts tiers connus : en-têtes Set-Cookie côté serveur + observation dans le navigateur (aperçu sans blocage, réservé à l'administrateur) — les cookies posés en JavaScript comme _ga sont vus, à la manière des scanners du marché.
* Un cookie de service tiers observé (_ga, _pk_id…) révèle automatiquement le service correspondant.
* Base de cookies internes connus (WordPress, WooCommerce, outils de consentement, Polylang/WPML, Jetpack…) traduite en 7 langues ; les cookies inconnus sont listés comme « Cookie interne du site ».
* Barre de progression pendant le scan, avec journal en direct des services et cookies trouvés.
* Résultats : nouveau tableau « Cookies observés » (origine serveur/navigateur, service, catégorie, description) ; la liste publique [freecookie_cookies] inclut les cookies internes observés (« Ce site »).
* Les passages du scanner ne comptent plus comme des visites.

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

= Comment obtenir et activer une clé Pro ? =
Achetez sur polar.sh/freeeconcept (10 $/an ou 45 $ à vie) : la clé de licence (FCPRO-…) vous est envoyée automatiquement par e-mail. Collez-la dans FreeCookie ▸ FreeCookie Pro ▸ Clé Pro, enregistrez — c'est tout. Fidèle au principe « 100 % local », le plugin ne contacte aucun serveur de licences : la clé reçue suffit (système de confiance).

= Y a-t-il des prérequis techniques ? =
La bibliothèque PHP GD est recommandée (détection de couleur depuis un logo PNG/JPG) ; sans elle, les autres sources de détection restent actives. Le blocage a priori réécrit le HTML des pages à la volée : coût mesuré inférieur à 1 ms par page.
