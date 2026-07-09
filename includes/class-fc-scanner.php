<?php
/**
 * Scanner de cookies LOCAL.
 *
 * Récupère quelques pages du site LUI-MÊME (boucle locale, pas un service tiers),
 * y détecte les services connus, puis compose la liste de cookies à partir de la
 * base livrée avec le plugin. Aucune donnée ne quitte le serveur.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Scanner {

	const OPTION = 'freecookie_scan';

	/**
	 * Détecte les services connus dans un HTML.
	 *
	 * @param string $html HTML d'une page.
	 * @return string[] Clés de services détectés.
	 */
	public static function detect_services( $html ) {
		$found = array();
		foreach ( FC_Categories::known_services() as $key => $svc ) {
			foreach ( $svc['patterns'] as $needle ) {
				if ( false !== stripos( $html, $needle ) ) {
					$found[ $key ] = true;
					break;
				}
			}
		}
		return array_keys( $found );
	}

	/**
	 * URLs du site à examiner : accueil + quelques contenus publiés.
	 *
	 * @param int $max Nombre max d'URLs.
	 * @return string[]
	 */
	public static function gather_urls( $max = 10 ) {
		$urls = array( home_url( '/' ) );

		$posts = get_posts(
			array(
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'publish',
				'posts_per_page' => $max - 1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);
		foreach ( $posts as $id ) {
			$link = get_permalink( $id );
			if ( $link ) {
				$urls[] = $link;
			}
		}
		return array_values( array_unique( $urls ) );
	}

	/**
	 * Lance le scan et mémorise le résultat.
	 *
	 * @return array{time:int,services:string[],scanned:int}
	 */
	public static function scan() {
		$urls     = self::gather_urls();
		$services = array();
		$scanned  = 0;

		foreach ( $urls as $url ) {
			$resp = wp_remote_get(
				$url,
				array(
					'timeout'    => 10,
					'sslverify'  => false, // boucle locale : le certificat local peut être auto-signé.
					'user-agent' => 'FreeCookie-Scanner/' . FREECOOKIE_VERSION,
				)
			);
			if ( is_wp_error( $resp ) ) {
				continue;
			}
			$body = (string) wp_remote_retrieve_body( $resp );
			if ( '' === $body ) {
				continue;
			}
			$scanned++;
			$services = array_merge( $services, self::detect_services( $body ) );
		}

		$services = array_values( array_unique( $services ) );
		$result   = array(
			'time'     => time(),
			'services' => $services,
			'scanned'  => $scanned,
		);
		update_option( self::OPTION, $result, false );
		return $result;
	}

	/**
	 * Dernier résultat de scan.
	 *
	 * @return array|null
	 */
	public static function last() {
		$r = get_option( self::OPTION, null );
		return is_array( $r ) ? $r : null;
	}

	/**
	 * Construit la liste de cookies par finalité (services détectés + base connue).
	 * S'il n'y a pas encore de scan, part de tous les services connus.
	 *
	 * @return array<string,array<int,array<string,string>>> category => cookies[]
	 */
	public static function report() {
		$db       = include FREECOOKIE_DIR . 'includes/data/known-cookies.php';
		$scan     = self::last();
		$services = ( $scan && ! empty( $scan['services'] ) ) ? $scan['services'] : array_keys( FC_Categories::known_services() );
		$map      = FC_Categories::known_services();

		$out = array();
		foreach ( $services as $svc ) {
			if ( empty( $map[ $svc ] ) || empty( $db[ $svc ] ) ) {
				continue;
			}
			$cat = $map[ $svc ]['category'];
			foreach ( $db[ $svc ] as $cookie ) {
				$out[ $cat ][] = array(
					'service'  => $svc,
					'name'     => $cookie['name'],
					'duration' => $cookie['duration'],
					'desc'     => $cookie['desc'],
				);
			}
		}
		return $out;
	}
}
