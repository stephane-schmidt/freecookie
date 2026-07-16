=== FreeCookie — GDPR/ePrivacy Cookie Consent Banner ===
Contributors: stephaneschmidt
Donate link: https://polar.sh/freeeconcept
Tags: cookies, gdpr, consent, privacy, cookie banner
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 0.13.8
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The cookie banner that practices what it preaches: trackers blocked before consent, zero third-party calls. GDPR, ePrivacy, CNIL & nLPD ready.

== Description ==

**FreeCookie** is a lightweight, privacy-first cookie consent manager (CMP) for WordPress. It shows an accessible consent banner and — the part that actually matters — **blocks third-party scripts and iframes *before* they run**. No tracking cookie is dropped until your visitor says yes. That is what GDPR and the ePrivacy Directive really ask for: **prior, informed, freely given consent, with nothing stored beforehand.**

And unlike many cookie plugins, FreeCookie makes **no external calls of its own** — no CDN, no remote API, no phone-home. Everything runs on your own server. A consent tool should be the one thing on your site you never have to worry about; that is exactly what this one is built to be.

= Why FreeCookie =

* **Prior blocking, by design.** Known third-party scripts and `<iframe>` embeds are neutralised at page render (rewritten to `type="text/plain"`) and released only once the matching consent category is granted. Nothing is deposited before agreement.
* **Refusing is as easy as accepting.** "Accept all" and "Reject all" carry equal visual weight (button parity) — the way CNIL guidelines expect real consent to look.
* **Google Consent Mode v2, denied by default.** `ad_storage`, `ad_user_data`, `ad_personalization` and `analytics_storage` all start *denied* and update on consent — privacy-safe ad delivery out of the box.
* **A scanner that finds your trackers for you.** The built-in **local scanner** crawls a sample of your own pages (server-side requests plus an in-admin browser check) and lists what it finds, with purpose, category and a plain-language risk level.
* **A preference centre visitors can actually read.** Every detected service shows its purpose, a factual risk level (Helpful / Mixed / Watch out) and its own on/off switch. Per-cookie detail cards (name, duration, description) sit right in the banner.
* **No dead embeds.** Blocked players (YouTube and friends) become a tidy click-to-load facade instead of an empty hole — refusing consent never breaks the page.
* **Geo-aware.** Optional region handling (EU / non-EU / Switzerland) applies the most protective regime by default.
* **Proof, kept.** Consent choices are recorded locally in your database for accountability.
* **26 languages, detected automatically.** The visitor banner follows Polylang/WPML, then the browser, then the site locale — with **RTL** support for Arabic and Hebrew. The admin interface is translatable via standard gettext (`.pot`/`.po`/`.mo`).
* **It dresses to match your site.** The floating badge is monochrome and auto-tinted to your site's dominant colour (detected from your logo, theme.json, Elementor kit and other page builders) — or set every colour and text yourself in the settings screen.
* **You won't feel it.** Under ~1 ms per page in measurements, and no render-blocking third-party requests.

= Compliance scope =

FreeCookie is built with **GDPR, the ePrivacy Directive, the French CNIL guidelines and the Swiss nLPD** in mind. It gives you the technical tools lawful consent needs — prior blocking, granular choice, easy refusal, a proof log, Consent Mode v2. It is a helper, **not legal advice**: activating the plugin does not, on its own, make a site compliant. Your privacy policy, your data-processing choices, special cases such as minors (GDPR Art. 8), and technologies FreeCookie does not block (e.g. server-side tracking, some localStorage/fingerprinting uses) remain your responsibility.

= Free and honest =

FreeCookie is **100% GPL and fully functional for everyone**. Every compliance feature is free — and always will be. An optional **Pro** tier unlocks purely cosmetic extras (extra decorative badge shapes) on an honour system. Pro never gates compliance. Ever.

Source code and issues: https://github.com/stephane-schmidt/freecookie

== Installation ==

