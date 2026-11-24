<?php
/**
 * Test du moteur de blocage quand PCRE abandonne — sans WordPress, sans dépendance.
 *
 * POURQUOI. Jusqu'à la 0.16.2, si `preg_replace_callback` renvoyait NULL sur le document
 * (limite de retours en arrière ou pile JIT atteinte), `(string) null` donnait une chaîne
 * vide : la page publique entière partait blanche, en 200, sans erreur. Ce test force cet
 * abandon et vérifie que la page est servie, réécrite exactement comme d'habitude.
 *
 * LANCER.   php .github/tests/test-script-blocker-pcre.php
 *           php .github/tests/test-script-blocker-pcre.php page1.html page2.html
 * Les fichiers passés en argument sont ajoutés au contrôle d'équivalence (pages réelles
 * enregistrées depuis un site). Code de sortie 0 si tout passe, 1 sinon.
 *
 * POURQUOI ICI. Le zip de publication n'embarque que l'arbre du plugin et refuse tout
 * fichier étranger (voir workflows/release.yml) ; `.github` en est exclu, les tests aussi.
 *
 * @package FreeCookie
 */

// phpcs:disable -- fichier de test autonome, hors WordPress.

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['fc_test_actions'] = array();

function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}
function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}
function home_url() {
	return 'https://exemple.test';
}
function apply_filters( $tag, $value ) {
	return $value;
}
function do_action( $tag, ...$args ) {
	$GLOBALS['fc_test_actions'][] = array( $tag, $args );
}

$racine = dirname( __DIR__, 2 );
require $racine . '/includes/class-fc-categories.php';
require $racine . '/includes/class-fc-script-blocker.php';

/**
 * Ouvre les deux chemins de réécriture pour pouvoir les comparer.
 */
class Fc_Test_Blocker extends Freecookie_Script_Blocker {
	/** Chemin habituel : expressions régulières (les limites PCRE doivent être normales). */
	public function par_motif( $html ) {
		return $this->process_html( $html );
	}
	/** Chemin de secours seul : analyseur linéaire, scripts puis iframes. */
	public function par_analyseur( $html ) {
		$html = $this->scan_tags( $html, 'script', true, 'cb_script' );
		return $this->scan_tags( $html, 'iframe', false, 'cb_iframe' );
	}
}

$echecs = 0;
$verifie = static function ( $condition, $libelle ) use ( &$echecs ) {
	echo ( $condition ? '  ok     ' : '  ECHEC  ' ) . $libelle . "\n";
	if ( ! $condition ) {
		++$echecs;
	}
};

$limite_normale = ini_get( 'pcre.backtrack_limit' );
$jit_normal     = ini_get( 'pcre.jit' );
$journal        = tempnam( sys_get_temp_dir(), 'fc-test-' );
ini_set( 'log_errors', '1' );
ini_set( 'error_log', $journal );

$bloqueur = new Fc_Test_Blocker( array() );
$exempte  = new Fc_Test_Blocker( array( 'youtube' ) );

// ---------------------------------------------------------------------------------------
// 1. Équivalence des deux chemins, limites PCRE normales.
// ---------------------------------------------------------------------------------------
$gtm     = '<script async src="https://www.googletagmanager.com/gtag/js?id=G-TEST"></script>';
$inline  = "<script>window.dataLayer=window.dataLayer||[];gtag('config', 'G-TEST');</script>";
$youtube = '<iframe width="560" src="https://www.youtube.com/embed/abc" class="video" allowfullscreen></iframe>';

