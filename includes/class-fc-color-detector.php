<?php
/**
 * Détection automatique des couleurs dominantes d'un site — 100 % local.
 *
 * Combine des sources structurées (kit Elementor, theme.json, theme mods) et,
 * en profondeur, une analyse de fréquence de la page d'accueil et de ses CSS.
 * Écarte le blanc, le noir et les gris pour ne garder que les couleurs de marque.
 * Aucun service tiers n'est sollicité.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Color_Detector {

	const OPTION = 'freecookie_colors_detected';

	/** Couleurs « d'usine » (Elementor / thèmes) souvent laissées telles quelles → à ne pas prendre pour la marque. */
	const FACTORY_DEFAULTS = array( '#6ec1e4', '#61ce70', '#54595f', '#7a7a7a', '#69727d', '#f5f5f5', '#e8e8e8', '#0654a1' );

	/**
	 * Palette détectée (jusqu'à 6 couleurs, la 1re = principale).
	 * Utilise le cache s'il existe, sinon les sources structurées (sans HTTP).
	 *
	 * @return string[] Liste de hex.
	 */
	public static function palette() {
		$cached = get_option( self::OPTION, null );
		if ( is_array( $cached ) && ! empty( $cached['colors'] ) && ( $cached['ver'] ?? '' ) === FREECOOKIE_VERSION ) {
			return $cached['colors'];
		}
		// Pas de cache valide (1re fois ou mise à jour du plugin) :
		// on calcule à partir des sources structurées (sans réseau) et on mémorise.
		$colors = self::rank( self::structured(), array() );
		update_option( self::OPTION, array( 'time' => time(), 'deep' => false, 'ver' => FREECOOKIE_VERSION, 'colors' => $colors ), false );
		return $colors;
	}

	/**
	 * Couleur principale détectée, ou '' si rien.
	 *
	 * @return string
	 */
	public static function primary() {
		$p = self::palette();
		return $p ? $p[0] : '';
	}

	/**
	 * Détection complète (structuré + fréquence), mise en cache.
	 *
	 * @param bool $deep Inclure l'analyse de fréquence (HTTP).
	 * @return string[]
	 */
	public static function detect( $deep = true ) {
		$structured = self::structured();
		$frequency  = $deep ? self::frequency() : array();
		$colors     = self::rank( $structured, $frequency );

		update_option(
			self::OPTION,
			array(
				'time'   => time(),
				'deep'   => (bool) $deep,
				'ver'    => FREECOOKIE_VERSION,
				'colors' => $colors,
			),
			false
		);
		return $colors;
	}

	/* ------------------------------------------------------------------ */
	/* Sources structurées (sans requête HTTP)                             */
	/* ------------------------------------------------------------------ */

	/**
	 * @return array<string,int> hex => score
	 */
	protected static function structured() {
		$scores = array();

		// 0) Logo du site — le signal de marque le plus fiable.
		$i = 0;
		foreach ( self::logo_colors() as $hex ) {
			self::add_score( $scores, $hex, 1200 - $i * 250 );
			$i++;
		}

		// 1) Kit Elementor.
		$kit_id = (int) get_option( 'elementor_active_kit' );
		if ( $kit_id ) {
			$s = get_post_meta( $kit_id, '_elementor_page_settings', true );
			if ( is_array( $s ) ) {
				$weight = array( 'primary' => 1000, 'secondary' => 700, 'accent' => 600, 'text' => 0 );
				if ( ! empty( $s['system_colors'] ) && is_array( $s['system_colors'] ) ) {
					foreach ( $s['system_colors'] as $c ) {
						if ( empty( $c['color'] ) || self::is_factory_default( $c['color'] ) ) {
							continue; // couleur d'usine Elementor → pas une vraie couleur de marque.
						}
						$id = $c['_id'] ?? '';
						$w  = $weight[ $id ] ?? 500;
						self::add( $scores, $c['color'], $w );
					}
				}
				// Couleurs personnalisées : les premières priment (le client les met en tête).
				if ( ! empty( $s['custom_colors'] ) && is_array( $s['custom_colors'] ) ) {
					$i = 0;
					foreach ( $s['custom_colors'] as $c ) {
						if ( ! empty( $c['color'] ) ) {
							self::add( $scores, $c['color'], max( 250, 520 - $i * 40 ) );
							$i++;
						}
					}
				}
			}
		}

		// 2) theme.json (thèmes à blocs).
		if ( function_exists( 'wp_get_global_settings' ) ) {
			$palette = wp_get_global_settings( array( 'color', 'palette' ) );
			$flat    = array();
			if ( is_array( $palette ) ) {
				foreach ( array( 'theme', 'custom' ) as $origin ) {
					if ( ! empty( $palette[ $origin ] ) && is_array( $palette[ $origin ] ) ) {
						$flat = array_merge( $flat, $palette[ $origin ] );
					}
				}
			}
			$weight = array( 'primary' => 800, 'accent' => 700, 'secondary' => 600 );
			foreach ( $flat as $entry ) {
				if ( empty( $entry['color'] ) ) {
					continue;
				}
				$w = $weight[ $entry['slug'] ?? '' ] ?? 300;
				self::add( $scores, $entry['color'], $w );
			}
		}

		// 3) Theme mods (réglages du personnalisateur), scan récursif des hex.
		$mods = get_theme_mods();
		if ( is_array( $mods ) ) {
			self::walk_mods( $mods, $scores );
		}

		// 4) Options de thèmes/constructeurs populaires (couleurs souvent en option).
		foreach ( array( 'astra-settings', 'kadence_global_palette', 'generate_settings', 'oceanwp_customizer', 'blocksy_options', 'fl_builder_settings' ) as $opt ) {
			$val = get_option( $opt );
			if ( $val ) {
				self::walk_mods( $val, $scores );
			}
		}

		return $scores;
	}

	/**
	 * Parcourt récursivement les theme mods pour y trouver des couleurs.
	 *
	 * @param mixed              $value  Valeur.
	 * @param array<string,int>  $scores Accumulateur (par référence).
	 */
	protected static function walk_mods( $value, array &$scores ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $v ) {
				self::walk_mods( $v, $scores );
			}
		} elseif ( is_string( $value ) ) {
			foreach ( self::extract_colors( $value ) as $hex ) {
				self::add( $scores, $hex, 250 );
			}
		}
	}

	/* ------------------------------------------------------------------ */
	/* Logo du site                                                        */
	/* ------------------------------------------------------------------ */

	/**
	 * Couleurs dominantes du logo (custom_logo, sinon icône du site).
	 *
	 * @return string[]
	 */
	protected static function logo_colors() {
		$id = (int) get_theme_mod( 'custom_logo' );
		if ( ! $id ) {
			$id = (int) get_option( 'site_icon' );
		}
		if ( ! $id ) {
			return array();
		}
		$path = get_attached_file( $id );
		if ( ! $path || ! is_readable( $path ) ) {
			return array();
		}
		if ( preg_match( '/\.svg$/i', $path ) ) {
			return self::svg_colors( $path );
		}
		return self::dominant_from_file( $path, 3 );
	}

	/**
	 * Couleurs de marque extraites d'un logo SVG (fichier vectoriel = texte XML).
	 *
	 * @param string $path Chemin du .svg.
	 * @return string[]
	 */
	public static function svg_colors( $path ) {
		$svg = @file_get_contents( $path, false, null, 0, 300000 );
		if ( ! $svg ) {
			return array();
		}
		// On ignore les couleurs de balises <style> génériques ? Non : on prend tout,
		// puis on filtre les neutres et on classe par fréquence d'apparition.
		$cols = self::extract_colors( $svg );

		// Couleurs nommées courantes (dont les bordeaux/rouges profonds).
		$named = array(
			'maroon'    => '#800000',
			'darkred'   => '#8b0000',
			'firebrick' => '#b22222',
			'crimson'   => '#dc143c',
			'brown'     => '#a52a2a',
			'indigo'    => '#4b0082',
			'purple'    => '#800080',
			'teal'      => '#008080',
			'navy'      => '#000080',
			'olive'     => '#808000',
		);
		foreach ( $named as $name => $hex ) {
			if ( preg_match( '/[:"\'\s=]' . $name . '[;"\'\s<]/i', $svg ) ) {
				$cols[] = $hex;
			}
		}

		$tally = array();
		foreach ( $cols as $hex ) {
			if ( self::is_brandish( $hex ) ) {
				$tally[ $hex ] = ( $tally[ $hex ] ?? 0 ) + 1;
			}
		}
		if ( empty( $tally ) ) {
			return array();
		}
		arsort( $tally );
		return array_slice( array_keys( $tally ), 0, 3 );
	}

	/**
	 * Couleurs dominantes d'une image matricielle (via GD), teintes de marque
	 * uniquement, la plus fréquente en tête. Ignore le transparent et les SVG.
	 *
	 * @param string $path Chemin du fichier.
	 * @param int    $max  Nombre de couleurs.
	 * @return string[]
	 */
	public static function dominant_from_file( $path, $max = 3 ) {
		if ( ! $path || ! is_readable( $path ) || ! extension_loaded( 'gd' ) ) {
			return array();
		}
		$info = @getimagesize( $path );
		if ( ! $info ) {
			return array(); // pas une image matricielle (ex. SVG).
		}
		switch ( $info[2] ) {
			case IMAGETYPE_JPEG:
				$img = @imagecreatefromjpeg( $path );
				break;
			case IMAGETYPE_PNG:
				$img = @imagecreatefrompng( $path );
				break;
			case IMAGETYPE_GIF:
				$img = @imagecreatefromgif( $path );
				break;
			case IMAGETYPE_WEBP:
				$img = function_exists( 'imagecreatefromwebp' ) ? @imagecreatefromwebp( $path ) : false;
				break;
			default:
				return array();
		}
		if ( ! $img ) {
			return array();
		}

		$w  = imagesx( $img );
		$h  = imagesy( $img );
		$mx = max( $w, $h );
		$sc = $mx > 56 ? 56 / $mx : 1;
		$sw = max( 1, (int) ( $w * $sc ) );
		$sh = max( 1, (int) ( $h * $sc ) );

		$small = imagecreatetruecolor( $sw, $sh );
		imagealphablending( $small, false );
		imagesavealpha( $small, true );
		imagecopyresampled( $small, $img, 0, 0, 0, 0, $sw, $sh, $w, $h );

		$tally = array();
		for ( $y = 0; $y < $sh; $y++ ) {
			for ( $x = 0; $x < $sw; $x++ ) {
				$rgba = imagecolorat( $small, $x, $y );
				$alpha = ( $rgba >> 24 ) & 0x7F; // 0 = opaque, 127 = transparent.
				if ( $alpha > 64 ) {
					continue;
				}
				$r   = ( $rgba >> 16 ) & 0xFF;
				$g   = ( $rgba >> 8 ) & 0xFF;
				$b   = $rgba & 0xFF;
				$hex = self::rgb_hex( $r, $g, $b );
				if ( ! self::is_brandish( $hex ) ) {
					continue;
				}
				// Quantification pour regrouper les nuances proches.
				$q            = self::rgb_hex( (int) ( round( $r / 24 ) * 24 ), (int) ( round( $g / 24 ) * 24 ), (int) ( round( $b / 24 ) * 24 ) );
				$tally[ $q ]  = ( $tally[ $q ] ?? 0 ) + 1;
			}
		}
		if ( empty( $tally ) ) {
			return array();
		}
		arsort( $tally );
		return array_slice( array_keys( $tally ), 0, $max );
	}

	/* ------------------------------------------------------------------ */
	/* Analyse de fréquence (HTTP local)                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * @return array<string,int> hex => occurrences
	 */
	protected static function frequency() {
		$counts = array();

		$home = wp_remote_get(
			home_url( '/' ),
			array( 'timeout' => 10, 'sslverify' => false, 'user-agent' => 'FreeCookie-Colors/' . FREECOOKIE_VERSION )
		);
		if ( is_wp_error( $home ) ) {
			return $counts;
		}
		$html = (string) wp_remote_retrieve_body( $home );

		// On retire notre propre bloc de variables pour ne pas s'auto-compter.
		$html = preg_replace( '/#freecookie-root,#freecookie-badge\{[^}]*\}/', '', $html );

		// Couleurs présentes dans le HTML (styles inline, blocs <style>).
		foreach ( self::extract_colors( $html ) as $hex ) {
			self::add( $counts, $hex, 1 );
		}

		// Feuilles de style de MÊME origine (hors FreeCookie), bornées.
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( preg_match_all( '#<link[^>]+rel=["\']stylesheet["\'][^>]*href=["\']([^"\']+)["\']#i', $html, $m ) ) {
			$done = 0;
			foreach ( $m[1] as $href ) {
				if ( $done >= 6 ) {
					break;
				}
				if ( false !== stripos( $href, 'freecookie' ) ) {
					continue;
				}
				$url = self::abs_url( $href );
				if ( wp_parse_url( $url, PHP_URL_HOST ) !== $host ) {
					continue;
				}
				$resp = wp_remote_get( $url, array( 'timeout' => 8, 'sslverify' => false ) );
				if ( is_wp_error( $resp ) ) {
					continue;
				}
				$css = substr( (string) wp_remote_retrieve_body( $resp ), 0, 500000 );
				foreach ( self::extract_colors( $css ) as $hex ) {
					self::add( $counts, $hex, 1 );
				}
				$done++;
			}
		}

		// La fréquence est un signal fort de la vraie couleur de marque : on l'amplifie,
		// mais on plafonne pour qu'une couleur omniprésente ne noie pas tout.
		foreach ( $counts as $hex => $n ) {
			$counts[ $hex ] = min( $n, 150 ) * 6;
		}
		return $counts;
	}

	/* ------------------------------------------------------------------ */
	/* Fusion / classement / filtrage                                      */
	/* ------------------------------------------------------------------ */

	/**
	 * Fusionne deux jeux de scores, écarte les neutres, fusionne les teintes
	 * proches, et renvoie les hex classés (max 6).
	 *
	 * @param array<string,int> $a Scores A.
	 * @param array<string,int> $b Scores B.
	 * @return string[]
	 */
	protected static function rank( array $a, array $b ) {
		$scores = $a;
		foreach ( $b as $hex => $n ) {
			self::add_score( $scores, $hex, $n );
		}

		// Filtre neutres.
		$scores = array_filter(
			$scores,
			function ( $score, $hex ) {
				return self::is_brandish( $hex );
			},
			ARRAY_FILTER_USE_BOTH
		);

		arsort( $scores );

		// Fusionne les couleurs perceptuellement proches (garde la mieux notée).
		$kept = array();
		foreach ( $scores as $hex => $score ) {
			$merged = false;
			foreach ( $kept as $k => $ks ) {
				if ( self::distance( $hex, $k ) < 26 ) {
					$kept[ $k ] += $score;
					$merged      = true;
					break;
				}
			}
			if ( ! $merged ) {
				$kept[ $hex ] = $score;
			}
		}
		arsort( $kept );

		return array_slice( array_keys( $kept ), 0, 6 );
	}

	/* ------------------------------------------------------------------ */
	/* Utilitaires couleur                                                 */
	/* ------------------------------------------------------------------ */

	/**
	 * Extrait tous les hex (#rgb/#rrggbb) et rgb()/rgba() d'un texte → hex normalisés.
	 *
	 * @param string $text Texte.
	 * @return string[]
	 */
	public static function extract_colors( $text ) {
		$out = array();

		if ( preg_match_all( '/#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})\b/', $text, $m ) ) {
			foreach ( $m[1] as $hex ) {
				$out[] = self::norm_hex( '#' . $hex );
			}
		}
		if ( preg_match_all( '/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/i', $text, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $set ) {
				$out[] = self::rgb_hex( (int) $set[1], (int) $set[2], (int) $set[3] );
			}
		}
		return $out;
	}

	/**
	 * Normalise un hex en #rrggbb minuscule.
	 *
	 * @param string $hex Couleur.
	 * @return string
	 */
	protected static function norm_hex( $hex ) {
		$hex = strtolower( ltrim( $hex, '#' ) );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		return '#' . $hex;
	}

	/**
	 * @param int $r Rouge.
	 * @param int $g Vert.
	 * @param int $b Bleu.
	 * @return string
	 */
	protected static function rgb_hex( $r, $g, $b ) {
		$c = function ( $x ) {
			return str_pad( dechex( max( 0, min( 255, $x ) ) ), 2, '0', STR_PAD_LEFT );
		};
		return '#' . $c( $r ) . $c( $g ) . $c( $b );
	}

	/**
	 * [r,g,b] d'un hex normalisé.
	 *
	 * @param string $hex Couleur.
	 * @return int[]
	 */
	protected static function rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
	}

	/**
	 * Une couleur « de marque » ? (ni blanc/noir, ni gris).
	 *
	 * @param string $hex Couleur.
	 * @return bool
	 */
	public static function is_brandish( $hex ) {
		list( $r, $g, $b ) = self::rgb( $hex );
		$mx = max( $r, $g, $b );
		$mn = min( $r, $g, $b );
		if ( 0 === $mx ) {
			return false;
		}
		$l = ( $mx + $mn ) / 2;
		$s = ( $mx - $mn ) / $mx;
		if ( $l > 232 || $l < 18 ) {
			return false; // trop clair / trop sombre.
		}
		if ( $s < 0.22 ) {
			return false; // gris.
		}
		return true;
	}

	/**
	 * La couleur correspond-elle à une teinte « d'usine » connue (Elementor, etc.) ?
	 *
	 * @param string $raw Couleur brute.
	 * @return bool
	 */
	protected static function is_factory_default( $raw ) {
		$c = self::extract_colors( (string) $raw );
		return ! empty( $c ) && in_array( $c[0], self::FACTORY_DEFAULTS, true );
	}

	/**
	 * Distance euclidienne RGB entre deux hex.
	 *
	 * @param string $a Couleur A.
	 * @param string $b Couleur B.
	 * @return float
	 */
	protected static function distance( $a, $b ) {
		list( $r1, $g1, $b1 ) = self::rgb( $a );
		list( $r2, $g2, $b2 ) = self::rgb( $b );
		return sqrt( ( $r1 - $r2 ) ** 2 + ( $g1 - $g2 ) ** 2 + ( $b1 - $b2 ) ** 2 );
	}

	/**
	 * Ajoute un score pour une couleur brute (validée + normalisée).
	 *
	 * @param array<string,int> $scores Accumulateur.
	 * @param string            $raw    Couleur brute.
	 * @param int               $weight Poids.
	 */
	protected static function add( array &$scores, $raw, $weight ) {
		$colors = self::extract_colors( (string) $raw );
		if ( empty( $colors ) ) {
			return;
		}
		self::add_score( $scores, $colors[0], $weight );
	}

	/**
	 * @param array<string,int> $scores Accumulateur.
	 * @param string            $hex    Hex normalisé.
	 * @param int               $weight Poids.
	 */
	protected static function add_score( array &$scores, $hex, $weight ) {
		$hex = self::norm_hex( $hex );
		$scores[ $hex ] = ( $scores[ $hex ] ?? 0 ) + $weight;
	}

	/**
	 * Résout une URL éventuellement relative/protocole-relative.
	 *
	 * @param string $href Href.
	 * @return string
	 */
	protected static function abs_url( $href ) {
		if ( 0 === strpos( $href, '//' ) ) {
			return ( is_ssl() ? 'https:' : 'http:' ) . $href;
		}
		if ( 0 === strpos( $href, 'http' ) ) {
			return $href;
		}
		return home_url( $href );
	}
}
