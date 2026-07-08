# GROUND TRUTH — SYSTEQ Web Integration Project

> **Fáze**: v0.5 | **Datum snapshotu**: 08.07.2026  
> **Autor**: Otevřená kódová základna — integrace vedlejších projektů

---

## 1. Současný stav projektu

| Položka | Stav |
|---------|------|
| **Parser engine VCF** | v20 — funkční, binární RE Ruida .VCF, 74B segmentová struktura |
| **Parser engine DXF** | v2.3.0 — geometrický indexer, sémantická analýza, ML vektor (55+ featů) |
| **Streamlit dashboard VCF** | Deployovaný (GCP Cloud Run), password-gated, role-based UI |
| **Streamlit dashboard DXF** | Deployovaný, semantic embedding + download artefaktů |
| **Web prezentační vrstva** | `systeq_v0.4.html` — single-file HTML demo s embedovanými daty |
| **Landing page** | `index.html` — produkční B2B landing s API docs a kontaktem |
| **Projektový dashboard** | `projekty/index.html` — rozcestník vedlejších fyzických projektů |
| **Ateliér můz** | `music.html` — audio tvorba, neuro-eseje, hudba |
| **Backend API** | **CHYBÍ** — demo používá deterministická embedded data, žádné live endpointy |

---

## 2. Architektonická rozhodnutí

### 2.1 Single-file HTML (bez build stepu)
**Rozhodnutí**: Záměrné — deployment na Webzdarma FTP nepodporuje Node.js/Python runtime. Demo je self-contained HTML s inline CSS, JS a embedded datovými objekty.

**Důsledky**:
- ✅ Okamžité nasazení přes FTP
- ✅ Žádné dependency, žádný build
- ❌ Vanilla JS state management je těžkopádný (přepínání parserů, view tabs)
- ❌ Duplikace render logiky mezi VCF a DXF moduly
- ❌ Data nelze aktualizovat bez redeploye celého HTML

### 2.2 Embedovaná data místo API callů
**Rozhodnutí**: Namísto live GCP endpointů (`POST /api/v1/parse-vcf`) demo používá statické JS objekty (`VCF_DATA`, `DXF_DATA`) přímo v `<script>` tagu.

**Důsledky**:
- ✅ Demo funguje offline, bez serveru
- ✅ Deterministický výstup (žádné API race conditions)
- ❌ Data jsou frozen snapshot — nereprezentují aktuální parser output
- ❌ Nahrání reálného souboru jen přepne na demo data (fake upload handler)