$cas = array(
	'page ordinaire, traceurs en src et en ligne'   => "<!doctype html><html><head><title>t</title>$gtm$inline</head><body><p>a</p>$youtube</body></html>",
	'scripts internes et JSON-LD laissés intacts'   => '<html><head><script src="https://exemple.test/app.js"></script><script type="application/ld+json">{"@type":"Thing"}</script></head><body></body></html>',
	'balises en capitales'                          => '<HTML><HEAD><SCRIPT SRC="https://connect.facebook.net/en_US/fbevents.js"></SCRIPT></HEAD><BODY><IFRAME SRC="https://player.vimeo.com/video/1"></IFRAME></BODY></HTML>',
	'chevron dans une valeur entre guillemets'      => '<html><head><script data-x="a>b" data-y=\'c>d\' src="https://static.hotjar.com/c/hotjar.js"></script></head><body></body></html>',
	'déjà neutralisé par le site'                   => '<html><head><script type="text/plain" data-fc-category="statistics" data-fc-src="https://www.googletagmanager.com/gtm.js"></script></head><body></body></html>',
	'faux amis : <scripts> et <iframes>'            => "<html><head><scripts>x</scripts>$gtm</head><body><iframes src=\"https://www.youtube.com/embed/z\"></iframes>$youtube</body></html>",
	'script jamais refermé en fin de document'      => "<html><head>$gtm</head><body><p>fin</p><script src=\"https://www.googletagmanager.com/gtm.js\">",
	'balise ouvrante jamais refermée'               => "<html><head>$gtm</head><body><script src=\"https://static.hotjar.com/x.js\" </body></html>",
	'guillemet orphelin dans une balise'            => "<html><head><script data-x=\"oups src=https://static.hotjar.com/x.js></script>$gtm</head><body>$youtube</body></html>",
	'iframe interne et iframe sans src'             => '<html><head></head><body><iframe src="https://exemple.test/carte"></iframe><iframe title="vide"></iframe></body></html>',
	'iframe déjà porteuse d\'une classe'            => '<html><head></head><body><iframe class="a b" src="https://www.google.com/maps/embed?pb=1"></iframe></body></html>',
	'plusieurs scripts sur une ligne, sans espaces' => "<html><head>$gtm$gtm$inline$inline</head><body>$youtube$youtube</body></html>",
	'document sans aucune balise visée'             => '<html><head><title>rien</title></head><body><p>texte &lt;script&gt; cité</p></body></html>',
);

echo "1. Les deux chemins donnent la même page (limites PCRE normales)\n";
foreach ( $cas as $nom => $html ) {
	$verifie( $bloqueur->par_motif( $html ) === $bloqueur->par_analyseur( $html ), $nom );
}
$html_exempt = $cas['page ordinaire, traceurs en src et en ligne'];
$verifie( $exempte->par_motif( $html_exempt ) === $exempte->par_analyseur( $html_exempt ), 'service exempté (YouTube) : même page par les deux chemins' );
$verifie( false !== strpos( $exempte->par_analyseur( $html_exempt ), 'src="https://www.youtube.com/embed/abc"' ), 'service exempté : l\'iframe garde son src par l\'analyseur' );

$attendu = $bloqueur->par_motif( $cas['page ordinaire, traceurs en src et en ligne'] );
$verifie( false !== strpos( $attendu, 'data-fc-src="https://www.googletagmanager.com/gtag/js?id=G-TEST"' ), 'le script tiers est bien neutralisé (contrôle du test lui-même)' );
$verifie( false !== strpos( $attendu, 'fc-blocked-embed' ), 'l\'iframe tierce est bien neutralisée (contrôle du test lui-même)' );

foreach ( array_slice( $argv, 1 ) as $fichier ) {
	$page = is_readable( $fichier ) ? file_get_contents( $fichier ) : false;
	if ( false === $page ) {
		$verifie( false, "fichier illisible : $fichier" );
		continue;
	}
	$verifie( $bloqueur->par_motif( $page ) === $bloqueur->par_analyseur( $page ), sprintf( 'page réelle %s (%d octets)', basename( $fichier ), strlen( $page ) ) );
}

// ---------------------------------------------------------------------------------------
// 2. PCRE abandonne sur les <script> : la page part quand même, identique.
// ---------------------------------------------------------------------------------------
echo "2. PCRE abandonne sur un gros script en ligne\n";
$gros     = '<script>var donnees="' . str_repeat( 'abcdefghij', 40000 ) . '";</script>'; // 400 Ko.
$document = "<!doctype html><html><head><title>t</title>$gtm$gros$inline</head><body><p>contenu</p>$youtube</body></html>";
$attendu  = $bloqueur->par_motif( $document ); // Limites normales : référence.

