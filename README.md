# FreeCookie — Consentement cookies RGPD/CNIL pour WordPress

Un bandeau de consentement **100 % local**, conforme **RGPD / ePrivacy / CNIL / nLPD**, qui **bloque réellement les traceurs avant le consentement** — sans dépendre d'aucun service tiers, sans compte, sans CDN.

Alternative libre et légère à CookieYes, Complianz, Cookiebot & co.

---

> ## Version bêta
>
> FreeCookie est en **bêta (0.1.5)**. Le plugin est fonctionnel et déjà en production sur des sites réels, mais il est jeune et évolue vite.
>
> **Vos retours sont précieux.** Un bug, une idée, un site où la détection de couleur ou le blocage se comporte mal, une incompatibilité de thème ? Ouvrez une [issue](../../issues) — chaque retour fait avancer le projet et sa fiabilité sur la diversité des sites WordPress.

---

## Pourquoi FreeCookie

La plupart des solutions du marché chargent la bannière, la logique de consentement **et** le journal de preuve depuis leur propre cloud, et facturent la conformité réelle. FreeCookie prend le contre-pied :

- **Tout en local.** Bannière, blocage et preuve servis par votre WordPress. Zéro requête vers un serveur tiers, rien à configurer chez un prestataire.
- **Conforme par défaut.** « Tout refuser » aussi accessible que « Tout accepter », aucune case pré-cochée, blocage réel *avant* le clic.
- **Gratuit pour la plupart des sites.** Voir [Modèle](#modèle).

## Fonctionnalités

- **Blocage a priori réel.** Les scripts et iframes tiers connus (Google Analytics, GTM, Meta Pixel, YouTube, Maps, Matomo, Hotjar, TikTok, LinkedIn…) sont neutralisés (`type="text/plain"`, `src` mis de côté) **tant que le visiteur n'a pas consenti**, puis débloqués au clic. **Compatible avec le cache de page** (le HTML mis en cache est identique pour tous ; la décision vit dans un cookie first-party lu en JavaScript).
- **Bannière accessible et conforme.** « Tout accepter » / « Tout refuser » / « Personnaliser » à parité stricte (mêmes styles, un seul clic pour refuser), navigation clavier, focus visible, aucune case cochée par défaut.
- **Google Consent Mode v2 natif.** Émission de `gtag('consent','default', … denied)` avant tout tag Google, puis `update` selon les finalités acceptées. Mapping automatique finalités → signaux (`analytics_storage`, `ad_storage`, `ad_user_data`, `ad_personalization`).
- **Journal de preuve dans votre base.** Chaque consentement est horodaté (finalités acceptées/refusées, version de la bannière, IP masquée et hachée) dans une table WordPress dédiée — exportable, hébergé nulle part ailleurs.
- **Scanner de cookies local.** Le site s'analyse lui-même (aucun service externe) pour détecter les traceurs présents et composer la liste de cookies affichée au visiteur.
- **Détection automatique de la couleur de marque.** La bannière prend la couleur dominante du site : lecture du **logo** (y compris **SVG**), du kit **Elementor**, de **theme.json**, des réglages du personnalisateur et des thèmes populaires (Astra, Kadence, GeneratePress…), plus une **analyse de fréquence** des couleurs récurrentes. Les couleurs d'usine et les gris/blanc/noir sont écartés.
- **Multilingue automatique.** Textes de bannière fournis en **7 langues** (FR, EN, DE, IT, ES, NL, PT), sélectionnés selon la langue du visiteur (WPML/Polylang → langue du site → navigateur). Extensible.
- **Géo-ciblage local.** Régime de consentement adapté à la région (UE / Suisse / hors-UE) à partir des en-têtes fournis par l'hébergement/CDN, sans base de géolocalisation tierce. Par défaut : le régime le plus protecteur.
- **Personnalisation complète.** Un menu **FreeCookie** dans l'administration : toutes les couleurs (color pickers), tous les textes par langue, les finalités et les options.
- **Léger.** Quelques kilo-octets, pas de jQuery en front, aucun appel réseau externe, bannière rendue côté serveur (pas de « flash »).

## Conformité

FreeCookie vise le socle commun exigé en **France (CNIL, art. 82)**, en **Suisse (nLPD / art. 45c LTC)**, en **Allemagne (§25 TDDDG)** et dans l'**UE (RGPD + directive ePrivacy)** :

1. blocage effectif des traceurs non exemptés avant tout consentement ;
2. refus aussi simple que l'acceptation, au premier niveau ;
3. aucune finalité pré-cochée ;
4. journal de preuve conservable et exportable ;
5. retrait du consentement possible à tout moment (badge/centre de préférences).

> FreeCookie fournit les outils techniques de la conformité. Il ne remplace pas un conseil juridique adapté à votre situation.

## Installation

1. Téléchargez la dernière archive `freecookie-x.y.z.zip` depuis les [Releases](../../releases).
2. Dans WordPress : **Extensions ▸ Ajouter ▸ Téléverser une extension**, choisissez le zip, puis **Activer**.
3. Ouvrez le menu **FreeCookie** : la couleur de marque est détectée automatiquement, la bannière est prête.

## Configuration

Tout se règle dans le menu **FreeCookie** de l'administration :

- **Apparence** : couleur principale (ou une pastille de couleur détectée), fond, textes, boutons, badge.
- **Textes** : titre, message et libellés des boutons, modifiables par langue.
- **Options** : blocage a priori, durée de validité du consentement, seuil gratuit.
- **Scanner** : détecte les cookies du site et rafraîchit la détection des couleurs.

Affichez la liste des cookies dans une page avec le shortcode `[freecookie_cookies]`.

## Comment ça marche

Le moteur de blocage réécrit, dans un tampon de sortie côté serveur, les balises tierces reconnues en version neutralisée. Côté client, un script sans dépendance lit le cookie de consentement, débloque les catégories acceptées (ré-injection des scripts, restauration des iframes) et émet les signaux Consent Mode. Comme le HTML mis en cache ne dépend pas du choix du visiteur, le blocage reste valable derrière un cache de page.

## Modèle

FreeCookie est **libre (GPL)** et **entièrement fonctionnel pour tout le monde**. Il est **gratuit** pour la grande majorité des sites (jusqu'à ~10 000 visites/mois). Au-delà, un **soutien** est proposé (à titre volontaire) pour aider le projet à durer — le plugin n'est jamais bridé, et la conformité de base reste toujours gratuite.

## Feuille de route

- Soumission au dépôt officiel WordPress.org.
- Fichiers de traduction `.po/.mo` et davantage de langues.
- Visualiseur du journal de consentement dans l'admin.
- Global Privacy Control (GPC), générateur de politique de cookies.
- IAB TCF (pour la publicité programmatique) — ultérieurement, sur besoin.

## Contribuer / retours

Le projet est en bêta et **les retours d'utilisateurs sont ce qui compte le plus** à ce stade. N'hésitez pas à :

- ouvrir une [issue](../../issues) (bug, incompatibilité de thème, couleur mal détectée, suggestion) ;
- proposer une **traduction** ou l'amélioration d'une existante ;
- signaler un service tiers à ajouter à la liste des traceurs auto-bloqués.

## Licence

[GPL-2.0-or-later](LICENSE). Vous êtes libre d'utiliser, d'étudier, de modifier et de redistribuer FreeCookie.

## Auteur

Développé par **Stéphane Schmidt** — [alveo.design](https://alveo.design).
