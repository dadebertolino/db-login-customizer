# DB Login Customizer

Personalizza la pagina di login e logout di WordPress con il tuo brand. Niente dipendenze, niente fronzoli.

**Versione:** 1.1.0  
**Autore:** [Davide Bertolino](https://www.davidebertolino.it)  
**Licenza:** GPL v2 or later  
**Richiede:** WordPress 5.8+ · PHP 7.4+  
**GitHub:** [dadebertolino/db-login-customizer](https://github.com/dadebertolino/db-login-customizer)

---

## Cosa fa

Sostituisce l'aspetto predefinito di `wp-login.php` con i colori, il logo e lo stile del tuo sito. Un form, salvi, funziona. Fine.

### Funzionalità

- **Sincronizzazione tema** — legge automaticamente colori, font, border radius e logo dal tema attivo (via theme.json, Global Styles, Customizer). Un checkbox e la pagina di login si adatta al tuo brand
- **Logo personalizzato** — carica il tuo logo dalla Media Library, sostituisce il logo WordPress. Con sync attivo, usa il logo del tema/sito se non ne hai caricato uno manuale
- **Colore di sfondo** — scegli un colore o carica un'immagine di sfondo (cover, contain, auto)
- **Stile form** — colore sfondo, border radius, ombra on/off
- **Colori** — bottone, testo bottone, etichette, link
- **Messaggio personalizzato** — testo mostrato sopra il form di login
- **Redirect** — dopo login e/o logout, verso URL a scelta
- **Nascondi elementi** — selettore lingua, link "Torna al sito"
- **CSS personalizzato** — campo per CSS aggiuntivo sulla pagina di login
- **Logo linkato al sito** — il logo punta alla homepage, non a wordpress.org

### Cosa NON fa

- Non modifica il funzionamento del login (nessun captcha, nessun 2FA, nessun limite tentativi)
- Non aggiunge frontend al sito (agisce solo su wp-login.php)
- Non carica script esterni
- Non traccia niente
- Non ha versione Pro

---

## Installazione

1. Scarica il file ZIP dalla [release GitHub](https://github.com/dadebertolino/db-login-customizer/releases)
2. WordPress Admin → Plugin → Aggiungi nuovo → Carica plugin → scegli il file ZIP
3. Attiva il plugin
4. Vai su **Impostazioni → Login Customizer**

---

## Utilizzo

La pagina di impostazioni è in **Impostazioni → Login Customizer**. Ogni sezione ha campi con anteprima:

| Sezione | Controlli |
|---------|-----------|
| Sincronizzazione tema | Checkbox sync + anteprima valori rilevati + bottone "Applica ai campi" |
| Logo | Immagine + larghezza |
| Sfondo | Colore + immagine + dimensione |
| Form | Sfondo + border radius + ombra |
| Colori | Bottone, testo, etichette, link |
| Opzioni | Messaggio, nascondi elementi |
| Redirect | URL post-login, URL post-logout |
| CSS | Textarea per CSS aggiuntivo |

Dopo il salvataggio, apri `wp-login.php` in una nuova scheda per verificare.

### Selettori CSS utili

```
body.login            — sfondo pagina
#login h1 a           — logo
#loginform            — box del form
.login label          — etichette
#wp-submit            — bottone "Accedi"
#nav a                — link sotto il form
#backtoblog a         — link "Torna a [sito]"
.login .message       — messaggi informativi
.login #login_error   — messaggi di errore
```

---

## Struttura cartelle

```
db-login-customizer/
├── db-login-customizer.php    # File principale
├── uninstall.php              # Pulizia opzioni
├── README.md
├── LICENSE
├── assets/
│   ├── css/
│   │   └── admin.css          # Stili pagina impostazioni
│   └── js/
│       └── admin.js           # Media upload + color picker
└── templates/
    └── admin-page.php         # Template pagina admin
```

---

## Note tecniche

- Tutte le personalizzazioni sono salvate in `wp_options` con prefisso `dblc_`
- Lo stile è iniettato via `login_enqueue_scripts` come `<style>` inline — nessun file CSS esterno sulla pagina di login
- La sincronizzazione tema legge da: `wp_get_global_settings()` (palette, font), `wp_get_global_styles()` (colori elementi, bottoni, link), `get_theme_mod('custom_logo')`, `get_option('site_icon')`, `get_background_color()`
- I font Google del tema vengono caricati con `<link>` preconnect + stylesheet
- I valori manuali hanno sempre priorità: il sync si applica solo ai campi lasciati ai valori predefiniti
- Il redirect post-login non sovrascrive redirect espliciti (`?redirect_to=`)
- L'uninstall rimuove tutte le opzioni dal database
- Nonce su tutti i form, capability check `manage_options`
- Accessibilità admin: focus visible, label associati, `screen-reader-text` per link esterni

---

## Changelog

### 1.1.0
- 🆕 Sincronizzazione tema: legge colori, font, border radius e logo dal tema attivo
- 🆕 Supporto theme.json (block themes), Global Styles, Customizer e site icon
- 🆕 Anteprima valori rilevati dal tema nella pagina impostazioni
- 🆕 Bottone "Applica ai campi" per copiare i valori del tema nei color picker
- 🆕 Font Google del tema caricati sulla pagina di login
- I valori manuali sovrascrivono sempre quelli del tema

### 1.0.0
- Release iniziale
- Logo personalizzato con Media Library
- Colori: sfondo, form, bottone, link, etichette
- Immagine di sfondo con dimensione configurabile
- Messaggio personalizzato sopra il form
- Redirect post-login e post-logout
- Nascondi selettore lingua e link "Torna al sito"
- Campo CSS personalizzato
- Uninstall completo

---

## Licenza

GPL v2 or later — sei libero di utilizzare, modificare e distribuire questo plugin.

---

## Autore

**Davide Bertolino**  
🌐 [davidebertolino.it](https://www.davidebertolino.it)  
📧 info@davidebertolino.it