### 2.3 Dark → Light přechod (design pivot)
**Původní plán**: Dark theme (teal #0D9488 na #0A0E14) — inspirováno terminálovou estetikou  
**Aktuální implementace**: Dark theme zachován jako primární, připraven light režim jako optional  
**Barevné schéma**:
- `--accent`: #0D9488 (teal) — SYS v logu, primární tlačítka, aktivní stavy
- `--logo-teq`: #D35400 (tlumená oranžová) — TEQ v logu (místo bílé)
- `--bg-base`: #0A0E14 (termínálová čerň)
- `--text-primary`: #E2E8F0 (slate-200)

**WCAG poznámka**: #D35400 na #0A0E14 má kontrastní poměr ~7.4:1 (AAA). Původní #E67E22 na off-white by bylo WCAG-marginální.

---

## 3. Demo data — provenience

### 3.1 VCF modul

| Dataset | Zdrojový soubor | Parser verze | Klíčové metriky |
|---------|----------------|-------------|-----------------|
| **1ks.VCF** | `vcf_integrace/data/demo/1ks.VCF` | v20 | 7 elementů, 3 704 mm, 3:13 min, 1 vrstva |
| **fluenz_l.VCF** | `vcf_integrace/data/demo/fluenz_l.VCF` | v20 | 137 elementů, 57 429 mm, 25:25 min, 2 vrstvy |

**Embedované objekty v JS**:
```javascript
VCF_DATA = {
  "1ks": { fname, ts, status, meta, kpi, savings, tags, layers_detail, groups, json, svg_paths },
  "fluenz_l": { ... }
}
```

**Ground truth zdroje**:
- `data/VCF_modul/1ks_vrstvy.csv` — extrakce vrstev (CSV)
- `data/VCF_modul/fluenz_l_vrstvy.csv` — extrakce vrstev (CSV)
- `data/VCF_modul/report_v20_1ks.md` — kompletní report
- `data/VCF_modul/report_v20_fluenz_l.md` — kompletní report
- `data/VCF_modul/1ks.VCF`, `fluenz_l.VCF` — zdrojové binární soubory

**Varování v demu** (přesně z reportů):
- 1ks: "⚠ Fixace — 80% obvodu páskou", "⚠ Malá plocha 0.12 m²", "⚠ Riziko posunu 1 skupiny"
- fluenz_l: "⚠ H2=-0.5 mm mimo doporučený rozsah", "⚠ Riziko posunu 4 skupin"

### 3.2 DXF modul

| Dataset | Zdrojový soubor | Indexer verze | Klíčové metriky |
|---------|----------------|---------------|-----------------|
| **3822_2ks** | `dxf_integrace/DXF_indexer_main/demo_data/3822_2ks.dxf` | v2.3.0 | 14 entit, 39 517 mm, cassette_panel, tool conflict |
| **PCB_C** | `dxf_integrace/DXF_indexer_main/demo_data/PCB_C.dxf` | v2.3.0 | 675 entit, 62 137 mm, 671 open paths, 257 conflict IDs |

**Embedované objekty v JS**:
```javascript
DXF_DATA = {
  "3822": { fname, ts, status, meta, kpi, savings, tags, tools, layers, json, svg_paths },
  "pcb_c": { ... }
}
```

**Ground truth zdroje**:
- `data/DXF_modul/3822_2ks_ml_vector.csv` — 55+ ML featů
- `data/DXF_modul/3822_2ks_embedding_prompt.txt` — strukturovaný LLM prompt
- `data/DXF_modul/3822_2ks_2d.png` — vizualizace 1920×1080
- `data/DXF_modul/PCB_C_ml_vector.csv` — ML featury (extrémní hustota)
- `data/DXF_modul/PCB_C_embedding_prompt.txt` — LLM prompt
- `data/DXF_modul/3822_2ks.dxf`, `PCB_C.dxf` — zdrojové DXF

**Tool conflict v 3822**: E_0000, E_0001 — detekován konflikt nástrojů (vibrate_cutter vs v_slot na stejné entitě)

---

## 4. Známé bugy a roadblocks

### 4.1 Frontend (web demo)

| # | Popis | Závažnost | Status |
|---|-------|-----------|--------|
| 1 | **Fake file upload** — nahrání reálného .VCF/.DXF jen přepne na embedovaná data | HIGH | **Známý limit** — chybí live parser endpoint |
| 2 | **CEO view "skutečný vs. odhad firmy"** — používá konzervativní benchmark (8 min pro 1ks), ne reálná data Moodpasty | MEDIUM | Vyžaduje kalibraci na produkčních datech |
| 3 | **Chybějící scroll arrow** — indikátor scrollování v result panelu | LOW | Opraveno ve v0.4 (scroll-hint komponenta) |
| 4 | **Duplicitní jméno autora** — "Ondřej Soušek" bylo v section title i profile-name | LOW | Opraveno ve v0.4 (sec-title → "Autor") |
| 5 | **Logo [SYSTEQ] s brackets** — neodpovídá brand guideline | LOW | Opraveno ve v0.4 (SYS/teal + TEQ/orange) |
| 6 | **1ks_vrstvy.csv I/O error** — reportováno, soubor existuje v Artifacts/VCF_modul/ | LOW | Nutno verifikovat encoding/path v runtime |

### 4.2 Backend / parser

| # | Popis | Závažnost | Status |
|---|-------|-----------|--------|
| 7 | **Chybějící produkční API endpointy** — demo nemá live backend | CRITICAL | GCP Cloud Run existuje, ale není integrován s webem |
| 8 | **report_v20_1ks.md / fluenz_l.md** — neexistují jako commitované soubory ve vcf_integrace | LOW | Generují se za runtime parserem; ve web repu jsou v data/ |
| 9 | **3822_2ks_ml_vector.csv / embedding_prompt.txt** — neexistují v dxf_integrace repu | LOW | Generují se za runtime; ve web repu jsou v data/ |
| 10 | **PCB_C ML model mimo kalibrační rozsah** — tac_per_meter=9.09, predikce času = "?" | MEDIUM | Extrémní dataset nad rámec trénovacích dat |

### 4.3 Design / WCAG

| # | Popis | Závažnost | Status |
|---|-------|-----------|--------|
| 11 | **WCAG kontrast** — oranžová #D35400 vs dark bg: OK (7.4:1), ale #E67E22 na light bg by bylo marginální | LOW | Dark theme zachován jako primární |
| 12 | **border-radius nekonzistence** — v0.3 měla mix 1px/2px | LOW | Opraveno ve v0.4 (konzistentní 4px/6px) |

---

## 5. Deviace od DEV_PLAN_SYSTEQ_v1.0_BETA

| Plánovaná funkce | Skutečný stav | Důvod |
|-----------------|---------------|-------|
| CEO view: "skutečný vs. odhad firmy" bar | CEO view: parser predikce vs. ruční benchmark | Konzervativní — reálný zákaznický benefit bude vyšší po kalibraci |
| Live API integrace | Embedovaná data v JS | Bezpečnější pro demo — žádné network dependency |
| Light theme jako primární | Dark theme jako primární | Zachováno pro konzistenci s parser dashboardy |
| Oddělené moduly (VCF.js, DXF.js) | Single-file monolit | FTP deployment vyžaduje jeden soubor |
| 5 demo datasetů | 4 datasety (2 VCF + 2 DXF) | PCB_A, 26_skladba, 3781_1, 3824_1/4 připraveny v demu datech, neembedovány |

---

## 6. Prerekvizity pro funkční demo v1.0

### Must-have
1. **Live API endpoint** — buď CORS-enabled GCP Cloud Run, nebo serverless function (Cloud Functions)
2. **Upload handler** — skutečné parsování nahraného souboru (aktuálně fake)
3. **Kalibrace na produkčních datech** — reálný porovnávací benchmark s manuálním odhadem Moodpasty
4. **Error handling** — parser může failovat na neznámých VCF minor verzích
5. **Mobile responsive** — aktuální layout je primárně desktopový

### Nice-to-have
6. **Light/dark toggle** — přepínač motivu v navigaci
7. **Více demo datasetů** — PCB_A, 26_skladba, 3781_1, 3824_1, 3824_4
8. **Download artefaktů z webu** — tlačítka pro JSON export (aktuálně jen v parser dashboardech)
9. **SEO metadata** — Open Graph tags, structured data
10. **Analytics** — alespoň basic page view counter

---

## 7. Adresářová struktura (v0.5)

```
web_integrace_systeq/
├── .gitignore
├── README.md
├── GROUND_TRUTH.md              ← tento soubor
├── CHANGELOG.md
├── src/
│   ├── index.html               ← produkční landing page (SYSTEQ B2B)
│   └── music.html               ← Ateliér můz (audio/eseje)
├── projekty/
│   ├── index.html               ← Dashboard rozcestník vedlejších projektů
│   ├── strecha_uvaly/           ← INTERNÍ (v .gitignore, pouze na FTP)
│   └── dodavka_kuba/            ← INTERNÍ (v .gitignore, pouze na FTP)
├── Demo_Threejs/
│   └── ...                      ← 3D demo, testy
├── docs/
│   ├── design/                  ← brand analýza, grafická vrstva, UI pilot
│   ├── planning/                ← dev plány, assessmenty
│   ├── handoffs/                ← dev poznámky, claude notes
│   ├── Audio/                   ← průvodce zvukovou tvorbou
│   └── GROUND_TRUTH.json        ← referenční data z VCF parseru
├── data/
│   ├── VCF_modul/               ← CSV, MD, zdrojové VCF
│   └── DXF_modul/               ← CSV, TXT, PNG, DXF
├── deploy/                      ← PHP counter, config
└── archive/                     ← starší HTML iterace (v03_beta, 0.1)
```

---

## 8. GitHub integrace — workflow plán

### Krok 1 (hotovo): Lokální repo připraveno
- Struktura vytvořena, soubory zkopírovány, opravy aplikovány
- Git init, první commit

### Krok 2: Vytvoření remote repa
- Autor vytvoří repo přes GitHub Web UI
- URL bude přidáno jako `origin` remote

### Krok 3: Push + CI/CD
- `git push -u origin main`
- (Future) GitHub Actions pro validaci HTML/CSS/JS
- (Future) Auto-deploy na Webzdarma přes FTP GitHub Action

### Krok 4: Integrace s parser repy
- `vcut-parser`, `CNC_2_LLM`, `cad2llm` — submoduly nebo cross-reference v README
- Eventuální monorepo? (TBD)

---

## 9. Kontakty a vazby

| Entita | URL / Kontakt |
|--------|---------------|
| GitHub org | github.com/outpost2026 |
| vcut-parser | github.com/outpost2026/vcut-parser |
| CNC_2_LLM | github.com/outpost2026/CNC_2_LLM |
| cad2llm | github.com/outpost2026/cad2llm |
| Autor | sousek@systeq.cz, +420 735 045 256 |
| Web hosting | Webzdarma (FTP) — doména systeq.cz |

---

*Tento dokument je živý — aktualizovat při každé významné změně architektury nebo stavu projektu.*
