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
		<defs>
			<mask id="fc-bite">
				<rect width="64" height="64" fill="#fff"/>
				<g fill="#000">
					<circle cx="60" cy="32" r="13"/>
					<circle cx="48" cy="24" r="4.5"/>
					<circle cx="48" cy="40" r="4.5"/>
					<circle cx="45" cy="32" r="4"/>
				</g>
			</mask>
		</defs>
		<circle class="fc-cookie__disc" cx="32" cy="32" r="27" mask="url(#fc-bite)"/>
		<g class="fc-cookie__hole" mask="url(#fc-bite)">
			<circle cx="24" cy="24" r="3.4"/>
			<circle cx="24" cy="40" r="3.4"/>
			<circle cx="30" cy="32" r="2.6"/>
			<circle cx="20" cy="32" r="2.1"/>
		</g>
	</svg>
</button>
