# DB Login Customizer

Personalizza la pagina di login e logout di WordPress con il tuo brand. Niente dipendenze, niente fronzoli.

**Versione:** 1.2.0  
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
- Non carica script né font esterni: nessuna richiesta a terzi dalla pagina di login
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
│   │   ├── db-admin-ui.css    # Design system admin condiviso (DB Admin UI)
│   │   └── admin.css          # Stili pagina impostazioni
│   └── js/
│       └── admin.js           # Media upload + color picker
├── inc/
│   └── class-updater.php      # Aggiornamenti automatici da GitHub Releases
└── templates/
    └── admin-page.php         # Template pagina admin
```

---

## Note tecniche

- Tutte le personalizzazioni sono salvate in `wp_options` con prefisso `dblc_`
- Lo stile è iniettato via `login_enqueue_scripts` come `<style>` inline — nessun file CSS esterno sulla pagina di login
- La sincronizzazione tema legge da: `wp_get_global_settings()` (palette, font), `wp_get_global_styles()` (colori elementi, bottoni, link), `get_theme_mod('custom_logo')`, `get_option('site_icon')`, `get_background_color()`
- I font del tema sono serviti dai file del tema stesso: `@font-face` generato dalle voci `fontFace` di theme.json (`file:./…` risolto con `get_theme_file_uri()`), nessuna richiesta a Google Fonts
- Aggiornamenti automatici da GitHub Releases (`DB_GitHub_Updater`)
- I valori manuali hanno sempre priorità: il sync si applica solo ai campi lasciati ai valori predefiniti
- Il redirect post-login non sovrascrive redirect espliciti (`?redirect_to=`)
- L'uninstall rimuove tutte le opzioni dal database
- Nonce su tutti i form, capability check `manage_options`
- Accessibilità admin: focus visible, label associati, `screen-reader-text` per link esterni

---

## Privacy

Il plugin non tratta dati personali e **la pagina di login non contatta servizi di terze parti**.

Fino alla 1.1.0, con la sincronizzazione tema attiva, il font del tema veniva caricato da `fonts.googleapis.com` / `fonts.gstatic.com`: ogni visitatore della pagina di login trasmetteva a Google il proprio indirizzo IP senza alcun consenso (cfr. LG München, 20.01.2022, 3 O 17493/20). Sulla pagina di login il banner cookie di norma non compare, quindi subordinare il caricamento al consenso non è praticabile: dalla 1.2.0 i font vengono **serviti dal sito**.

- Il plugin legge dal theme.json il font scelto (heading → body → primo della lista) e ne genera le regole `@font-face` usando i **file già presenti nel tema** (`fontFace.src`). I block theme dichiarano infatti i font con file locali: nessun file da scaricare, nessun elenco di font da mantenere.
- Una `src` remota (Google Fonts o altro CDN) viene scartata. Sono accettati solo percorsi del tema, percorsi relativi al sito e URL sugli host del sito (`home_url`, `site_url`, `content_url`); il filtro `dblc_font_allowed_hosts` permette di aggiungere il CDN statico del sito.
- Se il tema non fornisce file per quel font, la pagina usa i font di fallback della `font-family` dichiarata.

Nessuna integrazione con DB Privacy Hub è necessaria: non ci sono trattamenti da dichiarare né dati da esportare o cancellare.

---

## Changelog

### 1.2.0
- 🔒 **Privacy by design**: rimosso il caricamento dei font da Google Fonts sulla pagina di login (`<link>` a `fonts.googleapis.com` e preconnect a `fonts.gstatic.com`). Il font del tema viene ora servito dai file locali del tema tramite `@font-face` generato dalle voci `fontFace` di theme.json; le sorgenti remote vengono scartate
- 🆕 Filtro `dblc_font_allowed_hosts` per ammettere il CDN statico del sito come origine dei file font
- 🔒 Valori di `font-family` e `@font-face` ripuliti prima dell'inserimento nel CSS della pagina di login
- 🆕 Aggiornamenti automatici da GitHub Releases (`inc/class-updater.php`)
- 🆕 Design system DB Admin UI (`assets/css/db-admin-ui.css`) caricato come dipendenza di `admin.css`
- 🆕 Blocco "Privacy capabilities" nell'intestazione del plugin: nessun dato personale, nessuna richiesta a terzi
- 🆕 GitHub Actions: `ci.yml` (lint PHP 7.4 e 8.3, `node --check`, controllo assenza URL Google Fonts) e `release.yml` (verifica tag/header/costante, ZIP con cartella radice `db-login-customizer/` allegato alla Release)
- Nessuna impostazione da migrare: il plugin non ha mai offerto un selettore di font, il font continua a essere quello del tema

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
