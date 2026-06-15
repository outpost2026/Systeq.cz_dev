# HANDOFF: SYSTEQ 3D Scene — Entropy-to-Order Visualization (V3.1)

**Datum**: 15.06.2026
**Cíl**: Interaktivní Three.js demonstrace přechodu z datově entropického stavu do uspořádaného pomocí vyvíjeného parseru proprietárních CNC dat. Součást webové prezentace SYSTEQ — B2B argumentační nástroj pro IP jednání.

---

## 1. SÉMANTICKÝ KONCEPT

Hlavní narativ: **"Z nepoznaného chaosu (proprietární binární data) vzniká hodnota (čistá strukturovaná data → peníze)."**

| Entita | Symbolika |
|--------|-----------|
| Chaos fáze (0–8s) | Nečitelná data, frustrace obsluhy, zmetková výroba |
| Aktivace parseru (8–10s) | Průlom — přechod z "nevíme" do "víme" |
| Řád fáze (10–22s) | Deterministický výstup, metriky, peníze, klid |
| Stabilní stav (22s+) | ENGINE V20 · IDLE — kontinuální přísun čistých dat |

---

## 2. SCÉNA — OBJEKTY

Všechny souřadnice v Three.js prostoru (Y-up). Kamera: `(0, 1.2, 8)`, LookAt `(0, 0.2, 0)`, FOV 45°.

### 2.1 Pracovní stanice (PC) — X=-3, Y=0, Z=0
- Stůl (deska + nohy)
- Monitor (CanvasTexture s hex dump texturou pro chaos fázi)
- Tower

### 2.2 Operátor — X≈-2.1, Z≈1.1
- Nohy, trup, hlava, kšiltovka s "SYS/TEQ" logem
- **Animace dle fáze**:
  - 0–8s: hlava skloněná, ruka na čele (frustrace)
  - 8–10s: hlava trhne vzhůru (úžas)
  - 10–22s: zakloní se, založí ruce (spokojenost)
- Hrnek s párou (realtime animace)

### 2.3 Plotr (CNC) — X=0, Y=0, Z=0
- Stůl + 4 nohy
- Gantry (příčný pohyb po ose X)
- Vozík + nástroj
- Po t=10: gantry osciluje (sinusový pohyb)

### 2.4 Knihovna — X=+3, Y=0, Z=0
- 3 svislé pilíře + 3 police
- **0–8s**: prázdná / nevyužitý potenciál
- **16–22s**: zdroj dolarů a metrik (padají dolů)

### 2.5 Portál — X=-2, Y=0.4, Z=0.3
- TorusGeometry (bílo-žlutá, emissive)
- Aktivace: scale 0.5→1.5 (t=8–10)

### 2.6 Shockwave — pozice portálu
- SphereGeometry, poloprůhledná
- Rozpínání: scale 1→13 (t=8.5–10)
- Flash: objekty bílé na 0.3s (t=9–9.3)

### 2.7 Datový tok — PC → Plotr
- **6 červených kostek** (chaos): přímka, nerovnoměrné rozestupy
- **6 zelených kostek** (řád): sinusová dráha, rovnoměrné rozestupy
- CSS2D labely: čínské znaky (`数`, `据`, `乱`, `码`) + hex řetězce (`A7`, `3F`, `BC`, `E8`)
- Po t=10: texty → `G-CODE`, `CLEAN`, `ORDER`, `OK`

### 2.8 Desky (panels)
- BoxGeometry 1.2×0.1×0.8
- **Spawn**: z plotru (X=0.5, Y=0.2, Z=0.6), každých 0.6s
- **Pád**: volný pád na zem (GND=-0.55), náhodný drift X/Z
- **Defekt**: každá 3. deska — červený defect mesh (malá krychle = ulomený roh) + CSS2D varování (`⚠ H2=-0.5`)
- **Po t=10**: interpolace do 2 řad (Z=-1, Z=+1), defect zmizí, varování blikne a zmizí, barva → zelená

### 2.9 Dolary — z knihovny
- BoxGeometry 0.2×0.2×0.1, barva #F59E0B (zlatá)
- Padají z police (X=+3, Y=0.9) na zem
- 1/s od t=16, celkem 5 ks

### 2.10 Metriky — CSS2D z knihovny
- Padají stejnou trajektorií jako dolary, 1/s od t=16
- Pořadí: `3:14 min`, `0.12 m²`, `57 429 mm`, `137 elem`, `~260 Kč`
- Zelené CSS2D labely, zastaví se nad zemí

---

## 3. ČASOVÁ OSA

