<?php
/**
 * Écran d'administration : apparence (couleurs), textes, options, scan.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Admin {

	const PAGE  = 'freecookie';
	const GROUP = 'freecookie';

	/** Champs de couleur : clé => libellé. */
	protected function color_fields() {
		return array(
			'accent'         => __( 'Couleur principale (boutons, badge)', 'freecookie' ),
			'accent_text'    => __( 'Texte sur la couleur principale', 'freecookie' ),
			'bg'             => __( 'Fond de la bannière', 'freecookie' ),
			'text'           => __( 'Texte de la bannière', 'freecookie' ),
			'secondary_bg'   => __( 'Fond du bouton secondaire', 'freecookie' ),
			'secondary_text' => __( 'Texte du bouton secondaire', 'freecookie' ),
			'badge'          => __( 'Couleur du badge cookie', 'freecookie' ),
		);
	}

	/** Textes modifiables : clé => libellé. */
	protected function text_fields() {
		return array(
			'title'       => __( 'Titre', 'freecookie' ),
			'body'        => __( 'Message', 'freecookie' ),
			'accept_all'  => __( 'Bouton « Tout accepter »', 'freecookie' ),
			'reject_all'  => __( 'Bouton « Tout refuser »', 'freecookie' ),
			'customize'   => __( 'Bouton « Personnaliser »', 'freecookie' ),
			'save'        => __( 'Bouton « Enregistrer »', 'freecookie' ),
			'prefs_title' => __( 'Titre du panneau de préférences', 'freecookie' ),
		);
	}

	/** Réseaux sociaux proposés dans le volet À propos : clé => libellé. */
	protected function social_networks() {
		return array(
			'facebook'  => 'Facebook',
			'instagram' => 'Instagram',
			'tiktok'    => 'TikTok',
			'github'    => 'GitHub',
			'linkedin'  => 'LinkedIn',
			'x'         => 'X (Twitter)',
			'youtube'   => 'YouTube',
			'behance'   => 'Behance',
		);
	}

	/**
	 * Enregistre les hooks d'administration.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function menu() {
		add_menu_page(
			'FreeCookie',
			'FreeCookie',
			'manage_options',
			self::PAGE,
			array( $this, 'render' ),
			'dashicons-shield-alt',
			80
		);
	}

	public function settings() {
		register_setting(
			self::GROUP,
			'freecookie_settings',
			array( 'sanitize_callback' => array( $this, 'sanitize' ) )
		);
	}

	/**
	 * Charge le sélecteur de couleur WordPress sur notre page.
	 *
	 * @param string $hook Page courante.
	 */
	public function enqueue( $hook ) {
		if ( 'toplevel_page_' . self::PAGE !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'freecookie-admin', FREECOOKIE_URL . 'admin/admin.js', array( 'wp-color-picker', 'jquery' ), FREECOOKIE_VERSION, true );
	}

	/**
	 * Valide et nettoie les réglages soumis.
	 *
	 * @param array $input Données du formulaire.
	 * @return array
	 */
	public function sanitize( $input ) {
		$current = get_option( 'freecookie_settings', array() );
		$out     = wp_parse_args( is_array( $current ) ? $current : array(), FC_Plugin::default_settings() );

		$out['blocking_enabled'] = ! empty( $input['blocking_enabled'] );
		$out['detect_browser']   = ! empty( $input['detect_browser'] );
		$out['consent_days']     = max( 1, min( 3650, (int) ( $input['consent_days'] ?? 180 ) ) );
		$out['visit_threshold']  = max( 0, (int) ( $input['visit_threshold'] ?? 10000 ) );
		$out['badge_shape']      = FC_Shapes::valid( isset( $input['badge_shape'] ) ? sanitize_text_field( $input['badge_shape'] ) : '' );

		// Couleurs (vide autorisé = auto/dérivé).
		$colors = array();
		foreach ( array_keys( $this->color_fields() ) as $key ) {
			$colors[ $key ] = FC_Colors::sanitize( $input['colors'][ $key ] ?? '' );
		}
		$out['colors'] = $colors;

		// Textes : surcharge la langue soumise, préserve les autres.
		$lang = isset( $input['_lang'] ) ? FC_I18n::normalize( sanitize_text_field( $input['_lang'] ) ) : 'fr';
		$overrides = isset( $out['text_overrides'] ) && is_array( $out['text_overrides'] ) ? $out['text_overrides'] : array();
		$lang_over = array();
		foreach ( array_keys( $this->text_fields() ) as $key ) {
			$val = isset( $input['texts'][ $key ] ) ? sanitize_textarea_field( $input['texts'][ $key ] ) : '';
			if ( '' !== $val ) {
				$lang_over[ $key ] = $val;
			}
		}
		if ( $lang_over ) {
			$overrides[ $lang ] = $lang_over;
		} else {
			unset( $overrides[ $lang ] );
		}
		$out['text_overrides'] = $overrides;

		// À propos / réseaux.
		$about_in = isset( $input['about'] ) && is_array( $input['about'] ) ? $input['about'] : array();
		$social   = array();
		foreach ( array_keys( $this->social_networks() ) as $net ) {
			$url = isset( $about_in['social'][ $net ] ) ? esc_url_raw( trim( (string) $about_in['social'][ $net ] ) ) : '';
			if ( '' !== $url ) {
				$social[ $net ] = $url;
			}
		}
		$out['about'] = array(
			'enabled' => ! empty( $about_in['enabled'] ),
			'name'    => sanitize_text_field( $about_in['name'] ?? '' ),
			'tagline' => sanitize_text_field( $about_in['tagline'] ?? '' ),
			'website' => esc_url_raw( trim( (string) ( $about_in['website'] ?? '' ) ) ),
			'email'   => sanitize_email( $about_in['email'] ?? '' ),
			'donate'  => esc_url_raw( trim( (string) ( $about_in['donate'] ?? '' ) ) ),
			'social'  => $social,
		);

		add_settings_error( 'freecookie', 'saved', __( 'Réglages enregistrés.', 'freecookie' ), 'updated' );
		return $out;
	}

	/**
	 * Affiche la page.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Détection profonde automatique (logo + fréquence) à la première ouverture,
		// ou après une mise à jour du plugin (logique de détection changée).
		$det = get_option( FC_Color_Detector::OPTION );
		if ( empty( $det['deep'] ) || ( $det['ver'] ?? '' ) !== FREECOOKIE_VERSION ) {
			FC_Color_Detector::detect( true );
		}
		$s      = wp_parse_args( get_option( 'freecookie_settings', array() ), FC_Plugin::default_settings() );
		$colors = is_array( $s['colors'] ) ? $s['colors'] : array();
		$lang   = FC_I18n::detect( false );
		$bundle = FC_I18n::get( $lang );
		$over   = isset( $s['text_overrides'][ $lang ] ) && is_array( $s['text_overrides'][ $lang ] ) ? $s['text_overrides'][ $lang ] : array();
		$scan   = FC_Scanner::last();
		?>
		<div class="wrap">
			<h1>FreeCookie</h1>
			<?php settings_errors( 'freecookie' ); ?>

			<?php if ( isset( $_GET['fc_scanned'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success"><p><?php
					printf(
						/* translators: 1: pages, 2: services. */
						esc_html__( 'Scan terminé : %1$s pages analysées, %2$s services détectés.', 'freecookie' ),
						(int) $_GET['fc_scanned'], // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						(int) ( $_GET['fc_services'] ?? 0 ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					);
				?></p></div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( self::GROUP ); ?>
				<input type="hidden" name="freecookie_settings[_lang]" value="<?php echo esc_attr( $lang ); ?>">

				<h2 class="title"><?php esc_html_e( 'Apparence — couleurs', 'freecookie' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Laissez vide pour utiliser automatiquement la couleur dominante du site (ou une teinte dérivée).', 'freecookie' ); ?></p>

				<?php $detected = FC_Color_Detector::palette(); ?>
				<?php if ( $detected ) : ?>
					<style>.fc-swatch{width:30px;height:30px;border-radius:6px;border:1px solid rgba(0,0,0,.18);cursor:pointer;padding:0;margin:0 6px 6px 0;vertical-align:middle}.fc-detected{margin:2px 0 12px}</style>
					<p class="description" style="margin-top:0"><?php esc_html_e( 'Couleurs détectées sur votre site — cliquez pour l’appliquer comme couleur principale :', 'freecookie' ); ?></p>
					<p class="fc-detected">
						<?php foreach ( $detected as $hex ) : ?>
							<button type="button" class="fc-swatch" data-color="<?php echo esc_attr( $hex ); ?>" style="background:<?php echo esc_attr( $hex ); ?>" title="<?php echo esc_attr( $hex ); ?>" aria-label="<?php echo esc_attr( $hex ); ?>"></button>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>

				<table class="form-table" role="presentation"><tbody>
					<?php foreach ( $this->color_fields() as $key => $label ) : ?>
						<tr>
							<th scope="row"><label for="fc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td><input type="text" class="fc-color-field" id="fc-<?php echo esc_attr( $key ); ?>"
								name="freecookie_settings[colors][<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $colors[ $key ] ?? '' ); ?>" data-default-color=""></td>
						</tr>
					<?php endforeach; ?>
				</tbody></table>

				<h2 class="title"><?php esc_html_e( 'Forme du cookie', 'freecookie' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Choisissez la forme du badge flottant (elle prend la couleur du site).', 'freecookie' ); ?></p>
				<?php
				$fc_bv    = FC_Colors::css_vars( $s );
				$fc_shape = FC_Shapes::valid( $s['badge_shape'] ?? '' );
				?>
				<style>
					.fc-shapes{display:grid;grid-template-columns:repeat(auto-fill,minmax(86px,1fr));gap:10px;max-width:820px;margin:6px 0 4px}
					.fc-shape{position:relative;display:flex;flex-direction:column;align-items:center;gap:6px;border:1px solid #dcdcde;border-radius:10px;padding:12px 6px 8px;cursor:pointer;background:#fff}
					.fc-shape input{position:absolute;inset:0;opacity:0;margin:0;cursor:pointer}
					.fc-shape:has(input:checked){border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
					.fc-shape__ico{width:50px;height:50px}
					.fc-shape__ico svg{width:100%;height:100%;display:block}
					.fc-shape__lbl{font-size:11px;color:#50575e;text-align:center;line-height:1.2}
					.fc-shapes .fc-cookie__disc{fill:var(--fc-badge-solid)}
					.fc-shapes .fc-cookie__hole{fill:var(--fc-badge-hole)}
					.fc-shapes .fc-cookie__line{fill:none;stroke:var(--fc-badge-solid);stroke-width:3;stroke-linejoin:round}
					.fc-shapes .fc-cookie__ring{fill:none;stroke:var(--fc-badge-hole);stroke-width:3;opacity:.6}
				</style>
				<div class="fc-shapes" style="--fc-badge-solid:<?php echo esc_attr( $fc_bv['--fc-badge-solid'] ); ?>;--fc-badge-hole:<?php echo esc_attr( $fc_bv['--fc-badge-hole'] ); ?>">
					<?php foreach ( FC_Shapes::all() as $fc_id => $fc_s ) : ?>
						<label class="fc-shape">
							<input type="radio" name="freecookie_settings[badge_shape]" value="<?php echo esc_attr( $fc_id ); ?>" <?php checked( $fc_shape, $fc_id ); ?>>
							<span class="fc-shape__ico"><svg class="fc-cookie" viewBox="0 0 64 64" aria-hidden="true"><?php echo $fc_s['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></span>
							<span class="fc-shape__lbl"><?php echo esc_html( $fc_s['label'] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>

				<h2 class="title"><?php
					/* translators: %s: language code. */
					printf( esc_html__( 'Textes de la bannière (%s)', 'freecookie' ), esc_html( strtoupper( $lang ) ) );
				?></h2>
				<p class="description"><?php esc_html_e( 'Laissez vide pour garder le texte traduit fourni. Ces textes s’appliquent à la langue affichée ci-dessus.', 'freecookie' ); ?></p>
				<table class="form-table" role="presentation"><tbody>
					<?php foreach ( $this->text_fields() as $key => $label ) : ?>
						<tr>
							<th scope="row"><label for="fct-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<?php if ( 'body' === $key ) : ?>
									<textarea id="fct-<?php echo esc_attr( $key ); ?>" rows="3" class="large-text"
										name="freecookie_settings[texts][<?php echo esc_attr( $key ); ?>]"
										placeholder="<?php echo esc_attr( $bundle[ $key ] ?? '' ); ?>"><?php echo esc_textarea( $over[ $key ] ?? '' ); ?></textarea>
								<?php else : ?>
									<input type="text" id="fct-<?php echo esc_attr( $key ); ?>" class="regular-text"
										name="freecookie_settings[texts][<?php echo esc_attr( $key ); ?>]"
										value="<?php echo esc_attr( $over[ $key ] ?? '' ); ?>"
										placeholder="<?php echo esc_attr( $bundle[ $key ] ?? '' ); ?>">
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody></table>

				<h2 class="title"><?php esc_html_e( 'Options', 'freecookie' ); ?></h2>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Blocage a priori', 'freecookie' ); ?></th>
						<td><label><input type="checkbox" name="freecookie_settings[blocking_enabled]" value="1" <?php checked( ! empty( $s['blocking_enabled'] ) ); ?>>
							<?php esc_html_e( 'Bloquer les traceurs tiers avant le consentement', 'freecookie' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Détection de langue', 'freecookie' ); ?></th>
						<td><label><input type="checkbox" name="freecookie_settings[detect_browser]" value="1" <?php checked( ! empty( $s['detect_browser'] ) ); ?>>
							<?php esc_html_e( 'Utiliser la langue du navigateur en dernier recours', 'freecookie' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-days"><?php esc_html_e( 'Validité du consentement (jours)', 'freecookie' ); ?></label></th>
						<td><input type="number" id="fc-days" min="1" max="3650" name="freecookie_settings[consent_days]" value="<?php echo esc_attr( (int) $s['consent_days'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-thr"><?php esc_html_e( 'Seuil gratuit (visites/mois)', 'freecookie' ); ?></label></th>
						<td><input type="number" id="fc-thr" min="0" step="500" name="freecookie_settings[visit_threshold]" value="<?php echo esc_attr( (int) $s['visit_threshold'] ); ?>"></td>
					</tr>
				</tbody></table>

				<h2 class="title"><?php esc_html_e( 'À propos / réseaux', 'freecookie' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Un petit lien « À propos » sur la bannière ouvre un volet avec vos références et vos réseaux. Les libellés sont traduits automatiquement.', 'freecookie' ); ?></p>
				<?php $ab = is_array( $s['about'] ) ? $s['about'] : array(); ?>
				<?php $abs = is_array( $ab['social'] ?? null ) ? $ab['social'] : array(); ?>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Lien « À propos »', 'freecookie' ); ?></th>
						<td><label><input type="checkbox" name="freecookie_settings[about][enabled]" value="1" <?php checked( ! empty( $ab['enabled'] ) ); ?>> <?php esc_html_e( 'Afficher le lien sur la bannière', 'freecookie' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-ab-name"><?php esc_html_e( 'Nom', 'freecookie' ); ?></label></th>
						<td><input type="text" id="fc-ab-name" class="regular-text" name="freecookie_settings[about][name]" value="<?php echo esc_attr( $ab['name'] ?? '' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-ab-tag"><?php esc_html_e( 'Sous-titre', 'freecookie' ); ?></label></th>
						<td><input type="text" id="fc-ab-tag" class="regular-text" name="freecookie_settings[about][tagline]" value="<?php echo esc_attr( $ab['tagline'] ?? '' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-ab-web"><?php esc_html_e( 'Site web', 'freecookie' ); ?></label></th>
						<td><input type="url" id="fc-ab-web" class="regular-text" name="freecookie_settings[about][website]" value="<?php echo esc_attr( $ab['website'] ?? '' ); ?>" placeholder="https://"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-ab-mail"><?php esc_html_e( 'E-mail', 'freecookie' ); ?></label></th>
						<td><input type="email" id="fc-ab-mail" class="regular-text" name="freecookie_settings[about][email]" value="<?php echo esc_attr( $ab['email'] ?? '' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-ab-donate"><?php esc_html_e( 'Lien de don / café', 'freecookie' ); ?></label></th>
						<td><input type="url" id="fc-ab-donate" class="regular-text" name="freecookie_settings[about][donate]" value="<?php echo esc_attr( $ab['donate'] ?? '' ); ?>" placeholder="https://revolut.me/…"></td>
					</tr>
					<?php foreach ( $this->social_networks() as $net => $label ) : ?>
						<tr>
							<th scope="row"><label for="fc-soc-<?php echo esc_attr( $net ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td><input type="url" id="fc-soc-<?php echo esc_attr( $net ); ?>" class="regular-text" name="freecookie_settings[about][social][<?php echo esc_attr( $net ); ?>]" value="<?php echo esc_attr( $abs[ $net ] ?? '' ); ?>" placeholder="https://"></td>
						</tr>
					<?php endforeach; ?>
				</tbody></table>

				<?php submit_button(); ?>
			</form>

			<hr>
			<h2 class="title"><?php esc_html_e( 'Scanner de cookies', 'freecookie' ); ?></h2>
			<p class="description">
				<?php
				if ( $scan ) {
					printf(
						/* translators: 1: services count, 2: date. */
						esc_html__( 'Dernier scan : %1$s services connus détectés (%2$s).', 'freecookie' ),
						count( $scan['services'] ),
						esc_html( wp_date( 'j M Y H:i', (int) $scan['time'] ) )
					);
				} else {
					esc_html_e( 'Aucun scan encore effectué.', 'freecookie' );
				}
				?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="freecookie_scan">
				<?php wp_nonce_field( 'freecookie_scan' ); ?>
				<?php submit_button( __( 'Lancer un scan du site', 'freecookie' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}
}
