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
			self::menu_icon(),
			80
		);
	}

	/**
	 * Icône du menu : mini cookie blanc (mordu, pépites évidées), en data URI.
	 * Masque SVG : la morsure et les pépites laissent transparaître le fond de
	 * la barre d'administration, quel que soit son thème de couleurs.
	 *
	 * @return string data:image/svg+xml;base64,…
	 */
	protected static function menu_icon() {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">'
			. '<mask id="fcm">'
			. '<rect width="20" height="20" fill="#fff"/>'
			. '<circle cx="17.2" cy="7" r="3.6" fill="#000"/>'   // morsure latérale.
			. '<circle cx="7" cy="8.6" r="1.4" fill="#000"/>'    // pépites.
			. '<circle cx="11.6" cy="13.8" r="1.4" fill="#000"/>'
			. '<circle cx="6.4" cy="13.3" r="1.1" fill="#000"/>'
			. '<circle cx="12" cy="7" r="1.1" fill="#000"/>'
			. '</mask>'
			. '<circle cx="10" cy="10" r="8" fill="#ffffff" mask="url(#fcm)"/>'
			. '</svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- format attendu par add_menu_page pour un SVG.
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
		wp_localize_script(
			'freecookie-admin',
			'fcScan',
			array(
				'rest'     => esc_url_raw( rest_url( 'freecookie/v1/scan/' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'sniffUrl' => add_query_arg( 'fc_sniff', wp_create_nonce( 'fc_sniff' ), home_url( '/' ) ),
				'strings'  => array(
					'scanning'   => __( 'Analyse de la page %1$d sur %2$d…', 'freecookie' ),
					'sniffing'   => __( 'Observation des cookies dans le navigateur…', 'freecookie' ),
					'finishing'  => __( 'Consolidation des résultats…', 'freecookie' ),
					'done'       => __( 'Scan terminé : %1$d pages, %2$d services, %3$d cookies. Rechargement…', 'freecookie' ),
					'error'      => __( 'Le scan a échoué. Réessayez, ou utilisez le bouton de secours ci-dessous.', 'freecookie' ),
					'service'    => __( 'Service détecté : %s', 'freecookie' ),
					'cookieHttp' => __( 'Cookie observé (serveur) : %s', 'freecookie' ),
					'cookieJs'   => __( 'Cookie observé (navigateur) : %s', 'freecookie' ),
				),
			)
		);
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
		$out['hide_honor_notice'] = ! empty( $input['hide_honor_notice'] );
		$freq = isset( $input['scan_frequency'] ) ? sanitize_text_field( $input['scan_frequency'] ) : 'weekly';
		$out['scan_frequency'] = in_array( $freq, array( 'never', 'daily', 'weekly' ), true ) ? $freq : 'weekly';
		FC_Plugin::sync_schedule( $out['scan_frequency'] );
		$pages = (int) ( $input['scan_pages'] ?? 10 );
		$out['scan_pages'] = in_array( $pages, array( 10, 25, 50, 100 ), true ) ? $pages : 10;
		// Clé FreeCookie Pro (système de confiance : aucune vérification réseau).
		$out['license_key'] = sanitize_text_field( $input['license_key'] ?? ( $out['license_key'] ?? '' ) );

		// Forme du badge : les formes Pro exigent une clé active.
		$shape = FC_Shapes::valid( isset( $input['badge_shape'] ) ? sanitize_text_field( $input['badge_shape'] ) : '' );
		if ( FC_Shapes::is_pro( $shape ) && ! FC_Pro::active( $out ) ) {
			$prev  = FC_Shapes::valid( $out['badge_shape'] ?? '' );
			$shape = FC_Shapes::is_pro( $prev ) ? FC_Shapes::DEFAULT_ID : $prev;
			add_settings_error(
				'freecookie',
				'shape_pro',
				__( 'Cette forme fait partie de FreeCookie Pro : saisissez votre clé dans la section « FreeCookie Pro » pour l’utiliser. La forme précédente est conservée.', 'freecookie' ),
				'warning'
			);
		}
		$out['badge_shape'] = $shape;

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

			<?php if ( ! extension_loaded( 'gd' ) ) : ?>
				<div class="notice notice-warning"><p><?php esc_html_e( 'La bibliothèque PHP GD est absente : la détection de couleur depuis le logo (PNG/JPG) est désactivée. Les autres sources de détection restent actives.', 'freecookie' ); ?></p></div>
			<?php endif; ?>

			<div class="notice notice-info" style="padding:10px 14px">
				<p style="margin:0"><strong><?php esc_html_e( 'Important — l’activation de FreeCookie ne suffit pas à elle seule à rendre votre site conforme.', 'freecookie' ); ?></strong><br>
				<?php esc_html_e( 'Il vous reste à : rédiger une politique de cookies/confidentialité, vérifier les traceurs non détectés automatiquement, et adapter vos mentions légales. FreeCookie neutralise les scripts et iframes tiers connus avant consentement ; il ne bloque pas le localStorage ni le fingerprinting effectués par des scripts qu’il n’a pas neutralisés. Si votre site s’adresse à des mineurs, des règles renforcées s’appliquent (RGPD art. 8).', 'freecookie' ); ?></p>
			</div>

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
				$fc_pro   = FC_Pro::active( $s );
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
					.fc-shapes .fc-cookie__c1{fill:var(--fc-c1)}
					.fc-shapes .fc-cookie__c2{fill:var(--fc-c2)}
					.fc-shapes .fc-cookie__c3{fill:var(--fc-c3)}
					.fc-shapes .fc-cookie__c4{fill:var(--fc-c4)}
					.fc-shape--locked{opacity:.45}
					.fc-shape--locked input{cursor:not-allowed}
					.fc-shape__pro{position:absolute;top:4px;right:4px;font-size:9px;font-weight:700;letter-spacing:.04em;color:#fff;background:#8c8f94;border-radius:3px;padding:1px 4px;pointer-events:none}
					.fc-fam-title{margin:16px 0 2px;font-size:13px}
					.fc-fam-title .fc-fam-pro{font-size:10px;font-weight:700;color:#8c8f94;vertical-align:middle;margin-left:6px}
				</style>
				<div style="--fc-badge-solid:<?php echo esc_attr( $fc_bv['--fc-badge-solid'] ); ?>;--fc-badge-hole:<?php echo esc_attr( $fc_bv['--fc-badge-hole'] ); ?>;--fc-c1:<?php echo esc_attr( $fc_bv['--fc-c1'] ); ?>;--fc-c2:<?php echo esc_attr( $fc_bv['--fc-c2'] ); ?>;--fc-c3:<?php echo esc_attr( $fc_bv['--fc-c3'] ); ?>;--fc-c4:<?php echo esc_attr( $fc_bv['--fc-c4'] ); ?>;max-width:820px">
					<?php foreach ( FC_Shapes::families() as $fc_fam => $fc_fdef ) : ?>
						<?php $fc_locked = $fc_fdef['pro'] && ! $fc_pro; ?>
						<h4 class="fc-fam-title">
							<?php echo esc_html( $fc_fdef['label'] ); ?>
							<?php if ( $fc_fdef['pro'] ) : ?>
								<span class="fc-fam-pro"><?php echo $fc_locked ? esc_html__( 'PRO — clé requise', 'freecookie' ) : esc_html__( 'PRO', 'freecookie' ); ?></span>
							<?php endif; ?>
						</h4>
						<div class="fc-shapes">
							<?php foreach ( FC_Shapes::by_family( $fc_fam ) as $fc_id => $fc_s ) : ?>
								<label class="fc-shape<?php echo $fc_locked ? ' fc-shape--locked' : ''; ?>">
									<input type="radio" name="freecookie_settings[badge_shape]" value="<?php echo esc_attr( $fc_id ); ?>" <?php checked( $fc_shape, $fc_id ); ?> <?php disabled( $fc_locked ); ?>>
									<?php if ( $fc_locked ) : ?><span class="fc-shape__pro">PRO</span><?php endif; ?>
									<span class="fc-shape__ico"><svg class="fc-cookie" viewBox="0 0 64 64" aria-hidden="true"><?php echo $fc_s['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></span>
									<span class="fc-shape__lbl"><?php echo esc_html( $fc_s['label'] ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
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
						<td><input type="number" id="fc-days" min="1" max="3650" name="freecookie_settings[consent_days]" value="<?php echo esc_attr( (int) $s['consent_days'] ); ?>">
						<p class="description"><?php esc_html_e( '90 jours recommandé (lignes directrices EDPB/CNIL). Au-delà de 365 jours, le consentement risque d’être considéré comme périmé.', 'freecookie' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-thr"><?php esc_html_e( 'Seuil gratuit (visites/mois)', 'freecookie' ); ?></label></th>
						<td><input type="number" id="fc-thr" min="0" step="500" name="freecookie_settings[visit_threshold]" value="<?php echo esc_attr( (int) $s['visit_threshold'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-scanfreq"><?php esc_html_e( 'Scan automatique des traceurs', 'freecookie' ); ?></label></th>
						<td>
							<select id="fc-scanfreq" name="freecookie_settings[scan_frequency]">
								<option value="weekly" <?php selected( $s['scan_frequency'] ?? 'weekly', 'weekly' ); ?>><?php esc_html_e( 'Une fois par semaine (recommandé)', 'freecookie' ); ?></option>
								<option value="daily" <?php selected( $s['scan_frequency'] ?? '', 'daily' ); ?>><?php esc_html_e( 'Une fois par jour', 'freecookie' ); ?></option>
								<option value="never" <?php selected( $s['scan_frequency'] ?? '', 'never' ); ?>><?php esc_html_e( 'Jamais (scan manuel uniquement)', 'freecookie' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Le site s’analyse lui-même en tâche de fond pour tenir à jour la liste des traceurs et les couleurs.', 'freecookie' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="fc-scanpages"><?php esc_html_e( 'Pages analysées par scan', 'freecookie' ); ?></label></th>
						<td>
							<select id="fc-scanpages" name="freecookie_settings[scan_pages]">
								<?php foreach ( array( 10 => __( '10 pages (rapide, recommandé)', 'freecookie' ), 25 => __( '25 pages', 'freecookie' ), 50 => __( '50 pages', 'freecookie' ), 100 => __( '100 pages (approfondi)', 'freecookie' ) ) as $fc_np => $fc_lb ) : ?>
									<option value="<?php echo (int) $fc_np; ?>" <?php selected( (int) ( $s['scan_pages'] ?? 10 ), $fc_np ); ?>><?php echo esc_html( $fc_lb ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Les traceurs sont posés par le thème et les extensions : ils sont identiques sur tout le site, un échantillon suffit donc. Tous les types de contenus publics sont échantillonnés (articles, pages, produits…).', 'freecookie' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Avis de soutien', 'freecookie' ); ?></th>
						<td><label><input type="checkbox" name="freecookie_settings[hide_honor_notice]" value="1" <?php checked( ! empty( $s['hide_honor_notice'] ) ); ?>>
							<?php esc_html_e( 'Masquer l’avis de soutien affiché au-delà du seuil (le plugin reste entièrement fonctionnel)', 'freecookie' ); ?></label></td>
					</tr>
				</tbody></table>

				<h2 class="title"><?php esc_html_e( 'À propos / réseaux', 'freecookie' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Désactivé par défaut. Si vous l’activez, un petit lien « À propos » sur la bannière ouvre un volet avec VOS références et VOS réseaux — ces informations seront visibles par les visiteurs de votre site. Les libellés sont traduits automatiquement.', 'freecookie' ); ?></p>
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

				<h2 class="title"><?php esc_html_e( 'FreeCookie Pro', 'freecookie' ); ?></h2>
				<p class="description" style="max-width:820px">
					<?php esc_html_e( 'Pro ajoute du confort (familles de formes supplémentaires, et plus à venir) — la conformité de base reste toujours gratuite et complète. Système de confiance : la clé reçue après votre soutien (10 $/an ou 45 $ à vie) suffit, aucune vérification en ligne, aucune donnée envoyée.', 'freecookie' ); ?>
					<a href="<?php echo esc_url( FC_Pro::SUPPORT_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Soutenir le projet et recevoir une clé', 'freecookie' ); ?></a>
				</p>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><label for="fc-license"><?php esc_html_e( 'Clé Pro', 'freecookie' ); ?></label></th>
						<td>
							<input type="text" id="fc-license" class="regular-text" name="freecookie_settings[license_key]" value="<?php echo esc_attr( $s['license_key'] ?? '' ); ?>" autocomplete="off">
							<?php if ( FC_Pro::active( $s ) ) : ?>
								<span style="color:#1f9d55;font-weight:600;margin-left:8px"><?php esc_html_e( 'Pro actif — merci pour votre soutien.', 'freecookie' ); ?></span>
							<?php else : ?>
								<span style="color:#8c8f94;margin-left:8px"><?php esc_html_e( 'Aucune clé — formes Pro verrouillées.', 'freecookie' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
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
			<p><button type="button" class="button button-secondary" id="fc-scan-btn"><?php esc_html_e( 'Lancer un scan du site', 'freecookie' ); ?></button></p>

			<div id="fc-scan-ui" hidden style="max-width:820px">
				<style>
					#fc-scan-track{height:14px;background:#dcdcde;border-radius:100px;overflow:hidden}
					#fc-scan-bar{height:100%;width:0;background:#2271b1;border-radius:100px;transition:width .3s ease}
					#fc-scan-status{margin:6px 0 10px;font-weight:600}
					#fc-scan-log{margin:0;max-height:220px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:8px 12px;background:#fff;font-size:12px}
					#fc-scan-log li{margin:2px 0;list-style:none}
					#fc-scan-log .fc-found{color:#1f9d55;font-weight:600}
				</style>
				<div id="fc-scan-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div id="fc-scan-bar"></div></div>
				<p id="fc-scan-status" aria-live="polite"></p>
				<ul id="fc-scan-log"></ul>
			</div>

			<noscript>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="freecookie_scan">
					<?php wp_nonce_field( 'freecookie_scan' ); ?>
					<?php submit_button( __( 'Lancer un scan du site (sans JavaScript)', 'freecookie' ), 'secondary', 'submit', false ); ?>
				</form>
			</noscript>

			<?php
			// Prochain scan automatique.
			$fc_next = wp_next_scheduled( 'freecookie_scan_event' );
			if ( $fc_next ) {
				echo '<p class="description">' . sprintf(
					/* translators: %s: date/heure. */
					esc_html__( 'Prochain scan automatique : %s.', 'freecookie' ),
					esc_html( wp_date( 'j M Y H:i', $fc_next ) )
				) . '</p>';
			}

			// Résultats du dernier scan, directement sous le bouton.
			if ( $scan ) :
				$fc_services = ! empty( $scan['services'] ) ? $scan['services'] : array();
				$fc_cookies  = ( ! empty( $scan['cookies'] ) && is_array( $scan['cookies'] ) ) ? $scan['cookies'] : array();
				?>
				<h3 style="margin:18px 0 6px"><?php esc_html_e( 'Résultats du dernier scan', 'freecookie' ); ?></h3>
				<?php if ( empty( $fc_services ) && empty( $fc_cookies ) ) : ?>
					<div class="notice notice-success inline" style="margin:0;max-width:820px"><p>
						<?php esc_html_e( 'Aucun traceur tiers connu détecté et aucun cookie observé sur les pages analysées — votre site est propre. La bannière n’affichera que les catégories, sans lignes de traceurs.', 'freecookie' ); ?>
					</p></div>
				<?php endif; ?>
				<?php if ( ! empty( $fc_services ) ) : ?>
					<style>.fc-adm-score{font-size:11px;font-weight:700;padding:1px 8px;border-radius:100px;color:#fff;white-space:nowrap}.fc-adm-score--green{background:#1f9d55}.fc-adm-score--orange{background:#cf8500}.fc-adm-score--red{background:#d64545}</style>
					<table class="widefat striped" style="max-width:820px">
						<thead><tr>
							<th><?php esc_html_e( 'Service', 'freecookie' ); ?></th>
							<th><?php esc_html_e( 'Catégorie', 'freecookie' ); ?></th>
							<th><?php esc_html_e( 'Risque', 'freecookie' ); ?></th>
							<th><?php esc_html_e( 'Finalité', 'freecookie' ); ?></th>
						</tr></thead>
						<tbody>
						<?php foreach ( $fc_services as $fc_key ) : ?>
							<?php
							$fc_meta  = FC_Categories::meta( $fc_key );
							$fc_risk  = FC_Categories::risk_key( $fc_meta['score'] );
							$fc_color = FC_Categories::score_color( $fc_meta['score'] );
							$fc_rlbl  = isset( $bundle[ 'risk_' . $fc_risk ] ) ? $bundle[ 'risk_' . $fc_risk ] : $fc_risk;
							$fc_clbl  = isset( $bundle[ $fc_meta['category'] ] ) ? $bundle[ $fc_meta['category'] ] : $fc_meta['category'];
							?>
							<tr>
								<td><strong><?php echo esc_html( FC_Categories::service_label( $fc_key ) ); ?></strong></td>
								<td><?php echo esc_html( $fc_clbl ); ?></td>
								<td><span class="fc-adm-score fc-adm-score--<?php echo esc_attr( $fc_color ); ?>"><?php echo esc_html( $fc_rlbl ); ?></span></td>
								<td><?php echo esc_html( $fc_meta['purpose'] ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			<?php if ( ! empty( $fc_cookies ) ) : ?>
				<h4 style="margin:16px 0 6px"><?php printf( /* translators: %d: nombre de cookies. */ esc_html__( 'Cookies observés (%d)', 'freecookie' ), count( $fc_cookies ) ); ?></h4>
				<table class="widefat striped" style="max-width:820px">
					<thead><tr>
						<th><?php esc_html_e( 'Cookie', 'freecookie' ); ?></th>
						<th><?php esc_html_e( 'Origine', 'freecookie' ); ?></th>
						<th><?php esc_html_e( 'Service', 'freecookie' ); ?></th>
						<th><?php esc_html_e( 'Catégorie', 'freecookie' ); ?></th>
						<th><?php esc_html_e( 'Description', 'freecookie' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $fc_cookies as $fc_ck_name => $fc_ck ) : ?>
						<?php $fc_ck_clbl = isset( $bundle[ $fc_ck['cat'] ] ) ? $bundle[ $fc_ck['cat'] ] : $fc_ck['cat']; ?>
						<tr>
							<td><code><?php echo esc_html( $fc_ck_name ); ?></code></td>
							<td><?php echo ( 'js' === ( $fc_ck['src'] ?? 'http' ) ) ? esc_html__( 'Navigateur', 'freecookie' ) : esc_html__( 'Serveur (HTTP)', 'freecookie' ); ?></td>
							<td><?php echo ! empty( $fc_ck['service'] ) ? esc_html( FC_Categories::service_label( $fc_ck['service'] ) ) : esc_html__( 'Ce site', 'freecookie' ); ?></td>
							<td><?php echo esc_html( $fc_ck_clbl ); ?></td>
							<td><?php echo esc_html( FC_I18n::pick( $fc_ck['desc'], $lang ) ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
