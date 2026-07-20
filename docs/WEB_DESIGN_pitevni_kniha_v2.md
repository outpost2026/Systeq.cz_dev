# WEB DESIGN — Pitevní kniha v2

**Datum:** 2026-07-20 | **Verze:** 2 (rozšířena o historii repa, 96 commitů)
**Účel:** Jediný zdroj pravdivých ponaučení z celého vývoje web_integrace_systeq — frontend (HTML/CSS/JS) + backend (PHP) + 3D scény (Three.js) + deployment (FTP).
**Rozsah:** src/index.html (SYSTEQ B2B), projekty/index.html (portfolio služeb), src/music.html (Ateliér můz), Demo_Threejs (3D scény), deploy/counter (PHP counter), send.php
**Určení:** Výukový materiál pro deva, instrukce pro LLM, ground truth pro rozhodování

---

## 1. Mapa superseded artefaktů

| Původní soubor | Co se přebírá | Co se zahazuje |
|----------------|---------------|----------------|
| `WEB_DESIGN_pitevni_kniha_v1.md` | WEB-001 až WEB-008, W1-W8 | Omezení na projekty/ pouze |
| `HANDOFF_V3.2_ITERATION.md` | Z-order bug, panel offset | 3D scéna detaily |
| `DESIGN_V0.4_B2B_PLAYBOOK.md` | Telefon typo (052→045 256) | Brand specifika |
| Tento dokument je **v2** — supersedes v1, přidává nálezy z 96 commitů historie repa. | — | — |

---

## 2. Sémantické nálezy — co bylo opraveno

### 2.1 Vyřešené rozpory

| Rozpor | Původní stav | Stav v GT |
|--------|-------------|-----------|
| send.php cesta | Lokálně existuje, HTTP 404 na serveru | ✅ FTP upload nutný |
| "Volné termíny" font | `.75rem` muted (nečitelné) | ✅ `.track-title` styl |
| CTA box pozice | Uprostřed stránky | ✅ Konec (tail funnel) |
| Garden karta pozice | První v gridu | ✅ Poslední (offgrid první) |
| Export Prod regex order | buildDevGUI strip pozdě → false matches | ✅ buildDevGUI first |
| Export Prod missing params | Operator emoce + banner nestripovány | ✅ Přidáno do regex |
| Wall panel Z-order | tgtZ = WALL_Z - 0.08 (za zdí) | ✅ tgtZ = WALL_Z + 0.14 |
| idle.png cesta | `golden/idle.png` (neexistuje na FTP) | ✅ `idle.png` |
| Chatbot icon | 7× změna (android→robot→keyboard→mobil→barva) | ✅ Final: #0D9488, 48×48 |
| Lab card order | Chatbot karta nebyla první | ✅ Přesunuta na první |
| Telefon číslo | `052` místo `045 256` | ✅ Opraveno |

### 2.2 Zachované unikátní části

- **Single-file HTML pattern** — žádný build step, vše inline (3 stránky: 924 + 995 + 575 ř.)
- **Export Prod dev workflow** — lil-gui dev panel → regex strip → clean production HTML
- **Three.js golden-master testy** — 9 testů, 4 fáze, Playwright + Pillow
- **PHP backend na Webzdarma** — omezený runtime (žádný iconv, mb_*, composer, SMTP)
- **Tři design accenty** — teal (SYSTEQ), rust (Ateliér), brick (Projekty) — sdílený dark theme

---

## 3. Katalog chyb

Číslování WEB-001 až WEB-018.

---

#### WEB-001: PII leak v report.html
**Doména:** Repo security | **Status:** Fixed

**Symptom:** report.html obsahoval jména klientů (p. Mašek, David, Ondřej Žikovský), "Důvěrné" označení, interní finanční split.

**Root cause:** Klient data byla v .gitignore, ale report.html nebyl považován za citlivý.

**Fix:** Jména → role (klient, zprostředkovatel). "Důvěrné" → smazáno. Interní split → agregován.

**Pravidlo:** W1 — PII sanitace před commitem. Jména třetích osob nahradit rolemi.

