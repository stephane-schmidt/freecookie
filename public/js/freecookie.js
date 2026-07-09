/**
 * FreeCookie — moteur front (sans dépendance).
 * Gère l'état de consentement, le déblocage des scripts/iframes neutralisés,
 * Google Consent Mode v2 et le centre de préférences.
 */
(function () {
	'use strict';

	var D = window.FreeCookieData;
	if (!D) { return; }

	var root, banner, badge, panel;

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

	function saveConsent(granted, action) {
		var obj = { v: D.version, c: granted, id: uuid(), t: Math.floor(Date.now() / 1000) };
		writeCookie(D.cookie, JSON.stringify(obj), D.consentExpiry || 180);
		applyConsentMode(granted);
		unblock(granted);
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
	function unblock(granted) {
		// Scripts neutralisés.
		var scripts = document.querySelectorAll('script[type="text/plain"][data-fc-category]');
		Array.prototype.forEach.call(scripts, function (node) {
			if (granted.indexOf(node.getAttribute('data-fc-category')) === -1) { return; }
			var s = document.createElement('script');
			for (var i = 0; i < node.attributes.length; i++) {
				var a = node.attributes[i];
				if (a.name === 'type' || a.name === 'data-fc-category') { continue; }
				if (a.name === 'data-fc-src') { s.setAttribute('src', a.value); continue; }
				s.setAttribute(a.name, a.value);
			}
			if (node.textContent) { s.textContent = node.textContent; }
			node.parentNode.replaceChild(s, node);
		});
		// Iframes neutralisées.
		var frames = document.querySelectorAll('iframe[data-fc-src][data-fc-category]');
		Array.prototype.forEach.call(frames, function (f) {
			if (granted.indexOf(f.getAttribute('data-fc-category')) === -1) { return; }
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

	function openBanner() { show(root); banner.setAttribute('data-fc-state', 'banner'); hide(panel); }
	function openPanel() {
		show(root); show(panel); banner.setAttribute('data-fc-state', 'prefs');
		// Reflète l'état courant dans les cases.
		var c = getConsent();
		var granted = c ? c.c : [];
		Array.prototype.forEach.call(document.querySelectorAll('.fc-toggle'), function (t) {
			t.checked = granted.indexOf(t.getAttribute('data-fc-cat')) !== -1;
		});
	}
	function closeAll() { hide(root); show(badge); }

	function readToggles() {
		var out = [];
		Array.prototype.forEach.call(document.querySelectorAll('.fc-toggle'), function (t) {
			if (t.checked) { out.push(t.getAttribute('data-fc-cat')); }
		});
		return out;
	}

	function onClick(action) {
		if (action === 'accept') { saveConsent(optionalKeys(), 'accept'); closeAll(); }
		else if (action === 'reject') { saveConsent([], 'reject'); closeAll(); }
		else if (action === 'save') { saveConsent(readToggles(), 'save'); closeAll(); }
		else if (action === 'customize') { openPanel(); }
	}

	/* ---------- Init ---------- */
	function init() {
		root = document.getElementById('freecookie-root');
		badge = document.getElementById('freecookie-badge');
		if (!root) { return; }
		banner = document.getElementById('freecookie-banner');
		panel = root.querySelector('[data-fc-panel]');

		root.addEventListener('click', function (e) {
			var b = e.target.closest('[data-fc]');
			if (b) { e.preventDefault(); onClick(b.getAttribute('data-fc')); }
		});
		if (badge) { badge.addEventListener('click', openPanel); }

		var consent = getConsent();
		if (consent) {
			// Visiteur déjà décidé : on applique et on montre juste le badge.
			applyConsentMode(consent.c);
			unblock(consent.c);
			show(badge);
		} else {
			openBanner();
		}

		// API publique.
		window.FreeCookie = {
			open: openPanel,
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
