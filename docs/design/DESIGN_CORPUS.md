# SYSTEQ Design Corpus v1.0

> **Účel**: Jediný zdroj pravdy pro vizuální identitu SYSTEQ — napříč webem (systeq.cz), dashboardem (Streamlit), dokumentací a B2B komunikací.
> **Zdroje**: `SYSTEQ_v1.0_BETA_Graficka_esteticka_vrstva.md`, `DESIGN_V0.4_B2B_PLAYBOOK.md`, `ANALYZA_BRAND_DE_NOVO_v2.0.md`, `brand_lingvistic.md`, `web_ui_pilot.md`, `PRINCIPLES_DASHBOARD_DESIGN.md`, `DESIGN_V20_B2B_PLAYBOOK.md`
> **Aktualizováno**: 2026-06-11

---

## 1. BRAND IDENTITY

### 1.1 Název
**SYSTEQ** — zvoleno jako #1 ze 47 variant. Skóre 96/100 (fonetika 9, psychologie 10, marketing 10). Koncovka `-eq` evokuje Quality/Equipment. Zní jako etablovaný EU průmyslový standard.

### 1.2 Logo
- **SYS** = teal `#0d9488`, **TEQ** = orange `#D35400`
- Primární užití: textové logo v headeru webu + favicon
- Soubor: `systeq01.jpg`

### 1.3 Barevná paleta — Dark Theme (web + Streamlit)

| Role | Hex | CSS var | Použití |
|------|-----|---------|---------|
| Pozadí hlavní | `#0a0e14` | `--bg-base` | `<body>` / `.stApp` |
| Pozadí panel | `#0e1320` | `--bg-panel` | Metriky, sekce |
| Pozadí karet | `#131926` | `--bg-card` | Karty, moduly |
| Akcent teal | `#0d9488` | `--accent` | SYS logo, tlačítka, highlighty |
| Akcent orange | `#D35400` | n/a | TEQ logo |
| Text primární | `#e2e8f0` | `--text-primary` | Nadpisy, hlavní text |
| Text muted | `#64748b` | `--text-muted` | Popisky |
| Text dim | `#2e3d52` | `--text-dim` | Metadata |
| Zelená OK | `#22c55e` | `--green` | Statusy, OK badge |
| Amber warn | `#f59e0b` | `--amber` | Varování |
| Červená err | `#ef4444` | `--red` | Kritické chyby |
| Modrá info | `#3b82f6` | `--blue` | Info badge |

### 1.4 Typografie

| Role | Font |
|------|------|
| Web UI (tělo, nadpisy) | **JetBrains Mono** — monospace = technický průmyslový charakter |
| Monospace (kód, data) | JetBrains Mono (primární font je zároveň monospace) |

Hierarchie:
- H1: `clamp(1.9rem, 4.8vw, 3.4rem)` / weight 700 / letter-spacing -0.01em / line-height 1.1
- H2 (sekce): `1.1rem` / weight 500 / letter-spacing 0.02em
- Tělo: `14px` / weight 300–400 / line-height 1.6
- Small: `0.65–0.75rem` / pro tagy, badge, metadata

### 1.5 Motto
**"Hledám funkční řešení v balastu."**

---

## 2. LAYOUT & KOMPOZICE

### 2.1 Struktura stránky (top-down)

```
HEADER (sticky)
HERO (eyebrow + headline + sub + CTA)
DEMO CTA (placeholder screenshot + tlačítko)
CORE STACK (4 karty)
PARSER BRANCHES (produktové moduly)
METRICS (výkonnostní parametry)
ABOUT (profil + skills + timeline)
CONTACT (kontakt + API info)
FOOTER
```

### 2.2 Topografická pravidla (Gestalt + Miller + Hick)

1. **Max 5 chunků na Level 0** (Millerův zákon) — first glance nesmí zahlcovat
2. **Proximita > barva** — související prvky blízko sebe, nesouvisející oddělené separátorem
3. **Podmíněné zobrazení rizik** — neukazovat "žádná rizika", ukazovat jen při >0 (Normalizace deviace)
4. **Akce až po datech** (Fittsův zákon) — download/CTA až po tom, co uživatel viděl hodnotu
5. **Preattentivní vlastnosti**: cut time = dominantní velikost (48px), status = barevný pill