---

#### WEB-002: send.php HTTP 404
**Doména:** Backend / Deployment | **Status:** Fixed

**Symptom:** Formulář vracel HTTP 404 na `/projekty/send.php`.

**Root cause:** send.php existoval lokálně, nebyl uploadován na FTP.

**Fix:** FTP upload.

**Pravidlo:** W2 — FTP sync po každé PHP změně. Lokální existence ≠ produkční dostupnost.

---

#### WEB-003: iconv() fatal dependency
**Doména:** Backend / PHP | **Status:** Fixed

**Symptom:** `Fatal error: Call to undefined function iconv()`.

**Root cause:** Webzdarma hosting nemá iconv extension.

**Fix:** Nahradit regexem / string operacemi.

**Pravidlo:** W3 — Webzdarma: `function_exists()` před extenzí. Vyhýbat se: iconv, mb_*, IMAP, intl.

---

#### WEB-004: Email header injection
**Doména:** Backend / Security | **Status:** Fixed

**Symptom:** User email v `From:` headeru — možný CRLF injection.

**Root cause:** Raw user input v hlavičce bez sanitizace.

**Fix:** Pevný From (noreply@) + user email jen v `Reply-To:`. Sanitizace `preg_replace('/[^\x20-\x7E]/', '')`.

**Pravidlo:** W4 — Nikdy user input jako `From:`. Pevný From + Reply-To.

---

#### WEB-005: JS body stream double-read
**Doména:** Frontend / JavaScript | **Status:** Fixed

**Symptom:** `TypeError: body stream already read`.

**Root cause:** `r.json()` po `r.text()` — Response.body je ReadableStream (lze číst jednou).

**Fix:** `const t = await r.text(); JSON.parse(t)`.

**Pravidlo:** W5 — Response body číst jednou. JSON parse explicitně.

---

#### WEB-006: CTA box uprostřed stránky
**Doména:** Frontend / Layout | **Status:** Fixed

**Symptom:** CTA box mezi services a formulářem, ne na konci.

**Root cause:** Layout funnel ignorován.

**Fix:** CTA přesunuta za reference, před script.

**Pravidlo:** W6 — Single-page funnel: Hero → Services → Form → References → CTA (tail).

---

#### WEB-007: "Volné termíny" font nečitelný
**Doména:** Frontend / Typography | **Status:** Fixed

**Symptom:** `.75rem` + `text-muted` — důležitý CTA text nečitelný.

**Root cause:** Ad-hoc inline style bez kontextu.

**Fix:** `1.15rem/700/letter-spacing/.04em/#d0c8b8` (`.track-title` z music.html).

**Pravidlo:** W7 — Důležité info nestylovat jako muted. Používat existující design tokeny napříč stránkami.

---

#### WEB-008: Garden card na první pozici
**Doména:** Frontend / Content priority | **Status:** Fixed

**Symptom:** Zahradní práce první v gridu, ač offgrid je hlavní doména.

**Root cause:** Pořadí neaktualizováno po refactoru na portfolio.

**Fix:** Garden → poslední. Nové pořadí: offgrid → wood → iot → security → garden.

**Pravidlo:** W8 — Service karty dle business priority. Hlavní doména první.

---

#### WEB-009: Image path — FTP root vs lokální struktura
**Doména:** Frontend / Deployment | **Status:** Fixed

**Symptom:** 3D preview obrázek `idle.png` se nenačítá (404). Cesta `golden/idle.png`.

**Root cause:** Lokálně cesta `Demo_Threejs/tests/golden/idle.png` — na FTP neexistuje `golden/` adresář. Lokální cesta byla použita doslovně.

**Fix:** `idle.png` → zkopírovat do FTP root. Cesta v HTML: `idle.png`.

**Commit:** `eee454c`, `c64bc0c`

**Pravidlo:** W9 — Všechny cesty k assetům (obrázky, fonty, audio) ověřit proti FTP struktuře. Lokální `tests/golden/` ≠ produkční cesta.

---

