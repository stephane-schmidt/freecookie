<?php
/**
 * Bibliothèque de formes de cookie (badge). 20 variantes monochromes, teintées
 * à la couleur du site via les variables --fc-badge-solid / --fc-badge-hole.
 * Source unique utilisée par le front, l'écran d'admin et l'aperçu. Des
 * familles supplémentaires peuvent être enregistrées par une extension via le
 * filtre `freecookie_shape_families` (ex. FreeCookie Pro).
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
	 * Familles de formes disponibles. Le plugin fournit la famille
	 * « Classiques » ; une extension (ex. FreeCookie Pro) peut en enregistrer
	 * d'autres via le filtre `freecookie_shape_families`. Une famille absente
	 * n'est simplement pas proposée — aucune fonctionnalité n'est verrouillée.
	 *
	 * Format d'une famille :
	 * clé => array(
	 *   'label'  => libellé affiché dans l'admin,
	 *   'shapes' => array( id => array( 'label' => string, 'svg' => string ) ),
	 * ).
	 *
	 * @return array<string,array{label:string,shapes:array<string,array{label:string,svg:string}>}>
	 */
	public static function families() {
		$families = array(
			'classiques' => array(
				'label'  => __( 'Classiques', 'freecookie' ),
				'shapes' => self::base(),
			),
		);

		/**
		 * Permet à une extension d'ajouter des familles de formes de badge.
		 *
		 * @since 0.14.0
		 *
		 * @param array $families Familles au format documenté ci-dessus.
		 */
		$families = apply_filters( 'freecookie_shape_families', $families );

		// Garde-fou : ignore les entrées mal formées d'une extension.
		foreach ( $families as $key => $def ) {
			if ( ! is_array( $def ) || empty( $def['shapes'] ) || ! is_array( $def['shapes'] ) ) {
				unset( $families[ $key ] );
			}
		}
		return $families;
	}

	/**
	 * Famille d'une forme (repli sur « Classiques » si inconnue).
	 *
	 * @param string $id Identifiant.
	 * @return string
	 */
	public static function family_of( $id ) {
		foreach ( self::families() as $fam => $def ) {
			if ( isset( $def['shapes'][ $id ] ) ) {
				return $fam;
			}
		}
		return 'classiques';
	}

	/**
	 * Formes d'une famille donnée.
	 *
	 * @param string $family Clé de famille.
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function by_family( $family ) {
		$families = self::families();
		return isset( $families[ $family ]['shapes'] ) ? $families[ $family ]['shapes'] : array();
	}

	/**
	 * Toutes les formes (toutes familles confondues, extensions comprises).
	 *
	 * @return array<string,array{label:string,svg:string}>
	 */
	public static function all() {
		$all = array();
		foreach ( self::families() as $def ) {
			$all = array_merge( $all, $def['shapes'] );
		}
		return $all;
	}

	/**
	 * Valide un id de forme (repli sur la forme par défaut). Un id inconnu —
	 * par exemple une forme d'une extension désinstallée — retombe
	 * silencieusement sur la forme par défaut, sans erreur ni avertissement.
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
