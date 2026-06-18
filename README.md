# SYSTEQ — Web Integration

Tento repozitář **není** standardní projekt s jasným zadáním a jedním produktem.
Je to **živý výukový materiál**, technické portfolio a metodologický workshop
jednoho vývojáře, který se rozhodl přejít z CNC výroby do světa B2B software
— a celou cestu dokumentuje včetně všech omylů, architektonických rozhodnutí
a iterací.

---

## Co tady najdeš a proč to stojí za přečtení

### 1. B2B pivot v přímém přenosu

Autor (Ondřej Soušek, 14 let v CNC) začal stavbou **VCF/DXF parseru** —
reverzního inženýrství binárního formátu Ruida .VCF. Z toho vznikla
CAM Automation Platforma **SYSTEQ**. Tento repozitář je **webová prezentační
vrstva** té platformy: landing page, 3D demo, parser dashboardy.

**Proč to řešit?** Protože přechod z domény (CNC) do software není
o tom "naučit se kódovat". Je o tom **formalizovat tacitní znalosti**
do explicitních pravidel — a tu formalizaci tady uvidíš
na všech úrovních: od hex dumpu po Three.js animaci.

### 2. "Nepovedené" scény jako engine pro nové domény

Three.js demo (`Demo_Threejs/systeq_v3.html`) není hračka. Je to
**argumentační štít pro IP jednání** — zobrazuje abstraktní koncept
redukce entropie CNC dat jako 4-fázový příběh:

```
CHAOS (0-7s)    →  data v binárním chaosu
ACTIVATION (7-10s) →  strukturace, napojení na portál
ORDER (10-22s)  →  deterministický G-code, montáž panelů
IDLE (22s+)     →  hotový systém, připraveno k práci
```

Stejnou scénu — entropy-to-order narativ — můžeš transponovat do:
- cleanroom data pipeline
- CI/CD pipeline vizualizace
- network traffic orchestrace
- bioinformatické assembly

**To je pointa:** metodologie tvorby vizuálních scén je přenositelná.
Scéna je navržena jako **ontologický překladač** mezi doménovým jazykem
a Three.js geometrií. Dokladem je `Scene_design_ontology.txt`
a 1257-řádková `TECH_SPEC_SCENE_V3.1_REFACTOR.md`.

### 3. RAW FIRST + frakční kompilace

Metodologie, která se táhne celým repozitářem:

1. **RAW FIRST**: začni surovými daty (hex dump VCF, DXF entity)
2. **Frakční kompilace**: formalizuj po částech — každá iterace přidá
   vrstvu explicitního pravidla
3. **LLM-asistovaný vývoj**: člověk navrhuje architekturu, LLM generuje
   implementaci (Gemini, Deepseek, Claude — všechny tři jsou tady
   otestované včetně promptů v `prompt_systeq_v5.txt`)

Výsledek: parser v20, Streamlit dashboard na GCP Cloud Run,
Three.js demo, golden-master testy. Všechno živé, deployované,
s dokumentovanými bugy (`GROUND_TRUTH.md`).

### 4. Single-file HTML jako architektonické rozhodnutí

Celá webová vrstva je **single-file HTML**:
- Žádný build step, žádný Node.js, žádný Webpack
- Embedovaná testovací data přímo v HTML
- Deploy přes FTP na Webzdarma
- Dev verze s `?dev` parametrem aktivuje lil-gui panel

Tahle "low-tech" volba není náhoda. Umožňuje:
- Okamžitý deploy bez CI/CD pipeline
- Žádné závislosti (kromě CDN knihoven)
- Snadnou auditovatelnost — jeden soubor, jedna zodpovědnost
- Export Prod funkcí: dev verze umí ostripovat svůj vlastní ladící kód
  a vyexportovat čistý produkční HTML

### 5. Design jako první-class občan

Design není "až potom". Repozitář obsahuje systematickou dokumentaci:
- **DESIGN_CORPUS.md** — jediný zdroj pravdy pro vizuální identitu
- **PRINCIPLES_DASHBOARD_DESIGN.md** — kognitivní psychologie aplikovaná
  na dashboardy (Hickův zákon, Millerův zákon, Gestalt principy)
- **brand_lingvistic.md** — teorie průmyslového brandingu
- **ANALYZA_BRAND_DE_NOVO_v2.0.md** — kompletní brand analýza

Barevná paleta EGA (16 barev), font JetBrains Mono, CRT scanline overlay,
Norton Commander HUD — vše jsou vědomá rozhodnutí, ne náhody.

### 6. Testování jako regresní síť

`Demo_Threejs/tests/` obsahuje **golden-master testy** v Pythonu
(Playwright + Pillow):

```bash
python -m pytest Demo_Threejs/tests/ -v           # porovnání
python -m pytest Demo_Threejs/tests/ --update-golden  # regenerace
```

Každá ze 4 fází má golden-master PNG. Testy používají `__devSetSimTime()`
k deterministickému "zamrazení" simulace — jediný způsob, jak testovat
requestAnimationFrame-based animaci bez časového rozptylu.