#### WEB-010: Export Prod regex ordering
**Doména:** Frontend / Dev tooling | **Status:** Fixed

**Symptom:** Production HTML obsahoval dev-only kód (lil-gui panely) i po Export Prod. False matches v regex stripu.

**Root cause:** Dva regex stripy: `stripBuildDevGUI()` a `stripDevBanner()`. Špatné pořadí — `stripBuildDevGUI` běžel pozdě, takže dev kód v literal strings nebyl odstraněn.

**Fix:** `stripBuildDevGUI()` první, pak `stripDevBanner()`. Regex order = kritický.

**Commit:** `cc14ba3`

**Pravidlo:** W10 — Dev-to-prod transformace: regex order je kritický. Testovat na reálném HTML, ne jen na jednotlivých patternech.

---

#### WEB-011: Export Prod missing parameters
**Doména:** Frontend / Dev tooling | **Status:** Fixed

**Symptom:** Export prod HTML postrádal operator emotion parametry a dev banner/CSS zůstával v produkci.

**Root cause:** Regexy pro stripování nebyly aktualizovány při přidání nových dev features (operator emotion).

**Fix:** Přidány nové regex patterny pro operator emotion params + dev banner/CSS.

**Commit:** `73ca1e9`

**Pravidlo:** W11 — Dev-to-prod pipeline musí být udržována souběžně s každou novou dev feature. Checklist při přidání dev-only kódu.

---

#### WEB-012: Wall panel Z-order (Three.js)
**Doména:** Frontend / 3D | **Status:** Fixed

**Symptom:** Panely neviditelné — renderovaly se za wall plate (world z -2.58 vs wall surface -2.48).

**Root cause:** `tgtZ = WALL_Z - 0.08` — odečtení místo přičtení. Panely šly za stěnu.

**Fix:** `tgtZ = WALL_Z + 0.14`.

**Handoff:** `HANDOFF_V3.2_ITERATION.md`

**Pravidlo:** W12 — 3D pozicování: referenční body s explicitní kontrolou viditelnosti z kamery. Testovat po každé změně odsazení.

---

#### WEB-013: Icon design churn
**Doména:** Frontend / Design process | **Status:** Mitigated

**Symptom:** 7 commitů na jediný chatbot icon: android face → R2D2 robot → keyboard (40% larger) → mobile terminal → 48×48 sync → #0D9488. Každá změna vyžaduje full commit cycle.

**Root cause:** Žádný icon spec před implementací. Rozhodnutí učiněna ad-hoc v kódu, ne v design dokumentu.

**Commity:** `a524197`, `46694a9`, `6ac2be2`, `88e0d50`, `d712040`, `7665330` (7 commitů)

**Pravidlo:** W13 — Design asset (ikona, obrázek) specifikovat před implementací: (1) koncept, (2) SVG/PNG reference, (3) velikost, (4) barva. Až poté kód. 7 commitů na jednu ikonu = selhání procesu.

---

#### WEB-014: Micro-adjustment cascade
**Doména:** Frontend / CSS process | **Status:** Mitigated

**Symptom:** Sekvence mikro-změn velikostí: hero buttons +50% → contact font +30% → profile desc -25% → stack section -40% → use-card label +40% → Lab heading +40%. Každá vyžaduje samostatný commit.

**Root cause:** CSS hodnoty nastaveny ad-hoc bez design token stupnice. Každá změna = nová numerická hodnota, ne výběr z předdefinované škály.

**Commity:** `ecff6a0`, `cccad33`, `7a4be73`, `06a8b4e`, `96209e8`, `0eb2032`

**Pravidlo:** W14 — CSS typography: používat předdefinovanou font-size škálu (např. `.6rem .72rem .82rem .95rem 1.05rem 1.25rem 1.6rem`). Mikro-adjustment je symptom chybějícího design systému.

---

#### WEB-015: CSS var naming inconsistency napříč pilíři
**Doména:** Frontend / CSS architecture | **Status:** Known