ini_set( 'pcre.jit', '0' );
ini_set( 'pcre.backtrack_limit', '5000' );
$brut = preg_replace_callback( '#<script\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>(.*?)</script>#is', static function ( $m ) { return $m[0]; }, $document );
$verifie( null === $brut, 'condition du test : le motif des scripts renvoie bien NULL sous cette limite' );

$GLOBALS['fc_test_actions'] = array();
file_put_contents( $journal, '' );
$obtenu = $bloqueur->par_motif( $document );
ini_set( 'pcre.backtrack_limit', $limite_normale );
ini_set( 'pcre.jit', $jit_normal );

$verifie( '' !== $obtenu, 'la page n\'est pas vide' );
$verifie( strlen( $obtenu ) > 400000, sprintf( 'la page est complète (%d octets)', strlen( $obtenu ) ) );
$verifie( $obtenu === $attendu, 'la page est identique à celle du chemin habituel' );
$verifie( false !== strpos( $obtenu, 'data-fc-src="https://www.googletagmanager.com/gtag/js?id=G-TEST"' ), 'le script tiers reste neutralisé' );
$verifie( false !== strpos( $obtenu, 'fc-blocked-embed' ), 'l\'iframe tierce reste neutralisée' );
$signalees = array_values( array_filter( $GLOBALS['fc_test_actions'], static function ( $a ) { return 'freecookie_blocker_pcre_failure' === $a[0]; } ) );
$verifie( isset( $signalees[0] ) && 'script' === $signalees[0][1][0] && PREG_BACKTRACK_LIMIT_ERROR === $signalees[0][1][1], 'l\'action freecookie_blocker_pcre_failure est déclenchée (script, limite de retours)' );
$verifie( false !== strpos( (string) file_get_contents( $journal ), 'FreeCookie : PCRE a abandonné la réécriture des <script>' ), 'une ligne est écrite au journal d\'erreurs' );

// ---------------------------------------------------------------------------------------
// 3. PCRE abandonne sur les <iframe> (attributs démesurés) : même garantie.
// ---------------------------------------------------------------------------------------
echo "3. PCRE abandonne sur une iframe aux attributs démesurés\n";
$iframe   = '<iframe src="https://www.youtube.com/embed/long" data-remplissage=' . str_repeat( 'x', 60000 ) . '></iframe>';
$document = "<!doctype html><html><head><title>t</title></head><body><p>contenu</p>$iframe</body></html>";
$attendu  = $bloqueur->par_motif( $document );

ini_set( 'pcre.jit', '0' );
ini_set( 'pcre.backtrack_limit', '5000' );
$brut = preg_replace_callback( '#<iframe\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>#is', static function ( $m ) { return $m[0]; }, $document );
$verifie( null === $brut, 'condition du test : le motif des iframes renvoie bien NULL sous cette limite' );

$GLOBALS['fc_test_actions'] = array();
$obtenu = $bloqueur->par_analyseur( $document ); // Référence indépendante de PCRE sur le document.
$servi  = $bloqueur->par_motif( $document );
ini_set( 'pcre.backtrack_limit', $limite_normale );
ini_set( 'pcre.jit', $jit_normal );

$verifie( '' !== $servi, 'la page n\'est pas vide' );
$verifie( $servi === $obtenu, 'la page servie est celle de l\'analyseur de secours' );
$verifie( false !== strpos( $servi, '<p>contenu</p>' ), 'le contenu de la page est présent' );
$signalees = array_values( array_filter( $GLOBALS['fc_test_actions'], static function ( $a ) { return 'freecookie_blocker_pcre_failure' === $a[0]; } ) );
$verifie( isset( $signalees[0] ) && 'iframe' === $signalees[0][1][0], 'l\'action freecookie_blocker_pcre_failure est déclenchée (iframe)' );

@unlink( $journal );
echo $echecs ? "RESULTAT : $echecs échec(s)\n" : "RESULTAT : tout passe\n";
exit( $echecs ? 1 : 0 );
