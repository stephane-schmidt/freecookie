<?php
/**
 * Markup du bandeau + centre de préférences.
 * Variables disponibles : $strings (array), $cats (FC_Categories::all()).
 * Masqué par défaut ; c'est le JS qui décide de l'afficher selon le cookie.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="freecookie-root" hidden>
	<div id="freecookie-banner" class="fc-banner" role="dialog" aria-modal="false"
		aria-labelledby="fc-title" aria-describedby="fc-desc" data-fc-state="banner">
		<div class="fc-inner">
			<div class="fc-text">
				<h2 id="fc-title" class="fc-title"><?php echo esc_html( $strings['title'] ); ?></h2>
				<p id="fc-desc" class="fc-desc"><?php echo esc_html( $strings['body'] ); ?></p>
			</div>

			<div class="fc-actions">
				<button type="button" class="fc-btn fc-btn--ghost" data-fc="customize"><?php echo esc_html( $strings['customize'] ); ?></button>
				<button type="button" class="fc-btn fc-btn--secondary" data-fc="reject"><?php echo esc_html( $strings['reject_all'] ); ?></button>
				<button type="button" class="fc-btn fc-btn--primary" data-fc="accept"><?php echo esc_html( $strings['accept_all'] ); ?></button>
			</div>
		</div>

		<div class="fc-prefs" data-fc-panel hidden>
			<h3 class="fc-prefs__title"><?php echo esc_html( $strings['prefs_title'] ); ?></h3>
			<ul class="fc-cats">
				<?php foreach ( $cats as $key => $def ) : ?>
					<?php
					$label = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
					$desc  = isset( $strings[ $key . '_d' ] ) ? $strings[ $key . '_d' ] : '';
					$locked = ! empty( $def['locked'] );
					?>
					<li class="fc-cat">
						<label class="fc-cat__row">
							<span class="fc-cat__name"><?php echo esc_html( $label ); ?></span>
							<?php if ( $locked ) : ?>
								<span class="fc-cat__lock"><?php echo esc_html( $strings['always_on'] ); ?></span>
								<input type="checkbox" checked disabled aria-label="<?php echo esc_attr( $label ); ?>">
							<?php else : ?>
								<input type="checkbox" class="fc-toggle" data-fc-cat="<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
							<?php endif; ?>
						</label>
						<?php if ( $desc ) : ?>
							<p class="fc-cat__desc"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="fc-prefs__actions">
				<button type="button" class="fc-btn fc-btn--secondary" data-fc="reject"><?php echo esc_html( $strings['reject_all'] ); ?></button>
				<button type="button" class="fc-btn fc-btn--primary" data-fc="save"><?php echo esc_html( $strings['save'] ); ?></button>
			</div>
		</div>
	</div>
</div>

<button type="button" id="freecookie-badge" class="fc-badge" hidden aria-label="<?php echo esc_attr( $strings['manage'] ); ?>" title="<?php echo esc_attr( $strings['manage'] ); ?>">
	<svg class="fc-cookie" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
		<circle class="fc-cookie__disc" cx="32" cy="32" r="28"/>
		<path class="fc-cookie__sheen" d="M13 21a23 23 0 0 1 15-9"/>
		<g class="fc-cookie__chip">
			<ellipse cx="22" cy="20" rx="4.2" ry="3.4" transform="rotate(-18 22 20)"/>
			<ellipse cx="41" cy="23" rx="3.6" ry="3" transform="rotate(12 41 23)"/>
			<ellipse cx="26" cy="39" rx="4" ry="3.3" transform="rotate(24 26 39)"/>
			<ellipse cx="43" cy="41" rx="3.8" ry="3.2" transform="rotate(-14 43 41)"/>
			<ellipse cx="33" cy="30" rx="3" ry="2.6" transform="rotate(8 33 30)"/>
			<ellipse cx="17" cy="31" rx="2.6" ry="2.2"/>
			<ellipse cx="36" cy="14" rx="2.3" ry="2"/>
		</g>
		<g class="fc-cookie__speckle">
			<circle cx="31" cy="45" r="1"/><circle cx="46" cy="33" r="1.1"/>
			<circle cx="15" cy="39" r=".9"/><circle cx="38" cy="48" r=".9"/><circle cx="25" cy="12" r=".9"/>
		</g>
	</svg>
</button>