**Symptom:** SYSTEQ používá `--accent: #0d9488`, Projekty používají `--accent: #b8541a`, Ateliér používá `--rust: #b7410e`. Stejný koncept (accent color) — různá jména.

**Root cause:** Každá stránka je samostatný single-file HTML bez sdíleného CSS. Žádný design token file.

**Pravidlo:** W15 — Sdílený design token systém (single CSS file nebo konvence). `--accent` musí být stejná sémantika napříč stránkami. Pokud se liší per-pilíř, použít `--accent-primary`, `--accent-projects`, `--accent-music`.

---

#### WEB-016: Phone number typo
**Doména:** Frontend / Content | **Status:** Fixed

**Symptom:** `+420 735 052 256` místo `+420 735 045 256`. Prostřední trojčíslí špatně.

**Root cause:** Překlep při zadávání. Chyba odhalena až při B2B playbook revizi (DESIGN_V0.4_B2B_PLAYBOOK.md), ne při code review.

**Pravidlo:** W16 — Kontaktní údaje (telefon, email, adresa) testovat na reálné doručitelnosti před commitem. Nejen vizuální kontrola.

---

#### WEB-017: Golden-master test maintenance
**Doména:** Testing / 3D | **Status:** Known

**Symptom:** 3D golden-master testy (Playwright + Pillow, 9 testů, 4 fáze) vyžadují manuální regeneraci PNG při každé vizuální změně scény. `--update-golden` flag.

**Root cause:** Vizuální testování pixel-by-pixel. Každá změna osvětlení, pozice kamery, barvy = nové golden mastery.

**Pravidlo:** W17 — Vizuální golden-master testy: akceptovat režii při UI změnách. Automatizovat regeneraci (`--update-golden`). Při každém UI commitu spustit testy, ne až při selhání.

---

#### WEB-018: LLM path confusion (src/ vs FTP root)
**Doména:** Documentation / Workflow | **Status:** Mitigated

**Symptom:** LLM agenti používají lokální cesty (`src/index.html`), ale na FTP serveru je kořen jinde (`/index.html`). Relativní cesty v HTML musí vycházet z FTP rootu.

**Root cause:** Repo má `src/` prefix pro produkční HTML, ale FTP hosting tento prefix nezná. Konvence je jen lokální.

**Fix:** AGENTS.md dokumentuje mapování. `new URL('send.php', location.href)` pro dynamické cesty.

**Pravidlo:** W18 — Všechny relativní cesty v HTML počítat od FTP rootu. `src/` je lokální konvence, neprodukční. AGENTS.md musí obsahovat explicitní mapování.

---

## 4. Průřezová pravidla W1-W18 (konsolidovaná)

### W1 — PII sanitace
Jakýkoliv HTML/MD soubor s referencí na třetí osobu musí projít PII sanitací před commitem.

### W2 — FTP sync po každé PHP změně
Každý PHP soubor nahrát na FTP. Lokální existence ≠ produkční dostupnost.

### W3 — Webzdarma PHP omezení
`function_exists()` před extenzí. Vyhýbat se: iconv, mb_*, IMAP, intl, composer.

### W4 — Email header bezpečnost
Nikdy user input jako `From:`. Pevný From + user email v `Reply-To:`.

### W5 — Response body jednorázový stream
Číst jednou. `JSON.parse(await r.text())`.

### W6 — Single-page funnel
Hero → Services → Form → References → CTA (tail).

### W7 — Design tokeny před inline
Důležité info (termíny, ceny) nestylovat jako muted.

### W8 — Business priority v gridu
Service karty dle priority. Hlavní doména první.

### W9 — Cesty k assetům proti FTP struktuře
Obrázky, fonty, audio — ověřit FTP cestu před commitem. Lokální `tests/golden/` ≠ `/`.

### W10 — Dev-to-prod regex order
Regulérní výrazy pro stripování dev kódu mají kritické pořadí. Testovat na celém HTML.

### W11 — Dev-to-prod pipeline maintenance
Každá nová dev feature = aktualizace stripovacích regexů.

### W12 — 3D pozicování
Referenční body s explicitní kontrolou viditelnosti z kamery.