| Čas | Fáze | Dění | Entropie | Status |
|-----|------|------|----------|--------|
| 0–7 | Chaos | frustrovaný operátor, červená data, čínské znaky+hex, desky padají s defecty+varováními, knihovna prázdná | 94% (#EF4444) | UNSTRUCTURED DATA |
| 7–8 | Ticho | poslední defect deska padá pomaleji | 94% | — |
| 8–10 | Aktivace | portál škáluje 0.5→1.5, shockwave, flash, operátor vzhlédne | 94→50% (→#FDE047) | DECODING... ⚡ |
| 10–16 | Řád | data zelená, sinusová dráha, texty→G-CODE, desky se srovnají, defect zmizí | 50→6% (→#22C55E) | DETERMINISTIC OUTPUT |
| 16–22 | Výstup | dolary + metriky padají z knihovny (1/s), operátor zakloněn | 6% | + "Residual: 6%" |
| 22+ | Idle | gantry pulse, zelené tečky, stabilní scéna | 6% | ENGINE V20 · IDLE |

### Entropie — detail výpočtu
- `entropy(t)`:
  - t < 8: 94
  - 8 ≤ t ≤ 22: lineární interpolace 94 → 6
  - t > 22: 6

### Barva entropie (interpolace RGB)
- 94% → `#EF4444` (červená)
- 50% → `#FDE047` (žlutá)
- 6% → `#22C55E` (zelená)

### Barva pozadí
- 0–8s: `#0a0e14`
- 8.5–10: flash `#ffffff` (0.3s)
- 10–22+: jemný přechod do `#0a1218` (nádech teal)

---

## 4. TECHNICKÉ PRINCIPY

### 4.1 Struktura kódu (create* funkce)
```javascript
init()                // scéna, kamera, renderery, světla
createScene()         // bg, ground plane
createPC()            // stůl + monitor + tower
createOperator()      // operátor s animačními referencemi
createPlotter()       // CNC stůl + gantry + tool
createLibrary()       // pilíře + police
createDataFlow()      // 6 kostek + CSS2D znaky+hex
createPortal()        // Torus bílo-žlutý
createShockwave()     // SphereGeometry transparent
createEntropyLabel()  // CSS2D entropie + status
spawnPanel(defective) // generování desky
spawnDollar()         // zlatá kostka
spawnMetric(text)     // CSS2D metrika
rearrangePanels(t)    // srovnání desek do 2 řad
animate(t)            // hlavní smyčka
```

### 4.2 Závislosti
- Three.js: `https://unpkg.com/three@0.160.0/build/three.module.js`
- CSS2DRenderer: `https://unpkg.com/three@0.160.0/examples/jsm/renderers/CSS2DRenderer.js`
- Žádné externí textury — vše generováno programově (CSS2D, CanvasTexture)

### 4.3 Omezení (záměrně volná)
- Max ~120 objektů (operátor ~25, desky ~15, datové kostky ~6, dolary ~5, knihovna ~6, plotr ~8, PC ~8)
- CSS2D labely: ~15 (znaky, hex, varování, metriky, entropie, status)
- Žádné částicové systémy — jednotlivé meshe

### 4.4 Vydané verze
| Verze | Soubor | Stav |
|-------|--------|------|
| V1 | — | Koncept (nerealizováno) |
| V2 | `src/demo/systeq_3d_poc.html` | ✅ Existuje — základní 3D scéna s operátorem, entropií, datovým tokem, portálem |
| V3 | `src/demo/systeq_v3.html` | **Nerealizováno** — cílová verze dle tohoto handoffu |

---

## 5. ZNÁMÉ BUGY / ROADBLOCKS (V2 → V3)

| # | Popis | Závažnost |
|---|-------|-----------|
| 1 | **V2 nemá create*() strukturu** — refaktorovat pro V3 | MEDIUM |
| 2 | **V2 kamera (0,3,8) nevidí knihovnu** — změnit na (0,1.2,8) pro V3 | HIGH |
| 3 | **V2 nemá metriky z knihovny** — přidat spawnMetric() ve V3 | MEDIUM |
| 4 | **V2 desky nemají defect mesh** — přidat červenou kostičku ve V3 | MEDIUM |
| 5 | **V2 operátor bez animace** — přidat head/arm animace dle fáze | LOW |
| 6 | **V2 nemá sinusovou dráhu dat** — změnit trajektorii v řádu fázi | LOW |
| 7 | **V2 status display jen ENTROPY** — přidat STATUS řádek ve V3 | MEDIUM |
| 8 | **Entropie V2 skoková** — změnit na plynulou lineární interpolaci | HIGH |

---

## 6. DALŠÍ ITERACE — NÁVRHY

Priority pro příští session:
1. **Implementovat V3** — kompletní rewrite `systeq_v3.html` dle tohoto handoffu
2. **Otestovat timing** — 0–30s sekvence, plynulost entropie, synchronizace animací
3. **Integrovat do dema** — přidat jako 4. view tab do `src/demo/index.html`
4. **Napojit na reálná data** — místo fake metrik použít embedded VCF_DATA/DXF_DATA

---

*Tento dokument je živý — aktualizovat při každé významné změně scény nebo architektury.*