### 2.3 Sekce a whitespace

- Mezi sekcemi: `padding: 4rem 0`
- Oddělení sekcí: `border-bottom: 1px solid var(--border-subtle)`
- Container: `max-width: 1120px; padding: 0 2rem`

### 2.4 Grid

- Desktop: flexibilní grid, cards = `repeat(auto-fit, minmax(Npx, 1fr))`
- Mobile: `@media(max-width:768px)` → single column, menší padding

---

## 3. KOMPONENTY

### 3.1 Header

- Sticky, backdrop-filter blur
- Logo: textové SYS/TEQ + tag "CAM Automation"
- Status pill: zelená tečka + "API ONLINE" + blink animace
- Nav odkazy: GitHub, LinkedIn, telefon

### 3.2 Karty (stack / branch / metric)

| Typ | BG | Border | Padding |
|-----|-----|--------|---------|
| Stack card | `--bg-card` | `--border-subtle` | 1.4rem |
| Branch card | `--bg-card` | `--border-subtle` (hover → accent) | 1.6rem |
| Metric item | `--bg-panel` | none (grid gap) | 1.85rem |

Hover: `--bg-card-hover`, `transition: background .25s`

### 3.3 Tlačítka

| Typ | Styl |
|-----|------|
| Primary CTA | BG `--accent`, text `#fff`, padding 0.62rem 1.4rem, radius 4px |
| Ghost | Transparentní, border `--border-strong`, hover → accent |

### 3.4 Demo UI komponenty

- **Drop zone**: dashed border, hover → accent
- **Parser tabs**: border-bottom active accent
- **View tabs**: CEO/CTO/Design — border-bottom active accent
- **KPI grid**: `auto-fit, minmax(150px, 1fr)`
- **SVG canvas**: `--bg-inset` pozadí, border
- **Loading bar**: 2px, accent barva, animovaná šířka

### 3.5 Tabulky

- Bez vertikálních linek, horizontální oddělovače
- Hlavička: text-dim, uppercase, letter-spacing
- Řádky: border-bottom subtle, hover efekt

---

## 4. ANIMACE

| Prvek | Délka | Křivka |
|-------|-------|--------|
| Hover | 150ms | ease |
| Loading bar | 400ms | transition width |
| Fade-in | 200ms | ease |
| Blink (API dot) | 2.2s | infinite |

**Zákaz**: bounce, pulse, slide, parallax, 3D transformace.

---

## 5. RESPONSIVITA

- Breakpoint: `@media(max-width:768px)` — single column, menší padding (1.1rem)
- KPI grid → 2×2
- H1 → 1.75rem
- Dvou-sloupcové layouty → single column

---

## 6. IMPLEMENTAČNÍ RULES

1. Všechny barvy jako CSS proměnné v `:root`
2. Žádné `!important`
3. Font sizes v `rem`
4. Každý interaktivní prvek má `:hover` a `:focus-visible`
5. Scrollbar dark-themed
6. Žádné inline styly mimo dynamický JS obsah
7. Responzivní → mobil first media queries

---

## 7. ODKAZY NA ZDROJOVÉ DOKUMENTY

| Dokument | Repo | Téma |
|----------|------|------|
| `SYSTEQ_v1.0_BETA_Graficka_esteticka_vrstva.md` | web_integrace_systeq | Grafický design, typografie, barvy, kompozice |
| `DESIGN_V0.4_B2B_PLAYBOOK.md` | web_integrace_systeq | Design změny v SYSTEQ ekosystému |
| `ANALYZA_BRAND_DE_NOVO_v2.0.md` | web_integrace_systeq | Výběr brand jména (systeq vítěz) |
| `brand_lingvistic.md` | web_integrace_systeq | Lingvistická analýza B2B brandů |
| `web_ui_pilot.md` | web_integrace_systeq | B2B pivot strategie, IP objektivizace |
| `PRINCIPLES_DASHBOARD_DESIGN.md` | vcf_parser_b2b / web_integrace_systeq | Kognitivní psychologie (Hick, Miller, Gestalt, TMI, AF447) |
| `DESIGN_V20_B2B_PLAYBOOK.md` | vcf_parser_b2b | Streamlit dashboard design, HUD+MFD architektura |