### W13 — Design asset spec před implementací
Ikona: koncept → reference → velikost → barva. Poté kód. 7 commitů na 1 ikonu = procesní chyba.

### W14 — CSS typography škála
Předdefinovaná font-size škála. Mikro-adjustment = chybějící design systém.

### W15 — Sdílené design tokeny
`--accent` stejná sémantika napříč stránkami. Per-pilíř variance → prefixované proměnné.

### W16 — Kontaktní údaje testovat
Telefon, email — reálná doručitelnost před commitem.

### W17 — Golden-master údržba
`--update-golden` při UI změně. Testovat po každém UI commitu.

### W18 — FTP root cesty
Relativní cesty v HTML od FTP rootu. `src/` je lokální konvence.

---

## 5. Diagnostický checklist

### A — Bezpečnost
1. Obsahuje HTML/MD soubor jména třetích osob? (W1)
2. Je user input v email headers? (W4)
3. Je PHP extension použita bez `function_exists()`? (W3)
4. Jsou .env / credentials v repu? (W1)

### B — Deployment
5. Existuje send.php na FTP (nejen lokálně)? (W2)
6. Je FTP cesta shodná s HTML action URL? (W2)
7. Funguje PHP na hostingu? (W3)

### C — JavaScript
8. Čte se response body jen jednou? (W5)
9. Je JSON.parse v try/catch? (W5)
10. Je fetch URL dynamická (`new URL()`) nebo hardcoded? (W5)

### D — Layout & Design
11. Je CTA poslední sekce před scriptem? (W6)
12. Jsou ceny/termíny čitelné (ne muted)? (W7)
13. Je service grid řazen dle priority? (W8)
14. Je font konzistentní napříč stránkami? (W7, W14)
15. Je CSS var konvence jednotná napříč pilíři? (W15)

### E — Assety a cesty
16. Všechny cesty k obrázkům ověřeny proti FTP? (W9)
17. Neobsahuje cesta lokální prefix (golden/, src/)? (W9, W18)

### F — Dev tooling
18. Je Export Prod regex order správně? (W10)
19. Jsou všechny dev features pokryty stripovacími regexy? (W11)
20. Byl design asset specifikován před implementací? (W13)
21. Je kontaktní údaj otestován na doručitelnost? (W16)

### G — Three.js specific
22. Jsou 3D objekty viditelné z výchozí kamery? (W12)
23. Byly golden mastery regenerovány po UI změně? (W17)

### H — SEO
24. Existuje `<title>` a `<meta name="description">`?
25. Jsou headings (h1-h3) sémanticky strukturované?
26. Je favicon odkazován?
27. Jsou relativní cesty navigace konzistentní? (W18)

---

## 6. Decision framework

### 6.1 Kdy single-file HTML NEPOUŽÍT

| Situace | Proč nefunguje | Co místo toho |
|---------|---------------|---------------|
| Více než 3 stránky | Duplikace CSS/JS, údržba peklo | Static site generator |
| Sdílená komponenta (header/footer) | Copy-paste do každé stránky | PHP include |
| Interaktivní dashboard | State management v JS těžkopádný | SPA framework |
| Týmový vývoj | Merge konflikty | Komponentový framework |
| Design tokeny napříč stránkami | Žádné sdílení CSS proměnných | Sdílený CSS file |

### 6.2 Kdy single-file HTML POUŽÍT

| Situace | Proč funguje |
|---------|--------------|
| Jedna stránka / microsite | Nulový build step, okamžitý deploy |
| FTP hosting (Webzdarma) | Žádný runtime, žádné závislosti |
| LLM-assisted vývoj | Celý kontext v jednom souboru |
| MVP / prototyp | Rychlost > architektura |
| Portfolio stránka autora | Vše v jednom souboru |

### 6.3 Rozhodovací flowchart

