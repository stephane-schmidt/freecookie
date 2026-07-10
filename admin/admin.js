/* FreeCookie — administration : sélecteurs de couleur + pastilles détectées. */
( function ( $ ) {
	$( function () {
		$( '.fc-color-field' ).wpColorPicker();

		// Cliquer une couleur détectée l'applique au champ « Couleur principale ».
		$( '.fc-swatch' ).on( 'click', function () {
			var color = $( this ).data( 'color' );
			var $field = $( '#fc-accent' );
			if ( $field.length ) {
				$field.val( color ).trigger( 'change' );
				if ( $field.hasClass( 'wp-color-picker' ) ) {
					$field.wpColorPicker( 'color', color );
				}
			}
		} );
	} );
} )( jQuery );

/* FreeCookie — scan interactif : progression + journal en direct. */
( function () {
	if ( typeof fcScan === 'undefined' ) {
		return;
	}

	var btn = document.getElementById( 'fc-scan-btn' );
	if ( ! btn ) {
		return;
	}

	var ui     = document.getElementById( 'fc-scan-ui' );
	var bar    = document.getElementById( 'fc-scan-bar' );
	var track  = document.getElementById( 'fc-scan-track' );
	var status = document.getElementById( 'fc-scan-status' );
	var log    = document.getElementById( 'fc-scan-log' );

	function fmt( tpl ) {
		var args = Array.prototype.slice.call( arguments, 1 ), i = 0;
		return tpl.replace( /%\d*\$?[ds]/g, function () {
			return args[ i++ ];
		} );
	}

	function setProgress( pct ) {
		bar.style.width = pct + '%';
		track.setAttribute( 'aria-valuenow', String( Math.round( pct ) ) );
	}

	function addLog( text, found ) {
		var li = document.createElement( 'li' );
		li.textContent = text;
		if ( found ) {
			li.className = 'fc-found';
		}
		log.appendChild( li );
		log.scrollTop = log.scrollHeight;
	}

	function post( path, data ) {
		var body = new URLSearchParams( data || {} );
		return fetch( fcScan.rest + path, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'X-WP-Nonce': fcScan.nonce, 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString()
		} ).then( function ( r ) {
			if ( ! r.ok ) {
				throw new Error( 'HTTP ' + r.status );
			}
			return r.json();
		} );
	}

	/* Le NAVIGATEUR va chercher la page (même origine, sans cookies) : le site
	   n'a jamais à s'appeler lui-même — indispensable sur serveur mono-processus
	   (wp server local) et sur les hébergements qui bloquent le loopback. */
	function fetchPageHtml( url ) {
		return fetch( url, { credentials: 'omit', cache: 'no-store' } ).then( function ( r ) {
			if ( ! r.ok ) {
				throw new Error( 'HTTP ' + r.status );
			}
			return r.text();
		} );
	}

	function logFindings( data ) {
		( data.services || [] ).forEach( function ( s ) {
			addLog( fmt( fcScan.strings.service, s.label ), true );
		} );
		( data.cookies || [] ).forEach( function ( c ) {
			var tpl   = 'js' === c.src ? fcScan.strings.cookieJs : fcScan.strings.cookieHttp;
			var extra = c.service ? ' — ' + c.service : ( c.desc ? ' — ' + c.desc : '' );
			addLog( fmt( tpl, c.name ) + extra, true );
		} );
	}

	/* Iframe cachée : charge le site SANS blocage (admin + nonce), attend que les
	   scripts posent leurs cookies, puis lit document.cookie (même origine). */
	function sniffBrowserCookies() {
		return new Promise( function ( resolve ) {
			var iframe = document.createElement( 'iframe' );
			iframe.style.cssText = 'position:absolute;width:2px;height:2px;left:-9999px';
			iframe.src = fcScan.sniffUrl;
			var done = false;
			function finish() {
				if ( done ) {
					return;
				}
				done = true;
				var names = [];
				try {
					names = iframe.contentDocument.cookie.split( ';' ).map( function ( c ) {
						return c.split( '=' )[ 0 ].trim();
					} ).filter( Boolean );
				} catch ( e ) { /* iframe inaccessible : on continue sans */ }
				iframe.remove();
				resolve( names );
			}
			iframe.addEventListener( 'load', function () {
				window.setTimeout( finish, 4000 ); // laisser les traceurs s'exécuter.
			} );
			window.setTimeout( finish, 15000 ); // garde-fou.
			document.body.appendChild( iframe );
		} );
	}

	btn.addEventListener( 'click', function () {
		btn.disabled = true;
		ui.hidden    = false;
		log.textContent = '';
		setProgress( 0 );

		var urls = [];
		post( 'start' ).then( function ( data ) {
			urls = data.urls || [];
			var chain = Promise.resolve();
			urls.forEach( function ( url, idx ) {
				chain = chain.then( function () {
					status.textContent = fmt( fcScan.strings.scanning, idx + 1, urls.length );
					return fetchPageHtml( url ).then( function ( html ) {
						// Sonde Set-Cookie serveur sur la 1re page seulement.
						return post( 'step', { url: url, html: html, probe: 0 === idx ? '1' : '' } );
					} ).catch( function () {
						// Repli : le serveur ira chercher la page lui-même.
						return post( 'step', { url: url } );
					} ).then( function ( r ) {
						logFindings( r );
						setProgress( ( ( idx + 1 ) / urls.length ) * 80 );
					} );
				} );
			} );
			return chain;
		} ).then( function () {
			status.textContent = fcScan.strings.sniffing;
			setProgress( 85 );
			return sniffBrowserCookies();
		} ).then( function ( names ) {
			if ( names.length ) {
				return post( 'client-cookies', { names: names.join( ',' ) } ).then( logFindings );
			}
		} ).then( function () {
			status.textContent = fcScan.strings.finishing;
			setProgress( 95 );
			return post( 'finish' );
		} ).then( function ( r ) {
			setProgress( 100 );
			status.textContent = fmt( fcScan.strings.done, r.scanned, r.services, r.cookies );
			window.setTimeout( function () {
				window.location.reload();
			}, 1600 );
		} ).catch( function () {
			status.textContent = fcScan.strings.error;
			btn.disabled = false;
		} );
	} );
} )();
