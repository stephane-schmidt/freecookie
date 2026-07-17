<?php
/**
 * Markup du bandeau — vue UNIQUE : les catégories, les traceurs détectés
 * (description + niveau de risque) et leurs interrupteurs sont visibles
 * directement, sans étape « Personnaliser ».
 *
 * Variables : $strings, $cats, $services, $about, $alabels, $shape.
 * Masqué par défaut ; c'est le JS qui décide de l'afficher selon le cookie.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="freecookie-root"<?php echo ! empty( $fc_rtl ) ? ' dir="rtl"' : ''; ?> hidden>
	<div id="freecookie-banner" class="fc-banner" role="dialog" aria-modal="true"
		aria-labelledby="fc-title" aria-describedby="fc-desc" data-fc-state="banner">
		<div class="fc-inner">
			<div class="fc-text">
				<h2 id="fc-title" class="fc-title"><?php echo esc_html( $strings['title'] ); ?></h2>
				<p id="fc-desc" class="fc-desc"><?php echo esc_html( $strings['body'] ); ?></p>
			</div>

			<ul class="fc-cats">
				<?php foreach ( $cats as $key => $def ) : ?>
					<?php
					$label  = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
					$desc   = isset( $strings[ $key . '_d' ] ) ? $strings[ $key . '_d' ] : '';
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
						<?php if ( $locked && 'necessary' === $key && ! empty( $necessary_cookies ) ) : ?>
							<details class="fc-ck-details">
								<summary><?php echo esc_html( $strings['ck_details'] . ' (' . count( $necessary_cookies ) . ')' ); ?></summary>
								<?php foreach ( $necessary_cookies as $fc_nk ) : ?>
									<dl class="fc-ck">
										<div><dt><?php echo esc_html( $strings['ck_cookie'] ); ?></dt><dd><code><?php echo esc_html( $fc_nk['name'] ); ?></code></dd></div>
										<?php if ( '' !== $fc_nk['duration'] ) : ?>
											<div><dt><?php echo esc_html( $strings['ck_duration'] ); ?></dt><dd><?php echo esc_html( $fc_nk['duration'] ); ?></dd></div>
										<?php endif; ?>
										<?php if ( '' !== $fc_nk['desc'] ) : ?>
											<div><dt><?php echo esc_html( $strings['ck_desc'] ); ?></dt><dd><?php echo esc_html( $fc_nk['desc'] ); ?></dd></div>
										<?php endif; ?>
									</dl>
								<?php endforeach; ?>
							</details>
						<?php endif; ?>
						<?php if ( ! $locked && ! empty( $services[ $key ] ) ) : ?>
							<ul class="fc-svcs">
								<?php foreach ( $services[ $key ] as $svc ) : ?>
									<?php $fc_risk = isset( $strings[ 'risk_' . $svc['risk'] ] ) ? $strings[ 'risk_' . $svc['risk'] ] : ''; ?>
									<li class="fc-svc">
										<label class="fc-svc__row">
											<span class="fc-svc__main">
												<span class="fc-svc__name"><?php echo esc_html( $svc['label'] ); ?></span>
												<?php if ( $fc_risk ) : ?>
													<button type="button" class="fc-score fc-score--<?php echo esc_attr( $svc['color'] ); ?>" data-fc="edu"
														title="<?php echo esc_attr( $strings['edu_open'] ); ?>"
														aria-label="<?php echo esc_attr( $fc_risk . ' — ' . $strings['edu_open'] ); ?>"><?php echo esc_html( $fc_risk ); ?></button>
												<?php endif; ?>
											</span>
											<input type="checkbox" class="fc-svc-toggle" disabled
												data-fc-svc="<?php echo esc_attr( $svc['key'] ); ?>"
												data-fc-cat="<?php echo esc_attr( $key ); ?>"
												aria-label="<?php echo esc_attr( $svc['label'] . ( $fc_risk ? ' — ' . $fc_risk : '' ) ); ?>">
										</label>
										<?php if ( ! empty( $svc['purpose'] ) ) : ?>
											<p class="fc-svc__desc"><?php echo esc_html( $svc['purpose'] ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $svc['cookies'] ) ) : ?>
											<details class="fc-ck-details">
												<summary><?php echo esc_html( $strings['ck_details'] . ' (' . count( $svc['cookies'] ) . ')' ); ?></summary>
												<?php foreach ( $svc['cookies'] as $fc_ck ) : ?>
													<dl class="fc-ck">
														<div><dt><?php echo esc_html( $strings['ck_cookie'] ); ?></dt><dd><code><?php echo esc_html( $fc_ck['name'] ); ?></code></dd></div>
														<div><dt><?php echo esc_html( $strings['ck_duration'] ); ?></dt><dd><?php echo esc_html( $fc_ck['duration'] ); ?></dd></div>
														<div><dt><?php echo esc_html( $strings['ck_desc'] ); ?></dt><dd><?php echo esc_html( $fc_ck['desc'] ); ?></dd></div>
													</dl>
												<?php endforeach; ?>
											</details>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( ! empty( $no_trackers ) ) : ?>
				<p class="fc-note"><span class="fc-note__dot" aria-hidden="true"></span><?php echo esc_html( $strings['no_trackers'] ); ?></p>
			<?php endif; ?>

			<div class="fc-actions">
				<button type="button" class="fc-btn fc-btn--secondary" data-fc="save"><?php echo esc_html( $strings['save'] ); ?></button>
				<button type="button" class="fc-btn fc-btn--primary" data-fc="reject"><?php echo esc_html( $strings['reject_all'] ); ?></button>
				<button type="button" class="fc-btn fc-btn--primary" data-fc="accept"><?php echo esc_html( $strings['accept_all'] ); ?></button>
			</div>

			<div class="fc-foot">
				<button type="button" class="fc-link" data-fc="edu"><?php echo esc_html( $strings['edu_open'] ); ?></button>
				<?php if ( ! empty( $about['enabled'] ) ) : ?>
					<button type="button" class="fc-link" data-fc="about"><?php echo esc_html( $alabels['about'] ); ?></button>
				<?php endif; ?>
			</div>
		</div>

		<div class="fc-edu" data-fc-edu hidden>
			<h3 class="fc-edu__title"><?php echo esc_html( $strings['edu_title'] ); ?></h3>
			<p class="fc-edu__intro"><?php echo esc_html( $strings['edu_intro'] ); ?></p>
			<ul class="fc-edu__list">
				<li class="fc-edu__item fc-edu__item--useful">
					<span class="fc-edu__dot" aria-hidden="true"></span>
					<div><strong><?php echo esc_html( $strings['edu_useful_t'] ); ?></strong>
					<p><?php echo esc_html( $strings['edu_useful_d'] ); ?></p></div>
				</li>
				<li class="fc-edu__item fc-edu__item--mixed">
					<span class="fc-edu__dot" aria-hidden="true"></span>
					<div><strong><?php echo esc_html( $strings['edu_mixed_t'] ); ?></strong>
					<p><?php echo esc_html( $strings['edu_mixed_d'] ); ?></p></div>
				</li>
				<li class="fc-edu__item fc-edu__item--danger">
					<span class="fc-edu__dot" aria-hidden="true"></span>
					<div><strong><?php echo esc_html( $strings['edu_danger_t'] ); ?></strong>
					<p><?php echo esc_html( $strings['edu_danger_d'] ); ?></p></div>
				</li>
			</ul>
			<div class="fc-edu__actions">
				<button type="button" class="fc-btn fc-btn--secondary" data-fc="edu-back"><?php echo esc_html( $strings['edu_back'] ); ?></button>
			</div>
		</div>

		<?php if ( ! empty( $about['enabled'] ) ) : ?>
			<?php
			$fc_social = ! empty( $about['social'] ) && is_array( $about['social'] ) ? array_filter( $about['social'] ) : array();
			$fc_labels = array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'github' => 'GitHub', 'linkedin' => 'LinkedIn', 'x' => 'X', 'youtube' => 'YouTube', 'behance' => 'Behance' );
			?>
			<div class="fc-about" data-fc-about hidden>
				<h3 class="fc-about__title"><?php echo esc_html( $about['name'] ? $about['name'] : 'FreeCookie' ); ?></h3>
				<p class="fc-about__promo"><?php echo esc_html( $alabels['promo'] ); ?></p>
				<div class="fc-about__social">
					<?php if ( ! empty( $about['website'] ) ) : ?>
						<a href="<?php echo esc_url( $about['website'] ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( wp_parse_url( $about['website'], PHP_URL_HOST ) ? wp_parse_url( $about['website'], PHP_URL_HOST ) : 'Site' ); ?></a>
					<?php endif; ?>
					<?php foreach ( $fc_social as $fc_net => $fc_url ) : ?>
						<a href="<?php echo esc_url( $fc_url ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( isset( $fc_labels[ $fc_net ] ) ? $fc_labels[ $fc_net ] : ucfirst( $fc_net ) ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php if ( ! empty( $about['donate'] ) ) : ?>
					<div class="fc-about__coffee">
						<a href="<?php echo esc_url( $about['donate'] ); ?>" target="_blank" rel="noopener nofollow" class="fc-btn fc-btn--primary"><?php echo esc_html( $alabels['coffee'] ); ?></a>
					</div>
				<?php endif; ?>
				<div class="fc-about__actions">
					<button type="button" class="fc-btn fc-btn--secondary" data-fc="about-back"><?php echo esc_html( $alabels['back'] ); ?></button>
				</div>
			</div>
		<?php endif; ?>

		<span class="fc-sr" aria-live="polite" data-fc-live></span>
	</div>
</div>

<button type="button" id="freecookie-badge" class="fc-badge" hidden aria-expanded="false" aria-label="<?php echo esc_attr( $strings['manage'] ); ?>" title="<?php echo esc_attr( $strings['manage'] ); ?>">
	<svg class="fc-cookie" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><?php echo Freecookie_Shapes::get( $shape ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne statique de confiance. ?></svg>
</button>
