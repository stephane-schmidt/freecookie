<?php
define('ABSPATH', 1);
$GLOBALS['LOCALE'] = 'fr_CH';
function get_locale() { return $GLOBALS['LOCALE']; }
function sanitize_text_field($s) { return $s; }
function wp_unslash($s) { return $s; }
require __DIR__ . '/../includes/class-fc-i18n.php';

function cas($titre, $locale, $accept, $attendu) {
    $GLOBALS['LOCALE'] = $locale;
    $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $accept;
    $vu = Freecookie_I18n::detect(true);
    printf("  %-58s locale=%-6s navigateur=%-22s -> %-3s %s\n",
        $titre, $locale, $accept, $vu, $vu === $attendu ? 'OK' : "NON (attendu $attendu)");
    return $vu === $attendu;
}
$n = 0; $ko = 0;
$t = [
  ["site FR monolingue, visiteur au navigateur anglais",  'fr_CH', 'en-US,en;q=0.9',       'fr'],
  ["site FR monolingue, visiteur au navigateur allemand", 'fr_CH', 'de-CH,de;q=0.9',       'fr'],
  ["site FR monolingue, visiteur francophone",            'fr_CH', 'fr-CH,fr;q=0.9',       'fr'],
  ["site FR de France, navigateur anglais",               'fr_FR', 'en-GB,en;q=0.9',       'fr'],
  ["site EN monolingue, visiteur francophone",            'en_US', 'fr-CH,fr;q=0.9',       'en'],
  ["site DE suisse, navigateur italien",                  'de_CH', 'it-CH,it;q=0.9',       'de'],
  ["site FR, navigateur sans en-tete",                    'fr_CH', '',                     'fr'],
  ["site FR, navigateur dans une langue inconnue",        'fr_CH', 'xx-XX',                'fr'],
];
foreach ($t as $c) { $n++; if (!cas($c[0], $c[1], $c[2], $c[3])) $ko++; }
echo "\n  RESULTAT : $n cas, $ko echec(s)\n";
exit($ko === 0 ? 0 : 1);
