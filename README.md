# SYSTEQ — Web Integration

Deterministický parser proprietárních CNC formátů — webová prezentační vrstva a demo rozhraní.

```
SYS (teal) + TEQ (orange) — CAM Automation Platform
```

## Struktura

```
web_integrace_systeq/
├── src/                    # HTML produkční a demo soubory
│   ├── index.html          # Landing page (produkční)
│   └── systeq_v0.4.html    # Parser demo interface (v0.4 beta)
├── docs/                   # Dokumentace
│   ├── design/             # Brand analýza, grafická vrstva, lingvistika
│   ├── planning/           # Dev plány, assessmenty, pivot analýzy
│   ├── handoffs/           # Dev handoff poznámky
│   ├── GROUND_TRUTH.json   # Referenční data z VCF parseru
│   └── CHANGELOG.md        # Historie verzí VCF parseru
├── data/                   # Embedovaná demo data
│   ├── VCF_modul/          # VCF parser artefakty (CSV, MD reporty, zdrojové VCF)
│   └── DXF_modul/          # DXF parser artefakty (CSV, PNG, TXT, DXF)
├── deploy/                 # Deployment konfigurace (připraveno)
├── archive/                # Starší iterace HTML
└── .gitignore
```

## Architektura dema

Demo je **single-file HTML** s embedovanými deterministickými datovými objekty:
- **VCF modul**: 1ks.VCF (7 elementů, 3:13 min), fluenz_l.VCF (137 elementů, 25:25 min)
- **DXF modul**: 3822_2ks (cassette panel), PCB_C (675 entit, extrémní hustota)

Žádný build step, žádné moduly, přímé FTP nasazení na Webzdarma.

## Engine backend

- `vcut-parser` (VCF): github.com/outpost2026/vcut-parser
- `CNC_2_LLM` (DXF): github.com/outpost2026/CNC_2_LLM
- `cad2llm` (CAD): github.com/outpost2026/cad2llm
