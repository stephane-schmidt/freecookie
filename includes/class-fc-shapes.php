<?php
/**
 * Bibliothèque de formes de cookie (badge). 20 variantes monochromes, teintées
 * à la couleur du site via les variables --fc-badge-solid / --fc-badge-hole.
 * Source unique utilisée par le front, l'écran d'admin et l'aperçu.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Shapes {

	const DEFAULT_ID = 'croque-lateral';

	/**
	 * Les 20 formes de base (famille « Classiques », libre) :
	 * id => [ label, svg (contenu interne du <svg>) ].
	 *
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function base() {
		return array(
			'classique' => array(
				'label' => __( 'Classique', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g class="fc-cookie__hole"><ellipse cx="22" cy="21" rx="4" ry="3.3"/><ellipse cx="41" cy="24" rx="3.4" ry="2.9"/><ellipse cx="26" cy="40" rx="3.8" ry="3.2"/><ellipse cx="43" cy="40" rx="3.4" ry="2.9"/><ellipse cx="33" cy="31" rx="2.8" ry="2.5"/><ellipse cx="17" cy="31" rx="2.4" ry="2.1"/><ellipse cx="37" cy="14" rx="2.2" ry="2"/></g>',
			),
			'croque-lateral' => array(
				'label' => __( 'Croqué (côté)', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-cl"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="60" cy="32" r="13"/><circle cx="48" cy="24" r="4.5"/><circle cx="48" cy="40" r="4.5"/><circle cx="45" cy="32" r="4"/></g></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-cl)"/><g class="fc-cookie__hole" mask="url(#fcm-cl)"><circle cx="24" cy="24" r="3.4"/><circle cx="24" cy="40" r="3.4"/><circle cx="30" cy="32" r="2.6"/><circle cx="20" cy="32" r="2.1"/></g>',
			),
			'croque-haut' => array(
				'label' => __( 'Croqué (haut)', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-ch"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="55" cy="10" r="14"/><circle cx="40" cy="15" r="5"/><circle cx="45" cy="23" r="5.2"/><circle cx="51" cy="29" r="4.6"/></g></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-ch)"/><g class="fc-cookie__hole" mask="url(#fcm-ch)"><circle cx="24" cy="28" r="3.6"/><circle cx="38" cy="40" r="3.1"/><circle cx="26" cy="41" r="2.8"/><circle cx="20" cy="34" r="2.2"/></g>',
			),
			'croque-grand' => array(
				'label' => __( 'Grand mordu', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-cg"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="52" cy="14" r="18"/><circle cx="34" cy="14" r="6"/><circle cx="40" cy="26" r="6"/><circle cx="46" cy="33" r="5"/></g></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-cg)"/><g class="fc-cookie__hole" mask="url(#fcm-cg)"><circle cx="22" cy="26" r="3.6"/><circle cx="26" cy="40" r="3.2"/><circle cx="19" cy="34" r="2.6"/></g>',
			),
			'croque-double' => array(
				'label' => __( 'Double mordu', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-cd"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="56" cy="10" r="12"/><circle cx="44" cy="16" r="4"/><circle cx="49" cy="22" r="4"/><circle cx="8" cy="54" r="12"/><circle cx="18" cy="46" r="4"/><circle cx="14" cy="50" r="4"/></g></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-cd)"/><g class="fc-cookie__hole" mask="url(#fcm-cd)"><circle cx="34" cy="26" r="3"/><circle cx="28" cy="38" r="3"/><circle cx="40" cy="34" r="2.6"/></g>',
			),
			'dents' => array(
				'label' => __( 'Dents marquées', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-dt"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="56" cy="9" r="13"/><circle cx="38" cy="14" r="4.2"/><circle cx="43" cy="19" r="4.2"/><circle cx="48" cy="24" r="4.2"/><circle cx="52" cy="29" r="4"/><circle cx="34" cy="18" r="3.4"/></g></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-dt)"/><g class="fc-cookie__hole" mask="url(#fcm-dt)"><circle cx="24" cy="29" r="3.4"/><circle cx="37" cy="41" r="3"/><circle cx="27" cy="40" r="2.8"/></g>',
			),
			'rond' => array(
				'label' => __( 'Rond', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g class="fc-cookie__hole"><circle cx="26" cy="27" r="3.2"/><circle cx="40" cy="30" r="2.8"/><circle cx="30" cy="40" r="3"/></g>',
			),
			'minimal' => array(
				'label' => __( 'Minimal', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g class="fc-cookie__hole"><circle cx="24" cy="24" r="3.4"/><circle cx="42" cy="27" r="3"/><circle cx="28" cy="41" r="3.4"/><circle cx="43" cy="41" r="2.8"/><circle cx="34" cy="32" r="2.4"/></g>',
			),
			'ligne' => array(
				'label' => __( 'Ligne', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__line" cx="32" cy="32" r="26"/><g class="fc-cookie__line"><circle cx="23" cy="23" r="3.2"/><circle cx="41" cy="25" r="2.8"/><circle cx="27" cy="40" r="3"/><circle cx="42" cy="40" r="2.6"/><circle cx="33" cy="31" r="2.1"/></g>',
			),
			'ligne-croque' => array(
				'label' => __( 'Ligne croqué', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-lc"><rect width="64" height="64" fill="#fff"/><g fill="#000"><circle cx="55" cy="10" r="13"/><circle cx="41" cy="16" r="4.5"/><circle cx="46" cy="23" r="4.5"/></g></mask></defs><circle class="fc-cookie__line" cx="32" cy="32" r="26" mask="url(#fcm-lc)"/><g class="fc-cookie__line" mask="url(#fcm-lc)"><circle cx="24" cy="28" r="3"/><circle cx="28" cy="40" r="2.6"/></g>',
			),
			'doux' => array(
				'label' => __( 'Doux', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><circle class="fc-cookie__ring" cx="32" cy="32" r="23"/><g class="fc-cookie__hole"><ellipse cx="23" cy="24" rx="3.4" ry="3"/><ellipse cx="41" cy="26" rx="3" ry="2.7"/><ellipse cx="27" cy="40" rx="3.4" ry="3"/><ellipse cx="42" cy="40" rx="2.8" ry="2.6"/></g>',
			),
			'cracker' => array(
				'label' => __( 'Cracker', 'freecookie' ),
				'svg'   => '<rect class="fc-cookie__disc" x="7" y="7" width="50" height="50" rx="13"/><g class="fc-cookie__hole"><circle cx="21" cy="21" r="2.2"/><circle cx="32" cy="21" r="2.2"/><circle cx="43" cy="21" r="2.2"/><circle cx="21" cy="32" r="2.2"/><circle cx="32" cy="32" r="2.2"/><circle cx="43" cy="32" r="2.2"/><circle cx="21" cy="43" r="2.2"/><circle cx="32" cy="43" r="2.2"/><circle cx="43" cy="43" r="2.2"/></g>',
			),
			'coeur' => array(
				'label' => __( 'Cœur', 'freecookie' ),
				'svg'   => '<path class="fc-cookie__disc" d="M32 54 C6 36 8 17 22 15 C29 14 32 20 32 23 C32 20 35 14 42 15 C56 17 58 36 32 54 Z"/><g class="fc-cookie__hole"><circle cx="24" cy="26" r="2.8"/><circle cx="39" cy="27" r="2.6"/><circle cx="32" cy="36" r="2.6"/></g>',
			),
			'etoile' => array(
				'label' => __( 'Étoile', 'freecookie' ),
				'svg'   => '<path class="fc-cookie__disc" d="M32 5 L39.5 24.5 L60 25 L43.5 38 L49.5 58 L32 46 L14.5 58 L20.5 38 L4 25 L24.5 24.5 Z"/><g class="fc-cookie__hole"><circle cx="32" cy="28" r="2.6"/><circle cx="27" cy="36" r="2.2"/><circle cx="37" cy="36" r="2.2"/></g>',
			),
			'fleur' => array(
				'label' => __( 'Fleur', 'freecookie' ),
				'svg'   => '<g class="fc-cookie__disc"><circle cx="32" cy="32" r="15"/><circle cx="47" cy="32" r="11"/><circle cx="39.5" cy="45" r="11"/><circle cx="24.5" cy="45" r="11"/><circle cx="17" cy="32" r="11"/><circle cx="24.5" cy="19" r="11"/><circle cx="39.5" cy="19" r="11"/></g><g class="fc-cookie__hole"><circle cx="32" cy="32" r="4"/><circle cx="26" cy="28" r="2"/><circle cx="38" cy="36" r="2"/></g>',
			),
			'festonne' => array(
				'label' => __( 'Festonné', 'freecookie' ),
				'svg'   => '<g class="fc-cookie__disc"><circle cx="32" cy="32" r="22"/><circle cx="55" cy="32" r="5"/><circle cx="51.9" cy="43.5" r="5"/><circle cx="43.5" cy="51.9" r="5"/><circle cx="32" cy="55" r="5"/><circle cx="20.5" cy="51.9" r="5"/><circle cx="12.1" cy="43.5" r="5"/><circle cx="9" cy="32" r="5"/><circle cx="12.1" cy="20.5" r="5"/><circle cx="20.5" cy="12.1" r="5"/><circle cx="32" cy="9" r="5"/><circle cx="43.5" cy="12.1" r="5"/><circle cx="51.9" cy="20.5" r="5"/></g><g class="fc-cookie__hole"><circle cx="27" cy="28" r="2.6"/><circle cx="38" cy="30" r="2.4"/><circle cx="30" cy="39" r="2.6"/></g>',
			),
			'sandwich' => array(
				'label' => __( 'Sandwich', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><rect class="fc-cookie__hole" x="6" y="27" width="52" height="10" rx="5"/><g class="fc-cookie__hole"><circle cx="24" cy="18" r="2.4"/><circle cx="40" cy="18" r="2.4"/><circle cx="24" cy="46" r="2.4"/><circle cx="40" cy="46" r="2.4"/></g>',
			),
			'pepites-xxl' => array(
				'label' => __( 'Grosses pépites', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g class="fc-cookie__hole"><circle cx="23" cy="24" r="6"/><circle cx="42" cy="30" r="5.5"/><circle cx="28" cy="42" r="5.5"/></g>',
			),
			'demi' => array(
				'label' => __( 'Demi', 'freecookie' ),
				'svg'   => '<defs><mask id="fcm-demi"><rect width="64" height="64" fill="#fff"/><rect x="32" y="0" width="32" height="64" fill="#000"/></mask></defs><circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fcm-demi)"/><g class="fc-cookie__hole" mask="url(#fcm-demi)"><circle cx="20" cy="24" r="3.2"/><circle cx="24" cy="38" r="3"/><circle cx="16" cy="32" r="2.4"/></g>',
			),
			'gourmand' => array(
				'label' => __( 'Gourmand', 'freecookie' ),
				'svg'   => '<circle class="fc-cookie__disc" cx="32" cy="32" r="27"/><g class="fc-cookie__hole"><ellipse cx="21" cy="20" rx="4.4" ry="3.6"/><ellipse cx="42" cy="22" rx="3.8" ry="3.2"/><ellipse cx="25" cy="38" rx="4.2" ry="3.4"/><ellipse cx="44" cy="39" rx="3.8" ry="3.2"/><ellipse cx="34" cy="30" rx="3.2" ry="2.8"/><ellipse cx="16" cy="31" rx="2.8" ry="2.4"/><ellipse cx="37" cy="14" rx="2.4" ry="2.1"/><ellipse cx="31" cy="47" rx="2.6" ry="2.2"/></g>',
			),
		);
	}

	/**
	 * Familles de formes. « Classiques » est libre ; les quatre familles
	 * générées (80 formes) sont réservées à FreeCookie Pro.
	 *
	 * @return array<string,array{label:string,pro:bool}>
	 */
	public static function families() {
		return array(
			'classiques' => array( 'label' => __( 'Classiques', 'freecookie' ), 'pro' => false ),
			'cartoon'    => array( 'label' => __( 'Cartoon', 'freecookie' ), 'pro' => true ),
			'fournee'    => array( 'label' => __( 'Fournée', 'freecookie' ), 'pro' => true ),
			'croques'    => array( 'label' => __( 'Croqués & miettes', 'freecookie' ), 'pro' => true ),
			'nappes'     => array( 'label' => __( 'Nappés', 'freecookie' ), 'pro' => true ),
			'pastilles'  => array( 'label' => __( 'Pastilles du site', 'freecookie' ), 'pro' => true ),
			'gourmandes' => array( 'label' => __( 'Gourmandes', 'freecookie' ), 'pro' => true ),
			'fetes'      => array( 'label' => __( 'Fêtes', 'freecookie' ), 'pro' => true ),
			'duo'        => array( 'label' => __( 'Duo graphique', 'freecookie' ), 'pro' => true ),
			'decoupe'    => array( 'label' => __( 'Emporte-pièce', 'freecookie' ), 'pro' => true ),
		);
	}

	/**
	 * Famille d'une forme (déduite du préfixe d'identifiant).
	 *
	 * @param string $id Identifiant.
	 * @return string
	 */
	public static function family_of( $id ) {
		foreach ( array( 'cartoon', 'fournee', 'croques', 'nappes', 'pastilles', 'gourmandes', 'fetes', 'duo', 'decoupe' ) as $fam ) {
			if ( 0 === strpos( (string) $id, 'pro-' . $fam . '-' ) ) {
				return $fam;
			}
		}
		return 'classiques';
	}

	/**
	 * La forme appartient-elle à une famille Pro ?
	 *
	 * @param string $id Identifiant.
	 * @return bool
	 */
	public static function is_pro( $id ) {
		$families = self::families();
		$fam      = self::family_of( $id );
		return ! empty( $families[ $fam ]['pro'] );
	}

	/**
	 * Formes d'une famille donnée.
	 *
	 * @param string $family Clé de famille.
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function by_family( $family ) {
		if ( 'classiques' === $family ) {
			return self::base();
		}
		$out = array();
		foreach ( self::pro_shapes() as $id => $shape ) {
			if ( self::family_of( $id ) === $family ) {
				$out[ $id ] = $shape;
			}
		}
		return $out;
	}

	/**
	 * Toutes les formes (base + Pro).
	 *
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function all() {
		return array_merge( self::base(), self::pro_shapes() );
	}

	/**
	 * Les 80 formes Pro (4 familles × 20), générées paramétriquement.
	 * Déterministes (aucun aléa), monochromes, mêmes classes CSS que la base.
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
			$cache[ 'pro-cartoon-' . $n ] = array( 'label' => sprintf( __( 'Cartoon %d', 'freecookie' ), $n ), 'svg' => self::gen_cartoon( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-fournee-' . $n ] = array( 'label' => sprintf( __( 'Fournée %d', 'freecookie' ), $n ), 'svg' => self::gen_fournee( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-croques-' . $n ] = array( 'label' => sprintf( __( 'Croqué %d', 'freecookie' ), $n ), 'svg' => self::gen_croque( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-nappes-' . $n ] = array( 'label' => sprintf( __( 'Nappé %d', 'freecookie' ), $n ), 'svg' => self::gen_nappe( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-pastilles-' . $n ] = array( 'label' => sprintf( __( 'Pastilles %d', 'freecookie' ), $n ), 'svg' => self::gen_pastilles( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-gourmandes-' . $n ] = array( 'label' => sprintf( __( 'Gourmande %d', 'freecookie' ), $n ), 'svg' => self::gen_gourmande( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-fetes-' . $n ] = array( 'label' => sprintf( __( 'Fête %d', 'freecookie' ), $n ), 'svg' => self::gen_fete( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-duo-' . $n ] = array( 'label' => sprintf( __( 'Duo %d', 'freecookie' ), $n ), 'svg' => self::gen_duo( $n ) );
			/* translators: %d: variant number. */
			$cache[ 'pro-decoupe-' . $n ] = array( 'label' => sprintf( __( 'Découpe %d', 'freecookie' ), $n ), 'svg' => self::gen_decoupe( $n ) );
		}
		return $cache;
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
	 * Valide un id de forme (repli sur la forme par défaut).
	 *
	 * @param string $id Identifiant.
	 * @return string
	 */
	public static function valid( $id ) {
		$all = self::all();
		return isset( $all[ $id ] ) ? $id : self::DEFAULT_ID;
	}

	/**
	 * Contenu SVG interne d'une forme.
	 *
	 * @param string $id Identifiant.
	 * @return string
	 */
	public static function get( $id ) {
		$all = self::all();
		$id  = self::valid( $id );
		return $all[ $id ]['svg'];
	}
}
