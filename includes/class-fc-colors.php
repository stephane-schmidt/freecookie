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

class Freecookie_Colors {

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
		if ( class_exists( 'Freecookie_Color_Detector' ) ) {
			$detected = self::sanitize( Freecookie_Color_Detector::primary() );
			if ( '' !== $detected ) {
				return $detected;
			}
		}
		return '#1c7a6b';
	}

	/** Thèmes reconnus : auto (navigateur), light, dark. */
	const THEMES = array( 'auto', 'light', 'dark' );

	/**
	 * Palette de la maquette du 04/09 : monochrome, encre sur papier.
	 * Le jour : carte blanche, encre presque noire. La nuit : carte presque
	 * noire (jamais #000, elle doit se détacher d'un fond de page sombre),
	 * encre blanc cassé.
	 */
	const LIGHT_BG   = '#ffffff';
	const LIGHT_TEXT = '#1a1a1a';
	const DARK_BG    = '#1c1c1a';
	const DARK_TEXT  = '#f2f1ec';

	/**
	 * Thème réglé (valeur inconnue = auto).
	 *
	 * @param array $settings Réglages.
	 * @return string auto|light|dark
	 */
	public static function theme( array $settings ) {
		$t = isset( $settings['theme'] ) ? (string) $settings['theme'] : 'auto';
		return in_array( $t, self::THEMES, true ) ? $t : 'auto';
	}

	/**
	 * Luminance relative WCAG 2.x (0 = noir, 1 = blanc).
	 *
	 * @param string $hex Couleur.
	 * @return float
	 */
	public static function luminance( $hex ) {
		$out = array();
		foreach ( self::rgb( $hex ) as $c ) {
			$c     = $c / 255;
			$out[] = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		}
		return 0.2126 * $out[0] + 0.7152 * $out[1] + 0.0722 * $out[2];
	}

	/**
	 * Ratio de contraste WCAG entre deux couleurs (1 à 21).
	 *
	 * @param string $a Couleur.
	 * @param string $b Couleur.
	 * @return float
	 */
	public static function contrast( $a, $b ) {
		$x = self::luminance( $a );
		$y = self::luminance( $b );
		return ( max( $x, $y ) + 0.05 ) / ( min( $x, $y ) + 0.05 );
	}

	/**
	 * Pousse une couleur vers $towards, par pas de 7 %, jusqu'à ce qu'elle tienne
	 * $min:1 sur $bg. Un accent de marque sombre posé sur la carte de nuit se
	 * fondrait dans la carte : on le remonte au lieu de le laisser disparaître.
	 *
	 * @param string $hex     Couleur de départ.
	 * @param string $bg      Fond sur lequel elle doit tenir.
	 * @param string $towards Couleur vers laquelle pousser (l'encre du schéma).
	 * @param float  $min     Ratio minimal.
	 * @return string
	 */
	public static function lift( $hex, $bg, $towards, $min ) {
		for ( $i = 0; $i < 16 && self::contrast( $hex, $bg ) < $min; $i++ ) {
			$hex = self::mix( $hex, $towards, 0.07 );
		}
		return $hex;
	}

	/**
	 * Construit toutes les variables CSS à partir des réglages, pour UN schéma.
	 *
	 * @param array  $settings Réglages du plugin.
	 * @param string $scheme   light | dark.
	 * @return array<string,string> nom-de-variable => valeur
	 */
	public static function css_vars( array $settings, $scheme = 'light' ) {
		$c    = isset( $settings['colors'] ) && is_array( $settings['colors'] ) ? $settings['colors'] : array();
		$cd   = isset( $settings['colors_dark'] ) && is_array( $settings['colors_dark'] ) ? $settings['colors_dark'] : array();
		$dark = ( 'dark' === $scheme );

		$accent_set = self::sanitize( $c['accent'] ?? '' );
		$accent     = '' !== $accent_set ? $accent_set : self::default_accent();

		if ( $dark ) {
			$bg   = self::sanitize( $cd['bg'] ?? '' ) ?: self::DARK_BG;
			$text = self::sanitize( $cd['text'] ?? '' ) ?: self::DARK_TEXT;
		} else {
			$bg   = self::sanitize( $c['bg'] ?? '' ) ?: self::LIGHT_BG;
			$text = self::sanitize( $c['text'] ?? '' ) ?: self::LIGHT_TEXT;
		}
		// Un accent qui se fond dans la carte (seuil non-texte 3:1) est remonté vers l'encre.
		$accent      = self::lift( $accent, $bg, $text, 3 );
		$accent_text = self::sanitize( $c['accent_text'] ?? '' ) ?: self::readable_on( $accent );

		// Boutons secondaires (Personnaliser, Refuser) : aplat doux dérivé de la carte.
		// Les couleurs saisies pour le jour ne s'appliquent pas à la nuit : elles ont été
		// choisies sur une carte claire, on dérive.
		$sec_bg   = ( ! $dark && self::sanitize( $c['secondary_bg'] ?? '' ) ) ?: self::mix( $bg, $text, 0.10 );
		$sec_text = ( ! $dark && self::sanitize( $c['secondary_text'] ?? '' ) ) ?: $text;

		// Bouton principal (Accepter) : la maquette est MONOCHROME, encre sur papier.
		// Un accent choisi à la main dans les options prend sa place.
		if ( '' !== $accent_set ) {
			$prim_bg   = $accent;
			$prim_text = $accent_text;
		} else {
			$prim_bg   = $text;
			$prim_text = $bg;
		}
		$badge_solid = self::sanitize( $c['badge'] ?? '' ) ?: $accent;

		// Palette multicolore : les couleurs détectées du site (jusqu'à 4),
		// complétées par des dérivés de l'accent — pour les formes « Pastilles ».
		$palette = class_exists( 'Freecookie_Color_Detector' ) ? Freecookie_Color_Detector::palette() : array();
		$multi   = array();
		foreach ( $palette as $p ) {
			$p = self::sanitize( $p );
			if ( '' !== $p && count( $multi ) < 4 ) {
				$multi[] = $p;
			}
		}
		$fill = array( $accent, self::shade( $accent, 0.3 ), self::tint( $accent, 0.4 ), self::mix( $accent, '#ffffff', 0.65 ) );
		for ( $i = count( $multi ); $i < 4; $i++ ) {
			$multi[] = $fill[ $i ];
		}

		return array(
			'--fc-c1'             => $multi[0],
			'--fc-c2'             => $multi[1],
			'--fc-c3'             => $multi[2],
			'--fc-c4'             => $multi[3],
			'--fc-accent'         => $accent,
			'--fc-accent-deep'    => $dark ? self::tint( $accent, 0.12 ) : self::shade( $accent, 0.18 ),
			'--fc-accent-text'    => $accent_text,
			'--fc-bg'             => $bg,
			'--fc-text'           => $text,
			'--fc-muted'          => self::mix( $text, $bg, 0.22 ),
			'--fc-border'         => self::mix( $text, $bg, 0.86 ),
			'--fc-secondary-bg'   => $sec_bg,
			'--fc-secondary-text' => $sec_text,
			'--fc-secondary-deep' => self::mix( $sec_bg, $text, 0.10 ),
			'--fc-primary-bg'     => $prim_bg,
			'--fc-primary-text'   => $prim_text,
			'--fc-primary-deep'   => self::mix( $prim_bg, $bg, 0.16 ),
			'--fc-badge-solid'    => $badge_solid,
			'--fc-badge-hole'     => self::tint( $badge_solid, 0.58 ),
		);
	}

	/**
	 * Déclarations `--x:y;` d'une palette.
	 *
	 * @param array $vars Palette.
	 * @return string
	 */
	protected static function decl( array $vars ) {
		$decl = '';
		foreach ( $vars as $name => $value ) {
			$decl .= $name . ':' . $value . ';';
		}
		return $decl;
	}

	/**
	 * Bloc CSS inline scopé à la bannière et au badge : jour, nuit, ou les deux
	 * sous `prefers-color-scheme` (thème auto). En auto, un site qui expose son
	 * propre interrupteur jour/nuit sur <html> (data-theme, ou la classe .dark)
	 * l'emporte sur le réglage du navigateur : le visiteur l'a choisi lui-même.
	 *
	 * @param array $settings Réglages.
	 * @return string CSS.
	 */
	public static function inline_css( array $settings ) {
		// 0.16.0 : la barre du mode mini vit HORS de #freecookie-root — sans elle dans le
		// sélecteur, elle retombait sur les replis de la feuille (blanc, quel que soit le thème).
		$sel   = '#freecookie-root,#freecookie-badge,#freecookie-mini,#freecookie-trait';
		$light = self::decl( self::css_vars( $settings, 'light' ) ) . 'color-scheme:light;';
		$dark  = self::decl( self::css_vars( $settings, 'dark' ) ) . 'color-scheme:dark;';
		$theme = self::theme( $settings );
		if ( 'light' === $theme ) {
			return $sel . '{' . $light . '}';
		}
		if ( 'dark' === $theme ) {
			return $sel . '{' . $dark . '}';
		}
		$host = function ( $hosts ) {
			$out = array();
			foreach ( $hosts as $h ) {
				$out[] = $h . ' #freecookie-root';
				$out[] = $h . ' #freecookie-badge';
				$out[] = $h . ' #freecookie-mini';
				$out[] = $h . ' #freecookie-trait';
			}
			return implode( ',', $out );
		};
		return $sel . '{' . $light . '}'
			. '@media (prefers-color-scheme:dark){' . $sel . '{' . $dark . '}}'
			. $host( array( 'html[data-theme="dark"]', 'html[data-theme="night"]', 'html.dark' ) ) . '{' . $dark . '}'
			. $host( array( 'html[data-theme="light"]', 'html[data-theme="day"]' ) ) . '{' . $light . '}';
	}
}