```
Potřebuju webovou stránku?
├─ 1 stránka?
│  ├─ Statický obsah → Single-file HTML
│  ├─ Formulář → Single-file HTML + PHP
│  ├─ 3D scéna → Single-file HTML + Three.js (CDN)
│  └─ Dashboard/data → SPA framework
├─ 2-5 stránek?
│  ├─ Sdílený header/footer → PHP includes
│  └─ Každá jiná → Single-file + AGENTS.md s FTP mapou
└─ >5 stránek → Static site generator
```

---

## 7. Dědičnost — checklist pro nový web projekt

1. **Single-file HTML** — vše inline. Žádný build step.
2. **PHP backend** — `send.php` v cílovém adresáři. FTP upload po změně.
3. **PII sanitace** — žádná jména třetích osob v HTML/MD.
4. **Formulář** — JS fetch + `new URL()` + jednorázové body.
5. **Email headers** — pevný From + Reply-To. Sanitizace.
6. **Layout funnel** — Hero → Services → Form → References → CTA (tail).
7. **Design konzistence** — font, barvy z existujících stránek. Žádný ad-hoc inline.
8. **Business priority** — grid dle důležitosti.
9. **FTP upload** — po každé PHP změně.
10. **Asset cesty** — ověřit proti FTP rootu.
11. **Design spec** — ikony, barvy, velikosti před implementací.
12. **Kontaktní údaje** — testovat na doručitelnost.
13. **Dev-to-prod pipeline** — udržovat regex paralelně s dev features.

---

## 8. Statistiky

| Metrika | Hodnota |
|---------|---------|
| Celkem bugů (WEB-001 až WEB-018) | 18 |
| Fixed | 16 (89%) |
| Mitigated / Known | 2 (11%) — W13 icon churn, W15 css vars |
| Z toho frontend (HTML/CSS/JS/3D) | 10 (WEB-005,006,007,008,009,010,011,012,013,014) |
| Z toho backend (PHP/security) | 3 (WEB-002,003,004) |
| Z toho repo/doc/process | 5 (WEB-001,015,016,017,018) |
| Cross-cutting pravidel (W1-W18) | 18 |
| Fix commity v historii | 32 (33% z 96 celkem) |
| Z toho design iterace (ne bugy) | ~20 (icon churn, micro-adjustment) |
| Skutečné bugy (funkční chyby) | ~12 (zbylé po odfiltrování design iterací) |

---

## 9. Kategorie chyb — sémantická klasifikace

### 9.1 Deployment / FTP (4 chyby)
WEB-002 (404 send.php), WEB-009 (image path), WEB-018 (LLM path), W2/W9/W18 pravidla
→ **Root cause:** Žádný deployment checklist. Lokální struktura ≠ FTP.

### 9.2 PHP hosting omezení (2 chyby)
WEB-003 (iconv), WEB-004 (header injection)
→ **Root cause:** Webzdarma runtime není standardní PHP. Composer neexistuje.

### 9.3 JavaScript streaming (1 chyba)
WEB-005 (body stream double-read)
→ **Root cause:** Neznalost Fetch API — ReadableStream jednorázový.

### 9.4 Layout / UX (4 chyby)
WEB-006 (CTA pozice), WEB-007 (font), WEB-008 (karta pořadí), WEB-014 (micro-adjustment)
→ **Root cause:** Žádný layout spec před kódem.

### 9.5 Dev tooling (2 chyby)
WEB-010 (regex order), WEB-011 (missing params)
→ **Root cause:** Export Prod pipeline není testovaná jako celek.

### 9.6 3D / Three.js (2 chyby)
WEB-012 (Z-order), WEB-017 (golden master)
→ **Root cause:** 3D pozicování je křehké. Manuální testování nestačí.

### 9.7 Design process (2 chyby)
WEB-013 (icon churn), WEB-015 (css vars)
→ **Root cause:** Chybí design spec a sdílený token systém.

### 9.8 Content (1 chyba)
WEB-016 (phone typo)
→ **Root cause:** Žádná verifikace kontaktních dat.

---

*WEB_DESIGN_pitevni_kniha_v2.md — 2026-07-20 — v2 — Agregace všech nálezů z 96 commitů historie web_integrace_systeq. Supersedes v1.*
