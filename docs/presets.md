# FreeCookie — Presets de couleur (installables tels quels)

FreeCookie s'adapte **automatiquement** au mode clair/sombre du système (aucune config).
Pour reteinter le bandeau aux couleurs de ton site, colle **un** de ces blocs dans ton CSS
(Personnaliser → CSS additionnel, ou la feuille de style du thème). Chaque preset gère le
clair **et** le sombre, et garde des contrastes AA.

Tu peux aussi forcer le thème quel que soit l'OS en ajoutant `data-fc-theme="dark"` (ou
`"light"`) sur la balise `<html>`.

---

## 1. Ocean (bleu)
```css
#freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
  --fc-accent:#2b6cb0; --fc-accent-deep:#1e4e82; --fc-accent-text:#ffffff;
  --fc-badge-solid:#2b6cb0; --fc-badge-hole:#cfe0f2;
}
@media (prefers-color-scheme: dark){
  #freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
    --fc-accent:#63b3ed; --fc-accent-deep:#90cdf4; --fc-accent-text:#062033;
    --fc-badge-solid:#63b3ed; --fc-badge-hole:#123047;
  }
}
```

## 2. Plum (violet)
```css
#freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
  --fc-accent:#7c3aed; --fc-accent-deep:#5b21b6; --fc-accent-text:#ffffff;
  --fc-badge-solid:#7c3aed; --fc-badge-hole:#e4d7fb;
}
@media (prefers-color-scheme: dark){
  #freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
    --fc-accent:#b794f4; --fc-accent-deep:#d6bcfa; --fc-accent-text:#1e0f36;
    --fc-badge-solid:#b794f4; --fc-badge-hole:#2a1a4a;
  }
}
```

## 3. Amber (chaud)
```css
#freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
  --fc-accent:#b45309; --fc-accent-deep:#8a3f07; --fc-accent-text:#ffffff;
  --fc-badge-solid:#b45309; --fc-badge-hole:#f5e0c3;
}
@media (prefers-color-scheme: dark){
  #freecookie-root, #freecookie-badge, .fc-banner, .fc-prefs, .fc-about{
    --fc-accent:#f0a860; --fc-accent-deep:#f6c48a; --fc-accent-text:#2a1605;
    --fc-badge-solid:#f0a860; --fc-badge-hole:#3a2410;
  }
}
```

*(Le preset par défaut « Teal » est intégré au plugin — aucun snippet nécessaire.)*
