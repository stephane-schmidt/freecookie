<?php
/**
 * Palette : dérive toutes les teintes de la bannière à partir des réglages,
 * et devine la couleur principale du site quand l'accent n'est pas défini.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Colors {

	/**
	 * Valide un hex (#rgb ou #rrggbb) ; renvoie '' si invalide.
	 *
	 * @param string $hex Couleur.
	 * @return string
	 */
	public static function sanitize( $hex ) {
		$hex = trim( (string) $hex );
		if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $hex ) ) {
			return strtolower( $hex );
		}
		return '';
	}

	/**
	 * Hex → [r,g,b].
	 *
	 * @param string $hex Couleur validée.
	 * @return int[]
	 */
	protected static function rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		return array(
			hexdec( substr( $hex, 0, 2 ) ),
			hexdec( substr( $hex, 2, 2 ) ),
			hexdec( substr( $hex, 4, 2 ) ),
		);
	}

	/**
	 * [r,g,b] → hex.
	 *
	 * @param float[] $rgb Composantes.
	 * @return string
	 */
	protected static function hex( $rgb ) {
		$out = '#';
		foreach ( $rgb as $c ) {
			$c    = max( 0, min( 255, (int) round( $c ) ) );
			$out .= str_pad( dechex( $c ), 2, '0', STR_PAD_LEFT );
		}
		return $out;
	}

	/**
	 * Assombrit vers le noir (0 = inchangé, 1 = noir).
	 *
	 * @param string $hex Couleur.
	 * @param float  $p   Proportion.
	 * @return string
	 */
	public static function shade( $hex, $p ) {
		$rgb = self::rgb( $hex );
		foreach ( $rgb as &$c ) {
			$c = $c * ( 1 - $p );
		}
		return self::hex( $rgb );
	}

	/**
	 * Éclaircit vers le blanc (0 = inchangé, 1 = blanc).
	 *
	 * @param string $hex Couleur.
	 * @param float  $p   Proportion.
	 * @return string
	 */
	public static function tint( $hex, $p ) {
		$rgb = self::rgb( $hex );
		foreach ( $rgb as &$c ) {
			$c = $c + ( 255 - $c ) * $p;
		}
		return self::hex( $rgb );
	}

	/**
	 * Mélange deux couleurs ($p de $b).
	 *
	 * @param string $a Couleur A.
	 * @param string $b Couleur B.
	 * @param float  $p Proportion de B.
	 * @return string
	 */
	public static function mix( $a, $b, $p ) {
		$ra = self::rgb( $a );
		$rb = self::rgb( $b );
		$out = array();
		for ( $i = 0; $i < 3; $i++ ) {
			$out[ $i ] = $ra[ $i ] * ( 1 - $p ) + $rb[ $i ] * $p;
		}
		return self::hex( $out );
	}

	/**
	 * Couleur de texte lisible (noir ou blanc) sur un fond donné.
	 *
	 * @param string $hex Fond.
	 * @return string
	 */
	public static function readable_on( $hex ) {
		list( $r, $g, $b ) = self::rgb( $hex );
		// Luminance perçue.
		$lum = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
		return $lum > 0.6 ? '#1a2430' : '#ffffff';
	}

	/**
	 * Accent par défaut : couleur principale du site si détectable, sinon teal.
	 *
	 * @return string
	 */
	public static function default_accent() {
		$filtered = self::sanitize( (string) apply_filters( 'freecookie_default_accent', '' ) );
		if ( '' !== $filtered ) {
			return $filtered;
		}
		// Couleur dominante détectée sur le site (Elementor, theme.json, mods, fréquence).
		if ( class_exists( 'FC_Color_Detector' ) ) {
			$detected = self::sanitize( FC_Color_Detector::primary() );
			if ( '' !== $detected ) {
				return $detected;
			}
		}
		return '#1c7a6b';
	}

	/**
	 * Construit toutes les variables CSS à partir des réglages.
	 *
	 * @param array $settings Réglages du plugin.
	 * @return array<string,string> nom-de-variable => valeur
	 */
	public static function css_vars( array $settings ) {
		$c = isset( $settings['colors'] ) && is_array( $settings['colors'] ) ? $settings['colors'] : array();

		$accent = self::sanitize( $c['accent'] ?? '' );
		if ( '' === $accent ) {
			$accent = self::default_accent();
		}
		$bg   = self::sanitize( $c['bg'] ?? '' ) ?: '#ffffff';
		$text = self::sanitize( $c['text'] ?? '' ) ?: '#1a2430';

		$accent_text = self::sanitize( $c['accent_text'] ?? '' ) ?: self::readable_on( $accent );
		$sec_bg      = self::sanitize( $c['secondary_bg'] ?? '' ) ?: self::mix( $bg, $text, 0.06 );
		$sec_text    = self::sanitize( $c['secondary_text'] ?? '' ) ?: $text;
		$badge_solid = self::sanitize( $c['badge'] ?? '' ) ?: $accent;

		return array(
			'--fc-accent'         => $accent,
			'--fc-accent-deep'    => self::shade( $accent, 0.18 ),
			'--fc-accent-text'    => $accent_text,
			'--fc-bg'             => $bg,
			'--fc-text'           => $text,
			'--fc-muted'          => self::mix( $text, $bg, 0.38 ),
			'--fc-border'         => self::mix( $text, $bg, 0.86 ),
			'--fc-secondary-bg'   => $sec_bg,
			'--fc-secondary-text' => $sec_text,
			'--fc-badge-solid'    => $badge_solid,
			'--fc-badge-hole'     => self::tint( $badge_solid, 0.58 ),
		);
	}

	/**
	 * Bloc CSS inline scopé à la bannière et au badge.
	 *
	 * @param array $settings Réglages.
	 * @return string CSS.
	 */
	public static function inline_css( array $settings ) {
		$vars = self::css_vars( $settings );
		$decl = '';
		foreach ( $vars as $name => $value ) {
			$decl .= $name . ':' . $value . ';';
		}
		return '#freecookie-root,#freecookie-badge{' . $decl . '}';
	}
}
