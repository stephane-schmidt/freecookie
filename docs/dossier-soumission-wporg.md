# Dossier de soumission — FreeCookie sur WordPress.org

*Préparé le 21 juillet 2026 — état du code audité : branche `claude/macbook-session-backups-nrd8k6`, version `0.13.8` (voir la note de version ci-dessous).*

> **Note de version importante.** La mission mentionne une version **0.14.0** avec compteur de visites migré sur une route REST `freecookie/v1/visit` + cookie posé en JS. **Cette version n'existe nulle part dans le dépôt** (ni sur cette branche, ni sur `main`, ni dans l'historique) : le code audité est en **0.13.8**, avec un compteur « cookie-echo » posé **côté serveur** (`includes/class-fc-visit-counter.php`, cookie `fc_v`, `httponly`, 30 min) et **aucune** route `/visit`. L'audit ci-dessous porte donc sur le code réel ; un encadré § 1.9 donne les critères à respecter si la variante REST est finalisée avant soumission. **Avant de soumettre : réconcilier la version (en-tête du plugin, `FREECOOKIE_VERSION`, `Stable tag` du readme et tag SVN doivent être identiques).**

---

## 1. Audit de conformité aux Plugin Guidelines WordPress.org

### 1.1 Licence GPL compatible et déclarée — ✅ conforme

- ✅ `LICENSE` : texte complet GPL v2 présent à la racine.
- ✅ `freecookie.php:11-12` : `License: GPL-2.0-or-later` + `License URI` dans l'en-tête.
- ✅ `readme.txt:8-9` : `License: GPLv2 or later` + URI — cohérent avec l'en-tête PHP.
- ✅ Aucune bibliothèque tierce embarquée (zéro dépendance JS/PHP externe) : pas de question de compatibilité de licence.

### 1.2 Échappement / sanitisation / nonces — ✅ globalement conforme (2 points mineurs)

