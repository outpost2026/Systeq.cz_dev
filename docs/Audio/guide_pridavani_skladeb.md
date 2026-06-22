# Průvodce: Jak funguje `music.html` a jak přidávat skladby

> **Verze**: 1.0 — platí pro `src/music.html`

---

## 1. Co je zač — jediný soubor = celá stránka

`music.html` je **samostatný soubor**, který v sobě obsahuje všechno:

- **Vzhled** (CSS) — barvy, písma, rozložení
- **Obsah** (HTML) — nadpisy, tlačítka filtrů
- **Data** (JS) — seznam skladeb v poli `TRACKS`
- **Ovládání** (JS) — tlačítko Play, progress bar, filtrování

Žádný WordPress, žádná databáze. Otevřeš soubor v editoru (VS Code / Poznámkový blok), upravíš data, uložíš, nahraješ na FTP → hotovo.

---

## 2. Stromová struktura webu

```
systeq.cz/                         ← kořen (root = FTP složka)
├── music.html                     ← tato stránka (Ateliér můz)
├── index.html                     ← hlavní stránka SYSTEQ
├── music/                         ← složka s audio soubory
│   ├── My_jsme_pravé_úhly.mp4
│   ├── Vykrvácet_do_svislice.mp4
│   └── covers/                    ← složka s obrázky přebalů
│       ├── my_jsme_prave_uhly.png
│       └── vykrvacet_do_svislice.png
```

Když v `music.html` píšu `music/My_jsme_pravé_úhly.mp4`, znamená to: "vezmi soubor `music/My_jsme_pravé_úhly.mp4` ze stejného místa, kde leží `music.html`".

---

## 3. Srdce stránky: pole TRACKS

V souboru `music.html` je uvnitř oddílu `<script>` tato struktura:

```javascript
const TRACKS = [                     // ← hranatá závorka = seznam
  {                                  // ← složená závorka = jedna skladba
    id: 'my-jsme-prave-uhly',        // identifikátor (stačí cokoliv unikátního)
    title: 'My jsme pravý úhly',     // název skladby
    category: 'fentanyl-core',       // ← KATEGORIE (žánr pro filtry)
    format: 'hudba',                 // typ: hudba / esej / parodie
    duration: '—',                   // stopáž (nepovinné)
    bpm: '30 BPM',                   // tempo (nepovinné)
    tuning: 'Drop-F0',               // ladění (nepovinné)
    cover: 'music/covers/soubor.png',// cesta k obrázku přebalu
    audio: 'music/audio.mp4',        // cesta k audio souboru
    date: '2026',
    lyrics: [                        // ← text skladby
      'První řádek textu...',
      'Druhý řádek textu...'
    ]
  },                                 // ← čárka odděluje skladby
  {                                  // ← druhá skladba začíná
    // ...
  }
];
```

### Co jednotlivé položky znamenají

| Klíč | Povinný? | Význam |
|------|----------|--------|
| `id` | ano | Unikátní krátký identifikátor (použij anglické znaky, pomlčky místo mezer) |
| `title` | ano | Název skladby jak se zobrazí na stránce |
| `category` | ano | Kategorie pro filtry — musí souhlasit s tlačítkem (viz bod 4) |
| `format` | ne | Typ: `hudba`, `esej`, `parodie` |
| `duration` | ne | Stopáž, např. `'03:04'`. Pokud nevíš, napiš `'—'` |
| `bpm` | ne | Tempo, např. `'30 BPM'`. Pokud neplatí, napiš `null` |
| `tuning` | ne | Ladění, např. `'Drop-F0'`. Pokud neplatí, napiš `null` |
| `cover` | ne | Cesta k obrázku přebalu. Pokud není, napiš `null` |
| `audio` | ne | Cesta k MP4 souboru. Pokud není, napiš `null` (karta se zobrazí jako šedý placeholder) |
| `date` | ne | Rok vydání |
| `lyrics` | ne | Text skladby jako seznam řádků |

### Důležité typografické pravidlo

V JavaScriptu se řetězce (text) píší do **jednoduchých uvozovek** `'...'`. Pokud text sám obsahuje jednoduchou uvozovku (apostrof, např. `Let's go`), musíš ji "escapovat" zpětným lomítkem: `'Let\'s go'`.

---

## 4. Kategorie = tlačítka filtrů

Nahoře na stránce jsou filtrační tlačítka:

```html
<button class="filter-btn active" data-category="all">vše_</button>
<button class="filter-btn" data-category="fentanyl-core">fentanyl_core</button>
<button class="filter-btn" data-category="neuro-philosophy">neuro_eseje</button>
<button class="filter-btn" data-category="valley-miniatures">miniatury_z_udoli</button>
```

Aktuálně existují 3 kategorie + "vše_":

| Kategorie (`data-category`) | Zobrazí skladby s |
|-----------------------------|-------------------|
| `all` | všechny |
| `fentanyl-core` | `category: 'fentanyl-core'` |
| `neuro-philosophy` | `category: 'neuro-philosophy'` |
| `valley-miniatures` | `category: 'valley-miniatures'` |

