# DEV MARATÓN: SYSTEQ.CZ v1.0 BETA — KOMPLETNÍ PLÁN

**Verze:** 1.0
**Datum:** 10.6.2026
**Cíl:** Produkční beta 1.0 — vicevrstvý B2B web s funkčními parsing demy, prezentací autora a wow efektem na CEO Moodpasty.

---

## 1. ARCHITEKTURA WEBU (MULTI-LAYER, HIGH SNR)

Změna z one-page → vicevrstvý web při zachování přehlednosti a minimální datové zátěže.

### 1.1. Site Map

```
systeq.cz/
│
├── /                     # Landing — Hero + brand + CTA
├── /stack                # Core technology stack
├── /parser/              # Parser demos hub
│   ├── /parser/vcf       # VCF post-mortem analyzer
│   └── /parser/dxf       # DXF preprocessing demo
├── /author               # Autor profil, cv, portfolio
├── /solutions            # B2B/B2C solution areas
│   ├── /solutions/cnc    # CNC automation
│   ├── /solutions/offgrid# Off-grid LFP & FV
│   └── /solutions/iot    # IoT & perimeter
├── /contact              # Kontakt + API spec
└── /docs                 # API documentation (link na GitHub Pages)
```

### 1.2. Principy vrstvení

| Princip | Aplikace |
|---------|----------|
| **Progresivní zpřístupnění** | Každá sekce = samostatná "vrstva". Uživatel vidí jen to, co potřebuje. CEO vidí marketing → klikne na demo → vidí techniku. |
| **Single purpose per page** | Každá podstránka má jednu primární CTA. Žádný noise. |
| **High SNR** | Žádné hero slidy, žádné carousely, žádná stock fotografie. Každý pixel nese informaci. |
| **Statická kostra + JS interakce** | Základ = čisté HTML/CSS (okamžitý render). JS pouze pro loading stavů a drag&drop interakce. |
| **Žádný SPA framework** | Prosté HTML soubory. Přechody mezi sekcemi = buď in-page scroll (#hash), nebo prostý link. |

---

## 2. STRUKTURA PODSTRÁNEK — KOMPONENTY

### 2.1. Landing (/)

Současná beta 0.2 je v pořádku jako základ. Rozšířit:

**Hero sekce** — zůstává, doplnit:
- Pod-headline s explicitním mentionem autora: *"Ondřej Soušek — AI-Augmented Systems Engineer"*
- Trust badge: *"github.com/outpost2026 · 7 public repositories"*

**Sekce "Kdo jsem"** — nová:
- 3 věty: CNC operátor → self-taught Python developer → off-grid stavitel
- Odkaz na /author

**CTA blok** — tři cesty:
1. "Mám CNC zakázku → ukázat demo" → /parser/
2. "Potřebuji IoT/automatizaci" → /solutions/
3. "Chci off-grid řešení" → /solutions/offgrid

### 2.2. Stack (/stack)

Současná Core Stack sekce z beta 0.2 je OK. Rozšířit o:
- LFP SoC predict pipeline (Python, ML)
- OPC UA telemetrie (asyncua, real-time monitoring)
- ESP32 firmware (C++, Arduino)
- Sensor fusion (weight detector + PIR)

Formát: stejné karty jako beta 0.2, 4 nové karty.

### 2.3. Parser Demo (/parser/vcf, /parser/dxf) — KLÍČOVÁ KOMPONENTA

#### 2.3.1. Layout dema

```
┌─────────────────────────────────────────────────────────┐
│  [VCF] │ [DXF]  ← tab switching                        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌───────────────────┐  ┌───────────────────────────┐  │
│  │   DROP ZONE       │  │  FILE INFO                │  │
│  │   ┌───────────┐   │  │  Název: sample_01.VCF     │  │
│  │   │  ⬇        │   │  │  Velikost: 14.2 KB       │  │
│  │   │ Drag &    │   │  │  Verze stroje: Ruida v3  │  │
│  │   │ Drop here │   │  │  Segmenty: 47            │  │
│  │   └───────────┘   │  └───────────────────────────┘  │
│  │  [PARSE] button   │                                 │
│  └───────────────────┘                                 │
├─────────────────────────────────────────────────────────┤
│  ╔═══════════════════════════════════════════════════╗  │
│  ║  VÝSLEDKY PARSOVÁNÍ (3 role views)              ║  │
│  ║  [ CEO ] [ CTO ] [ DESIGN ]  ← role tabs        ║  │
│  ╚═══════════════════════════════════════════════════╝  │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │  (Vizualizace dle vybrané role)                 │   │
│  │                                                  │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

#### 2.3.2. VCF Parser — Výstupy per role

##### CEO VIEW (Cíle: ROI, peníze, rozhodnutí)

| Metrika | Vizualizace | Popis |
|---------|-------------|-------|
| **Čas řezu** | Velké číslo + progress bar (skutečný vs. odhad firmy) | "14.7 s → o 23 % rychleji než manuální odhad" |
| **Úspora na zakázce** | Kč hodnota (výchozí kurzy: 300 Kč/hod stroj) | "59 Kč úspora na této zakázce" |
| **Waste Factor** | Semafor (zelená < 5 %, žlutá < 15 %, červená > 15 %) | "3.2 % — optimální" |
| **Bottleneck detekce** | Textový výstup: "Segment H2 vrstva 3: 40 % celkového času" | Kde stroj ztrácí čas |
| **Cenový návrh** | Automatická kalkulace: "Doporučená cena: 240 Kč/ks" | Implementace multi-tier pricing z market analýzy |
| **ERP export** | Tlačítko "Stáhnout CSV pro Odoo" | Hotový CSV formát |

##### CTO VIEW (Cíle: technická data, validace, integrace)

| Metrika | Vizualizace | Popis |
|---------|-------------|-------|
| **Segment breakdown** | Tabulka: Segment ID → typ → čas [s] → waste [%] | Per-segment detail |
| **H2 vrstvy analýza** | Hierarchický strom (zanoření segmentů) | Odhalení struktury kresby |
| **Technologická rizika** | Červené/žluté/zelené tagy | "Posun prvku detekován", "Fixace kritická" |
| **Velocity profiling** | SVG graf: rychlost hlavy v čase | Odhalení zbytečných přejezdů |
| **JSON raw output** | Kolapsovatelný <pre> blok | Pro technickou validaci |
| **API endpoint** | Zobrazení volaného endpointu + payload struktury | "POST /api/v1/parse-vcf → 200 OK" |

##### DESIGN VIEW (Cíle: vizuální kontrola, vrstvy, kvalita)

| Metrika | Vizualizace | Popis |
|---------|-------------|-------|
| **Cut path vizualizace** | SVG canvas: 2D vykreslení drah nástroje | Barevně odlišené průchody |
| **Vrstvy (layers)** | Barevně odlišené overlay vrstvy | Každá barva = jiný nástroj/hloubka |
| **Materiálový list** | Tabulka: rozměr, plocha, odpad | Pro designéra |
| **Nástrojová mapa** | Který nůž kde řeže | "Nůž 1 (6mm): 80 % délky" |

#### 2.3.3. DXF Parser — Výstupy per role

##### CEO VIEW
| Metrika | Vizualizace |
|---------|-------------|
| **Počet dílů** | Velké číslo |
| **Celková délka řezu** | Metry + odhad času |
| **Cenová kalkulace** | Kč (dle zvoleného tarifu) |
| **Materiálová vytíženost** | Procenta |

##### CTO VIEW
| Metrika | Vizualizace |
|---------|-------------|
| **Entity breakdown** | Tabulka: entity typ (line, arc, circle) → count |
| **Geometrické nekonzistence** | Seznam chyb: přerušené křivky, duplicitní entity |
| **Bounding box** | Rozměry: XxY mm |
| **JSON výstup** | Kolapsovatelný <pre> blok |
| **Nesting suggestion** | Procento využití desky |

##### DESIGN VIEW
| Metrika | Vizualizace |
|---------|-------------|
| **2D náhled DXF** | SVG canvas s geometrií |
| **Layer browser** | Checkbox seznam všech layers, toggle visibility |
| **Barevné kódování** | Dle entity typu |
| **Měřítko** | Zobrazení reálných rozměrů |

---

## 3. TECHNICKÁ ARCHITEKTURA

### 3.1. Front-end

```
systeq.cz/
├── index.html              # Landing
├── stack.html              # Core stack
├── parser/
│   ├── index.html          # Parser hub (tabs VCF/DXF)
│   ├── vcf.html            # VCF demo standalone
│   └── dxf.html            # DXF demo standalone
├── author.html             # O autorovi
├── solutions.html          # Solutions hub
├── contact.html            # Kontakt
├── style.css               # Sdílený CSS (CSS proměnné, reset, komponenty)
├── js/
│   ├── parser-core.js      # Sdílená logika parser dema
│   ├── render-ceo.js       # CEO view renderer
│   ├── render-cto.js       # CTO view renderer
│   ├── render-design.js    # Design view renderer
│   └── svg-utils.js        # SVG kreslení toolpathů
└── assets/
    └── (žádné — vše inline nebo svg)
```

**Zásady:**
- Sdílený CSS soubor, žádný inline style kromě kritického render-blocking CSS pro hero
- JS moduly oddělené logikou, import přes ES modules nebo prostý script load
- Žádný build step — prosté .html/.css/.js nahrání na Webzdarma FTP

### 3.2. Backend (samostatná kapitola — pouze architektonický nástřel)

```
Parser Engine (Python, GCP Cloud Run)
    │
    ├── POST /api/v1/parse-vcf    ← VCF parser endpoint
    ├── POST /api/v1/parse-dxf    ← DXF parser endpoint
    ├── GET  /api/v1/health       ← Health check
    └── GET  /api/v1/version      ← Engine version
```

Detaily deploy a autentizace budou řešeny v samostatném dokumentu.

### 3.3. Data flow parser dema

```
User (Webzdarma)
  │
  ├── Drag & drop file do dropzone
  │   → JS čte File API (client-side validace přípony, velikosti)
  │
  ├── Click "Parse"
  │   → JS vytvoří FormData { file: File }
  │   → fetch(POST /api/v1/parse-{format}, { body: FormData })
  │   → Zobrazí loading spinner (nejdéle 5s timeout)
  │
  ├── Response 200
  │   → JSON payload
  │   → JS rozhodne: který view (CEO/CTO/Design → localStorage nebo default CEO)
  │   → renderCEO(data), renderCTO(data), renderDesign(data)
  │   → swap třídou .active-tab na role tlačítkách
  │
  └── Response 4xx/5xx/Timeout
      → Chybová hláška (srozumitelná, ne technická)
      → Možnost "Zkusit demo data" (fallback JSON)
```

---

## 4. DESIGN A GRAFICKÉ PRVKY

### 4.1. Rozšíření design systému (oproti beta 0.2)

| Prvek | Beta 0.2 | v1.0 Beta |
|-------|----------|-----------|
| **Typografie** | JetBrains Mono | JetBrains Mono + Space Grotesk (pro delší texty) |
| **Karty** | 1 typ | 3 typy: stack karta, parser karta, solution karta |
| **SVG ikony** | Pouze GitHub/LinkedIn | + ikony pro: Python, Docker, GCP, OPC UA, ESP32, LFP, FV, sensor |
| **Progress indikátor** | Chybí | Loading bar při upload/parse, stavový automat |
| **Role switcher** | Chybí | CEO/CTO/Design tab bar (viz 2.3.2) |
| **SVG canvas** | Chybí | Pro vykreslení toolpathů, DXF geometrie |
| **Animace** | Žádné | Minimal: loading pulse, hover na kartách, fade-in výsledků |

### 4.2. Stavový automat parser dema

```
IDLE ──(drag file)──→ FILE_SELECTED ──(click Parse)──→ PARSING ──(success)──→ RESULTS
                        │                                    │                    │
                        └──(click Clear)──→ IDLE              └──(error)──→ ERROR ──(retry)──→ PARSING
                                                                                  │
                                                                                  └──(load demo data)──→ RESULTS
```

Každý stav má vizuální reprezentaci:
- **IDLE**: prázdná dropzone, dashed border
- **FILE_SELECTED**: plný border, zobrazen název souboru, aktivní tlačítko Parse
- **PARSING**: loading spinner, progress bar (simulovaný na 5s), deaktivovaná dropzone
- **RESULTS**: results grid + role switcher + export buttons
- **ERROR**: červený border, chybová zpráva, tlačítka "Zkusit znovu" a "Použít demo data"

---

## 5. PARSER OUTPUT DESIGN — DETAILNÍ SPECIFIKACE

### 5.1. CEO View — Wireframe

```
┌────────────────────────────────────────────────────────────┐
│  [CEO]  [CTO]  [Design]              Export: [CSV] [JSON] │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │  14.7 s  │  │  3.2 %   │  │  59 Kč   │  │  VÝBORNĚ │  │
│  │ Čas řezu │  │  Waste   │  │ Úspora   │  │  Stav    │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Časová osa řezu (SVG horizontal bar chart)        │    │
│  │  ████████████████░░░░░░░░░░░░░░░░  Segment 1 60%  │    │
│  │  ██████░░░░░░░░░░░░░░░░░░░░░░░░░░  Segment 2 25%  │    │
│  │  ███░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  Segment 3 15%  │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Doporučená cena: 240 Kč/ks                        │    │
│  │  [Stáhnout CSV pro Odoo]                           │    │
│  └────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────┘
```

### 5.2. CTO View — Wireframe

```
┌────────────────────────────────────────────────────────────┐
│  [CEO]  [CTO]  [Design]              Export: [CSV] [JSON] │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─── Segment Breakdown ───────────────────────────────┐   │
│  │  ID │ Typ │ Čas [s] │ Waste [%] │ Riziko           │   │
│  │─────│─────│─────────│───────────│──────────────────│   │
│  │ S01 │ H2  │   5.2   │   1.1     │ ✅ OK            │   │
│  │ S02 │ H1  │   3.8   │   0.8     │ ⚠️ Posun prvku  │   │
│  │ S03 │ H2  │   0.7   │   1.3     │ ❌ Fixace krit.  │   │
│  └────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─── Raw JSON ───────────────────────────────────────┐   │
│  │  { "segments": [ { "type": "H2", ... } ],          │   │
│  │    "metadata": { "version": "v18.3", ... } }       │   │
│  │  [Copy JSON]                                       │   │
│  └────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─── API Info ───────────────────────────────────────┐   │
│  │  POST /api/v1/parse-vcf → 200 OK (1.24s)          │   │
│  │  Engine: v18.3 deterministic                       │   │
│  └────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────┘
```

### 5.3. Design View — Wireframe

```
┌────────────────────────────────────────────────────────────┐
│  [CEO]  [CTO]  [Design]              Export: [SVG] [DXF] │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────┐  ┌─── Layers ───────────────────┐    │
│  │  SVG Canvas     │  │  ☑ Cut path (červená)       │    │
│  │  (2D toolpath   │  │  ☑ Engrave (modrá)           │    │
│  │   vizualizace)  │  │  ☐ Scoring (zelená)          │    │
│  │                 │  │  ☐ Travel (šedá)             │    │
│  └─────────────────┘  └─────────────────────────────┘    │
│                                                             │
│  ┌─── Nástrojová mapa ───────────────────────────────┐   │
│  │  Nůž 1 (6mm oscilace): 80 % délky                │   │
│  │  Nůž 2 (3mm V-cut): 20 % délky                   │   │
│  └────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────┘
```

---

## 6. ROZŠÍŘENÍ O B2C SEKCE

### 6.1. Sekce /solutions/offgrid

Prezentace autorových off-grid realizací:
- Energy Hub (35 m² dřevostavba)
- FVE 2,4 kWp + LiFePO4 17,5 kWh (630 Ah)
- SoC predikční pipeline (Python ML model pro predikci stavu baterie na základě r_int)
- IoT monitoring (ESP32 + senzorová fúze)

**Vizualizace:**
- SVG diagram off-grid systému (panely → MPPT → baterie → střídač → zátěž)
- Reálné grafy SoC, napětí, proudu (z LFP_soc_predict_pipeline)
- Kalkulačka: "Spočítejte si svůj off-grid systém"

### 6.2. Sekce /solutions/iot

- Outpost Security Perimeter (váhový detektor + PIR + dvoustupňová validace)
- ESP32 firmware (C++)
- Sensor fusion pipeline
- OPC UA telemetrický agent

### 6.3. Sekce /solutions/cnc

- VCF parser (post-mortem analýza výroby)
- DXF preprocessing pipeline
- OEE monitoring
- ERP integrace (Odoo, CSV, REST API)

---

## 7. AUTOR PREZENTACE (/author)

### 7.1. Struktura

```
┌────────────────────────────────────────────────────────────┐
│  Ondřej Soušek                                             │
│  AI-Augmented Systems Engineer                             │
│  ─────────────────────────────────────────────────────────  │
│                                                             │
│  ╔═══════════════════════════════════════════════════════╗  │
│  ║  "Hledám funkční řešení v balastu."                  ║  │
│  ╚═══════════════════════════════════════════════════════╝  │
│                                                             │
│  3 domény:                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ CNC & CAM    │  │ IoT & Cloud  │  │ Off-grid     │     │
│  │ Automatizace  │  │ Automatizace │  │ Stavitelství │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                             │
│  Kontakt: tel, email, GitHub, LinkedIn                     │
│                                                             │
│  ═══════ CV ═══════                                         │
│  --> Zde kompletní CV (z dokumentace, přeformátované)       │
│                                                             │
│  ═══════ Projekty ═══════                                   │
│  --> VCF parser, CNC_2_LLM, cad2llm, Outpost Security,     │
│      LFP SoC Predict, Energy Hub                            │
│                                                             │
│  ═══════ Timeline ═══════                                   │
│  --> Časová osa: off-grid (2024) → JAPS CNC (2025)         │
│      → Moodpasta (2026) → SYSTEQ (2026)                    │
└────────────────────────────────────────────────────────────┘
```

---

## 8. EXEKUČNÍ PLÁN (DEV MARATHON)

### Fáze 0: Příprava kontextu (30 min)

- [ ] Doplň chybějící dokumenty (viz sekce 9)
- [ ] Ověř aktuální GCP Cloud Run endpointy a auth schéma
- [ ] Získej FTP přístup k Webzdarma administraci

### Fáze 1: Kostra webu (2 hodiny)

- [ ] Vytvoř adresářovou strukturu na FTP
- [ ] Nahraj sdílený style.css s CSS proměnnými a resetem
- [ ] Vytvoř index.html (landing) — vylepšení beta 0.2
- [ ] Vytvoř author.html (autor profil)

### Fáze 2: Parser demo — front-end (4 hodiny)

- [ ] Vytvoř parser/vcf.html s dropzone, file info, loading stavy
- [ ] Vytvoř parser/dxf.html (sdílená komponenta, jiný endpoint)
- [ ] Implementuj parser-core.js (FormData, fetch, timeout, error handling)
- [ ] Implementuj render-ceo.js (CEO view: metriky, bar chart, cena)
- [ ] Implementuj render-cto.js (CTO view: tabulka, raw JSON, API info)
- [ ] Implementuj render-design.js (Design view: SVG canvas, layers)
- [ ] Implementuj svg-utils.js (SVG vykreslení toolpathů)

### Fáze 3: B2C sekce (2 hodiny)

- [ ] Vytvoř solutions.html (hub)
- [ ] Vytvoř solutions/offgrid.html
- [ ] Vytvoř solutions/iot.html

### Fáze 4: Kontakt + docs (1 hodina)

- [ ] Vytvoř contact.html (kontakt + API endpoint reference)
- [ ] Nastav odkaz na GitHub Pages docs (docs.systeq.cz)

### Fáze 5: Testování a deploy (1 hodina)

- [ ] Otestuj drag & drop v Chrome, Firefox, Edge
- [ ] Otestuj fallback "demo data" (žádný backend = funkční ukázka)
- [ ] Otestuj responzivitu (mobil, tablet, desktop)
- [ ] Nahraj vše na Webzdarma FTP
- [ ] Ověř live na systeq.cz

---

## 9. CHYBĚJÍCÍ KONTEXT — DOTAZ NA DOPLNĚNÍ

Pro tvorbu v1.0 beta jsou vyžadovány následující podklady, které v aktuální dokumentaci chybí nebo jsou neúplné:

### 9.1. Povinné (blokují nasazení parser dema)

- [ ] **GCP Cloud Run endpoint URL** — aktuální URL nasazeného API (nebo alespoň sandbox)
- [ ] **Auth schéma** — API key / Bearer token / žádná auth? Jak má front-end volat?
- [ ] **Ukázkový JSON response** — reálný výstup VCF parseru (pro návrh rendererů a fallback demo data)
- [ ] **Ukázkový JSON response** — reálný výstup DXF pipeline

### 9.2. Důležité (ovlivní obsah B2C sekcí)

- [ ] **Off-grid systém detail** — specifikace aktuální sestavy: panely (značka, Wp, počet), MPPT, střídač, BMS, baterie (LiFePO4 chemie, kapacita, config)
- [ ] **LFP SoC pipeline** — jak predikce funguje, jaká data sbíráš, jaký model používáš
- [ ] **Energy Hub fotky / schémata** — vizuální podklady do /solutions/offgrid

### 9.3. Užitečné (zvýší wow efekt)

- [ ] **Video / GIF z parseru v akci** — nahrávka obrazovky s live parsingem
- [ ] **Screenshoty Odoo integrace** — pokud existuje CSV export do Odoo
- [ ] **Reference / testimonial** — cokoliv od JAPS, Moodpasty, nebo známých

### 9.4. Technické specifikace pro vizualizace

- [ ] **VCF binární struktura** — seznam polí, která parser extrahuje (potřebuji znát názvy a typy pro správné renderování)
- [ ] **DXF entity typy** — které entity DXF parser podporuje (LINE, ARC, CIRCLE, POLYLINE, atd.)

---

## 10. PŘIPOMENUTÍ: BACKEND DEPLOY

Backend deploy (GCP Cloud Run, Docker image, CI/CD pipeline) je **samostatná kapitola** mimo rozsah tohoto plánu. Tento dokument se soustředí výhradně na front-end a vizuální prezentaci.

Pro backend deploy bude vytvořen samostatný dokument: `DEPLOY_BACKEND_GCP_v1.0.md`

---

*Konec plánu. Po doplnění chybějícího kontextu (sekce 9) bude plán finalizován a může začít dev marathon.*