1. In your WordPress admin, go to **Plugins → Add New → Upload Plugin** and upload the FreeCookie ZIP, or install it directly from the Plugins directory.
2. Click **Activate**.
3. Open the **FreeCookie** menu that appears in the admin sidebar.
4. Click **Run a scan** to detect the trackers currently used by your site.
5. Review the detected services, adjust categories, colours and texts if you wish, and save.
6. Visit your site in a private/incognito window (logged out, cache cleared) to confirm the banner appears and that trackers are blocked before consent.

That's it — the consent banner and prior blocking are active immediately with sensible defaults (blocking on, Consent Mode v2 on, browser language detection on).

== Frequently Asked Questions ==

= Does FreeCookie really block cookies before consent? =

Yes — that is the whole point. Known third-party scripts and iframes are rewritten at page render so they cannot execute, and are released only when the visitor grants the relevant consent category. Nothing is stored before agreement: prior blocking, exactly as GDPR/ePrivacy require.

= Does the plugin call any external server? =

No, none. FreeCookie makes no third-party calls of its own: no CDN, no remote fonts, no analytics, no licensing server. Everything is processed on your own site.

= Will it slow down my site? =

You won't notice it — measured under ~1 ms per page. And because FreeCookie adds no external requests, it can actually leave your pages faster than a cloud-based consent tool would.

= How does the automatic scan work? =

FreeCookie visits a sample of your own published pages locally, inspects the cookies and third-party resources they load, and lists the services it recognises — each with its purpose and risk level. You choose how many pages to sample. Services appear in the banner only *after* a scan has run.

= Is FreeCookie a certified Google CMP? =

Not currently. FreeCookie implements Consent Mode v2 the privacy-first way (everything denied by default), which keeps ad delivery lawful — but it is not on Google's certified-CMP list. If you run Google ads to EU/EEA visitors and need certified-CMP status for personalised ads, keep this in mind.

= Does it work with page caching and multilingual plugins? =

Yes to both. FreeCookie is cache-friendly (consent is applied client-side), and the banner's language follows Polylang/WPML, then the visitor's browser, then the site locale. GTranslate-style translation setups work too.

= What about Google Funding Choices / AdSense's own consent message? =

If your AdSense account publishes its own "Funding Choices" message, Google injects it at runtime and no site-side CMP can remove it — so you would see two banners. The fix is simple: once FreeCookie is your CMP, disable that message in your AdSense account (Privacy & messaging → GDPR).

= Is it free? How is the project funded? =

Free, forever — the core plugin is GPL and complete on its own. If you want to support development, an optional Pro tier (cosmetic badge shapes, nothing more) is available on an honour system. Compliance is never behind a paywall.

= How do I get and activate a Pro key? =

Purchase FreeCookie Pro from the project store; a license key is generated and emailed to you automatically. Paste it into the FreeCookie Pro section of the settings and you're done. A key only unlocks the decorative extras — it never affects compliance.

== Privacy ==

FreeCookie makes **no external calls and sends nothing to anyone** — no CDN, no remote API, no analytics, no phone-home. All processing happens on your own server.

To support your accountability obligations (GDPR Art. 5(2) and 7(1) — being able to demonstrate that consent was given), FreeCookie keeps a **local consent log** in a dedicated database table (`wp_freecookie_log`). Each entry stores: a random consent ID (UUID, not tied to a user account), the categories granted, the action taken, banner and policy versions, language, region, the page URL, the browser user-agent string, and a **minimised, irreversible IP digest** — the address is first truncated (last octet for IPv4, /64 prefix for IPv6), then hashed with SHA-256 using your site's own salt. The clear IP address is never stored.

Retention: entries are kept until you delete them (no automatic purge in this version) — export or prune the table as your own retention policy requires. On uninstall, the log table is **preserved by default** for auditability; a settings checkbox lets you choose to delete it together with the plugin.

The consent cookie itself (`freecookie_consent`) is a strictly necessary first-party cookie storing the visitor's choice.

== Screenshots ==

1. The consent banner (desktop): detailed categories, unticked boxes, "Reject all" with strict parity against "Accept all".
2. The banner on mobile (375 px): compact centered modal, stacked full-width buttons, scrollable category list.
3. The educational "Understand cookies" panel: three reading levels (Helpful / Mixed / Watch out).
4. The banner in Arabic: full RTL support, one of the 26 shipped languages.