**Důležité pravidlo:** Hodnota `data-category` u tlačítka se **musí přesně shodovat** s `category` u skladby. Pokud se liší byť jen háčkem nebo velkým písmenem, filtr nebude fungovat.

### Chceš přidat nový žánr?

1. U nové skladby napiš `category: 'muj-novy-zanr'`
2. V HTML (kolem řádku 214) přidej nové tlačítko:
   ```html
   <button class="filter-btn" data-category="muj-novy-zanr">muj_novy_zanr</button>
   ```
3. Kdyby ses spletl, stačí upravit na jednom z těchto dvou míst, aby seděly.

---

## 5. Workflow: přidání nové skladby (krok za krokem)

### Krok 1 — Nahraj soubory na FTP

```
music/                      ← sem nahraj MP4 soubor
  Ma_nova_song.mp4

music/covers/               ← sem nahraj obrázek přebalu
  ma_nova_song.png
```

Pojmenovávej soubory konzistentně (např. `muj-song.mp4`, `muj-song.png`).

### Krok 2 — Otevři `music.html` v editoru

Najdi v souboru `const TRACKS = [` a přidej nový blok za poslední skladbu. Postupuj přesně podle tohoto vzoru:

```javascript
  {
    id: 'ma-nova-song',                     // unikátní id
    title: 'Má nová song',                  // název
    category: 'fentanyl-core',              // = který filtr ji zachytí
    format: 'hudba',
    duration: '—',
    bpm: null,
    tuning: null,
    cover: 'music/covers/ma_nova_song.png',  // cesta k obrázku
    audio: 'music/Ma_nova_song.mp4',         // cesta k audiu
    date: '2026',
    lyrics: [
      'První řádek textu písně...',
      '',
      'SLOKA I:',
      'Za tónovaným sklem...'
    ]
  },
```

### Krok 3 — Zkontroluj čárky

- Mezi jednotlivými skladbami musí být **čárka** (`,` za `}`)
- Za **poslední** skladbou čárka **nesmí být** (za `}` už je jen `];`)

Tohle je nejčastější chyba začátečníků. Pokud stránka přestane fungovat, pravděpodobně chybí nebo přebývá čárka.

### Krok 4 — Ulož a nahraj na FTP

Přepiš soubor `music.html` na serveru → hotovo. Obnov stránku v prohlížeči.

---

## 6. Co dělat, když něco nefunguje

| Problém | Pravděpodobná příčina |
|---------|----------------------|
| Skladba se nezobrazí vůbec | Chybí čárka mezi skladbami, nebo je čárka navíc za poslední skladbou |
| Filtr skladbu neukáže | `category` u skladby nesouhlasí s `data-category` u tlačítka (lišší se háček/písmeno) |
| Obrázek přebalu chybí (prázdný čtverec) | Cesta v `cover` neodpovídá umístění souboru na FTP. Zkontroluj, zda obrázek skutečně leží tam, kam cesta ukazuje |
| Tlačítko Play nereaguje nebo zmáčkneš a nic | Cesta v `audio` neodpovídá umístění MP4. Zkus soubor otevřít přímo v prohlížeči (zadej URL do adresního řádku) |
| Skladba je šedá, nejde na ni kliknout | `audio: null` → chybí audio soubor. Nahraj soubor a změň `null` na cestu |
| Stránka nejde vůbec otevřít (bílá obrazovka) | Chyba v JavaScriptu. Otevři konzoli (F12 → záložka Console) — přesně napíše na kterém řádku je chyba |

### Rychlá kontrola přes prohlížeč

1. Otevři `music.html` v Chrome (klikni na soubor → otevře se v prohlížeči)
2. Stiskni **F12** → otevře se panel Developer Tools
3. Klikni na záložku **Console**
4. Pokud je něco špatně, uvidíš červenou chybovou hlášku

---

## 7. Rychlý slovníček pojmů

| Pojem | Význam |
|-------|--------|
| **HTML** | Kostra stránky (nadpisy, tlačítka, struktura) |
| **CSS** | Vzhled (barvy, písma, mezery, animace) |
| **JS / JavaScript** | Logika a data (seznam skladeb, přehrávání, filtry) |
| **Pole / Array** | Seznam položek v hranatých závorkách `[...]` |
| **Objekt** | Jedna položka ve složených závorkách `{...}` |
| **Klíč: hodnota** | Např. `title: 'Můj song'` — pojmenovaná vlastnost |
| **null** | "prázdno" — daná vlastnost neexistuje, na stránce se nezobrazí |
| **FTP** | Způsob nahrávání souborů z počítače na server |
| **Editor** | Program pro úpravu kódu (doporučuji VS Code) |
| **Console** | Panel v prohlížeči (F12), který ukazuje chyby |

---

## 8. Závěrem

Jediné, co musíš dělat, je:

1. Nahrát MP4 + obrázek na FTP do správných složek
2. Přidat jeden blok do seznamu `TRACKS` v `music.html`
3. Nahrát upravený `music.html` na FTP

Všechno ostatní (vzhled, přehrávač, progress bar, filtrování) je už hotové a nemusíš se o to starat.

---

*Verze 1.0 · Platí pro `src/music.html` · SYSTEQ Ateliér můz*
