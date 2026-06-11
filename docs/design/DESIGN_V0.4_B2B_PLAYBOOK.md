# DESIGN V0.4 — B2B Visual & Topographical Playbook

**Verze:** 1.0 (Červen 2026)  
**Repo:** `web_integrace_systeq`  
**Cíl:** Dokumentovat vizuální design změny provedené napříč SYSTEQ ekosystémem.  
**Rozsah:** `src/index.html`, `GROUND_TRUTH.md`, `docs/handoffs/`

---

## Přehled změn (CO)

| # | Změna | Soubor | Řádky |
|---|-------|--------|-------|
| 1 | **Telefon** — oprava na `+420 735 045 256` | `src/index.html` | 431, 757 |
| 2 | **Email** — oprava na `sousek@systeq.cz` | `src/index.html` | 761 |
| 3 | **GROUND_TRUTH** — oprava kontaktu | `GROUND_TRUTH.md` | 226 |
| 4 | **Handoff doc** — oprava emailu (2×) | `docs/handoffs/handoff_beta0.2-1.0.txt` | 15, 120 |

---

## Detailní dokumentace (CO + PROČ + KDE + JAK)

### 1. Oprava telefonního čísla

**CO:** `+420 735 052 56` → `+420 735 045 256`  
**PROČ:** Původní číslo chybělo `045` — uvedeno jako `052` místo `045 256`. Oprava na správné číslo autora.  
**KDE:** `src/index.html` — 3 výskyty:

| Výskyt | Kontext |
|--------|---------|
| Řádek 431 | Header navigace — zobrazený text |
| Řádek 757 | Contact sekce — `href="tel:+..."` a zobrazený text |

**JAK:** Prostá textová náhrada. HTML:

```html
<!-- PŮVODNÍ -->
<a href="tel:+420735052256">+420 735 052 56</a>

<!-- NOVĚ -->
<a href="tel:+420735045256">+420 735 045 256</a>
```

### 2. Oprava emailové adresy

**CO:** `ondra.sousek@gmail.com` → `sousek@systeq.cz`  
**PROČ:** Autor přešel na profesionální doménu SYSTEQ. Gmail adresa byla osobní — pro B2B komunikaci je vhodnější firemní email pod vlastní doménou.  
**KDE:** `src/index.html` — 1 výskyt v Contact sekci (řádek 761).  
**JAK:**

```html
<!-- PŮVODNÍ -->
<a href="mailto:ondra.sousek@gmail.com">ondra.sousek@gmail.com</a>

<!-- NOVĚ -->
<a href="mailto:sousek@systeq.cz">sousek@systeq.cz</a>
```

### 3. GROUND_TRUTH.md

**CO:** Oprava kontaktního řádku v tabulce.  
**KDE:** Řádek 226.  
**JAK:**

```
| Autor | ondra.sousek@gmail.com, +420 735 052 56 |   →   | Autor | sousek@systeq.cz, +420 735 045 256 |
```

### 4. Handoff dokument

**CO:** Oprava emailu na 2 místech.  
**KDE:** `docs/handoffs/handoff_beta0.2-1.0.txt` — řádky 15 a 120.

---

## Vizuální architektura HTML (referenční)

Pro pochopení layoutu Streamlit dashboardu (který kopíruje HTML vizuální styl):

### Header struktura (`index.html` řádky 413–436):
```
[SYS]TEQ  CAM Automation     [•] API ONLINE    GitHub    LinkedIn    +420 735 045 256
```
- Sticky pozice, `backdrop-filter: blur`
- Logo: SYS v `#0d9488`, TEQ v `#D35400`
- Zelená tečka s `@keyframes blink`

### Metrics struktura (`index.html` řádky 614–642):
```
33+ VCF souborů parsováno  |  100% RPA automatizace  |  86% přesnost odhadu  |  0% halucinací
```
- 4-sloupcový grid s `gap: 1px` (border efekt)
- Každá buňka: velké číslo + popisek + poznámka

### Footer struktura (`index.html` řádky 787–795):
```
[SYS]TEQ © 2026    Engine v20 deterministic · outpost2026    "Hledám funkční řešení v balastu."
```
- Flexbox, `justify-content: space-between`
- Stejné barvy jako header

---

## Provádění korekcí (JAK pro dev)

### Chci změnit kontakt v HTML:
Otevři `src/index.html` a hledej:
- `735 045 256` — 2 výskyty (header + contact)
- `sousek@systeq.cz` — 1 výskyt (contact)

### Chci změnit barvu TEQ v logu:
Hledej `.logo-teq { color: #D35400; }` v `<style>` bloku (řádek 78). Změň hodnotu.

### Chci změnit barvu SYS v logu:
Hledej `.logo-sys { color: #0d9488; }` v `<style>` bloku (řádek 77). Změň hodnotu.

### Chci změnit metriky:
Hledej `<section class="metrics-section">` — každý `<div class="metric-item">` je jeden sloupec. Data jsou statická.