**Sorties (escaping) :**
- ✅ `public/partials/banner.php` : toutes les sorties passent par `esc_html` / `esc_attr` / `esc_url` (vérifié ligne par ligne, y compris fiches cookies, volet À propos, liens sociaux).
- ✅ `includes/class-fc-admin.php` (render, 350 lignes de HTML) : `esc_html_e`, `esc_attr`, `esc_url`, `esc_textarea`, `(int)` sur `$_GET['fc_scanned']` / `fc_services` — propre.
- ✅ `includes/class-fc-cookie-list.php` et `class-fc-plugin.php` (honor_notice) : tout est échappé.
- ⚠️ **À justifier (pas bloquant)** : deux `echo` non échappés de SVG internes avec `phpcs:ignore` — `public/partials/banner.php:179` (`FC_Shapes::get()`) et `includes/class-fc-admin.php:340` (`$fc_s['svg']`). Le SVG est statique et interne (pas d'entrée utilisateur), le commentaire d'ignore est présent : garder la justification, mais s'attendre à une question du reviewer — envisager `wp_kses` avec une allow-list SVG en une passe.

**Entrées (sanitisation) :**
- ✅ `includes/class-fc-admin.php:146-222` : `sanitize_callback` déclaré dans `register_setting`, chaque champ passé par `sanitize_text_field` / `sanitize_textarea_field` / `esc_url_raw` / `sanitize_email` / casts + listes blanches (`in_array` strict pour fréquence, pages, forme).
- ✅ `includes/class-fc-rest.php:220-249` : chaque paramètre REST re-sanitisé (`preg_replace`, `sanitize_text_field`, `esc_url_raw`) avant insertion ; `FC_Consent_Store::record()` tronque en plus chaque champ (`substr`) et utilise `$wpdb->insert` (préparé par WP).
- ✅ Superglobales toujours lues via `wp_unslash` + `sanitize_text_field` (`class-fc-visit-counter.php:45,51`, `class-fc-geo.php:34-45`, `class-fc-scanner.php:36`, `class-fc-consent-store.php:94`).
- ⚠️ `includes/class-fc-rest.php:114` : `$html = (string) $req->get_param( 'html' )` non sanitisé mais **jamais stocké ni affiché** (uniquement balayé par `stripos` puis jeté), avec `phpcs:ignore` justificatif — acceptable, garder le commentaire tel quel pour le reviewer.

**Nonces et capacités :**
- ✅ Formulaire de réglages : Settings API (`settings_fields` = nonce automatique) + `current_user_can( 'manage_options' )` sur le rendu (`class-fc-admin.php:228`).
- ✅ Scan sans JS : `check_admin_referer( 'freecookie_scan' )` + capacité (`class-fc-plugin.php:158-161`) ; le formulaire pose `wp_nonce_field` (`class-fc-admin.php:520`).
- ✅ Routes REST de scan : `permission_callback` = `manage_options` (`class-fc-rest.php:37-83`).
- ✅ Route publique `/consent` : `permission_callback => '__return_true'` **mais** nonce `wp_rest` vérifié dans le callback (`class-fc-rest.php:223-226`) + rate-limit 10/min/IP (l.229-236). Le `__return_true` est un flag classique de review : **ajouter une phrase de justification en commentaire au-dessus du `permission_callback`** (« endpoint public par nature : consentement d'un visiteur anonyme, protégé par nonce + rate-limit »). C'est déjà partiellement commenté — le rendre explicite.
- ✅ Aperçu « sniff » : nonce `fc_sniff` + `manage_options` (`class-fc-scanner.php:32-41`).
- ✅ SQL : `dbDelta` à l'installation, `$wpdb->insert` (auto-préparé) ; les deux requêtes interpolées (`DROP TABLE`, `COUNT(*)`) n'utilisent que `$wpdb->prefix` — pas d'entrée utilisateur.

### 1.3 Pas d'appels externes non déclarés (« phoning home »), pas de tracking — ✅ conforme

- ✅ **Aucun appel réseau sortant vers un tiers.** Les seuls `wp_safe_remote_get` du code visent le site lui-même : scanner (`class-fc-scanner.php:109,153`, boucle locale) et détecteur de couleurs (`class-fc-color-detector.php:337,369`, home + CSS du thème local). Vérifié par grep exhaustif (`wp_remote*`, `curl_*`, `file_get_contents`, `fsockopen`) : rien d'autre.
- ✅ Aucun CDN, aucune police externe, aucun pixel : CSS/JS servis depuis le plugin.
- ✅ Licence Pro en « système de confiance » : **zéro vérification en ligne** (`class-fc-pro.php:35-38` — simple test de longueur de clé).
- ✅ Liens sortants uniquement **cliqués par l'admin** (polar.sh, revolut.me, github.com — `class-fc-pro.php:24-27`, `class-fc-plugin.php:200-201`) : autorisé (upsell/documentation), pas du phoning home.
- ✅ Compteur de visites : agrégat mensuel local (`option freecookie_visits`), aucun identifiant individuel, rien d'envoyé nulle part.
- ⚠️ **Mineur** : `sslverify => false` sur les requêtes loopback (`class-fc-scanner.php:113,160`, `class-fc-color-detector.php:369`) — flag récurrent de review ; le commentaire « certificat local auto-signé » existe déjà dans le scanner, **ajouter le même commentaire justificatif ligne 369 du color-detector** (une phrase suffit).

### 1.4 Pas de code obfusqué — ✅ conforme

- ✅ Aucun `eval`, `create_function`, `assert`, code encodé ou minifié illisible.
- ✅ Le seul `base64_encode` (`class-fc-admin.php:97`) encode l'icône SVG du menu au format data-URI attendu par `add_menu_page`, avec `phpcs:ignore` justifié — usage standard, accepté en review.
- ✅ JS livré lisible et commenté (non minifié) — WP.org exige que les sources lisibles soient disponibles : c'est le cas.

### 1.5 Préfixage et collisions — ⚠️ un vrai chantier

- ✅ Fonctions globales : `freecookie_activate`, `freecookie_deactivate`, `freecookie_boot` — bien préfixées.
- ✅ Options : `freecookie_settings`, `freecookie_visits`, `freecookie_db_version`, `freecookie_colors_detected`, `freecookie_scan` — cohérentes.
- ✅ Hooks/filtres : `freecookie_region`, `freecookie_country`, `freecookie_known_first_party`, `freecookie_scan_event` — bien préfixés.
- ✅ Constantes : `FREECOOKIE_*` ; table : `{$prefix}freecookie_log` ; shortcode `[freecookie_cookies]` ; text domain `freecookie` = slug.
- ⚠️ **Classes `FC_*` (tous les fichiers `includes/class-fc-*.php`) : préfixe de 2 lettres, explicitement refusé par la review team (« prefixes must be at least 4-5 characters and unique »).** Risque réel de collision (d'autres plugins utilisent `FC_`). Correctif en une phrase : renommer `FC_Plugin` → `FreeCookie_Plugin` (etc.) ou envelopper `includes/` dans un `namespace FreeCookie;` — c'est mécanique (aucune API publique PHP n'expose ces classes) et c'est LE point qui déclenche presque à coup sûr un aller-retour de review.
- ⚠️ **Mineur** : transients `fc_scan_run` (`class-fc-scanner.php:21`) et `fc_rl_*` (`class-fc-rest.php:231`), cookie `fc_v`, variable JS globale `fcScan` — préfixe court ; risque faible (valeurs éphémères) mais profiter du renommage pour passer en `freecookie_*` / `fcookie_*`.
- ✅ Globals JS front : `FreeCookieData`, `window.FreeCookie` — distinctifs, OK.

### 1.6 Désinstallation propre — ✅ conforme

- ✅ `uninstall.php` : garde `WP_UNINSTALL_PLUGIN`, supprime **les 5 options** du plugin (liste vérifiée exhaustive par grep : aucune option orpheline).
- ✅ Table `freecookie_log` supprimée seulement si `purge_on_uninstall` — choix documenté (auditabilité RGPD du journal de preuve) : défendable en review, la politique est commentée en tête de fichier.
- ✅ Cron `freecookie_scan_event` nettoyé à la désactivation (`freecookie.php:82-85`), qui précède toujours la désinstallation.
- ⚠️ **Mineur** : les transients `fc_scan_run` / `fc_rl_*` ne sont pas purgés (auto-expiration ≤ 15 min — tolérable) et l'option `purge_on_uninstall` testée dans `uninstall.php:15` **n'a aucun champ dans l'écran d'admin** (introuvable dans `class-fc-admin.php`) : ajouter la case à cocher correspondante dans les Options, sinon la purge est inatteignable.

### 1.7 readme.txt au format officiel — ⚠️ plusieurs corrections avant soumission

- ✅ En-tête complet : `Contributors`, `Tags` (5, maximum respecté), `Requires at least: 6.0`, `Tested up to`, `Requires PHP: 7.4`, `Stable tag`, `License` + URI — tous présents et cohérents avec l'en-tête PHP.
- ✅ Sections `Description`, `FAQ`, `Screenshots`, `Changelog` présentes et riches (le changelog détaillé est un vrai plus en review).
- ⚠️ `readme.txt:11` : **description courte de 171 caractères — le maximum est 150** (elle sera tronquée) ; raccourcir, p. ex. « Bandeau cookies 100 % local, conforme RGPD/CNIL : blocage réel avant consentement, journal de preuve, Consent Mode v2. Zéro appel tiers. »
- ⚠️ `readme.txt:5` : `Tested up to: 6.8` — à mettre à jour vers la dernière version stable de WordPress au moment de la soumission (6.9+ mi-2026) après un test réel.
- ⚠️ `readme.txt:7` : `Stable tag: 0.13.8` — devra correspondre EXACTEMENT à la version de l'en-tête PHP et au tag SVN au moment de l'upload (voir note de version en tête de dossier).
- ⚠️ Section `== Installation ==` absente — non bloquant mais standard ; ajouter 3 lignes (installer, activer, lancer un scan).
- ⚠️ `readme.txt:33` : la note « les images sont dans `docs/img/` du dépôt » n'a pas sa place sur WP.org — les captures vont dans le dossier **`assets/` du SVN** (voir § 3) ; supprimer cette ligne du readme final.
- ⚠️ `readme.txt:24` : « **Gratuit jusqu'à 10 000 visites/mois** » — formulation dangereuse au regard de la guideline 5 (trialware) : elle laisse croire à un quota bloquant alors que seul un avis d'admin s'affiche. Reformuler : « Toutes les fonctionnalités de conformité sont gratuites et illimitées ; au-delà de 10 000 visites/mois, un simple avis (masquable) invite à soutenir le projet. »
- ⚠️ `readme.txt:2` : `Contributors: stephaneschmidt` — doit être le **nom d'utilisateur WordPress.org exact** de Stéphane (à vérifier lors de la création du compte, § 4).
- ⚠️ Readme intégralement en **français** : accepté techniquement, mais le répertoire est anglophone (recherche, review). Fortement recommandé : readme principal en anglais, français conservé via les traductions du répertoire (translate.wordpress.org).

### 1.8 i18n — ✅ conforme (architecture double assumée)

- ✅ `Text Domain: freecookie` = slug, `Domain Path: /languages` (`freecookie.php:13-14`).
- ✅ `load_plugin_textdomain` appelé sur `plugins_loaded` (`class-fc-plugin.php:81`).
- ✅ Toutes les chaînes d'admin passent par `__()` / `esc_html__()` / `esc_html_e()` avec le bon domaine (vérifié dans `class-fc-admin.php`, `class-fc-plugin.php`) ; les placeholders ont leurs commentaires `/* translators: */`.
- ✅ `languages/` : `freecookie.pot` + 27 paires `.po`/`.mo` (26 langues).
- ✅ Le bandeau **visiteur** utilise un système maison (`FC_I18n`, jeux de chaînes embarqués, détection Polylang/WPML/Accept-Language correctement sanitisée `class-fc-i18n.php:61-64`) : choix de conception légitime (langue du visiteur ≠ locale du site), pas une violation — mais le documenter en une ligne dans la FAQ du readme pour prévenir la question du reviewer (« pourquoi ces chaînes ne sont-elles pas dans le .pot ? »).
- ℹ️ Une fois le plugin accepté, les traductions officielles passeront par translate.wordpress.org ; les `.mo` livrés restent une solution de repli valide.

### 1.9 Compteur de visites et cookies posés — ✅ conforme (dans sa version actuelle)

État audité (0.13.8, `class-fc-visit-counter.php`) :
- ✅ **Aucune donnée personnelle** : le cookie `fc_v` ne contient que `pending`/`counted` (pas d'identifiant, pas d'IP, pas d'empreinte) ; seul un agrégat mensuel `{'2026-07': N}` est stocké (option autoload désactivé, 12 mois glissants).
- ✅ **Cookie fonctionnel/exempté** : durée 30 min, `HttpOnly`, `SameSite=Lax`, `Secure` si SSL — pas de consentement requis (exemption « mesure strictement nécessaire » assumée et l'avis d'admin parle honnêtement d'« approximation locale, sans traceur »).
- ✅ Exclusions propres : admin, AJAX, cron, REST, sniff du scan, bots par UA (l.36-48).
- ✅ Le cookie de consentement `freecookie_consent` (JSON de choix, posé en JS) est lui aussi strictement fonctionnel, listé en transparence dans le bandeau (`class-fc-frontend.php:160-177`) — exemplaire.
- ⚠️ **Si la migration 0.14.0 vers une route REST `freecookie/v1/visit` + cookie JS est finalisée avant soumission**, points de contrôle pour rester conforme : (1) le cookie doit rester sans identifiant individuel (sinon il devient un traceur nécessitant consentement) ; (2) la route doit avoir un `permission_callback` explicite (même `__return_true` commenté) + le même rate-limit que `/consent` ; (3) ne pas stocker l'IP ni l'UA pour ce comptage ; (4) exclure `REST_REQUEST` du double comptage ; (5) mettre à jour la fiche du cookie dans la catégorie « Strictement nécessaires » du bandeau (le nom `fc_v` y est déjà géré, `class-fc-frontend.php:184`).

### 1.10 Guideline 5 (« trialware ») — ⚠️ LE point bloquant potentiel

- ⚠️ **12 familles de formes Pro sur 13 sont livrées DANS le plugin gratuit mais verrouillées par clé** (`class-fc-shapes.php:118-129`, verrous UI `class-fc-admin.php:328-339`, champ clé l.460-480). La guideline 5 interdit les fonctionnalités présentes dans le code mais désactivées derrière un paiement (« paid functionality must be removed, not just disabled »). Correctif en une phrase : **retirer les familles Pro du build WP.org et les livrer dans une extension compagnon « FreeCookie Pro » téléchargée après achat** (le champ clé peut rester, il activerait l'addon) — c'est aussi ce qui simplifie le re-pricing du § 5.
- ✅ Le seuil de visites, lui, est conforme (aucune fonction désactivée, avis masquable) sous réserve de la reformulation du readme (§ 1.7).
- ✅ L'upsell (bouton « Passer à Pro », liens boutique) est autorisé : admin uniquement, non intrusif, pas de fausse urgence.

### 1.11 Divers relevés au fil de l'audit

- ✅ `ABSPATH` gardé en tête de **tous** les fichiers PHP (vérifié sur les 20 fichiers).
- ✅ Pas de `error_reporting`/`ini_set`, pas d'écriture de fichiers, pas de session PHP.
- ✅ Assets enfilés proprement (`wp_enqueue_*`, versionnés `FREECOOKIE_VERSION`, footer) ; `wp_localize_script` pour la config.
- ✅ Consent Mode v2 : signaux « denied » par défaut imprimés localement (`class-fc-consent-mode.php`) — aucun chargement de script Google par le plugin lui-même.
- ⚠️ **Vie privée / journal de preuve** : la table `freecookie_log` stocke l'UA complet + un hash SHA-256 salé d'IP tronquée (`class-fc-consent-store.php:76-77,91-103` — bonne minimisation). Ajouter dans le readme un court paragraphe « données stockées localement » et, en nice-to-have post-acceptation, brancher les personal data exporter/eraser de WP sur cette table.
- ⚠️ **README.md GitHub** : sera distribué dans le zip ; soit l'exclure du build, soit vérifier qu'il ne contredit pas le readme.txt.
- ℹ️ CSS inline dans les vues admin (`<style>` dans `class-fc-admin.php`) : toléré pour un écran unique, mais un reviewer peut suggérer `wp_add_inline_style` — non bloquant.

### Synthèse de l'audit

| # | Point | Verdict |
|---|-------|---------|
| 1 | Licence GPL | ✅ |
| 2 | Échappement / sanitisation / nonces | ✅ (2 justifications à soigner) |
| 3 | Phoning home / tracking | ✅ (commentaire sslverify à ajouter) |
| 4 | Code obfusqué | ✅ |
| 5 | Préfixage | ⚠️ classes `FC_*` à renommer |
| 6 | Désinstallation | ✅ (case `purge_on_uninstall` à exposer) |
| 7 | readme.txt | ⚠️ 8 corrections listées |
| 8 | i18n | ✅ |
| 9 | Compteur de visites / cookies | ✅ (critères 0.14.0 fournis) |
| 10 | Trialware (formes Pro verrouillées) | ⚠️ **à corriger avant soumission** |

---

## 2. Disponibilité du slug

- **`freecookie` : LIBRE.** Vérifié le 21/07/2026 : `https://wordpress.org/plugins/freecookie/` renvoie une page « nothing matched your query » (aucun plugin existant, équivalent 404). Aucun plugin homonyme dans le répertoire.
- Attention : le slug définitif est **attribué par la review team** à partir du nom du plugin ; le champ `Plugin Name` (« FreeCookie — Cookie Consent RGPD/CNIL ») devrait produire `freecookie` mais il est possible de demander explicitement le slug dans le formulaire. Une fois attribué, il est **définitif**.
- Slugs de repli si `freecookie` était réservé entre-temps :
  1. **`freecookie-consent`** — garde la marque, décrit la fonction ;
  2. **`freecookie-cookie-banner`** — plus long mais riche en mots-clés de recherche.
- Le nom commercial « FreeCookie » ne pose pas de problème de marque déposée connue (pas de produit homonyme dans le répertoire ni de trademark évidente type « WordPress »/« WooCommerce » dans le nom — interdit par la guideline 17).

---

## 3. Assets à produire (dossier `assets/` du SVN, PAS dans le zip du plugin)

| Fichier | Dimensions | Contenu recommandé |
|---|---|---|
| `banner-1544x500.png` | 1544 × 500 | Le badge cookie mordu (forme « croque-lateral ») en grand à gauche, accroche à droite : « Consentement cookies 100 % local — RGPD · CNIL · Consent Mode v2 », fond aux couleurs alveo.design. |
| `banner-772x250.png` | 772 × 250 | Même composition resserrée (version non-retina, obligatoire en plus de la grande). |
| `icon-256x256.png` | 256 × 256 | Le cookie mordu seul, à plat, sur fond uni contrasté (c'est l'icône vue dans la recherche du répertoire et l'admin WP). |
| `icon-128x128.png` | 128 × 128 | Déclinaison exacte de la 256 (lisibilité vérifiée à petite taille : garder 4-5 pépites max). |
| `screenshot-1.png` | ≥ 1200 px de large | Bandeau desktop : catégories dépliées, cases décochées, « Tout refuser » / « Tout accepter » à parité (correspond à la légende 1 du readme). |
| `screenshot-2.png` | capture mobile 375 px | Modale compacte centrée, boutons empilés pleine largeur, liste défilable (légende 2). |
| `screenshot-3.png` | ≥ 1200 px | Volet pédagogique « Comprendre les cookies » avec les trois niveaux Utile / À nuancer / À surveiller (légende 3). |
| `screenshot-4.png` | ≥ 1200 px | Bandeau en arabe, RTL complet — argument multilingue fort (légende 4). |

Notes :
- Les 4 captures existent déjà dans `docs/img/screenshot-1.png` à `screenshot-4.png` : vérifier leur résolution/fraîcheur (elles doivent montrer la v soumise, avec la modale centrée 0.13.8) puis les copier dans `assets/` du SVN.
- La numérotation des fichiers doit correspondre EXACTEMENT à l'ordre des légendes de `== Screenshots ==` du readme.
- Recommandé en plus : `screenshot-5.png` — l'écran d'administration avec le scanner et sa barre de progression (fonctionnalité différenciante, à ajouter aussi dans le readme).
- Formats acceptés : PNG/JPG (les bannières peuvent aussi exister en `banner-1544x500-rtl.png` plus tard).

---

## 4. Checklist de soumission pas à pas

### Part Stéphane (~30 minutes en tout, en 3 moments espacés)

**Moment 1 — création du compte et envoi (≈ 15 min)**
1. Créer un compte sur https://login.wordpress.org/register (e-mail : stephane.schmidt@hotmail.com ou une adresse projet — **c'est cette adresse qui recevra tous les mails de la review team**, la surveiller, y compris les spams).
2. Noter le nom d'utilisateur choisi et me le transmettre → je mets à jour `Contributors:` dans le readme avant de fabriquer le zip.
3. Je fournis le zip final (corrigé des ⚠️ du § 1) : le vérifier une dernière fois en l'installant sur un site de test (upload via Extensions ▸ Ajouter).
4. Aller sur https://wordpress.org/plugins/developers/add/ (connecté), téléverser le zip, cocher les cases de conformité aux guidelines, envoyer. Un seul plugin en attente à la fois par compte : normal.
5. Réception immédiate d'un accusé automatique par e-mail avec le slug demandé.

**Moment 2 — pendant la review (≈ 5 min, si sollicité)**
6. Délai typique : **2 à 12 semaines** (la file varie ; les cookie banners sont examinés attentivement car la catégorie est encombrée). Ne pas re-soumettre pendant l'attente.
7. Si la review team écrit (adresse `plugins@wordpress.org`) : **répondre depuis le compte e-mail du compte WP.org, sans changer le sujet du mail**, même pour dire « corrigé ». Me transférer le mail : je prépare le correctif et le nouveau zip, Stéphane n'a qu'à répondre en le joignant. Une absence de réponse sous ~3 mois clôt le dossier.

**Moment 3 — après acceptation (≈ 10 min)**
8. Le mail d'acceptation contient l'URL du dépôt SVN (`https://plugins.svn.wordpress.org/freecookie/`) et active l'accès avec les identifiants WP.org de Stéphane.
9. Me transmettre l'URL SVN reçue (PAS le mot de passe en clair dans un canal non sûr — on conviendra du mécanisme).
10. Plus tard : activer la 2FA sur le compte WP.org et vérifier que la fiche du plugin (page publique) affiche bien bannière/icône.

### Part Lili (moi)

- Avant soumission : appliquer tous les correctifs ⚠️ du § 1 (renommage `FC_*`, extraction des formes Pro, readme, versions réconciliées), fabriquer le zip propre (sans `.git`, `.gitignore`, `docs/`, `README.md`), le tester sur WP dernière version + PHP 7.4 et 8.3, passer Plugin Check (l'outil officiel de pré-review) et PHPCS/WPCS.
- Pendant la review : rédiger chaque réponse technique aux mails de l'équipe et produire les zips corrigés.
- Après acceptation : **je m'occupe de tout le SVN** — checkout, arborescence `trunk`/`tags`/`assets`, commit de la version, tag `0.x.y` = `Stable tag`, upload des bannières/icônes/captures dans `assets/`, puis le rituel de chaque release.
- Ensuite : mise en place des releases synchronisées GitHub → SVN et surveillance des premiers retours support.

---

## 5. Re-pricing — rappel du plan CAP-12-MOIS et découpage gratuit/pro

Rappel du plan : sur 12 mois, version gratuite distribuée sur WP.org comme canal d'acquisition + offre pro à **29-49 $/an** (repositionnement au-dessus du 10 $/an actuel de polar.sh, qui devient le tarif early-bird à honorer à vie pour les clés déjà émises).

Découpage le plus simple à maintenir (en 5 lignes) :

1. **Gratuit (WP.org) = 100 % de la conformité** : bandeau, blocage a priori, scanner, journal de preuve, Consent Mode v2, 26 langues, 1 famille de formes — jamais de quota bloquant (l'avis de soutien disparaît au profit d'un simple lien « Pro »).
2. **Pro (addon séparé, 29 $/an « Site ») = le confort visuel** : les 12 familles de formes (240 badges), presets de couleurs installables, dark mode auto — exactement le code déjà écrit, déplacé dans l'addon (résout du même coup le ⚠️ trialware du § 1.10).
3. **Pro 49 $/an (« Studio », jusqu'à 10 sites)** : même addon + les futures fonctions d'exploitation (export CSV/PDF du journal de preuve, statistiques de consentement agrégées) — une seule base de code, la clé distingue le palier.
4. **Frontière de maintenance** : le gratuit ne dépend jamais de l'addon ; l'addon ne touche qu'aux hooks/filters publics du gratuit (`freecookie_*`) — une seule review WP.org à entretenir, l'addon vit sur polar.sh sans contrainte de délai.
5. **Règle d'arbitrage pour la suite** : tout ce qui rend un site *conforme* reste gratuit à vie (argument de confiance et de différenciation face à Cookiebot/Axeptio) ; tout ce qui rend l'expérience *plus belle ou plus pratique pour un pro* est payant.

---

*Dossier rédigé par Lili le 21/07/2026. Fichiers de référence : `freecookie.php`, `includes/` (20 classes), `admin/admin.js`, `public/` (js, css, partials), `uninstall.php`, `readme.txt`, `languages/` (55 fichiers), `docs/img/` (4 captures).*
