<?php
// ⚠️ SANS CE SECOND BANC, LE PREMIER NE PROUVE RIEN : huit cas qui rendent tous « fr » sont
// exactement ce que rendrait une fonction qui aurait PERDU la selection par navigateur. Il faut
// montrer que le navigateur GAGNE ENCORE quand le site parle vraiment sa langue.
define('ABSPATH', 1);
$GLOBALS['LOCALE'] = 'fr_CH';
$GLOBALS['PLL'] = ['fr', 'en', 'de'];          // le site declare trois langues
function get_locale() { return $GLOBALS['LOCALE']; }
function sanitize_text_field($s) { return $s; }
function wp_unslash($s) { return $s; }
function pll_languages_list($a = []) { return $GLOBALS['PLL']; }
function pll_current_language($f = 'slug') { return ''; }   // hors boucle : Polylang ne tranche pas
require __DIR__ . '/../includes/class-fc-i18n.php';

$n = 0; $ko = 0;
foreach ([
  ["site multilingue FR/EN/DE, visiteur anglais  -> le navigateur GAGNE", 'en-US,en;q=0.9', 'en'],
  ["site multilingue FR/EN/DE, visiteur allemand -> le navigateur GAGNE", 'de-DE,de;q=0.9', 'de'],
  ["site multilingue FR/EN/DE, visiteur italien  -> non offert, site",    'it-IT,it;q=0.9', 'fr'],
  ["site multilingue, visiteur francophone",                             'fr-CH,fr;q=0.9', 'fr'],
] as $c) {
    $n++;
    $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $c[1];
    $vu = Freecookie_I18n::detect(true);
    printf("  %-62s -> %-3s %s\n", $c[0], $vu, $vu === $c[2] ? 'OK' : "NON (attendu {$c[2]})");
    if ($vu !== $c[2]) $ko++;
}
echo "\n  RESULTAT : $n cas, $ko echec(s)\n";
exit($ko === 0 ? 0 : 1);
