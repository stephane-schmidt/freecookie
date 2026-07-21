<?php
/**
 * Bibliothèque des familles de formes FreeCookie Pro. Les 260 formes
 * supplémentaires (12 familles), générées paramétriquement, sont enregistrées
 * dans FreeCookie via le filtre `freecookie_shape_families` : le plugin
 * gratuit les affiche et les sert exactement comme ses formes « Classiques ».
 *
 * @package FreeCookie_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCPro_Shapes {

	/**
	 * Familles Pro : clé => libellé (ordre d'affichage de l'écran d'admin).
	 *
	 * @return array<string,string>
	 */
	public static function families() {
		return array(
			'cartoon'    => __( 'Cartoon', 'freecookie-pro' ),
			'fournee'    => __( 'Fournée', 'freecookie-pro' ),
			'croques'    => __( 'Croqués & miettes', 'freecookie-pro' ),
			'nappes'     => __( 'Nappés', 'freecookie-pro' ),
			'pastilles'  => __( 'Pastilles du site', 'freecookie-pro' ),
			'gourmandes' => __( 'Gourmandes', 'freecookie-pro' ),
			'fetes'      => __( 'Fêtes', 'freecookie-pro' ),
			'duo'        => __( 'Duo graphique', 'freecookie-pro' ),
			'decoupe'    => __( 'Emporte-pièce', 'freecookie-pro' ),
			'consent'    => __( 'Consentement', 'freecookie-pro' ),
			'reglages'   => __( 'Réglages', 'freecookie-pro' ),
			'verre'      => __( 'Verre', 'freecookie-pro' ),
		);
	}

	/**
	 * Enregistre les familles Pro auprès de FreeCookie (callback du filtre
	 * `freecookie_shape_families`). Les identifiants `pro-<famille>-<n>` sont
	 * inchangés : un site qui avait choisi une forme Pro la retrouve telle
	 * quelle en installant l'extension.
	 *
	 * @param array $families Familles déjà connues du plugin gratuit.
	 * @return array
	 */
	public static function register( $families ) {
		$shapes = self::pro_shapes();
		foreach ( self::families() as $fcpro_fam => $fcpro_label ) {
			$fcpro_out = array();
			foreach ( $shapes as $fcpro_id => $fcpro_shape ) {
				if ( 0 === strpos( $fcpro_id, 'pro-' . $fcpro_fam . '-' ) ) {
					$fcpro_out[ $fcpro_id ] = $fcpro_shape;
				}
			}
			$families[ $fcpro_fam ] = array(
				'label'  => $fcpro_label,
				'shapes' => $fcpro_out,
			);
		}
		return $families;
	}

	/**
	 * Les 260 formes Pro (11 familles × 20 + « Verre » × 40), générées
	 * paramétriquement. Déterministes (aucun aléa), mêmes classes CSS que les
	 * formes « Classiques » du plugin gratuit.
	 *
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function pro_shapes() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}
		$cache = array();
		for ( $n = 1; $n <= 20; $n++ ) {
			/* translators: %d: variant number. */
			$cache[ 'pro-cartoon-' . $n ] = array( 'label' => sprintf( __( 'Cartoon %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_cartoon( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-fournee-' . $n ] = array( 'label' => sprintf( __( 'Fournée %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_fournee( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-croques-' . $n ] = array( 'label' => sprintf( __( 'Croqué %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_croque( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-nappes-' . $n ] = array( 'label' => sprintf( __( 'Nappé %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_nappe( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-pastilles-' . $n ] = array( 'label' => sprintf( __( 'Pastilles %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_pastilles( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-gourmandes-' . $n ] = array( 'label' => sprintf( __( 'Gourmande %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_gourmande( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-fetes-' . $n ] = array( 'label' => sprintf( __( 'Fête %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_fete( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-duo-' . $n ] = array( 'label' => sprintf( __( 'Duo %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_duo( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-decoupe-' . $n ] = array( 'label' => sprintf( __( 'Découpe %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_decoupe( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-consent-' . $n ] = array( 'label' => sprintf( __( 'Consentement %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_consent( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-reglages-' . $n ] = array( 'label' => sprintf( __( 'Réglage %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_reglages( $n ) );
		}
		// Famille « Verre » : plus fournie (40 variantes, demande Stéphane).
		for ( $n = 1; $n <= 40; $n++ ) {
			/* translators: %d: variant number. */
			$cache[ 'pro-verre-' . $n ] = array( 'label' => sprintf( __( 'Verre %d', 'freecookie-pro' ), $n ), 'svg' => self::gen_verre( $n ) );
		}
		return $cache;
	}

	/**
	 * Famille « Verre » : cookie translucide façon verre dépoli — corps teinté à
	 * faible opacité, liseré clair, reflet lumineux en arc, bulles à éclat blanc.
	 * Reste teinté par la couleur du site (classes .fc-cookie__disc/__hole) ;
	 * les éclats blancs/ombres sont neutres (opacité) donc valables sur toute teinte.
	 *
	 * @param int $n Numéro de variante (1-20).
	 * @return string
	 */
	protected static function gen_verre( $n ) {
		$k      = $n - 1;
		$sub    = $k % 5;
		$v      = (int) floor( $k / 5 ); // intensité (0-7 pour 40 formes)
		$vb     = $v % 4;                // borne les décalages/opacités pour rester propre
		$op     = array( '.48', '.4', '.32', '.6' );
		$discop = $op[ $vb ];
		// Reflet de verre commun : corps translucide + liseré clair + arc lumineux.
		$gloss  = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27" fill-opacity="' . $discop . '"/>'
			. '<circle cx="32" cy="32" r="27" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.5"/>'
			. '<path d="M13 30 A20.5 20.5 0 0 1 33 10.5" fill="none" stroke="#fff" stroke-opacity=".55" stroke-width="3" stroke-linecap="round"/>'
			. '<ellipse cx="23" cy="20" rx="7" ry="3.4" fill="#fff" opacity=".22" transform="rotate(-35 23 20)"/>';
		$bubble = function ( $cx, $cy, $r ) {
			return '<circle class="fc-cookie__hole" cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill-opacity=".55"/>'
				. '<circle cx="' . round( $cx - $r * 0.35, 1 ) . '" cy="' . round( $cy - $r * 0.35, 1 ) . '" r="' . round( $r * 0.32, 1 ) . '" fill="#fff" opacity=".7"/>';
		};

		if ( 0 === $sub ) { // Bulles éparpillées.
			$svg = $gloss;
			$c   = 5 + $vb;
			for ( $i = 0; $i < $c; $i++ ) {
				$a    = deg2rad( fmod( $n * 47 + $i * 137.5, 360 ) );
				$d    = 6 + fmod( $i * 6.1 + $n * 2.0, 15 );
				$svg .= $bubble( round( 32 + $d * cos( $a ), 1 ), round( 34 + $d * sin( $a ), 1 ), 2.4 + ( $i % 3 ) * 0.9 );
			}
			return $svg;
		}
		if ( 1 === $sub ) { // Couronne de bulles + bulle centrale.
			$svg = $gloss;
			$c   = 6 + $vb;
			for ( $i = 0; $i < $c; $i++ ) {
				$a    = deg2rad( $i * 360 / $c + $v * 15 + 90 );
				$svg .= $bubble( round( 32 + 15 * cos( $a ), 1 ), round( 34 + 15 * sin( $a ), 1 ), 3 );
			}
			return $svg . $bubble( 32, 34, 3.6 );
		}
		if ( 2 === $sub ) { // Verre croqué (mordu latéral).
			$mid = 'fcm-vr' . $n;
			$svg = '<defs><mask id="' . $mid . '"><rect width="64" height="64" fill="#fff"/><g fill="#000">'
				. '<circle cx="58" cy="' . ( 16 + $vb * 5 ) . '" r="13"/><circle cx="46" cy="' . ( 11 + $vb * 5 ) . '" r="4.6"/><circle cx="50" cy="' . ( 25 + $vb * 5 ) . '" r="4.6"/>'
				. '</g></mask></defs><g mask="url(#' . $mid . ')">' . $gloss;
			for ( $i = 0; $i < 3 + $v; $i++ ) {
				$svg .= $bubble( 22 + $i * 6, 38 - ( $i % 2 ) * 8, 2.8 );
			}
			return $svg . '</g>';
		}
		if ( 3 === $sub ) { // Grosse bulle centrale + satellites.
			$svg = $gloss . $bubble( 33, 33, 9 + $vb );
			for ( $i = 0; $i < 4; $i++ ) {
				$a    = deg2rad( $i * 90 + 45 + $v * 20 );
				$svg .= $bubble( round( 32 + 18 * cos( $a ), 1 ), round( 34 + 18 * sin( $a ), 1 ), 2.6 );
			}
			return $svg;
		}
		// 4 : givré minimal (reflet net + fines bulles), varié selon la variante.
		$svg = $gloss;
		$cnt = 3 + ( $v % 3 );
		for ( $i = 0; $i < $cnt; $i++ ) {
			$svg .= $bubble( 22 + $i * 7 + ( $v % 2 ) * 3, 40 - ( ( $i + $v ) % 2 ) * 6, 2 + ( $i % 2 ) );
		}
		$ex = 38 + ( $v % 3 ) * 3;
		return $svg . '<ellipse cx="' . $ex . '" cy="26" rx="4" ry="2.2" fill="#fff" opacity=".2" transform="rotate(-30 ' . $ex . ' 26)"/>';
	}

	/**
	 * Échantillonne un arc de cercle centré en (32,32) en points « x,y »
	 * (degrés, sens trigonométrique, y inversé pour SVG).
	 *
	 * @param float $r    Rayon.
	 * @param float $a0   Angle de départ (deg).
	 * @param float $a1   Angle de fin (deg, > a0).
	 * @param float $step Pas (deg).
	 * @return string[]
	 */
	protected static function arc_pts( $r, $a0, $a1, $step = 12 ) {
		$pts = array();
		if ( $a1 < $a0 ) {
			$a1 += 360;
		}
		for ( $a = $a0; $a < $a1; $a += $step ) {
			$rad   = deg2rad( $a );
			$pts[] = round( 32 + $r * cos( $rad ), 1 ) . ',' . round( 32 - $r * sin( $rad ), 1 );
		}
		$rad   = deg2rad( $a1 );
		$pts[] = round( 32 + $r * cos( $rad ), 1 ) . ',' . round( 32 - $r * sin( $rad ), 1 );
		return $pts;
	}

	/**
	 * Cartoon : gros contour, gouttes de chocolat brillantes, gribouillis.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_cartoon( $n ) {
		$k    = $n - 1;
		$svg  = '<circle class="fc-cookie__disc" cx="32" cy="32" r="26"/>';
		$svg .= '<circle cx="32" cy="32" r="26" fill="none" stroke="#000" stroke-opacity=".5" stroke-width="' . ( 2.4 + ( $k % 3 ) * 0.5 ) . '"/>';
		$squig = 3 + ( $k % 2 );
		for ( $i = 0; $i < $squig; $i++ ) {
			$a    = deg2rad( $k * 41 + $i * ( 360 / $squig ) + 20 );
			$d    = 16 + ( ( $i + $k ) % 3 ) * 2.5;
			$x    = round( 32 + $d * cos( $a ) - 6, 1 );
			$y    = round( 32 - $d * sin( $a ), 1 );
			$svg .= '<path d="M' . $x . ' ' . $y . ' q3 -3.2 6 0 q3 3.2 6 0" fill="none" stroke="#000" stroke-opacity=".38" stroke-width="1.7" stroke-linecap="round"/>';
		}
		$chips = 5 + ( $k % 3 );
		$drops = '';
		$shine = '';
		for ( $i = 0; $i < $chips; $i++ ) {
			$a      = deg2rad( fmod( $n * 59 + $i * 137.508, 360 ) );
			$d      = 4 + fmod( $i * 6.1 + $k * 2.3, 13 );
			$x      = round( 32 + $d * cos( $a ), 1 );
			$y      = round( 32 - $d * sin( $a ), 1 );
			$s      = 3.4 + ( ( $i + $k ) % 3 ) * 0.6;
			$rot    = ( $i * 53 + $k * 17 ) % 360;
			$drops .= '<path transform="translate(' . $x . ' ' . $y . ') rotate(' . $rot . ') scale(' . round( $s / 4.5, 2 ) . ')" d="M0 -4.5 C3.4 -2.8 3.2 2.6 0 4.5 C-3.2 2.6 -3.4 -2.8 0 -4.5Z"/>';
			$shine .= '<circle transform="translate(' . $x . ' ' . $y . ') rotate(' . $rot . ')" cx="-1" cy="-1.6" r="' . round( $s * 0.22, 1 ) . '" fill="#fff" fill-opacity=".55"/>';
		}
		return $svg . '<g class="fc-cookie__hole">' . $drops . '</g>' . $shine;
	}

	/**
	 * Fournée : assortiment réaliste — craquelé, sucre glace, brownie inversé,
	 * aux noix, pâton irrégulier.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_fournee( $n ) {
		$k   = $n - 1;
		$sub = $k % 5;
		$v   = (int) floor( $k / 5 );

		if ( 4 === $sub ) {
			// Pâton irrégulier + pépites irrégulières.
			$pts = array();
			for ( $i = 0; $i < 14; $i++ ) {
				$a     = deg2rad( $i * 360 / 14 );
				$r     = 23 + 3.2 * sin( $i * 2.3 + $n );
				$pts[] = round( 32 + $r * cos( $a ), 1 ) . ',' . round( 32 - $r * sin( $a ), 1 );
			}
			$svg = '<polygon class="fc-cookie__disc" points="' . implode( ' ', $pts ) . '"/><g class="fc-cookie__hole">';
			for ( $i = 0; $i < 4 + $v; $i++ ) {
				$a    = deg2rad( fmod( $n * 67 + $i * 137.5, 360 ) );
				$d    = 5 + fmod( $i * 6.7 + $n, 13 );
				$cx   = round( 32 + $d * cos( $a ), 1 );
				$cy   = round( 32 - $d * sin( $a ), 1 );
				$svg .= '<ellipse cx="' . $cx . '" cy="' . $cy . '" rx="' . ( 2.6 + ( $i % 3 ) * 0.8 ) . '" ry="' . ( 2 + ( ( $i + 1 ) % 3 ) * 0.8 ) . '" transform="rotate(' . ( ( $i * 47 ) % 360 ) . ' ' . $cx . ' ' . $cy . ')"/>';
			}
			return $svg . '</g>';
		}

		$svg = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';

		if ( 0 === $sub ) {
			// Craquelé (cookie crinkle).
			$c = 7 + $v;
			for ( $i = 0; $i < $c; $i++ ) {
				$a1   = $i * 360 / $c + $v * 11;
				$r0   = 5 + ( $i % 3 ) * 4;
				$p1   = round( 32 + $r0 * cos( deg2rad( $a1 ) ), 1 ) . ' ' . round( 32 - $r0 * sin( deg2rad( $a1 ) ), 1 );
				$p2   = round( 32 + ( $r0 + 7 ) * cos( deg2rad( $a1 + 9 ) ), 1 ) . ' ' . round( 32 - ( $r0 + 7 ) * sin( deg2rad( $a1 + 9 ) ), 1 );
				$p3   = round( 32 + ( $r0 + 14 ) * cos( deg2rad( $a1 - 7 ) ), 1 ) . ' ' . round( 32 - ( $r0 + 14 ) * sin( deg2rad( $a1 - 7 ) ), 1 );
				$svg .= '<path d="M' . $p1 . ' L' . $p2 . ' L' . $p3 . '" fill="none" stroke="#000" stroke-opacity=".32" stroke-width="1.8" stroke-linecap="round"/>';
			}
			return $svg;
		}
		if ( 1 === $sub ) {
			// Sucre glace.
			for ( $i = 0; $i < 15 + 3 * $v; $i++ ) {
				$a    = deg2rad( fmod( $n * 31 + $i * 137.508, 360 ) );
				$d    = 2 + fmod( $i * 4.9, 22 );
				$svg .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r="' . ( 0.9 + ( $i % 3 ) * 0.35 ) . '" fill="#fff" fill-opacity=".6"/>';
			}
			for ( $i = 0; $i < 4; $i++ ) {
				$a    = deg2rad( $n * 83 + $i * 90 );
				$d    = 8 + ( $i % 2 ) * 9;
				$svg .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r=".8" fill="#000" fill-opacity=".25"/>';
			}
			return $svg;
		}
		if ( 2 === $sub ) {
			// Brownie inversé : pâte sombre, pépites claires.
			$svg .= '<circle cx="32" cy="32" r="27" fill="#000" fill-opacity=".28"/><g class="fc-cookie__hole">';
			for ( $i = 0; $i < 6 + $v; $i++ ) {
				$a    = deg2rad( fmod( $n * 47 + $i * 137.508, 360 ) );
				$d    = 4 + fmod( $i * 6.3 + $n * 2.1, 15 );
				$svg .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r="' . ( 2.4 + ( ( $i + $k ) % 3 ) * 0.6 ) . '"/>';
			}
			return $svg . '</g>';
		}
		// 3 : aux noix (gros éclats).
		for ( $i = 0; $i < 3 + ( $v % 2 ); $i++ ) {
			$a    = deg2rad( fmod( $n * 79 + $i * 121, 360 ) );
			$d    = 6 + fmod( $i * 7.1 + $n, 11 );
			$cx   = round( 32 + $d * cos( $a ), 1 );
			$cy   = round( 32 - $d * sin( $a ), 1 );
			$rot  = ( $i * 61 + $n * 13 ) % 360;
			$svg .= '<ellipse cx="' . $cx . '" cy="' . $cy . '" rx="5" ry="4.1" transform="rotate(' . $rot . ' ' . $cx . ' ' . $cy . ')" fill="#000" fill-opacity=".3"/>';
			$svg .= '<ellipse cx="' . round( $cx - 1.3, 1 ) . '" cy="' . round( $cy - 1.2, 1 ) . '" rx="1.7" ry="1.1" transform="rotate(' . $rot . ' ' . $cx . ' ' . $cy . ')" fill="#fff" fill-opacity=".4"/>';
		}
		$svg .= '<g class="fc-cookie__hole">';
		for ( $i = 0; $i < 3; $i++ ) {
			$a    = deg2rad( $n * 51 + $i * 137.5 );
			$d    = 12 + ( ( $i * 5 + $k ) % 8 );
			$svg .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r="1.8"/>';
		}
		return $svg . '</g>';
	}

	/**
	 * Croqués & miettes : morsures avec miettes volantes, doubles morsures,
	 * puis parts découpées façon camembert.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_croque( $n ) {
		$k = $n - 1;

		if ( $k >= 14 ) {
			// Part découpée, légèrement écartée.
			$i0    = $k - 14;
			$a0    = $i0 * 60 + 10;
			$sweep = 46 + ( $i0 % 3 ) * 10;
			$body  = self::arc_pts( 26, $a0 + $sweep, $a0 + 360, 10 );
			array_unshift( $body, '32,32' );
			$svg = '<polygon class="fc-cookie__disc" points="' . implode( ' ', $body ) . '"/>';
			$phi = deg2rad( $a0 + $sweep / 2 );
			$ox  = round( 7 * cos( $phi ), 1 );
			$oy  = round( -7 * sin( $phi ), 1 );
			$wed = self::arc_pts( 26, $a0, $a0 + $sweep, 8 );
			array_unshift( $wed, '32,32' );
			$svg .= '<polygon class="fc-cookie__disc" transform="translate(' . $ox . ' ' . $oy . ')" points="' . implode( ' ', $wed ) . '"/>';
			$svg .= '<g class="fc-cookie__hole">';
			foreach ( array( array( 150, 12 ), array( 250, 10 ), array( 320, 15 ) ) as $c ) {
				$a    = deg2rad( $a0 + $c[0] );
				$svg .= '<circle cx="' . round( 32 + $c[1] * cos( $a ), 1 ) . '" cy="' . round( 32 - $c[1] * sin( $a ), 1 ) . '" r="2.8"/>';
			}
			$svg .= '<circle transform="translate(' . $ox . ' ' . $oy . ')" cx="' . round( 32 + 15 * cos( $phi ), 1 ) . '" cy="' . round( 32 - 15 * sin( $phi ), 1 ) . '" r="2.6"/></g>';
			// Miettes près de l'entaille.
			$svg .= '<g class="fc-cookie__disc">';
			for ( $i = 0; $i < 3; $i++ ) {
				$aa   = $phi + deg2rad( -14 + $i * 14 );
				$dd   = 20 + ( $i % 2 ) * 4;
				$svg .= '<circle cx="' . round( 32 + $dd * cos( $aa ) + $ox * 0.4, 1 ) . '" cy="' . round( 32 - $dd * sin( $aa ) + $oy * 0.4, 1 ) . '" r="' . ( 0.9 + ( $i % 2 ) * 0.5 ) . '"/>';
			}
			return $svg . '</g>';
		}

		// Morsure(s) tournantes + miettes volantes.
		$theta = $k * 26;
		$rad   = deg2rad( $theta );
		$biter = 9.5 + ( $k % 4 ) * 2;
		$bites = '<circle cx="' . round( 32 + 27 * cos( $rad ), 1 ) . '" cy="' . round( 32 - 27 * sin( $rad ), 1 ) . '" r="' . $biter . '"/>';
		foreach ( array( -15, 15 ) as $off ) {
			$a      = deg2rad( $theta + $off );
			$bites .= '<circle cx="' . round( 32 + 26 * cos( $a ), 1 ) . '" cy="' . round( 32 - 26 * sin( $a ), 1 ) . '" r="' . round( $biter * 0.32 + 1.3, 1 ) . '"/>';
		}
		if ( $k >= 10 ) {
			$rad2   = deg2rad( $theta + 150 );
			$bites .= '<circle cx="' . round( 32 + 27 * cos( $rad2 ), 1 ) . '" cy="' . round( 32 - 27 * sin( $rad2 ), 1 ) . '" r="' . round( $biter * 0.75, 1 ) . '"/>';
		}
		$m   = 'fcm-pc' . $n;
		$svg = '<defs><mask id="' . $m . '"><rect width="64" height="64" fill="#fff"/><g fill="#000">' . $bites . '</g></mask></defs>'
			. '<circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#' . $m . ')"/>';
		$svg .= '<g class="fc-cookie__hole" mask="url(#' . $m . ')">';
		for ( $i = 0; $i < 3; $i++ ) {
			$a    = deg2rad( fmod( $n * 61 + $i * 137.5, 360 ) );
			$d    = 6 + fmod( $i * 6.4 + $n * 2.7, 12 );
			$svg .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r="' . ( 2.4 + ( ( $i + $k ) % 3 ) * 0.6 ) . '"/>';
		}
		$svg .= '</g><g class="fc-cookie__disc">';
		$nb   = 4 + ( $k % 3 );
		for ( $i = 0; $i < $nb; $i++ ) {
			$aa   = deg2rad( $theta - 26 + $i * 13 );
			$dd   = 29.5 + ( $i % 3 ) * 1.1;
			$svg .= '<circle cx="' . round( 32 + $dd * cos( $aa ), 1 ) . '" cy="' . round( 32 - $dd * sin( $aa ), 1 ) . '" r="' . ( 0.9 + ( ( $i + $k ) % 3 ) * 0.45 ) . '"/>';
		}
		return $svg . '</g>';
	}

	/**
	 * Nappés : moitié trempée dans le chocolat + pastilles, ou filets de nappage.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_nappe( $n ) {
		$k   = $n - 1;
		$svg = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';

		if ( $k < 16 ) {
			$a0    = ( $k % 8 ) * 45;
			$cover = 165 + ( (int) floor( $k / 8 ) ) * 40;
			$svg  .= '<polygon points="' . implode( ' ', self::arc_pts( 26.6, $a0, $a0 + $cover, 10 ) ) . '" fill="#000" fill-opacity=".27"/>';
			$dots  = 7 + ( $k % 3 );
			for ( $i = 0; $i < $dots; $i++ ) {
				$a  = deg2rad( fmod( $n * 43 + $i * 137.508, 360 ) );
				$d  = 5 + fmod( $i * 5.3 + $k * 1.7, 16 );
				$cx = round( 32 + $d * cos( $a ), 1 );
				$cy = round( 32 - $d * sin( $a ), 1 );
				$r  = 2 + ( ( $i + $k ) % 3 ) * 0.8;
				$svg .= ( 0 === $i % 2 )
					? '<circle class="fc-cookie__hole" cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '"/>'
					: '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="#fff" fill-opacity=".55"/>';
			}
			return $svg;
		}

		// Filets de nappage ondulés.
		$rot  = ( $k - 16 ) * 45;
		$svg .= '<g transform="rotate(' . $rot . ' 32 32)">';
		for ( $i = 0; $i < 4; $i++ ) {
			$y    = 17 + $i * 10;
			$svg .= '<path d="M11 ' . $y . ' q5.5 -5 11 0 t11 0 t11 0" fill="none" stroke="#000" stroke-opacity=".3" stroke-width="2.4" stroke-linecap="round"/>';
		}
		$svg .= '<path d="M13 27 q5.5 -5 11 0 t11 0 t11 0" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.4" stroke-linecap="round"/></g>';
		return $svg . '<g class="fc-cookie__hole"><circle cx="22" cy="13" r="2.2"/><circle cx="42" cy="51" r="2.2"/></g>';
	}

	/**
	 * Pastilles du site : MULTICOLORE — les pastilles prennent les couleurs
	 * détectées du site (variables --fc-c1..--fc-c4, repli dérivés de l'accent).
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_pastilles( $n ) {
		$k   = $n - 1;
		$sub = $k % 4;
		$v   = (int) floor( $k / 4 );
		$svg = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';
		$dot = function ( $cx, $cy, $r, $i ) {
			return '<circle class="fc-cookie__c' . ( ( $i % 4 ) + 1 ) . '" cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '"/>';
		};
		if ( 0 === $sub ) {
			// Éparpillées.
			for ( $i = 0; $i < 8 + $v * 2; $i++ ) {
				$a    = deg2rad( fmod( $n * 49 + $i * 137.508, 360 ) );
				$d    = 4 + fmod( $i * 5.7 + $n * 2.2, 17 );
				$svg .= $dot( round( 32 + $d * cos( $a ), 1 ), round( 32 - $d * sin( $a ), 1 ), 2.2 + ( ( $i + $k ) % 3 ) * 0.6, $i );
			}
			return $svg;
		}
		if ( 1 === $sub ) {
			// En couronne + pastille centrale.
			$c = 8 + $v;
			for ( $i = 0; $i < $c; $i++ ) {
				$a    = deg2rad( $i * 360 / $c + $v * 12 );
				$svg .= $dot( round( 32 + 17 * cos( $a ), 1 ), round( 32 - 17 * sin( $a ), 1 ), 2.8, $i );
			}
			return $svg . $dot( 32, 32, 3.4, $v );
		}
		if ( 2 === $sub ) {
			// Moitié chocolat + pastilles des deux côtés.
			$svg .= '<polygon points="' . implode( ' ', self::arc_pts( 26.6, 90 + $v * 45, 270 + $v * 45, 10 ) ) . '" fill="#000" fill-opacity=".25"/>';
			for ( $i = 0; $i < 7 + $v; $i++ ) {
				$a    = deg2rad( fmod( $n * 43 + $i * 137.508, 360 ) );
				$d    = 5 + fmod( $i * 5.3 + $k * 1.9, 16 );
				$svg .= $dot( round( 32 + $d * cos( $a ), 1 ), round( 32 - $d * sin( $a ), 1 ), 2.4, $i );
			}
			return $svg;
		}
		// Grappes de trois.
		for ( $g = 0; $g < 3; $g++ ) {
			$a  = deg2rad( $g * 120 + $v * 25 + $n * 7 );
			$gx = 32 + 12 * cos( $a );
			$gy = 32 - 12 * sin( $a );
			foreach ( array( array( 0, -3 ), array( -3, 2.4 ), array( 3, 2.4 ) ) as $j => $o ) {
				$svg .= $dot( round( $gx + $o[0], 1 ), round( $gy + $o[1], 1 ), 2.5, $g * 3 + $j + $v );
			}
		}
		return $svg;
	}

	/**
	 * Gourmandes : COULEURS NATURELLES de cookies (pâtes dorées, chocolat au
	 * lait, noir, caramel… et chocolat blanc) — couleurs fixes, pas de teinte.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_gourmande( $n ) {
		$k   = $n - 1;
		$sub = $k % 5;
		$v   = (int) floor( $k / 5 );
		$chips = function ( $count, $seed, $color ) {
			$out = '';
			for ( $i = 0; $i < $count; $i++ ) {
				$a    = deg2rad( fmod( $seed * 53 + $i * 137.508, 360 ) );
				$d    = 4 + fmod( $i * 6.1 + $seed * 2.4, 15 );
				$out .= '<circle cx="' . round( 32 + $d * cos( $a ), 1 ) . '" cy="' . round( 32 - $d * sin( $a ), 1 ) . '" r="' . ( 2.3 + ( $i % 3 ) * 0.6 ) . '" fill="' . $color . '"/>';
			}
			return $out;
		};
		if ( 0 === $sub ) {
			// Dorée aux pépites de chocolat noir.
			return '<circle cx="32" cy="32" r="27" fill="#e6bf7f" stroke="#b98a4a" stroke-width="2"/>' . $chips( 6 + $v, $n, '#4a2c14' );
		}
		if ( 1 === $sub ) {
			// Tout chocolat aux pépites de chocolat BLANC.
			return '<circle cx="32" cy="32" r="27" fill="#6b4226" stroke="#4a2c14" stroke-width="2"/>' . $chips( 6 + $v, $n, '#f6efdd' );
		}
		if ( 2 === $sub ) {
			// Trempée au chocolat blanc.
			$svg  = '<circle cx="32" cy="32" r="27" fill="#c68b4a" stroke="#9a6633" stroke-width="2"/>';
			$svg .= '<polygon points="' . implode( ' ', self::arc_pts( 26.4, 100 + $v * 40, 280 + $v * 40, 10 ) ) . '" fill="#f6efdd"/>';
			return $svg . $chips( 4, $n, '#7b4a24' );
		}
		if ( 3 === $sub ) {
			// Filet de caramel sur pâte dorée.
			$svg  = '<circle cx="32" cy="32" r="27" fill="#e0b06a" stroke="#b98a4a" stroke-width="2"/>';
			$svg .= '<g transform="rotate(' . ( $v * 30 ) . ' 32 32)">';
			for ( $i = 0; $i < 3; $i++ ) {
				$y    = 20 + $i * 11;
				$svg .= '<path d="M12 ' . $y . ' q5.5 -4.5 11 0 t11 0 t11 0" fill="none" stroke="#a8611f" stroke-width="2.6" stroke-linecap="round"/>';
			}
			return $svg . '</g>' . $chips( 3, $n, '#5c3a1e' );
		}
		// Marbrée chocolat blanc / chocolat au lait.
		$svg = '<circle cx="32" cy="32" r="27" fill="#a9713c" stroke="#7b4a24" stroke-width="2"/>';
		for ( $i = 0; $i < 5 + $v; $i++ ) {
			$a    = deg2rad( fmod( $n * 61 + $i * 137.508, 360 ) );
			$d    = 4 + fmod( $i * 6.7 + $n * 1.9, 15 );
			$cx   = round( 32 + $d * cos( $a ), 1 );
			$cy   = round( 32 - $d * sin( $a ), 1 );
			$svg .= '<ellipse cx="' . $cx . '" cy="' . $cy . '" rx="' . ( 3.4 + ( $i % 3 ) ) . '" ry="' . ( 2.4 + ( ( $i + 1 ) % 3 ) * 0.8 ) . '" transform="rotate(' . ( ( $i * 43 ) % 360 ) . ' ' . $cx . ' ' . $cy . ')" fill="#f6efdd"/>';
		}
		return $svg;
	}

	/**
	 * Fêtes : sapin, bonhomme de pain d'épices, flocon, rayures de sucre
	 * d'orge, étoile glacée — monochrome + glaçage blanc.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_fete( $n ) {
		$k   = $n - 1;
		$sub = $k % 5;
		$v   = (int) floor( $k / 5 );
		if ( 0 === $sub ) {
			// Sapin.
			$svg = '<g class="fc-cookie__disc"><polygon points="32,7 20,24 44,24"/><polygon points="32,15 16,36 48,36"/><polygon points="32,26 13,50 51,50"/><rect x="29" y="50" width="6" height="7" rx="2"/></g>';
			for ( $i = 0; $i < 3 + $v; $i++ ) {
				$svg .= '<circle cx="' . ( 24 + ( ( $i * 7 + $v * 3 ) % 17 ) ) . '" cy="' . ( 20 + $i * 8 ) . '" r="1.7" fill="#fff" fill-opacity=".75"/>';
			}
			return $svg;
		}
		if ( 1 === $sub ) {
			// Bonhomme de pain d'épices.
			$svg  = '<g class="fc-cookie__disc"><circle cx="32" cy="17" r="8"/><circle cx="32" cy="37" r="10.5"/>';
			$svg .= '<rect x="13" y="29" width="15" height="6.5" rx="3.2" transform="rotate(-22 20 32)"/><rect x="36" y="29" width="15" height="6.5" rx="3.2" transform="rotate(22 44 32)"/>';
			$svg .= '<rect x="23" y="45" width="6.5" height="14" rx="3.2" transform="rotate(14 26 52)"/><rect x="34.5" y="45" width="6.5" height="14" rx="3.2" transform="rotate(-14 38 52)"/></g>';
			$svg .= '<circle cx="29" cy="15.5" r="1.2" fill="#fff" fill-opacity=".85"/><circle cx="35" cy="15.5" r="1.2" fill="#fff" fill-opacity=".85"/>';
			$svg .= '<path d="M29 20 q3 2.4 6 0" fill="none" stroke="#fff" stroke-opacity=".8" stroke-width="1.3" stroke-linecap="round"/>';
			for ( $i = 0; $i < 2 + ( $v % 2 ); $i++ ) {
				$svg .= '<circle cx="32" cy="' . ( 32 + $i * 5.5 ) . '" r="1.4" fill="#fff" fill-opacity=".8"/>';
			}
			return $svg;
		}
		if ( 2 === $sub ) {
			// Flocon glacé.
			$svg = '<circle class="fc-cookie__disc" cx="32" cy="32" r="26"/>';
			for ( $i = 0; $i < 6; $i++ ) {
				$a    = deg2rad( $i * 60 + $v * 10 );
				$x2   = round( 32 + 20 * cos( $a ), 1 );
				$y2   = round( 32 - 20 * sin( $a ), 1 );
				$svg .= '<line x1="' . round( 32 + 4 * cos( $a ), 1 ) . '" y1="' . round( 32 - 4 * sin( $a ), 1 ) . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="#fff" stroke-opacity=".75" stroke-width="2.2" stroke-linecap="round"/>';
				$svg .= '<circle cx="' . $x2 . '" cy="' . $y2 . '" r="1.6" fill="#fff" fill-opacity=".75"/>';
			}
			return $svg . '<circle cx="32" cy="32" r="2.2" fill="#fff" fill-opacity=".8"/>';
		}
		if ( 3 === $sub ) {
			// Rayures de sucre d'orge.
			$m   = 'fcm-fe' . $n;
			$svg = '<defs><mask id="' . $m . '"><circle cx="32" cy="32" r="26" fill="#fff"/></mask></defs>';
			$svg .= '<circle class="fc-cookie__disc" cx="32" cy="32" r="26"/>';
			$svg .= '<g mask="url(#' . $m . ')" transform="rotate(' . ( 35 + $v * 25 ) . ' 32 32)">';
			for ( $i = 0; $i < 4; $i++ ) {
				$svg .= '<rect x="4" y="' . ( 12 + $i * 12 ) . '" width="56" height="5.5" rx="2.7" fill="#fff" fill-opacity=".7"/>';
			}
			return $svg . '</g>';
		}
		// Étoile glacée.
		$pts = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$r     = ( 0 === $i % 2 ) ? 26 : 11.5;
			$a     = deg2rad( -90 + $i * 36 + $v * 9 );
			$pts[] = round( 32 + $r * cos( $a ), 1 ) . ',' . round( 32 + $r * sin( $a ), 1 );
		}
		$svg = '<polygon class="fc-cookie__disc" points="' . implode( ' ', $pts ) . '"/>';
		return $svg . '<polygon points="' . implode( ' ', $pts ) . '" fill="none" stroke="#fff" stroke-opacity=".65" stroke-width="1.8" stroke-linejoin="round" transform="translate(0 0) scale(.82) translate(7 7)"/>';
	}

	/**
	 * Duo graphique : motifs bicolores francs — moitié, quartiers, cible,
	 * damier, rayures.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_duo( $n ) {
		$k   = $n - 1;
		$sub = $k % 5;
		$v   = (int) floor( $k / 5 );
		$m   = 'fcm-du' . $n;
		$def = '<defs><mask id="' . $m . '"><circle cx="32" cy="32" r="27" fill="#fff"/></mask></defs>';
		$svg = $def . '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';
		if ( 0 === $sub ) {
			// Moitié / moitié.
			return $svg . '<g mask="url(#' . $m . ')" transform="rotate(' . ( $v * 45 ) . ' 32 32)"><rect class="fc-cookie__hole" x="32" y="0" width="32" height="64"/></g>';
		}
		if ( 1 === $sub ) {
			// Quartiers opposés.
			$a0  = $v * 22;
			$q1  = self::arc_pts( 27, $a0, $a0 + 90, 10 );
			$q2  = self::arc_pts( 27, $a0 + 180, $a0 + 270, 10 );
			array_unshift( $q1, '32,32' );
			array_unshift( $q2, '32,32' );
			return $svg . '<polygon class="fc-cookie__hole" points="' . implode( ' ', $q1 ) . '"/><polygon class="fc-cookie__hole" points="' . implode( ' ', $q2 ) . '"/>';
		}
		if ( 2 === $sub ) {
			// Cible.
			$rings = array( array( 20, 'hole' ), array( 13.5, 'disc' ), array( 7, 'hole' ) );
			if ( $v > 1 ) {
				$rings[] = array( 3, 'disc' );
			}
			foreach ( $rings as $r ) {
				$svg .= '<circle class="fc-cookie__' . $r[1] . '" cx="32" cy="32" r="' . $r[0] . '"/>';
			}
			return $svg;
		}
		if ( 3 === $sub ) {
			// Damier.
			$cell = 8 + $v * 2;
			$g    = '<g mask="url(#' . $m . ')" class="fc-cookie__hole">';
			for ( $i = 0; $i < 10; $i++ ) {
				for ( $j = 0; $j < 10; $j++ ) {
					if ( 0 === ( $i + $j ) % 2 ) {
						$x = $i * $cell;
						$y = $j * $cell;
						if ( $x < 64 && $y < 64 ) {
							$g .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cell . '" height="' . $cell . '"/>';
						}
					}
				}
			}
			return $svg . $g . '</g>';
		}
		// Rayures.
		$g = '<g mask="url(#' . $m . ')" transform="rotate(' . ( $v * 45 ) . ' 32 32)" class="fc-cookie__hole">';
		for ( $i = 0; $i < 4; $i++ ) {
			$g .= '<rect x="0" y="' . ( 6 + $i * 15 ) . '" width="64" height="7.5"/>';
		}
		return $svg . $g . '</g>';
	}

	/**
	 * Emporte-pièce : lune, nuage, patte, goutte, champignon — silhouettes
	 * découpées, monochromes.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_decoupe( $n ) {
		$k   = $n - 1;
		$sub = $k % 5;
		$v   = (int) floor( $k / 5 );
		$chips = function ( $count, $seed, $cx0 = 32, $cy0 = 32, $spread = 10 ) {
			$out = '<g class="fc-cookie__hole">';
			for ( $i = 0; $i < $count; $i++ ) {
				$a    = deg2rad( fmod( $seed * 57 + $i * 137.508, 360 ) );
				$d    = 3 + fmod( $i * 4.3 + $seed * 1.7, $spread );
				$out .= '<circle cx="' . round( $cx0 + $d * cos( $a ), 1 ) . '" cy="' . round( $cy0 - $d * sin( $a ), 1 ) . '" r="2.1"/>';
			}
			return $out . '</g>';
		};
		if ( 0 === $sub ) {
			// Lune.
			$m   = 'fcm-de' . $n;
			$svg = '<defs><mask id="' . $m . '"><rect width="64" height="64" fill="#fff"/><circle cx="' . ( 44 + $v ) . '" cy="' . ( 22 - $v ) . '" r="20" fill="#000"/></mask></defs>';
			$svg .= '<g transform="rotate(' . ( $v * 20 ) . ' 32 32)"><circle class="fc-cookie__disc" cx="30" cy="34" r="24" mask="url(#' . $m . ')"/></g>';
			return $svg . $chips( 3 + ( $v % 2 ), $n, 24, 40, 8 );
		}
		if ( 1 === $sub ) {
			// Nuage.
			$svg = '<g class="fc-cookie__disc"><circle cx="22" cy="36" r="10"/><circle cx="33" cy="29" r="13"/><circle cx="44" cy="36" r="10"/><rect x="14" y="36" width="38" height="10" rx="5"/></g>';
			return $svg . $chips( 3 + $v, $n, 32, 35, 9 );
		}
		if ( 2 === $sub ) {
			// Patte.
			$svg = '<g class="fc-cookie__disc" transform="rotate(' . ( $v * 12 - 18 ) . ' 32 32)"><ellipse cx="32" cy="41" rx="12.5" ry="10"/><circle cx="18" cy="26" r="5.4"/><circle cx="32" cy="21" r="5.6"/><circle cx="46" cy="26" r="5.4"/></g>';
			return $svg . $chips( 2 + ( $v % 2 ), $n, 32, 41, 6 );
		}
		if ( 3 === $sub ) {
			// Goutte.
			$svg = '<g transform="rotate(' . ( $v * 25 ) . ' 32 32)"><path class="fc-cookie__disc" d="M32 7 C45 21 44 40 32 51 C20 40 19 21 32 7 Z"/></g>';
			return $svg . $chips( 3 + $v, $n, 32, 32, 9 );
		}
		// Champignon.
		$svg  = '<path class="fc-cookie__disc" d="M11 33 A21 21 0 0 1 53 33 Z"/><rect class="fc-cookie__disc" x="26" y="33" width="12" height="17" rx="5"/>';
		for ( $i = 0; $i < 3 + ( $v % 2 ); $i++ ) {
			$svg .= '<circle cx="' . ( 19 + ( ( $i * 9 + $v * 4 ) % 27 ) ) . '" cy="' . ( 24 + ( ( $i + $v ) % 2 ) * 5 ) . '" r="2" fill="#fff" fill-opacity=".7"/>';
		}
		return $svg;
	}

	/**
	 * Consentement : badge abstrait « c'est réglé » — pastille organique
	 * (blob) portant une coche + un élan de mouvement. Pas un petit gâteau :
	 * évoque l'approbation, le choix administré.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_consent( $n ) {
		$k    = $n - 1;
		$sub  = $k % 4;
		$v    = (int) floor( $k / 4 );
		// Pastille organique légèrement tournée (blob) — 12 points ondulés.
		$rot  = $k * 17;
		$pts  = array();
		for ( $i = 0; $i < 12; $i++ ) {
			$a     = deg2rad( $i * 30 );
			$r     = 27 + 2.4 * sin( $i * 1.5 + $k );
			$pts[] = round( 32 + $r * cos( $a ), 1 ) . ',' . round( 32 + $r * sin( $a ), 1 );
		}
		$blob = ( 0 === $sub )
			? '<circle class="fc-cookie__disc" cx="32" cy="32" r="27" transform="rotate(' . $rot . ' 32 32)"/>'
			: '<polygon class="fc-cookie__disc" points="' . implode( ' ', $pts ) . '" transform="rotate(' . $rot . ' 32 32)"/>';
		// Coche.
		$check = '<path d="M21 33 l7.5 8 14 -17" fill="none" stroke="#fff" stroke-opacity=".92" stroke-width="' . ( 4.2 + ( $v % 2 ) * 0.8 ) . '" stroke-linecap="round" stroke-linejoin="round"/>';
		// Élan de mouvement : 3 traits obliques décroissants.
		$speed = '';
		if ( 1 !== $sub ) {
			$sn = 3 + ( $v % 2 );
			for ( $i = 0; $i < $sn; $i++ ) {
				$off    = $i * 5;
				$speed .= '<line x1="' . ( 40 + $off ) . '" y1="' . ( 40 - $i * 2 ) . '" x2="' . ( 48 + $off - $i * 2 ) . '" y2="' . ( 32 - $i * 2 ) . '" stroke="#fff" stroke-opacity="' . ( 0.7 - $i * 0.15 ) . '" stroke-width="3" stroke-linecap="round"/>';
			}
		}
		// Variante « puce » : petite pastille de couleur (accent secondaire).
		$dot = ( 2 === $sub ) ? '<circle class="fc-cookie__hole" cx="46" cy="18" r="5"/>' : '';
		// Variante « bouclier » : contour intérieur.
		$ring = ( 3 === $sub ) ? '<circle class="fc-cookie__ring" cx="32" cy="32" r="21"/>' : '';
		return $blob . $ring . $dot . $check . $speed;
	}

	/**
	 * Réglages : badge abstrait « paramétrable » — engrenage, curseurs,
	 * interrupteur, molette. Évoque le contrôle fin, l'administration.
	 *
	 * @param int $n Variante 1-20.
	 * @return string
	 */
	protected static function gen_reglages( $n ) {
		$k   = $n - 1;
		$sub = $k % 4;
		$v   = (int) floor( $k / 4 );
		if ( 0 === $sub ) {
			// Engrenage : dents = petits rectangles autour, trou central.
			$teeth = 8 + $v;
			$m     = 'fcm-rg' . $n;
			$g     = '<g fill="#000">';
			for ( $i = 0; $i < $teeth; $i++ ) {
				$a  = $i * 360 / $teeth;
				$g .= '<rect x="30" y="1" width="4" height="10" rx="1.5" transform="rotate(' . round( $a, 1 ) . ' 32 32)"/>';
			}
			$g .= '</g>';
			return '<defs><mask id="' . $m . '"><rect width="64" height="64" fill="#fff"/><circle cx="32" cy="32" r="9" fill="#000"/></mask></defs>'
				. '<g mask="url(#' . $m . ')"><circle class="fc-cookie__disc" cx="32" cy="32" r="24"/>' . str_replace( 'fill="#000"', 'class="fc-cookie__disc" fill=""', $g ) . '</g>'
				. '<circle class="fc-cookie__ring" cx="32" cy="32" r="13"/>';
		}
		if ( 1 === $sub ) {
			// Curseurs (sliders) : 3 pistes horizontales + poignées.
			$svg  = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g>';
			$posx = array( 40, 24, 44 );
			for ( $i = 0; $i < 3; $i++ ) {
				$y    = 20 + $i * 12;
				$svg .= '<rect x="14" y="' . ( $y - 1.5 ) . '" width="36" height="3" rx="1.5" fill="#fff" fill-opacity=".55"/>';
				$svg .= '<circle cx="' . ( $posx[ ( $i + $v ) % 3 ] ) . '" cy="' . $y . '" r="4.6" fill="#fff" fill-opacity=".92"/>';
			}
			return $svg . '</g>';
		}
		if ( 2 === $sub ) {
			// Interrupteur (toggle) ON.
			$on   = 0 === $v % 2;
			$svg  = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';
			$svg .= '<rect x="15" y="24" width="34" height="16" rx="8" fill="#fff" fill-opacity=".28"/>';
			$svg .= '<circle cx="' . ( $on ? 41 : 23 ) . '" cy="32" r="6.4" fill="#fff" fill-opacity=".95"/>';
			return $svg;
		}
		// Molette / bouton rotatif avec graduations + repère.
		$svg  = '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/>';
		$svg .= '<circle class="fc-cookie__ring" cx="32" cy="32" r="18"/>';
		$ticks = 12;
		for ( $i = 0; $i < $ticks; $i++ ) {
			$a    = deg2rad( $i * 360 / $ticks );
			$x1   = round( 32 + 22 * cos( $a ), 1 );
			$y1   = round( 32 + 22 * sin( $a ), 1 );
			$x2   = round( 32 + 25 * cos( $a ), 1 );
			$y2   = round( 32 + 25 * sin( $a ), 1 );
			$svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="#fff" stroke-opacity=".5" stroke-width="2" stroke-linecap="round"/>';
		}
		$a = deg2rad( 40 + $v * 60 );
		$svg .= '<line x1="32" y1="32" x2="' . round( 32 + 13 * cos( $a ), 1 ) . '" y2="' . round( 32 + 13 * sin( $a ), 1 ) . '" stroke="#fff" stroke-opacity=".95" stroke-width="3.4" stroke-linecap="round"/>';
		return $svg;
	}
}
