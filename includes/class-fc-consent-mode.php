<?php
/**
 * Google Consent Mode v2 — signaux « default = denied » émis très tôt,
 * mis à jour côté JS au choix de l'utilisateur.
 *
 * @package FreeCookie
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FC_Consent_Mode {

	/**
	 * Imprime dans le <head>, avant tout tag Google, l'état par défaut « refusé ».
	 * Doit être hooké très tôt sur wp_head (priorité 0).
	 */
	public function print_default() {
		?>
<script data-fc-consent-mode="default">
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{
	'ad_storage':'denied',
	'ad_user_data':'denied',
	'ad_personalization':'denied',
	'analytics_storage':'denied',
	'functionality_storage':'denied',
	'personalization_storage':'denied',
	'security_storage':'granted',
	'wait_for_update': 500
});
gtag('set','ads_data_redaction', true);
gtag('set','url_passthrough', true);
</script>
		<?php
	}
}