---

## Struktura repozitáře (sémantická)

```
web_integrace_systeq/
│
├── src/index.html               # PRODUKCE: landing page (systeq.cz)
│
├── Demo_Threejs/
│   ├── systeq_v3.html           # PRODUKCE: 4-fázové 3D demo
│   ├── systeq_v3_dev.html       # DEV: s lil-gui (aktivace ?dev)
│   ├── systeq_v3_dev_v2.html    # DEV v2: testovací API (__dev*)
│   ├── systeq_v3_dev.json       # Ukázkový preset pro lil-gui
│   ├── systeq_v3.1_dev.bat      # Dev server: python + chrome ?dev
│   ├── SYSTEQ_V3_ARCHITECTURE_LLM.md  # Architektonická dokumentace
│   ├── handoff_threejs_demo.txt       # Handoff pro LLM asistenty
│   ├── prompt_systeq_v5.txt           # V5 prompt — 859 ř. specifikace
│   └── tests/
│       ├── conftest.py           # Pytest fixtures (Playwright, server)
│       ├── test_visual.py        # Golden-master testy (9 testů)
│       └── golden/*.png          # GM screenshoty 4 fází
│
├── docs/
│   ├── CHANGELOG.md             # Historie VCF parseru
│   ├── GROUND_TRUTH.json        # Právní + technický audit (452 ř.)
│   ├── design/
│   │   ├── TECH_SPEC_SCENE_V3.1_REFACTOR.md  # 1257 ř. spec Three.js scény
│   │   ├── TECH_SPEC_SCENE_V3.2_LITE.md      # Zjednodušená verze
│   │   ├── DESIGN_CORPUS.md                  # Design systém
│   │   ├── DESIGN_V0.4_B2B_PLAYBOOK.md       # B2B design změny
│   │   ├── PRINCIPLES_DASHBOARD_DESIGN.md    # Dashboard principy
│   │   ├── SYSTEQ_v1.0_BETA_Graficka_esteticka_vrstva.md
│   │   ├── ANALYZA_BRAND_DE_NOVO_v2.0.md
│   │   ├── brand_lingvistic.md
│   │   ├── web_ui_pilot.md
│   │   └── Scene_design_ontology.txt         # Ontologie 3D scény
│   ├── handoffs/
│   └── planning/
│       ├── ANALYZA_BRAND_WEB_PIVOT_v1.0.md
│       ├── ASSESSMENT_DESIGN_GITHUB_v1.0.md
│       └── DEV_PLAN_SYSTEQ_v1.0_BETA.md
│
├── data/
│   ├── VCF_modul/   # VCF parser artefakty (vč. ML vektorů)
│   └── DXF_modul/   # DXF parser artefakty (vč. tool configu)
│
├── archive/         # Starší iterace (index_0.1.html, beta)
└── deploy/          # Připraveno pro nasazení
```

---

## Rychlý start pro paralelního developera

| Chci… | Tak… |
|-------|------|
| Vidět 3D demo | Otevři `Demo_Threejs/systeq_v3_dev_v2.html?dev` v Chrome |
| Ladit parametry | Přidej `?dev` → otevře se lil-gui panel vpravo |
| Exportovat produkční verzi | V lil-gui: Dev Tools → Export Prod |
| Spustit lokální server | `systeq_v3.1_dev.bat` (python server + Chrome) |
| Spustit testy | `python -m pytest Demo_Threejs/tests/ -v` |
| Aktualizovat golden-mastery | `--update-golden` flag |
| Pochopit architekturu scény | Čti `TECH_SPEC_SCENE_V3.1_REFACTOR.md` |
| Pochopit design systém | Čti `docs/design/DESIGN_CORPUS.md` |
| Znát aktuální stav projektu | Čti `GROUND_TRUTH.md` |

---

## Engine backend (samostatné repozitáře)

- **vcut-parser** (VCF): github.com/outpost2026/vcut-parser
- **CNC_2_LLM** (DXF): github.com/outpost2026/CNC_2_LLM
- **cad2llm** (CAD): github.com/outpost2026/cad2llm

---

## Transfer learning: co si odnést

Tento repozitář není učebnice. Je to **deník stavitele**. Pokud paralelně
pracuješ na více projektech, můžeš si odnést:

1. **Jak formalizovat doménové znalosti** do kódu — od hex dumpu
   po Three.js animaci
2. **Proč single-file HTML není "low-tech"**, ale architektonická volba
   s konkrétními trade-offy
3. **Jak testovat requestAnimationFrame-based animace** —
   golden-master s injekcí simulačního času
4. **Jak navrhnout 3D scénu jako argumentační nástroj** —
   entropy-to-order narativ přenositelný do libovolné domény
5. **Jak LLM-assisted workflow skutečně vypadá** — ne teorie, ale
   konkrétní prompt chainy, handoffs a iterace
6. **Že "nepovedená" iterace není prohra** — archiv je stejně
   důležitý jako produkce

---

*Ondřej Soušek · sousek@systeq.cz · +420 735 045 256*
