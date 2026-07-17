<?php
/**
 * Liste de cookies publique — shortcode [freecookie_cookies].
 * Tableau par finalité, composé à partir du dernier scan + base connue.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Freecookie_Cookie_List {

	/** En-têtes de colonnes par langue. */
	protected static function headers( $lang ) {
		$h = array(
			'fr' => array( 'name' => 'Cookie', 'service' => 'Service', 'score' => 'Risque', 'duration' => 'Durée', 'desc' => 'Finalité' ),
			'en' => array( 'name' => 'Cookie', 'service' => 'Service', 'score' => 'Risk', 'duration' => 'Duration', 'desc' => 'Purpose' ),
			'de' => array( 'name' => 'Cookie', 'service' => 'Dienst', 'score' => 'Risiko', 'duration' => 'Dauer', 'desc' => 'Zweck' ),
			'it' => array( 'name' => 'Cookie', 'service' => 'Servizio', 'score' => 'Rischio', 'duration' => 'Durata', 'desc' => 'Finalità' ),
			'es' => array( 'name' => 'Cookie', 'service' => 'Servicio', 'score' => 'Riesgo', 'duration' => 'Duración', 'desc' => 'Finalidad' ),
			'nl' => array( 'name' => 'Cookie', 'service' => 'Dienst', 'score' => 'Risico', 'duration' => 'Duur', 'desc' => 'Doel' ),
			'pt' => array( 'name' => 'Cookie', 'service' => 'Serviço', 'score' => 'Risco', 'duration' => 'Duração', 'desc' => 'Finalidade' ),
		);
		return isset( $h[ $lang ] ) ? $h[ $lang ] : $h['en'];
	}

	/** « Ce site » dans la langue servie (cookies internes, sans service tiers). */
	protected static function site_label( $lang ) {
		$l = array( 'fr' => 'Ce site', 'en' => 'This site', 'de' => 'Diese Website', 'it' => 'Questo sito', 'es' => 'Este sitio', 'nl' => 'Deze site', 'pt' => 'Este site' );
		return isset( $l[ $lang ] ) ? $l[ $lang ] : $l['en'];
	}

	/**
	 * Rendu du shortcode.
	 *
	 * @return string HTML.
	 */
	public static function render() {
		$lang    = Freecookie_I18n::detect();
		$strings = Freecookie_I18n::get( $lang );
		$head    = self::headers( $lang );
		$report  = Freecookie_Scanner::report( $lang );

		if ( empty( $report ) ) {
			return '';
		}

		$order = array_keys( Freecookie_Categories::all() );
		$out   = '<div class="fc-cookie-list">';

		foreach ( $order as $cat ) {
			$label = isset( $strings[ $cat ] ) ? $strings[ $cat ] : $cat;

			if ( 'necessary' === $cat ) {
				// Toujours affichée, même sans service détecté.
				$out .= '<h3 class="fc-cl__cat">' . esc_html( $label ) . '</h3>';
				$out .= '<p class="fc-cl__note">' . esc_html( $strings['necessary_d'] ) . '</p>';
			}

			if ( empty( $report[ $cat ] ) ) {
				continue;
			}

			if ( 'necessary' !== $cat ) {
				$out .= '<h3 class="fc-cl__cat">' . esc_html( $label ) . '</h3>';
			}

			$out .= '<div class="fc-cl__scroll"><table class="fc-cl__table"><thead><tr>'
				. '<th>' . esc_html( $head['name'] ) . '</th>'
				. '<th>' . esc_html( $head['service'] ) . '</th>'
				. '<th>' . esc_html( $head['score'] ) . '</th>'
				. '<th>' . esc_html( $head['duration'] ) . '</th>'
				. '<th>' . esc_html( $head['desc'] ) . '</th>'
				. '</tr></thead><tbody>';

			foreach ( $report[ $cat ] as $c ) {
				if ( '' === $c['service'] ) {
					// Cookie interne du site : risque faible par définition.
					$meta = array( 'score' => 9 );
					$svc  = self::site_label( $lang );
				} else {
					$meta = Freecookie_Categories::meta( $c['service'] );
					$svc  = Freecookie_Categories::service_label( $c['service'] );
				}
				$color = Freecookie_Categories::score_color( $meta['score'] );
				$risk  = Freecookie_Categories::risk_key( $meta['score'] );
				$rlbl  = isset( $strings[ 'risk_' . $risk ] ) ? $strings[ 'risk_' . $risk ] : $risk;
				$out  .= '<tr>'
					. '<td><code>' . esc_html( $c['name'] ) . '</code></td>'
					. '<td>' . esc_html( $svc ) . '</td>'
					. '<td><span class="fc-score fc-score--' . esc_attr( $color ) . '">' . esc_html( $rlbl ) . '</span></td>'
					. '<td>' . esc_html( $c['duration'] ) . '</td>'
					. '<td>' . esc_html( $c['desc'] ) . '</td>'
					. '</tr>';
			}
			$out .= '</tbody></table></div>';
		}

		$out .= '</div>';
		return $out;
	}
}
