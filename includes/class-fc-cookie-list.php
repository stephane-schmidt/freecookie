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

class FC_Cookie_List {

	/** En-têtes de colonnes par langue. */
	protected static function headers( $lang ) {
		$h = array(
			'fr' => array( 'name' => 'Cookie', 'service' => 'Service', 'score' => 'Note', 'duration' => 'Durée', 'desc' => 'Finalité' ),
			'en' => array( 'name' => 'Cookie', 'service' => 'Service', 'score' => 'Rating', 'duration' => 'Duration', 'desc' => 'Purpose' ),
			'de' => array( 'name' => 'Cookie', 'service' => 'Dienst', 'score' => 'Note', 'duration' => 'Dauer', 'desc' => 'Zweck' ),
			'it' => array( 'name' => 'Cookie', 'service' => 'Servizio', 'score' => 'Voto', 'duration' => 'Durata', 'desc' => 'Finalità' ),
		);
		return isset( $h[ $lang ] ) ? $h[ $lang ] : $h['en'];
	}

	/**
	 * Rendu du shortcode.
	 *
	 * @return string HTML.
	 */
	public static function render() {
		$lang    = FC_I18n::detect();
		$strings = FC_I18n::get( $lang );
		$head    = self::headers( $lang );
		$report  = FC_Scanner::report();

		if ( empty( $report ) ) {
			return '';
		}

		$order = array_keys( FC_Categories::all() );
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
				$meta  = FC_Categories::meta( $c['service'] );
				$color = FC_Categories::score_color( $meta['score'] );
				$out  .= '<tr>'
					. '<td><code>' . esc_html( $c['name'] ) . '</code></td>'
					. '<td>' . esc_html( FC_Categories::service_label( $c['service'] ) ) . '</td>'
					. '<td><span class="fc-score fc-score--' . esc_attr( $color ) . '">' . (int) $meta['score'] . '/10</span></td>'
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