== Changelog ==

= 0.13.8 =
* Modal centered on screen (no longer anchored to the bottom), slightly reduced (560px max). Welcome title aligned to the same margin as the text (some host themes were offsetting the h2), larger by default (22px) and automatically reduced when the site name is long. Action buttons centered, tightened, and more generous on click.

= 0.13.7 =
* Modal scroll hint: when part of the content stays below the fold (~20% hidden on some phones with no indication at all), a gradient fade now appears at the bottom of the banner as long as there is more content to see, and on first display the list does a small "breathing" motion (18px back and forth, gentle) that shows the gesture without explaining it. Discreet by design: the fade disappears at the end of the scroll, the breathing motion plays only once, and it respects `prefers-reduced-motion`.

= 0.13.6 =
* More compact mobile banner: spacing and buttons tightened on narrow screens (reduced padding and gaps), and above all `margin: 0` enforced on the banner buttons — some host themes (Elementor observed) applied their own margins to `<button>` elements, which stretched the Save / Reject All / Accept All stack vertically on phones. The modal stays centered with equal outer margins.

= 0.13.5 =
* More honest visit counter: it was counting not only real browsers but also every cookie-less request (curl, browser-identity bots, uptime monitors, internal cron jobs), which inflated the total significantly. A visit is now only counted if the browser returns a probe set on the previous page load (the "cookie-echo" method), WordPress cron jobs are excluded, and the admin notice now refers to "browsing sessions (local approximation, no tracking)" instead of "visits". No tracking, still 100% local.

= 0.13.4 =
* Responsive banner on mobile and narrow screens: the modal now widens to 86% of the width (capped at 380px) instead of the previous "postage stamp" 70%, stays centered, and scrolls internally if it is too tall. Accept/Reject buttons go full width without overflowing, even when the site theme applies a margin to buttons. Support for foldable cover screens (Honor Magic V2, Galaxy Z Fold ≈ 280–380px): reduced padding, no more content overflow (long cookie and domain names now truncate, the explanatory grid switches to a single column).

= 0.13.3 =
* New badge shape family "Glass" (Pro): 40 translucent frosted-glass-style cookies — transparent body, light reflection, glossy bubbles. Like all shape families, they automatically follow the site's primary color.

= 0.13.2 =
* Banner display fixes (reported in production): the "What are cookies for?" link is now a discreet left-aligned link (previously: a floating pill on the right, straddling the separator); opening the "Understanding cookies" panel now properly replaces the content (no more overlap); buttons no longer show a double border when the site theme adds its own outline (the focus ring now only appears via keyboard navigation).

= 0.13.1 =
* The visitor banner now speaks 26 languages (parity with the admin): added Arabic, Hebrew, Czech, Danish, Greek, Finnish, Hungarian, Indonesian, Japanese, Korean, Norwegian, Polish, Portuguese (Portugal AND Brazil, distinct), Romanian, Russian, Swedish, Turkish, Ukrainian, Simplified AND Traditional Chinese. Region-aware detection for Portuguese and Chinese (non-interchangeable variants).
* Support for right-to-left languages (Arabic, Hebrew): the banner automatically switches to RTL.
* New filter `freecookie_known_first_party`: a theme/site can declare its own internal cookies (preferences, language, etc.) without modifying the plugin.

= 0.13.0 =
* Admin interface is now translatable and shipped translated in 26 languages (the same as SwitchMyBar), plus English: gettext `.pot` / `.po` / `.mo` files in `/languages/` (French source). Until now the admin UI had no language files.
* Reminder: the visitor banner already automatically detects the browser language (the "Language detection" option, enabled by default) and is still served from the plugin's built-in string sets (FC_I18n). Extending the visitor banner to these 26 languages is planned for an upcoming release.
* Non-Latin translations (Arabic, Hebrew, Japanese, Korean, Chinese) are machine-quality and would benefit from a review by a native speaker.

