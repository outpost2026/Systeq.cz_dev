# WEB DESIGN — Pitevní kniha v1

**Datum:** 2026-07-20 | **Verze:** 1
**Účel:** Jediný zdroj pravdivých ponaučení z vývoje projekty/index.html — frontend (HTML/CSS/JS) + backend (PHP) + deployment (FTP).
**Rozsah:** projekty/index.html, send.php, klient.html, report.html
**Určení:** Výukový materiál pro deva, instrukce pro LLM, ground truth pro rozhodování

---

## 1. Mapa superseded artefaktů

| Původní soubor | Co se přebírá | Co se zahazuje |
|----------------|---------------|----------------|
| Tento dokument je první verzí — žádné předchozí pitevní knihy pro web design neexistují. | — | — |

---

## 2. Sémantické nálezy — co bylo opraveno

### 2.1 Vyřešené rozpory

| Rozpor | Původní stav | Stav v GT |
|--------|-------------|-----------|
| send.php cesta | Lokálně existuje, HTTP 404 na serveru | ✅ Fixed — FTP upload |
| "Volné termíny" font | `.75rem` (příliš malý, tmavý) | ✅ Fixed — `.track-title` styl (1.15rem/700/#d0c8b8) |
| CTA box pozice | Uprostřed stránky (za services, před form) | ✅ Fixed — přesun na konec (za reference) |
| Garden karta pozice | První v gridu | ✅ Fixed — poslední (offgrid první) |

### 2.2 Zachované unikátní části

- **Single-file HTML pattern** — žádný build step, vše inline
- **PHP backend na Webzdarma** — omezený runtime (žádný iconv, composer, SMTP)
- **JSONL logování** — strukturovaný log do `data/` místo SMTP fallbacku

---

## 3. Katalog chyb

Číslování WEB-001 až WEB-008.

---

#### WEB-001: PII leak v report.html
**Doména:** Frontend / Repo security | **Status:** Fixed

**Symptom:** report.html obsahoval jména klientů (p. Mašek→klient, David→zprostředkovatel, Ondřej Žikovský), označení "Důvěrné" a interní finanční split (kolik jede komu).

**Root cause:** Klient data byla v .gitignore, ale report.html nebyl považován za citlivý. Jména třetích osob unikla do veřejného repa.

**Fix:**
- p. Mašek → klient
- David → zprostředkovatel
- Ondřej Žikovský → odstraněn (IRE)
- "Důvěrné" → smazáno
- Interní split → agregován na projektovou úroveň

**Pravidlo:** W1 — Jakýkoliv HTML soubor s referencí na třetí osobu (klient, dodavatel, zprostředkovatel) musí projít PII sanitací před commitem. Jména nahradit rolemi.

---

#### WEB-002: send.php HTTP 404
**Doména:** Backend / Deployment | **Status:** Fixed

**Symptom:** Formulář vracel HTTP 404 na `/projekty/send.php`. JS log: `HTTP 404`.

**Root cause:** send.php existoval lokálně, ale nebyl uploadován na FTP. Server hledá soubory v FTP rootu, ne v lokálním repu.

**Fix:** FTP upload send.php na Webzdarma.

**Pravidlo:** W2 — Každý PHP soubor (send.php, atd.) musí být po vytvoření/úpravě nahrán na FTP. Lokální existence ≠ produkční dostupnost.

---

#### WEB-003: iconv() fatal dependency
**Doména:** Backend / PHP | **Status:** Fixed

**Symptom:** `Fatal error: Call to undefined function iconv()`. PHP skript padal dřív, než stihl cokoliv zpracovat.

**Root cause:** Webzdarma hosting nemá `iconv` extension. Původní kód používal iconv pro sanitaci diakritiky v email hlavičkách.

**Fix:** Nahradit iconv jednoduchým regexem / string operacemi (bez diakritiky).

**Pravidlo:** W3 — PHP skript pro Webzdarma: před použitím extenze ověř `function_exists()`. Vyhýbat se: iconv, mb_*, IMAP, intl.

---

#### WEB-004: Email header injection
**Doména:** Backend / Security | **Status:** Fixed

**Symptom:** User email v `$headers .= "From: $email"` — možný header injection.

**Root cause:** User input (email) nebyl sanitizován. `$email` mohl obsahovat `\r\nBcc: attacker@evil.com`.

**Fix:**
```php
$email = preg_replace('/[^\x20-\x7E]/', '', $email);  // ASCII only
$headers = "From: noreply@systeq.cz\r\nReply-To: $email";
```

**Pravidlo:** W4 — Nikdy nepoužívat user input jako `From:` header. Vždy pevný From + user email do `Reply-To`.

---

#### WEB-005: JS body stream double-read
**Doména:** Frontend / JavaScript | **Status:** Fixed

**Symptom:** `TypeError: body stream already read` při zpracování fetch response.

**Root cause:** `Response.body` je ReadableStream — lze číst jen jednou. Původní kód volal `r.json()` po `r.text()`.

**Fix:**
```javascript
const t = await r.text();  // čti jednou
let d;
try { d = JSON.parse(t); } catch(_) { throw new Error('Chyba komunikace se serverem.'); }
```

**Pravidlo:** W5 — Response body je jednorázový stream. Čti jednou (`r.text()` nebo `r.json()`), nikdy obojí. JSON parse explicitně.

---

#### WEB-006: CTA box uprostřed stránky
**Doména:** Frontend / Layout | **Status:** Fixed

**Symptom:** CTA box (Máte konkrétní představu? Ozvěte se + telefon/email) byl mezi services a formulářem, nikoliv na konci stránky jako finální call-to-action.

**Root cause:** Layout navržen bez zohlednění funnelu: služby → formulář → reference → CTA (konec). CTA byl před formulářem.

**Fix:** CTA section přesunuta HTML editací na konec — za reference, před script tag.

**Pravidlo:** W6 — Single-page layout funnel: Hero → Services → Form → References → CTA (tail). CTA je poslední prvek před scriptem.

---

#### WEB-007: "Volné termíny" font nečitelný
**Doména:** Frontend / Typography | **Status:** Fixed

**Symptom:** `style="color:var(--text-muted);font-size:.75rem"` — písmo příliš malé a tmavé pro důležitý CTA text (urgentní informace o volných termínech).

**Root cause:** Inline style použit bez zohlednění kontextu. `.75rem` + `#64748b` (text-muted) = špatná čitelnost na dark bg.

**Fix:**
```html
<span style="font-size:1.15rem;font-weight:700;letter-spacing:.04em;color:#d0c8b8;line-height:1.3">
```
Styl převzat z `.track-title` v music.html.

**Pravidlo:** W7 — Důležité informace (termíny, ceny, urgent) nikdy nestylovat jako muted. Používat existing design tokeny z jiných stránek (`.track-title`) místo ad-hoc inline hodnot.

---

#### WEB-008: Garden card na první pozici
**Doména:** Frontend / Content priority | **Status:** Fixed

**Symptom:** Karta "Zahradní práce" byla první v services gridu (zleva nahoře), ačkoliv offgrid je hlavní doména.

**Root cause:** Původní pořadí: garden, offgrid, wood, iot, security. Po refactoru na portfolio zůstalo garden na první pozici.

**Fix:** HTML edit — garden přesunuta na poslední pozici. Nové pořadí: offgrid → wood → iot → security → garden.

**Pravidlo:** W8 — Service karty řadit podle business priority, ne podle abecedy nebo data vzniku. Hlavní doména (offgrid) je první.

---

## 4. Průřezová pravidla W1-W8 (konsolidovaná)

### W1 — PII sanitace
Jakýkoliv HTML/MD soubor s referencí na třetí osobu musí projít PII sanitací před commitem. Jména nahradit rolemi (klient, zprostředkovatel).

### W2 — FTP sync po každé PHP změně
Každý PHP soubor (send.php, config) musí být po vytvoření/úpravě nahrán na FTP. Lokální existence ≠ produkční dostupnost.

### W3 — Webzdarma PHP omezení
Před použitím PHP extenze ověř `function_exists()`. Vyhýbat se: iconv, mb_*, IMAP, intl. Composer neexistuje — vše ručně.

### W4 — Email header bezpečnost
Nikdy user input jako `From:` header. Pevný From (noreply@) + user email do `Reply-To:`. Sanitizace `preg_replace('/[^\x20-\x7E]/', '', $input)`.

### W5 — Response body jednorázový stream
Čti jednou (`r.text()` nebo `r.json()`). JSON parse explicitně přes `JSON.parse(await r.text())`.

### W6 — Single-page funnel
Pořadí sekcí: Hero → Services → Form → References → CTA (tail). CTA je poslední vizuální prvek.

### W7 — Design tokeny před inline
Důležité info (termíny, ceny) nestylovat jako muted. Používat existující design tokeny z jiných stránek namísto ad-hoc inline hodnot.

### W8 — Business priority v gridu
Service karty řadit podle business priority. Hlavní doména (offgrid) první, vedlejší (zahrada) poslední.

---

## 5. Diagnostický checklist — pro web projekt

### A — Bezpečnost
1. Obsahuje HTML/MD soubor jména třetích osob? → sanitizovat (W1)
2. Je user input v email headers? → pevný From (W4)
3. Je PHP extension použita bez `function_exists()`? → přidat guard (W3)
4. Jsou .env / credentials v repu? → .gitignore + FTP only

### B — Deployment
5. Existuje send.php na FTP (nejen lokálně)? (W2)
6. Je FTP cesta shodná s HTML action URL? (W2)
7. Funguje PHP na hostingu? (zkusit info.php)

### C — JavaScript
8. Čte se response body jen jednou? (W5)
9. Je JSON.parse v try/catch? (W5)
10. Je fetch URL dynamická (new URL()) nebo hardcoded? (W5)

### D — Layout & Design
11. Je CTA poslední sekce před scriptem? (W6)
12. Jsou ceny/termíny čitelné (ne muted)? (W7)
13. Je service grid řazen dle priority? (W8)
14. Je font konzistentní napříč stránkami? (W7)

### E — SEO
15. Existuje `<title>` a `<meta name="description">`?
16. Jsou headings (h1-h3) sémanticky strukturované?
17. Jsou service karty v `<section>` s `id`?
18. Je favicon odkazován?

---

## 6. Decision framework

### 6.1 Kdy single-file HTML NEPOUŽÍT

| Situace | Proč nefunguje | Co místo toho |
|---------|---------------|---------------|
| Více než 3 stránky | Duplikace CSS/JS, údržba peklo | Static site generator (Eleventy, Hugo) |
| Sdílená komponenta (header/footer) | Copy-paste do každé stránky | Server-side include (PHP include) |
| Interaktivní dashboard | State management v JS je těžkopádný | SPA framework (Vue, React, Svelte) |
| Týmový vývoj | Merge konflikty v 2000+ řádcích | Komponentový framework |

### 6.2 Kdy single-file HTML POUŽÍT

| Situace | Proč funguje |
|---------|--------------|
| Jedna stránka / microsite | Nulový build step, okamžitý deploy |
| FTP hosting (Webzdarma) | Žádný runtime, žádné závislosti |
| LLM-assisted vývoj | Celý kontext v jednom souboru |
| MVP / prototyp | Rychlost > architektura |
| Portfolio stránka autora | Vše vidět v jednom souboru |

### 6.3 Rozhodovací flowchart

```
Potřebuju webovou stránku?
├─ Jedna stránka?
│  ├─ Statický obsah? → Single-file HTML
│  ├─ Formulář? → Single-file HTML + PHP (send.php)
│  └─ Dashboard/data? → Zvaž SPA
├─ 2-5 stránek?
│  ├─ Sdílený header/footer? → PHP includes
│  └─ Každá stránka jiná? → Single-file HTML (duplikace je OK)
└─ >5 stránek? → Static site generator / framework
```

---

## 7. Dědičnost — checklist pro nový web projekt

Při zakládání nové HTML stránky pro Webzdarma:

1. **Single-file HTML** — vše inline (CSS + JS). Žádný build step.
2. **PHP backend** — `send.php` v cílovém adresáři. FTP upload po změně.
3. **PII sanitace** — žádná jména třetích osob v HTML/MD.
4. **Formulář** — JS fetch s `new URL('send.php', location.href)` + jednorázové čtení body.
5. **Email headers** — pevný From, user input jen v Reply-To. Sanitizace ASCII.
6. **Layout funnel** — Hero → Services → Form → References → CTA (tail).
7. **Design konzistence** — font, barvy, spacing dle existujících stránek.
8. **Business priority** — service grid řazen dle důležitosti, ne abecedy.
9. **FTP upload** — po každé změně PHP/send.php nahrát na FTP.

---

## 8. Statistiky

| Metrika | Hodnota |
|---------|---------|
| Celkem bugů (WEB-001 až WEB-008) | 8 |
| Fixed | 8 (100%) |
| Z toho frontend (HTML/CSS/JS) | 5 (WEB-005, 006, 007, 008) |
| Z toho backend (PHP/security) | 3 (WEB-002, 003, 004) |
| Z toho repo security | 1 (WEB-001) |
| Cross-cutting pravidel (W1-W8) | 8 |

---

*WEB_DESIGN_pitevni_kniha_v1.md — 2026-07-20 — v1 — První pitevní kniha pro web design. Vychází z Fáze 1 projekty/index.html.*
