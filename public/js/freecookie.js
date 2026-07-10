/**
 * FreeCookie — moteur front (sans dépendance).
 * Gère l'état de consentement, le déblocage des scripts/iframes neutralisés,
 * Google Consent Mode v2 et le centre de préférences.
 */
(function () {
	'use strict';

	var D = window.FreeCookieData;
	if (!D) { return; }

	var root, banner, badge;
	var usedKeyboard = false; // dernier mode d'interaction : clavier ou pointeur.

	/* ---------- Couleur dominante de la page (mode auto) ---------- */
	function rgbOf(h){ h = h.slice(1); return [parseInt(h.slice(0,2),16), parseInt(h.slice(2,4),16), parseInt(h.slice(4,6),16)]; }
	function toHex(c){
		if (!c) { return ''; }
		if (c.charAt(0) === '#') { var s = c.slice(1); if (s.length === 3) { s = s[0]+s[0]+s[1]+s[1]+s[2]+s[2]; } return '#' + s.toLowerCase(); }
		var m = c.match(/rgba?\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)(?:[,\s\/]+([\d.]+))?/i);
		if (!m) { return ''; }
		if (m[4] !== undefined && parseFloat(m[4]) < 0.35) { return ''; } // quasi transparent
		return '#' + [m[1], m[2], m[3]].map(function (x) { x = (+x).toString(16); return x.length < 2 ? '0' + x : x; }).join('');
	}
	function isBrand(h){ var r = rgbOf(h), mx = Math.max(r[0],r[1],r[2]), mn = Math.min(r[0],r[1],r[2]); if (mx === 0) { return false; } var l = (mx+mn)/2, s = (mx-mn)/mx; return l <= 232 && l >= 18 && s >= 0.22; }
	function shade(h,p){ return '#' + rgbOf(h).map(function (c){ c = Math.round(c*(1-p)); c = c.toString(16); return c.length<2?'0'+c:c; }).join(''); }
	function tint(h,p){ return '#' + rgbOf(h).map(function (c){ c = Math.round(c+(255-c)*p); c = c.toString(16); return c.length<2?'0'+c:c; }).join(''); }
	function readable(h){ var r = rgbOf(h); return (0.299*r[0] + 0.587*r[1] + 0.114*r[2]) / 255 > 0.6 ? '#1a2430' : '#ffffff'; }

	function detectPageColor(){
		var tally = {};
		function add(c, w){
			var h = toHex(c); if (!h || !isBrand(h)) { return; }
			var r = rgbOf(h), q = '#' + r.map(function (x){ x = Math.min(255, Math.round(x/24)*24).toString(16); return x.length<2?'0'+x:x; }).join('');
			tally[q] = (tally[q] || 0) + w;
		}
		var els = document.querySelectorAll('h1,h2,h3,a,button,.btn,.button,[class*="btn"]');
		var n = Math.min(els.length, 300);
		for (var i = 0; i < n; i++){
			var el = els[i];
			if (root && (el === badge || (root.contains && root.contains(el)))) { continue; } // pas nos propres éléments
			var cs = getComputedStyle(el);
			add(cs.color, 1); add(cs.backgroundColor, 2); add(cs.borderTopColor, 1);
		}
		var best = '', bs = 0;
		for (var k in tally){ if (tally[k] > bs){ bs = tally[k]; best = k; } }
		return bs >= 3 ? best : '';
	}
	function applyAccentVars(hex){
		var v = { '--fc-accent': hex, '--fc-accent-deep': shade(hex,0.18), '--fc-accent-text': readable(hex),
			'--fc-badge-solid': hex, '--fc-badge-hole': tint(hex,0.58) };
		[root, badge].forEach(function (el){ if (!el) { return; } for (var k in v){ el.style.setProperty(k, v[k]); } });
	}

	/* ---------- Cookie ---------- */
	function readCookie(name) {
		var m = document.cookie.match('(?:^|; )' + name.replace(/([.*+?^${}()|[\]\\])/g, '\\$1') + '=([^;]*)');
		return m ? decodeURIComponent(m[1]) : null;
	}
	function writeCookie(name, value, days) {
		var d = new Date();
		d.setTime(d.getTime() + days * 864e5);
		var secure = location.protocol === 'https:' ? '; Secure' : '';
		document.cookie = name + '=' + encodeURIComponent(value) +
			'; Expires=' + d.toUTCString() + '; Path=/; SameSite=Lax' + secure;
	}

	function uuid() {
		if (window.crypto && crypto.randomUUID) { return crypto.randomUUID(); }
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
			var r = (Math.random() * 16) | 0, v = c === 'x' ? r : (r & 0x3) | 0x8;
			return v.toString(16);
		});
	}

	/* ---------- État ---------- */
	function optionalKeys() {
		return D.categories.filter(function (c) { return !c.locked; }).map(function (c) { return c.key; });
	}

	function getConsent() {
		var raw = readCookie(D.cookie);
		if (!raw) { return null; }
		try {
			var obj = JSON.parse(raw);
			if (obj.v !== D.version) { return null; } // version du bandeau changée → re-demander
			return obj;
		} catch (e) { return null; }
	}

	function saveConsent(granted, off, action) {
		off = off || [];
		var obj = { v: D.version, c: granted, off: off, id: uuid(), t: Math.floor(Date.now() / 1000) };
		writeCookie(D.cookie, JSON.stringify(obj), D.consentExpiry || 180);
		applyConsentMode(granted);
		unblock(granted, off);
		logConsent(obj, action);
		return obj;
	}

	/* ---------- Consent Mode v2 ---------- */
	function applyConsentMode(granted) {
		if (typeof window.gtag !== 'function') { return; }
		var signals = {
			ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied',
			analytics_storage: 'denied', functionality_storage: 'denied', personalization_storage: 'denied'
		};
		granted.forEach(function (cat) {
			var mapped = D.consentModeMap[cat] || [];
			mapped.forEach(function (s) { signals[s] = 'granted'; });
		});
		window.gtag('consent', 'update', signals);
	}

	/* ---------- Déblocage ---------- */
	function unblock(granted, off) {
		off = off || [];
		function allowed(cat, svc) {
			return granted.indexOf(cat) !== -1 && (!svc || off.indexOf(svc) === -1);
		}
		// Scripts neutralisés.
		var scripts = document.querySelectorAll('script[type="text/plain"][data-fc-category]');
		Array.prototype.forEach.call(scripts, function (node) {
			if (!allowed(node.getAttribute('data-fc-category'), node.getAttribute('data-fc-service'))) { return; }
			var s = document.createElement('script');
			for (var i = 0; i < node.attributes.length; i++) {
				var a = node.attributes[i];
				if (a.name === 'type' || a.name === 'data-fc-category' || a.name === 'data-fc-service') { continue; }
				if (a.name === 'data-fc-src') { s.setAttribute('src', a.value); continue; }
				s.setAttribute(a.name, a.value);
			}
			if (node.textContent) { s.textContent = node.textContent; }
			node.parentNode.replaceChild(s, node);
		});
		// Iframes neutralisées.
		var frames = document.querySelectorAll('iframe[data-fc-src][data-fc-category]');
		Array.prototype.forEach.call(frames, function (f) {
			if (!allowed(f.getAttribute('data-fc-category'), f.getAttribute('data-fc-service'))) { return; }
			f.setAttribute('src', f.getAttribute('data-fc-src'));
			f.removeAttribute('data-fc-src');
			f.classList.remove('fc-blocked-embed');
		});
	}

	/* ---------- Journal (REST) ---------- */
	function logConsent(obj, action) {
		if (!D.restUrl) { return; }
		try {
			fetch(D.restUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': D.nonce },
				body: JSON.stringify({
					consent_id: obj.id, categories: obj.c.join(','), action: action || 'save',
					version: D.version, lang: D.lang, region: D.region
				}),
				keepalive: true
			});
		} catch (e) { /* silencieux : le cookie fait foi côté visiteur */ }
	}

	/* ---------- UI ---------- */
	function show(el) { if (el) { el.hidden = false; } }
	function hide(el) { if (el) { el.hidden = true; } }

	function aboutEl() { return root.querySelector('[data-fc-about]'); }
	function eduEl() { return root.querySelector('[data-fc-edu]'); }
	function announce(msg) { var l = root.querySelector('[data-fc-live]'); if (l) { l.textContent = msg || ''; } }

	// Reflète l'état courant dans les cases (catégories + services).
	function reflect() {
		var c = getConsent();
		var granted = c ? c.c : [];
		var off = c ? (c.off || []) : [];
		Array.prototype.forEach.call(root.querySelectorAll('.fc-toggle'), function (t) {
			t.checked = granted.indexOf(t.getAttribute('data-fc-cat')) !== -1;
		});
		Array.prototype.forEach.call(root.querySelectorAll('.fc-svc-toggle'), function (t) {
			var on = granted.indexOf(t.getAttribute('data-fc-cat')) !== -1;
			t.checked = on && off.indexOf(t.getAttribute('data-fc-svc')) === -1;
			t.disabled = !on;
		});
	}

	// Piège de focus : tant que le bandeau est ouvert, Tab reste dedans.
	function trapTab(e) {
		if (e.key !== 'Tab' || !root || root.hidden) { return; }
		var all = root.querySelectorAll('button, a[href], input:not([disabled])');
		var list = Array.prototype.filter.call(all, function (el) { return el.offsetParent !== null; });
		if (!list.length) { return; }
		var first = list[0], last = list[list.length - 1];
		if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && (document.activeElement === last || !root.contains(document.activeElement))) { e.preventDefault(); first.focus(); }
	}

	function openBanner() {
		show(root); hide(aboutEl()); hide(eduEl());
		banner.setAttribute('data-fc-state', 'banner');
		reflect();
		if (badge) { badge.setAttribute('aria-expanded', 'true'); }
		announce(D.strings.prefs_title || '');
		var first = root.querySelector('button, input:not([disabled])');
		if (first) { first.focus(); }
	}
	function openAbout() {
		show(root); show(aboutEl()); hide(eduEl());
		banner.setAttribute('data-fc-state', 'about');
		var t = root.querySelector('.fc-about__title');
		announce(t ? t.textContent : '');
	}
	function openEdu() {
		show(root); show(eduEl()); hide(aboutEl());
		banner.setAttribute('data-fc-state', 'edu');
		var t = root.querySelector('.fc-edu__title');
		announce(t ? t.textContent : '');
		var back = eduEl() ? eduEl().querySelector('[data-fc="edu-back"]') : null;
		if (back) { back.focus(); }
	}
	function closeAll() {
		hide(root); show(badge);
		if (badge) {
			badge.setAttribute('aria-expanded', 'false');
			// On ne rend le focus au badge qu'aux utilisateurs CLAVIER : à la
			// souris, un focus programmatique fait apparaître l'anneau de focus
			// du thème (ou le nôtre) autour du badge — le fameux « cadre rouge ».
			if (usedKeyboard) {
				badge.focus();
			} else if (document.activeElement && document.activeElement.blur) {
				document.activeElement.blur();
			}
		}
	}

	function readToggles() {
		var cats = [];
		Array.prototype.forEach.call(document.querySelectorAll('.fc-toggle'), function (t) {
			if (t.checked) { cats.push(t.getAttribute('data-fc-cat')); }
		});
		var off = [];
		Array.prototype.forEach.call(document.querySelectorAll('.fc-svc-toggle'), function (t) {
			if (!t.checked) { off.push(t.getAttribute('data-fc-svc')); }
		});
		return { cats: cats, off: off };
	}

	function onClick(action) {
		if (action === 'accept') { saveConsent(optionalKeys(), [], 'accept'); closeAll(); }
		else if (action === 'reject') { saveConsent([], [], 'reject'); closeAll(); }
		else if (action === 'save') { var t = readToggles(); saveConsent(t.cats, t.off, 'save'); closeAll(); }
		else if (action === 'customize') { openBanner(); } // rétro-compat : tout est déjà visible.
		else if (action === 'about') { openAbout(); }
		else if (action === 'about-back') { openBanner(); }
		else if (action === 'edu') { openEdu(); }
		else if (action === 'edu-back') { openBanner(); }
	}

	/* ---------- Épinglage au viewport VISUEL (Safari iOS zoomé) ----------
	   Sur iOS, quand la page est zoomée (pincement, zoom de page, ou dézoom
	   automatique d'une mise en page trop large), les éléments position:fixed
	   restent accrochés au viewport de MISE EN PAGE et semblent dériver au
	   milieu de l'écran. On les ré-épingle au viewport visuel — et on remet
	   les styles de la feuille dès que le zoom revient à 1 (aucun effet
	   ailleurs). Technique standard des widgets flottants. */
	function initViewportGlue() {
		var vv = window.visualViewport;
		if (!vv) { return; }
		var raf = false;
		function apply() {
			raf = false;
			// Uniquement le scale et les décalages : la largeur est trompeuse
			// (une simple barre de défilement desktop réduit vv.width).
			var zoomed = ( vv.scale && Math.abs(vv.scale - 1) > 0.02 ) || vv.offsetLeft > 1 || vv.offsetTop > 1;
			if (!zoomed) {
				if (badge) { badge.style.left = ''; badge.style.bottom = ''; }
				if (banner) { banner.style.left = ''; banner.style.bottom = ''; banner.style.width = ''; banner.style.transform = ''; }
				return;
			}
			var s = vv.scale || 1;
			var m = ( vv.width <= 560 ? 12 : 20 ) / s;
			var bottomBase = window.innerHeight - vv.offsetTop - vv.height;
			if (badge) {
				badge.style.left = ( vv.offsetLeft + m ) + 'px';
				badge.style.bottom = ( bottomBase + m ) + 'px';
			}
			if (banner) {
				var bm = 8 / s;
				banner.style.left = ( vv.offsetLeft + bm ) + 'px';
				banner.style.bottom = ( bottomBase + bm ) + 'px';
				banner.style.width = ( vv.width - 2 * bm ) + 'px';
				banner.style.transform = 'none';
			}
		}
		function onchange() { if (!raf) { raf = true; requestAnimationFrame(apply); } }
		vv.addEventListener('resize', onchange);
		vv.addEventListener('scroll', onchange);
		window.addEventListener('scroll', onchange, { passive: true });
		apply();
	}

	/* ---------- Mode diagnostic (?fcdebug=1) : mesures en direct à l'écran ---------- */
	function initDebugOverlay() {
		if (!/[?&]fcdebug=1/.test(window.location.search)) { return; }
		var p = document.createElement('div');
		p.style.cssText = 'position:fixed;top:8px;left:8px;right:8px;z-index:2147483646;background:rgba(0,0,0,.82);color:#7CFC9A;font:11px/1.5 monospace;padding:8px 10px;border-radius:8px;pointer-events:none;white-space:pre-wrap;';
		document.body.appendChild(p);
		function tick() {
			var vv = window.visualViewport;
			var b = badge ? badge.getBoundingClientRect() : null;
			var cs = badge ? getComputedStyle(badge) : null;
			p.textContent = 'FreeCookie debug ' + (D.version || '')
				+ '\ninner: ' + window.innerWidth + 'x' + window.innerHeight + '  scrollW: ' + document.documentElement.scrollWidth
				+ (vv ? '\nvv: ' + Math.round(vv.width) + 'x' + Math.round(vv.height) + '  scale=' + (vv.scale || 1).toFixed(3) + '  off=' + Math.round(vv.offsetLeft) + ',' + Math.round(vv.offsetTop) : '\nvv: absent')
				+ (b ? '\nbadge: left=' + Math.round(b.left) + '  bottom=' + Math.round(window.innerHeight - b.bottom) + '  (' + Math.round(b.left / window.innerWidth * 100) + '% gauche)  visible=' + ( ! badge.hidden ) : '\nbadge: absent')
				+ (cs ? '\nposition=' + cs.position + '  filter=' + ( 'none' === cs.filter ? 'none' : 'PRESENT' ) + '  glue L/B=' + (badge.style.left || '-') + '/' + (badge.style.bottom || '-') : '')
				+ '\nUA: ' + navigator.userAgent.slice(0, 90);
		}
		tick();
		window.setInterval(tick, 600);
	}

	/* ---------- Badge : estompé après inactivité, réveil à l'approche ---------- */
	function initBadgeProximity() {
		if (!badge) { return; }
		var mx = -9999, my = -9999, wasNear = false, timer, raf = false;
		function scheduleDim() { clearTimeout(timer); timer = setTimeout(function () { badge.classList.add('fc-badge--dim'); }, 10000); }
		function isNear() {
			var r = badge.getBoundingClientRect();
			if (!r.width) { return false; }
			var dx = Math.max(r.left - mx, 0, mx - r.right);
			var dy = Math.max(r.top - my, 0, my - r.bottom);
			return dx * dx + dy * dy <= 100 * 100;
		}
		function update() {
			raf = false;
			var near = isNear();
			if (near && !wasNear) { wasNear = true; clearTimeout(timer); badge.classList.remove('fc-badge--dim'); badge.classList.add('fc-badge--near'); }
			else if (!near && wasNear) { wasNear = false; badge.classList.remove('fc-badge--near'); scheduleDim(); }
		}
		document.addEventListener('mousemove', function (e) { mx = e.clientX; my = e.clientY; if (raf) { return; } raf = true; requestAnimationFrame(update); }, { passive: true });
		document.addEventListener('touchstart', function () { badge.classList.remove('fc-badge--dim'); badge.classList.add('fc-badge--near'); }, { passive: true });
		scheduleDim();
	}

	/* ---------- Init ---------- */
	function init() {
		root = document.getElementById('freecookie-root');
		badge = document.getElementById('freecookie-badge');
		if (!root) { return; }
		banner = document.getElementById('freecookie-banner');

		// Mode auto : le badge/bannière adopte la couleur dominante de CETTE page.
		if ( D.autoColor ) {
			var pageColor = detectPageColor();
			if ( pageColor ) { applyAccentVars( pageColor ); }
		}

		root.addEventListener('click', function (e) {
			var b = e.target.closest('[data-fc]');
			if (b) { e.preventDefault(); onClick(b.getAttribute('data-fc')); }
		});
		if (badge) { badge.addEventListener('click', openBanner); }
		document.addEventListener('keydown', trapTab, true);
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Tab' || e.key === 'Enter' || e.key === ' ') { usedKeyboard = true; }
		}, true);
		document.addEventListener('pointerdown', function () { usedKeyboard = false; }, true);
		initBadgeProximity();
		initViewportGlue();
		initDebugOverlay();

		// Synchro : (dé)cocher une catégorie (dé)coche et (dés)active ses services.
		root.addEventListener('change', function (e) {
			var t = e.target;
			if (t && t.classList && t.classList.contains('fc-toggle')) {
				var cat = t.getAttribute('data-fc-cat');
				Array.prototype.forEach.call(root.querySelectorAll('.fc-svc-toggle[data-fc-cat="' + cat + '"]'), function (s) {
					s.disabled = !t.checked;
					s.checked = t.checked;
				});
			}
		});

		var consent = getConsent();
		if (consent) {
			// Visiteur déjà décidé : on applique et on montre juste le badge.
			applyConsentMode(consent.c);
			unblock(consent.c, consent.off || []);
			show(badge);
		} else {
			openBanner();
		}

		// API publique.
		window.FreeCookie = {
			open: openBanner,
			accept: function () { onClick('accept'); },
			reject: function () { onClick('reject'); },
			get: function () { var c = getConsent(); return c ? c.c : []; }
		};
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