= 0.12.12 =
* New service recognized and blocked by default: "Google Sign-In" (`accounts.google.com/gsi/client`, often injected by Google Site Kit). The "Sign in with Google" button only loads after consent for the Preferences family; the `g_state` cookie is documented in the fact sheets (7 languages).

= 0.12.11 =
* More discreet banner: never more than 70% of screen width or 60% of screen height (it was taking up almost the entire screen on mobile). Accessibility floors preserved: 280px minimum width (usable toggles and buttons), 340px minimum height (mobile landscape). Desktop unchanged (680px).

= 0.12.10 =
* Blocked embed facade: sizing reliability improved. The overlay now follows the player's actual size via ResizeObserver (iframe + parent) instead of a single measurement on load — fixes the small cropped box that appeared when the player was laid out late (tab switch, aspect-ratio, fonts). Embeds hidden on load (background tab) also get their facade, sized once they appear.

= 0.12.9 =
* A blocked player is never a dead end anymore: each neutralized embed (YouTube, Vimeo, etc.) shows a "Load player" facade that accepts ONLY that specific service (granular consent, the banner does not reopen). New per-service allow-list in the consent cookie (`on`), reflected in the panel: a service's checkbox stays usable even when its family is rejected. Texts translated into 7 languages, buttons in the site's colors.

