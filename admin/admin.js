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