= 0.12.8 =
* Default blocking of Google Funding Choices (Google's advertising consent window): the `fundingchoicesmessages.google.com` script and its inline bootstraps (`googlefc`) are neutralized before consent, like other marketing trackers. Avoids a double banner when a site hardcodes the Funding Choices tag. New "Google Funding Choices" service in the scan, described in 7 languages.

= 0.12.7 =
* Recognition of functional cookies from the self-hosted Wise Chat (pseudonym session, display preferences): classified as strictly necessary, described in 7 languages. The scan honestly shows them as functional, not as trackers.

= 0.12.6 =
* Service descriptions fully translated into all 7 languages: each tracker's purpose (Google Analytics, AdSense, YouTube, Meta, Maps, etc.) now displays in the visitor's language instead of French. The banner is finally 100% consistent on non-French-speaking sites.

= 0.12.5 =
* Scan reliability: an automatic scan that fails to fetch any page (single-process server, loopback blocked by the host, temporary network outage) no longer overwrites a previous valid result — no more incorrect "no trackers detected" shown after a failed scheduled scan. The interactive scan (HTML supplied by the admin's browser) remains the most reliable path.

= 0.12.4 =
* GTranslate support: the functional cookie "googtrans" (chosen display language) is recognized and classified as strictly necessary, described in 7 languages. The language selector remains functional — its scripts are not neutralized before consent, since changing language is an explicit visitor action.

= 0.12.3 =
* Fix for the "cookie floating in the middle of the screen": some themes'/kits' global button styles (min-width or min-height, width 100%) inflated the badge button into a large invisible rectangle — the cookie appeared to float a third of the way down the screen. Badge dimensions are now locked (35×35, immune to kit styles).
* ?fcdebug=1 mode: the badge's actual size is now displayed (this is what revealed the culprit).

= 0.12.2 =
* The badge and banner now automatically re-parent themselves into <body>: some templates (Elementor footers with effects, containers using transform/filter/backdrop-filter/contain) were capturing "fixed" elements and making them drift — no longer possible.
* Enhanced diagnostic mode (?fcdebug=1): red outline on the FreeCookie badge, green marker at the expected corner, list of ancestor elements with trapping effects — a single screenshot is now enough to identify any culprit element.

= 0.12.1 =
* The banner now adapts to screen HEIGHT: on small laptops, typography and spacing become more compact (two breakpoints, 860px and 700px), and if the category list still overflows, it is the one that scrolls — the Accept/Reject/Save buttons always remain visible without scrolling.

= 0.12.0 =
* The scan result is always visible to visitors: when no third-party tracker is detected, the banner proudly displays it ("Good news: no third-party tracker was detected on this site") instead of staying silent — translated into all 7 languages.
* Full transparency: the "Strictly necessary" category now lists its actual cookies in expandable fact sheets — FreeCookie's own consent cookie (with its configured duration) and the internal cookies observed by the scan.

= 0.11.1 =
* Tracker badges now speak the same language as the educational panel: "Useful / Nuanced / To watch" (instead of "Low/Medium/High risk", deemed alarming), in all 7 languages.
* The badge is now clickable: it opens "Understanding cookies" to explain the color code.

= 0.11.0 =
* Online Pro purchase: "Upgrade to Pro" button linking to the store (polar.sh/freeeconcept, $10/year or $45 lifetime) — the license key is generated and sent AUTOMATICALLY by email upon purchase. Paste it into the Pro Key field, that's it: true to the "100% local" principle, no online verification.
* The support notice (past the visit threshold) now points to the store; the "Buy me a coffee" donation remains separate.

= 0.10.3 =
* iOS fix: the badge's shadow is now carried by the internal SVG rather than the floating button — on iOS, a CSS filter applied to a position:fixed element breaks its anchoring (WebKit bug) and was making the badge drift to the middle of the screen.
* Built-in diagnostic mode: add ?fcdebug=1 to the site URL to display live measurements (viewport, zoom, badge position) — useful for mobile support.

= 0.10.2 =
* The badge and banner now stay pinned to the correct corner on Safari iOS even when the page is zoomed (pinch, page zoom, or automatic zoom-out from an overly wide layout): re-pinned to the visual viewport, with no effect when zoom is at 100%.

= 0.10.1 =
* Fix: no more focus ring ("red frame") around the badge after a click — some themes draw a ring on focused buttons. Focus is now only rendered on the badge for keyboard navigation (accessibility preserved), and the theme's rings (outline, box-shadow, border) are neutralized on the badge.

= 0.10.0 =
* New "What are cookies for?" button in the banner: an educational panel explains that a cookie is not inherently bad, with three examples color-coded — useful (green), nuanced (orange), to watch (red). Translated into all 7 languages.
* FreeCookie Pro: two new ABSTRACT (non-cookie) shape families for the badge — "Consent" (approval checkmark + momentum) and "Settings" (gear, sliders, toggle, dial). 240 shapes total across 12 families.

= 0.9.1 =
* Choice of number of pages analyzed per scan: 10 (recommended), 25, 50, or 100 — since trackers are set by the theme and plugins, a representative sample is enough; crawling the entire site adds nothing more.
* The scan now samples all public content types (posts, pages, products, custom content, etc.).
* The scheduled scan now stops cleanly after 20 seconds on limited hosting, keeping what was already found.
* The scan no longer shows the admin's session cookies (wordpress_*, wp-settings-*): these never exist for visitors.

= 0.9.0 =
* FreeCookie Pro (honor-system trust model, no online verification): 9 additional badge shape families — 180 generated shapes — including two COLOR families: "Site swatches" (pills in your site's detected colors) and "Tasty" (natural cookie colors: golden dough, all-chocolate, white-chocolate-dipped, caramel, marbled). Plus Cartoon, Batch, Bitten & crumbs, Glazed, Festive, Graphic duo, and Cookie cutter. Unlocked with the key received after your support; base compliance always remains free and complete.
* Admin: shape picker grouped by family with PRO locks, new "FreeCookie Pro" section (key, status, support link).
* More reliable scan everywhere: the admin's browser now supplies the HTML of pages to analyze itself — no more server self-request, works locally and with hosts that block loopback (server fallback retained).
* Language: when the option is enabled, the visitor's browser language now genuinely takes priority over the site's language.

= 0.8.0 =
* The scan now detects REAL cookies, not just known third-party scripts: server-side Set-Cookie headers + in-browser observation (preview only, no blocking, admin-only) — cookies set via JavaScript such as _ga are now seen, in the same way as commercial scanners.
* An observed third-party service cookie (_ga, _pk_id, etc.) now automatically reveals the corresponding service.
* Database of known internal cookies (WordPress, WooCommerce, consent tools, Polylang/WPML, Jetpack, etc.) translated into all 7 languages; unknown cookies are listed as "Internal site cookie".
* Progress bar during the scan, with a live log of services and cookies found.
* Results: new "Observed cookies" table (server/browser origin, service, category, description); the public [freecookie_cookies] list now includes observed internal cookies ("This site").
* Scanner visits no longer count as visits.

= 0.7.1 =
* Per-cookie fact sheets fully translated into all 7 languages (descriptions + durations, token-neutral cookie database). Previously these texts stayed in French.
* Public [freecookie_cookies] list: column headers added in Spanish, Dutch, and Portuguese.

= 0.7.0 =
* Per-cookie details in the banner: under each tracker, an expandable fact sheet lists its cookies (name, duration, description), automatically translated.

= 0.6.0 =
* Automatic scheduled scan (daily / weekly / manual, selectable in the options): the site is analyzed in the background, first scan launched automatically on activation.
* Scan results (service, category, risk, purpose) now display directly under the scan button in the admin.

= 0.5.0 =
* Main window: detected trackers, their purpose, and their risk level (low/medium/high) are now visible directly, with a toggle per tracker — no more "Customize" step.
* Ethics/compliance: "About" panel disabled by default (strict opt-in), 90-day default consent duration (EDPB/CNIL recommendation), factual risk levels instead of numeric scores, blocking limitations documented, admin warnings (legal, minors, GD).
* Accessibility: focus trap on the banner, correct aria-modal, aria-live announcements, focus restored on close, badge with aria-expanded.
* Security: hardened neutralization pattern (trapped attributes), local requests via wp_safe_remote_get, anti-abuse protection on the logging endpoint, cleaned-up uninstall.
* Admin: support notice can be disabled.

= 0.4.2 =
* Consistent buttons: "Accept All" and "Reject All" now identical (parity), "Customize" now outlined with the same shape; buttons protected from theme styling. Responsive (mobile) improvements: stacked buttons, adjusted margins.

= 0.4.1 =
* Badge: slightly smaller, fades out after 10s of inactivity and becomes fully visible again when the mouse approaches (100px), on hover, or on keyboard focus.

= 0.4.0 =
* Transparent preferences center: each detected tracker now shows its purpose, a privacy-respect score out of 10 (green/orange/red badge), and an individual toggle to enable or disable it.

= 0.3.0 =
* 20 selectable cookie shapes for the badge, with a visual picker in the admin.

= 0.2.0 =
* "About" panel: discreet link on the banner opening a concise panel (social links, "Buy me a coffee" button, mention of the free tier), fully translated. Configurable in the admin.

= 0.1.9 =
* "About" link on the banner: opens a panel with your credentials and social links (automatically translated labels). Configurable in the admin.

= 0.1.8 =
* Adaptive per-page color: in auto mode, the badge takes on the dominant color of the displayed page. A color fixed in the settings still takes priority.

= 0.1.7 =
* Badge: cookie bitten on the side (side bite).

= 0.1.6 =
* New badge: minimal bitten cookie with teeth marks (monochrome, in the site's color).

= 0.1.5 =
* Banner titles protected from theme styling; the preferences title now takes on the dominant color.

= 0.1.4 =
* More reliable color detection: now reads SVG logos, automatic deep detection on open, additional theme sources. Dedicated FreeCookie menu in the admin.

= 0.1.3 =
* Color detection: now also samples the site logo (the most reliable brand signal). Badge slightly smaller.

= 0.1.2 =
* Automatic detection of the site's dominant colors (Elementor kit, theme.json, settings, frequency analysis); clickable swatches in the admin.

= 0.1.1 =
* Badge: neutralizes borders/outlines that some themes apply to buttons, without breaking keyboard focus.

= 0.1.0 =
* Initial development version: default-blocking engine, accessible banner, Consent Mode v2, proof log, FR/EN/DE/IT multilingual support, honor-system counter.

== Upgrade Notice ==

= 0.13.8 =
Centered, better-proportioned consent modal with a scroll hint for content below the fold, plus refined mobile button spacing. Recommended for all sites.
