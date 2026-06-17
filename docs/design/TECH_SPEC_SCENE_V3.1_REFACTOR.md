# TECHNICKÁ SPECIFIKACE — SYSTEQ V3.1 THREE.JS SCÉNA

**Verze dokumentu:** 1.0  
**Určeno pro:** Specializované modely geometrického a abstraktního uvažování  
**Cíl:** Single-file HTML (`systeq_v3.1.html`) s Three.js r160, CSS2DRenderer, žádný build step  
**Base source:** `systeq_v3.1.html` (523 ř.) z repa  
**Design reference:** `prompt_systeq_v5.txt` (EGA DOS retro spec)  

---

## 1. ARCHITEKTURA SOUBORU

### 1.1 Hierarchie (pořadí v dokumentu)

```
<head>
  <meta>, <link font>, <style>
</head>
<body>
  <div id="crtoverlay">
  <script type="importmap">       // three@0.160.0
  <script type="module">          // veškerý kód
    import * as THREE
    import CSS2DRenderer, CSS2DObject
    import OrbitControls

    // === 1. KONSTANTY A KONFIGURACE ===
    // === 2. EGA 16-BAREVNÁ PALETA ===
    // === 3. FACTORY FUNKCE (geometrie, materiály, textury) ===
    // === 4. HELPER FUNKCE (el, bezt, dosBar, max) ===
    // === 5. CANVASTEXTURE GENERÁTORY ===
    // === 6. GLOBÁLNÍ PROMĚNNÉ ===
    // === 7. CLEANUP REGISTR ===
    // === 8. CREATE FUNKCE (scéna, wall, PC, operátor, plotr, portál, teleport, dataFlow, HUD, toolpath) ===
    // === 9. SPAWN FUNKCE (wallPanel, questionMark, bizSymbol, dollar) ===
    // === 10. UPDATE FUNKCE (rozdělený animate) ===
    // === 11. INIT ===
    // === 12. DISPOSE ===
  </script>
</body>
```

### 1.2 CDN závislosti

```javascript
// Import map:
{"imports": {
  "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
  "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
}}

// Imports:
import * as THREE from 'three';
import { CSS2DRenderer, CSS2DObject } from 'three/addons/renderers/CSS2DRenderer.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
```

### 1.3 CSS stylování

```css
/* Reset */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #000; overflow: hidden; font-family: 'Press Start 2P', 'Courier New', monospace; image-rendering: pixelated; }
canvas { display: block; }

/* HUD labels — box-drawing znaky, 8bit vzhled */
.css-hud {
  font-family: 'Press Start 2P', 'Courier New', monospace;
  white-space: pre; line-height: 1.35;
  text-shadow: 0 0 4px rgba(0,0,0,1);
  pointer-events: none; font-size: 10px;
  image-rendering: pixelated;
}

/* Data flow labels */
.css-label {
  font-family: 'Press Start 2P', 'Courier New', monospace;
  white-space: nowrap;
  text-shadow: 0 0 8px rgba(0,0,0,0.95);
  pointer-events: none;
  image-rendering: pixelated;
}

/* CRT scanline overlay */
#crtoverlay {
  position: fixed; inset: 0; pointer-events: none; z-index: 999;
  background: repeating-linear-gradient(
    0deg,
    rgba(0,0,0,0.08) 0px,
    rgba(0,0,0,0.08) 1px,
    transparent 1px,
    transparent 3px
  );
  mix-blend-mode: multiply;
}
```

### 1.4 Google Font

```
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
```

---

## 2. KONSTANTY A KONFIGURACE

### 2.1 Prostorové konstanty

```javascript
const GND = -0.55;                        // úroveň země v Y
const DSK_Y = 0.22;                       // výška desky stolu

// ZEĎ — posunuta ZA plotr oproti v3.1 (byl WALL_Z=1.5)
const WALL_Z = 3.0;                       // Z pozice stěny (byla mezi kamerou a plotrem!)
const WALL_W = 5.5;                       // šířka stěny
const WALL_H = 3.2;                       // výška stěny
const WALL_Y = 0.5;                       // Y pozice středu stěny
const WALL_GRID_COLS = 5;                 // sloupců gridu
const WALL_GRID_ROWS = 4;                 // řádků gridu
const CELL_W = WALL_W / WALL_GRID_COLS;   // 1.1
const CELL_H = WALL_H / WALL_GRID_ROWS;   // 0.8

// ČÁSTICE
const PARTICLE_COUNT = 12;                // hlavní částice (zjednodušeno oproti v5)
const BURST_PER = 6;                      // burst částic na jednu morph

// DOLARY
const DOLLAR_MAX = 6;

// LABELY
const BIZ_TEXTS = [
  'ČAS ZPRACOVÁNÍ', 'MARŽE', 'ERP', 'VÝTĚŽ', 'OPTIMALIZACE',
  'TOOLPATH OK', 'H2=-0.3', 'NÁKLAD', 'VÝNOS', 'DETERM.'
];
const CNC_LABELS = [
  'ČAS', 'MARŽE', 'VÝTĚŽ', 'H2', 'ERP', 'NÁSTROJ', 'TOOL', 'OK', 'OPT', 'Kč'
];
```

### 2.2 Časové konstanty (fáze)

```javascript
const PHASE = {
  CHAOS_END: 7,          // 0–7s: chaos
  ACTIVATION_START: 8,   // 8–10s: portál + teleport
  ORDER_START: 10,       // 10–22s: deterministický řád
  IDLE_START: 22         // 22s+: idle
};
```

### 2.3 Kamera

```javascript
// PerspectiveCamera(45, aspect, 0.1, 40)
// Pozice: (0, 1.5, 7)
// LookAt: (0, 0.3, 1.2) — cíl mezi plotr a stěnu

// OrbitControls:
// target: (0, 0.3, 1.2)
// enableDamping: true, dampingFactor: 0.08
// autoRotate: true, autoRotateSpeed: 0.2
// enablePan: false
// minDistance: 5, maxDistance: 15
// maxPolarAngle: PI * 0.65
// enabled: false (zapnout až t > 12)
```

---

## 3. EGA 16-BAREVNÁ PALETA

**Všechny barvy objektů, světel a textur MUSÍ používat výhradně tuto paletu.**  
Výjimka: lidská kůže (`0xF5D0A9`), barvy canvas textur (hex stringy v 2D kontextu).

```javascript
const EGA = {
  BLACK:     0x000000,
  BLUE:      0x0000AA,
  GREEN:     0x00AA00,
  CYAN:      0x00AAAA,
  RED:       0xAA0000,
  MAGENTA:   0xAA00AA,
  BROWN:     0xAA5500,
  LTGRAY:    0xAAAAAA,
  DKGRAY:    0x555555,
  LTBLUE:    0x5555FF,
  LTGREEN:   0x55FF55,
  LTCYAN:    0x55FFFF,
  LTRED:     0xFF5555,
  LTMAGENTA: 0xFF55FF,
  YELLOW:    0xFFFF55,
  WHITE:     0xFFFFFF
};

// Mimo EGA (povolené):
const SKIN = 0xF5D0A9;         // lidská kůže
const SHIRT = EGA.BLUE;        // modrá košile
const CAP_COLOR = 0xD35400;    // červená kšiltovka
const BEARD_COLOR = 0x92400E;  // rezavý vous
const MUG_COLOR = 0x78350F;    // hrnek (brown)
```

---

## 4. FACTORY FUNKCE

### 4.1 Geometrie factory

```javascript
const box  = (w, h, d) => new THREE.BoxGeometry(w, h, d);
const cyl  = (r, h, s) => new THREE.CylinderGeometry(r, r, h, s || 8);
const tor  = (R, r, rs, ts) => new THREE.TorusGeometry(R, r, rs || 12, ts || 24);
const ico  = (r, d) => new THREE.IcosahedronGeometry(r, d || 0);
const sph  = (r, w, h) => new THREE.SphereGeometry(r, w || 8, h || 8);
const cone = (r, h, s) => new THREE.ConeGeometry(r, h, s || 6);
```

### 4.2 Materiál factory

```javascript
// Standardní materiál (flatShading = pixel vzhled)
const mF = (c) => new THREE.MeshStandardMaterial({
  color: c, flatShading: true, roughness: 0.85
});

// Emisivní materiál (pro portál, teleport, světla)
const mE = (c, ei) => new THREE.MeshStandardMaterial({
  color: c, emissive: c, emissiveIntensity: ei || 0.5,
  flatShading: true, roughness: 0.7
});

// Základní materiál (pro burst částice, shockwave)
const mB = (c, opts) => new THREE.MeshBasicMaterial({
  color: c, transparent: true, opacity: 0.8, ...opts
});
```

### 4.3 Mesh factory (geometrie + materiál v jednom)

```javascript
const M = (g, c) => new THREE.Mesh(g, mF(c));
```

### 4.4 CanvasTexture factory

```javascript
function makeCanvasTex(w, h, drawFn) {
  const c = document.createElement('canvas');
  c.width = w; c.height = h;
  drawFn(c.getContext('2d'), w, h);
  const t = new THREE.CanvasTexture(c);
  t.minFilter = THREE.NearestFilter;    // pixel-art
  t.magFilter = THREE.NearestFilter;    // pixel-art
  return t;
}
```

**Všechny textury musí používat `makeCanvasTex`** — žádné přímé `new THREE.CanvasTexture`.

---

## 5. CANVASTEXTURE GENERÁTORY

### 5.1 `monitorTex()` — hex dump na monitoru

```
Rozměry: 256 × 160 px
POZADÍ: EGA.BLACK
RÁM: EGA.RED, stroke 3px, rect(6, 6, 244, 148)
TEXT řádek 1: "数据乱码错" (Chinese fail), #E2E8F0, 15px monospace, pozice (14, 34)
Hex dump: 4–6 řádků, každý 6×2 hex znaky + mezery, #94A3B8, 11px monospace
  - Generovat náhodně z '0123456789ABCDEF'
  - Pozice: (14, 60 + r*15), r = 0..4
STATUS: "FORMAT: UNKNOWN", EGA.RED, bold 12px monospace, (14, 145)
```

### 5.2 `makeGuruTex()` — Amiga Guru Meditation

```
Rozměry: 256 × 144 px
POZADÍ: EGA.BLACK
"GURU MEDITATION": EGA.RED, bold 18px, center, (w/2, 50)
"#80000003": EGA.LTRED, bold 30px, center, (w/2, 95)
"AMIGA 1985": EGA.LTGRAY, 11px, center, (w/2, 120)
```

### 5.3 `makeQuestionTex()` — otazník

```
Rozměry: 48 × 48 px
POZADÍ: EGA.BLACK
"?" : EGA.RED, bold 34px, center, (w/2, h/2+2)
RÁM: EGA.LTRED, stroke 2px, rect(3, 3, 42, 42)
```

### 5.4 `makeWallTex()` — grid stěny (Norton Speed Disk)

```
Rozměry: 440 × 256 px
POZADÍ: #0F172A (tmavě modrá — mimo EGA, povoleno jen zde)
VNĚJŠÍ RÁM: EGA.CYAN, stroke 3px, rect(2, 2, 436, 252)
VNITŘNÍ GRID 5×4:
  - pozice: (4 + c*w/5, 4 + r*h/4)
  - velikost: (w/5 - 8, h/4 - 8)
  - stroke: #1E293B, lineWidth 1
  - text v buňce: String.fromCharCode(65+r)+(c+1), #334155, 8px, center
HEADER: "SYSTEQ DEFRAG ENGINE", #64748B, bold 11px, center, Y=14
```

### 5.5 `makeDollarTex()` — dolarový symbol

```
Rozměry: 64 × 64 px
POZADÍ: EGA.YELLOW (#FFFF55)
RÁM: EGA.BROWN (#AA5500), stroke 3px, rect(4, 4, 56, 56)
"$": EGA.BROWN, bold 32px, center
"SYSTEQ": EGA.BROWN, 7px, top-center
```

### 5.6 `makePanelTex(defective)` — textura panelu na zeď

```
Rozměry: 256 × 96 px
Pokud defective:
  POZADÍ: EGA.RED (#AA0000)
  RÁM: EGA.YELLOW (#FFFF55), stroke 4px, rect(4,4,248,88)
  TEXT: "SCRAP", EGA.YELLOW, bold 20px, center
Pokud OK:
  POZADÍ: EGA.BROWN (#AA5500)
  RÁM: EGA.DKGRAY (#555555), stroke 3px, rect(4,4,248,88)
  TEXT: "UNKNOWN", EGA.DKGRAY, 14px, center
```

### 5.7 `makeParamTex(label, value, variant)` — ordered panel

```
Rozměry: 256 × 96 px
POZADÍ: vždy EGA.BLACK
RÁM: dle varianty (0=EGA.GREEN, 1=EGA.BROWN, 2=EGA.RED), stroke 3px
label: EGA.LTGRAY, 9px, left, (10, 28)
value: dle varianty (0=EGA.LTGREEN, 1=EGA.YELLOW, 2=EGA.LTRED), bold 18px, left, (10, 62)
```

---

## 6. GLOBÁLNÍ PROMĚNNÉ

```javascript
// === SCÉNA & RENDER ===
let scene, camera, renderer, lr, controls;
let main;                    // hlavní Group → všechny objekty
let startT, lastFrame, animFrameId;
let isPaused = false;
let deltaAccum = 0;

// === REFERENCE PRO ANIMACI ===
// Wall
let wallMesh, wallGroup;
let wallPanels = [];         // Mesh[]
let orderedPanels = [];      // Mesh[]
let lastPanelSpawn = -0.6;
let rearrangeStart = -1;
let defragDone = false;

// Portál
let portalOuter, portalInner, portalVortex = [], portalGroup;

// Teleport (náhrada shockwave)
let teleportCore, teleportShell, teleportArcs = [], teleportGlow;

// Operator
let operatorGroup, opTorso, opHead, opCap, opBeard;
let opLeftArm, opRightArm, opMug, opSteam = [];
let opLegL, opLegR;

// PC
let monitorMesh, monitorTex;

// Plotr
let plGantry, plSpindle, plLight;

// Data flow
let dataParticles = [];      // hlavní částice (PC → plotr)
let burstPool = [];          // burst částice (při průchodu portálem)
let dataLabels = [];         // CSS2D labely na datech

// Question marks
let questionMarks = [];
let qPool = [];              // Mesh[] — object pool 12 ks
let lastQSpawn = 0;

// Biz symbols
let bizSymbols = [];
let lastBizSpawn = 0;

// Dollars
let dollars = [];
let dollarTex;
let lastDollarSpawn = 0;

// HUD
let entropyDiv, statusDiv, metricsDiv;

// Guru
let guruShown = false;

// Toolpath
let pathLine;
```

---

## 7. CLEANUP REGISTR

```javascript
const _geos = [];    // všechny BufferGeometry
const _mats = [];    // všechny materiály
const _texs = [];    // všechny CanvasTexture
const _objs = [];    // všechny Mesh/Group přidané do scene

function regG(g) { _geos.push(g); return g; }
function regM(m) { _mats.push(m); return m; }
function regT(t) { _texs.push(t); return t; }
// M() už musí používat regG + regM
// makeCanvasTex už musí používat regT
```

---

## 8. CREATE FUNKCE

### 8.1 `createScene()`

```javascript
// 1. new THREE.Scene()
//    background = EGA.BLACK
//    fog = new THREE.Fog(EGA.BLACK, 15, 35)

// 2. Kamera (viz 2.3)

// 3. WebGLRenderer
//    antialias: false
//    setPixelRatio(1)
//    setSize(innerWidth, innerHeight)
//    appendChild(renderer.domElement)

// 4. CSS2DRenderer
//    setSize(innerWidth, innerHeight)
//    domElement.style = 'position:absolute;top:0;pointer-events:none'
//    appendChild(lr.domElement)

// 5. OrbitControls (viz 2.3)

// 6. Světla:
//    AmbientLight(EGA.DKGRAY)
//    DirectionalLight(EGA.LTGRAY, 0.8) na (2, 6, 4)
//    PointLight — plLight, EGA.YELLOW, 0.5, distance=6, pozice (0, 0.3, 0)

// 7. main = new THREE.Group()

// 8. Podlaha:
//    PlaneGeometry(8, 3, 7)
//    MeshStandardMaterial(EGA.BLACK, metalness 0.3, roughness 0.8, flatShading)
//    rotace X = -PI/2, Y = GND - 0.01

// 9. GridHelper(8, 16, EGA.DKGRAY, EGA.BLACK), Y = GND

// 10. Resize event listener
```

### 8.2 `createWall()`

```javascript
// Group na (0, WALL_Y, WALL_Z) — ZÁSADNÍ: WALL_Z=3.0, ne 1.5!

// wallMesh: PlaneGeometry(WALL_W, WALL_H)
//   MeshStandardMaterial({map: makeWallTex(), roughness: 0.9, flatShading})

// Rám: 4× box, EGA.LTGRAY
//   top:    box(WALL_W+0.12, 0.06, 0.08), Y = WALL_H/2 + 0.03
//   bottom: box(WALL_W+0.12, 0.06, 0.08), Y = -WALL_H/2 - 0.03
//   left:   box(0.06, WALL_H+0.12, 0.08), X = -WALL_W/2 - 0.03
//   right:  box(0.06, WALL_H+0.12, 0.08), X = WALL_W/2 + 0.03
```

**`wallGridPos(cellIdx)` — přepočteno na nové WALL_Z:**

```javascript
function wallGridPos(cellIdx) {
  const col = cellIdx % WALL_GRID_COLS;
  const row = Math.floor(cellIdx / WALL_GRID_COLS);
  return new THREE.Vector3(
    -WALL_W/2 + CELL_W/2 + col * CELL_W,
    WALL_Y + WALL_H/2 - CELL_H/2 - row * CELL_H,
    WALL_Z + 0.04
  );
}
```

Výsledný rozsah:  
- X: -2.2 až 2.2 (v 5 krocích po 1.1)  
- Y: 0.5+1.6-0.4=1.7 (horní řada) až 0.5-1.6+0.4=-0.7 (spodní řada) — světové souřadnice  
- Z: 3.04

### 8.3 `createPC()` — detailnější stůl

**ODLIŠNĚ OD v3.1 — vylepšený model:**

```javascript
// Group na (-3, 0, 0.5)  [posunuto, aby se vešlo s novou Z]

// DESKA STOLU (tlustší, s bočními hranami):
//   box(0.95, 0.06, 0.65), EGA.DKGRAY, Y = DSK_Y + 0.02
//   Horní lem: box(0.97, 0.01, 0.67), EGA.LTGRAY, Y = DSK_Y + 0.05
//   Přední hrana: box(0.95, 0.02, 0.03), EGA.LTGRAY, Y = DSK_Y + 0.03, Z = 0.34

// NOHY (CylinderGeometry místo boxů — lepší vzhled):
//   4× CylinderGeometry(0.04, 0.04, 0.55, 8), EGA.BLUE
//   Pozice: X = ±0.40, Z = ±0.28, Y = DSK_Y - 0.28 (střed)

// PŘÍČKA MEZI NOHAMA (vzadu):
//   box(0.80, 0.03, 0.04), EGA.DKGRAY, Y = DSK_Y - 0.20, Z = -0.28

// STOJAN MONITORU (s krkem — nově):
//   Základna: box(0.20, 0.03, 0.15), EGA.DKGRAY, Y = DSK_Y + 0.06, Z = 0.10
//   Krk: box(0.04, 0.15, 0.04), EGA.DKGRAY, Y = DSK_Y + 0.15, Z = 0.10

// MONITOR:
//   box(0.55, 0.38, 0.03), MeshStandardMaterial({map: monitorTex(), roughness: 0.2})
//   Y = DSK_Y + 0.32, Z = 0.10
//   Rám monitoru: box(0.57, 0.40, 0.01), EGA.DKGRAY, Y = DSK_Y + 0.32, Z = 0.085

// KLÁVESNICE (šikmá):
//   box(0.40, 0.03, 0.15), EGA.DKGRAY, Y = DSK_Y + 0.035, Z = 0.32
//   rotation.x = -0.15 (náklon)

// TOWER:
//   box(0.14, 0.44, 0.38), EGA.BLUE, X = -0.50, Y = GND + 0.22, Z = 0.25
//   Power LED: box(0.02, 0.02, 0.01), EGA.GREEN, X = -0.56, Y = GND + 0.40, Z = 0.43
```

### 8.4 `createOperator()`

**Převzít z v3.1 (ř. 167–218) — model je již kvalitní:**

```
- Nohy: 2× CylinderGeometry(0.09, 0.09, 0.35, 8), EGA.DKGRAY
- Holeně: 2× box(0.15, 0.32, 0.15), SKIN
- Trup: box(0.52, 0.42, 0.28), SHIRT + box(0.48, 0.22, 0.28) pas
- Levé rameno: Group s cyl(0.07, 0.42) rukáv + Sphere(0.07) dlaň + 3 box prsty
- Pravé rameno: zrcadlově
- Hlava: Group
  - box(0.40, 0.40, 0.40) SKIN
  - oči: 2× box(0.07, 0.05, 0.03) WHITE
  - ústa: box(0.12, 0.03, 0.02) EGA.BLACK
  - kšiltovka (opCap): box(0.44,0.08,0.34) + box(0.48,0.04,0.38) CAP_COLOR + kšilt box(0.14,0.04,0.22) EGA.BLACK
  - vous: ConeGeometry(0.14, 0.09, 6) BEARD_COLOR
- Hrnek: cyl(0.09,0.16) + torus ucho MUG_COLOR
- Pára: 3× SphereGeometry(0.03), transparent, EGA.LTGRAY, opacity 0.65
```

**Animace operátora — totožná s v3.1 (ř. 482–507):**

| Čas | Postoj |
|-----|--------|
| t < 7 | Předklon, sklopená hlava, ruce na stole |
| 7–9 | Přechod: narovnávání, zvedání hlavy |
| 9–20 | Zaklonění, ruce v týl, nohy zkřížené |
| t ≥ 20 | Idle + popíjení kávy (sinus pohyb hrnku, pokývnutí hlavy) |

### 8.5 `createPlotter()`

**Převzít z v3.1 (ř. 221–229) + přidat:**

```javascript
// Stůl: box(2.5, 0.06, 1.2), EGA.CYAN, Y = DSK_Y + 0.03
// 4 nohy: box(0.08, 0.46, 0.08), EGA.BLUE, X=±1.1, Z=±0.5, Y=GND+0.23

// Gantry (reference plGantry):
//   box(0.12, 0.16, 1.32), EGA.LTBLUE, Y = DSK_Y + 0.11
//   V animate(): X = sin(t * 0.5) * 0.2 (od t=10)

// Spindle head:
//   box(0.20, 0.14, 0.20), EGA.DKGRAY, Y = DSK_Y + 0.18
// Nástrojová hlavice (plSpindle reference):
//   CylinderGeometry(0.04, 0.04, 0.14, 8), EGA.LTGRAY, Y = DSK_Y + 0.02

// VEDENÍ (nově — přidáno):
//   2× box(0.04, 0.04, 1.30), EGA.DKGRAY, Y = DSK_Y + 0.10, Z = ±0.60

// CNC kabel (dekorativní):
//   TorusKnotGeometry(0.02, 0.01, 16, 8), EGA.RED, Y = DSK_Y + 0.04, Z = -0.50
```

### 8.6 `createPortal()`

**Převzít z v3.1 (ř. 233–248) — beze změny:**

```javascript
// portalGroup na (-2, 0.4, 0.3), visible=false
// Aktivace t=8–10: scale 0.3→1

// Vnější torus: TorusGeometry(0.35, 0.07, 16, 32), mE(0xFEF3C7, 0.35)
//   rotation.x = PI/2
// Vnitřní torus: TorusGeometry(0.22, 0.05, 12, 24), mE(0xFDE047, 0.5)
//   rotation.x = PI/2

// Vortex: 16× IcosahedronGeometry(0.03, 0), MeshBasicMaterial(WHITE, transparent, opacity 0.55)
//   Pozice na kružnici: (cos(a)*0.28, sin(i*0.3)*0.10, sin(a)*0.28)
//   userData: {angle, speed}
//   Rotace v animate: angle += speed * 0.03
```

### 8.7 `createTeleport()` — NOVÝ EFEKT (místo shockwave)

**Nahrazuje `createShockwave()` z v3.1:**

```javascript
// Group na (-2, 0.4, 0.3) — stejná pozice jako portalGroup
// visible=false

// 1. CORE — pulzující koule:
//    IcosahedronGeometry(0.15, 1), MeshBasicMaterial(WHITE, transparent, opacity 0.6)
//    emissive WHITE, emissiveIntensity: 0.5 + 0.5*sin(t*12)

// 2. SHELL — wireframe obal:
//    SphereGeometry(0.25, 12, 12) → EdgesGeometry → LineSegments
//    LineBasicMaterial(WHITE, transparent, opacity 0.3)
//    rotation.x += 0.02, rotation.y += 0.03 v animate

// 3. ELECTRICAL ARCS — 8 jiskřivých čar:
//    BufferGeometry s 16 vertexy (8 párů start-end)
//    Start: center (0,0,0)
//    End: random direction * (0.2 + random*0.3)
//    Každý frame: nové random koncové body (blikání)
//    LineSegments, LineBasicMaterial(WHITE, transparent, opacity 0.5)

// 4. GLOW HALO — průhledná koule:
//    SphereGeometry(0.35, 16, 16), MeshBasicMaterial(WHITE, transparent, opacity 0.15, side=DoubleSide)
//    scale pulzuje: 1 + 0.1*sin(t*8)

// Timing:
//   t=0–7.9:  visible=false
//   t=8.0–9.5: visible=true, scale 0.3→2.5 (expand)
//   t>9.5:    visible=false

// Intenzita v čase:
//   t=8.0–8.3:  fade in (opacity 0→1)
//   t=8.3–9.0:  full (core pulzuje, arcs jiskří)
//   t=9.0–9.5:  fade out + expand (scale 1→2.5, opacity 1→0)
```

### 8.8 `createDataFlow()`

```javascript
// BURST POOL — 72 koulí (PARTICLE_COUNT=12, BURST_PER=6):
//   SphereGeometry(0.02, 4, 4), MeshBasicMaterial(WHITE, transparent, opacity 0)
//   visible=false, userData: {life: 0}
//   burstPool[] + main

// HLANÍ ČÁSTICE — 12 ks:
//   Pro i=0..11:
//     IcosahedronGeometry(0.06, 0), mF(EGA.RED) — chaos fáze
//     BoxGeometry(0.08, 0.08, 0.08), mF(EGA.GREEN) — ordered fáze, visible=false
//     CSS2D label s čínským znakem (el(, '9px', '#AA0000'))
//     userData: {ico, cube, label, progress: i/12, speed: 0.08+rand*0.05,
//                phase: rand*6.28, burstIdx: i*6, zOff: (rand-0.5)*0.3}
//     main.add(ico); main.add(cube); main.add(label)

// DATA LABELY — 5 ks (čínské znaky):
//   ['数据', '乱码', '错误', '未知', '失败']
//   CSS2DObject(el(, '13px', '#EF4444'))
//   Pozice: na trajektorii PC→portál→plotr
//   V ordered fázi: text se změní na CNC_LABELS, barva na #22C55E

// TRAJEKTORIE:
//   chaosSrc = (-3.3, 0.55, 0.5 + zOff)
//   chaosMid = (-1.5 + 0.3*midOff, 0.7, 0.3 + zOff*0.5)
//   chaosTgt = (0, 0.28, 0.2 + zOff*0.3)

// BEZIER: bezt(p0, p1, p2, t) — quadratic bezier
//   mt = 1-t
//   return Vector3(mt²·p0 + 2·mt·t·p1 + t²·p2)

// TIME DILATION (t=9–10):
//   timeScale = 0.3 + (t-9)² * 1.0, max 1.3

// MORPH/BURST (t≥9, |progress-0.5| < (t-9)*0.3):
//   Ico → Cube morph (ico.visible=false, cube.visible=true)
//   6 burst částic z burstPool, random direction

// TRAIL LINES (zjednodušeno — bez vertex color gradient):
//   BufferGeometry s 3 vertexy
//   LineBasicMaterial(EGA.RED/EGA.GREEN, transparent, opacity 0.5)
//   Update: poslední 3 pozice částice
//   (Toto je zjednodušení oproti v5 — stačí jednoduchá čára)
```

### 8.9 `createHUD()`

```javascript
// 3× CSS2DObject s .css-hud třídou
// Group na (0, 2.7, 0)

// entropyDiv:
//   "┌─ ENTROPY ─────────┐\n│ █████████░░░ 94% │\n└────────────────────┘"

// statusDiv:
//   "┌─ STATUS ──────────┐\n│ UNSTRUCTURED DATA  │\n└────────────────────┘"

// metricsDiv:
//   "┌─ METRICS ─────────┐\n│ T:0s P:0/0        │\n└────────────────────┘"
```

### 8.10 `createToolpath()`

```javascript
// Dráha nástroje na plotru — LineSegments
// Y = GND + 0.05, Z = 0

// 4 obdélníkové segmenty + 1 kruh + pár click bodů
// BufferGeometry, LineBasicMaterial(EGA.LTBLUE, transparent, opacity 0.25)

// Segmenty (24 bodů každý):
//   top:    (-0.8, TY, -0.5) → (0.8, TY, -0.5)
//   right:  (0.8, TY, -0.5) → (0.8, TY, 0.5)
//   bottom: (0.8, TY, 0.5) → (-0.8, TY, 0.5)
//   left:   (-0.8, TY, 0.5) → (-0.8, TY, -0.5)
// Kruh: 16 bodů, radius 0.25, center (0, TY, 0)
```

---

## 9. SPAWN FUNKCE

### 9.1 `spawnWallPanel(defective)`

```javascript
// textura: makePanelTex(defective)
// geometrie: box(CELL_W * 0.65, CELL_H * 0.4, 0.04) = ~box(0.72, 0.32, 0.04)
// materiál: MeshStandardMaterial({map: tex, roughness: 0.6, flatShading})
// startPos: (0.3, GND + 0.25, 0.4) — z plotru
// targetCell: Math.floor(Math.random() * 20)

// POSUNOUT start Z souřadnici vzhledem k novému WALL_Z!
// Panel letí OD plotru (Z≈0.4) NA zeď (Z≈3.04)

// userData:
//   {defective, targetCell, targetPos, startPos, flightTime: 0,
//    maxFlight: 1.5+rand*1.2, arrived: false, tex, defectMesh, warningLabel}

// Pokud defective:
//   ConeGeometry(CELL_W*0.06, CELL_H*0.08, 4), EGA.RED
//   pozice: (CELL_W*0.15, CELL_H*0.08, 0.02), rotation.z=0.4
//   CSS2D label s varováním: "⚠ H2=-0.5" | "⚠ FIXACE!" | "⚠ POSUN" | "⚠ CONFLICT"
```

### 9.2 `spawnQuestionMark()`

```javascript
// qPool.find(q => !q.visible) — object pool
// visible=true, opacity=0.9
// pozice: (0.3, GND+0.18, 0.4) — z plotru
// userData: {life: 0, velX:(rand-0.5)*0.018, velY:0.008+rand*0.015, velZ:0.015+rand*0.01}
// questionMarks.push(q)
```

### 9.3 `spawnBizSymbol()`

```javascript
// random text z BIZ_TEXTS
// random barva z ['#22C55E','#0D9488','#3B82F6','#F59E0B']
// CSS2DObject(el(txt, '11px', color))
// start pozice: (0.3, GND+0.22, 0.4) — z plotru

// LEVITACE NAD PLOTREM (nový design):
//   userData: {st: currentTime, tY: 0.5 + rand*1.2, tX: (rand-0.5)*1.5, tZ: (rand-0.5)*0.8}
//   V animate: smooth lerp k (tX, tY, tZ) — volně levituje nad plotrem
//   Po 3s: fade opacity → 0, pak remove

// t ≥ 10, každých 0.9s
```

### 9.4 `spawnDollar()`

```javascript
// makeDollarTex()
// box(0.15, 0.15, 0.03), MeshStandardMaterial({map: dollarTex, emissive: EGA.YELLOW,
//   emissiveIntensity: 0.3, flatShading, transparent, opacity: 1})
// start pozice: (0.5, GND+0.6, 0.3) — nad plotrem
// userData: {st: currentTime, vy: 0.008, vx:(rand-0.5)*0.005, vz:(rand+0.5)*0.01,
//            rotSpeed:(rand-0.5)*0.06, life: 0}

// V animate:
//   vy -= 0.00015 (gravity)
//   y += vy, x += vx, z += vz (drift ke zdi)
//   if y <= GND+0.1: y = GND+0.1
//   rotation.x += rotSpeed, rotation.y += rotSpeed*0.6
//   life = t - st
//   if life > 8: opacity fade
//   if life > 10: main.remove + splice

// t ≥ 15, každých 1.5s, max DOLLAR_MAX (6)
// t > 22, každých 3s (pomaleji)
```

---

## 10. UPDATE FUNKCE (rozdělený animate)

**Místo monolitního `animate()` — 10 samostatných funkcí volaných z hlavní smyčky:**

```javascript
function animate() {
  if (isPaused) { animFrameId = requestAnimationFrame(animate); return; }

  const now = performance.now();
  const delta = Math.min((now - lastFrame) / 1000, 0.05);  // clamped!
  lastFrame = now;
  deltaAccum += delta;

  const t = (now - startT) / 1000;
  const phase = getPhase(t);

  if (controls.enabled) controls.update();
  else if (t > 12) controls.enabled = true;

  updateBackground(t, phase);
  updateOperator(t, delta);
  updatePortal(t, delta);
  updateTeleport(t, delta);         // nový terminátor efekt
  updateDataFlow(t, delta, phase);
  updateQuestionMarks(t, delta);
  updateBizSymbols(t, delta);
  updateWallPanels(t, delta, phase);
  updateDollars(t, delta);
  updatePlotter(t, delta);
  updateHUD(t, phase);
  updateGuru(t);
  updateIdleEffects(t, delta);

  renderer.render(scene, camera);
  lr.render(scene, camera);
  animFrameId = requestAnimationFrame(animate);
}
```

### 10.1 `getPhase(t)` — stavový automat

```javascript
function getPhase(t) {
  if (t < PHASE.CHAOS_END) return 'CHAOS';
  if (t < PHASE.ORDER_START) return 'ACTIVATION';  // 7–10s
  if (t < PHASE.IDLE_START) return 'ORDER';
  return 'IDLE';
}
```

### 10.2 `updateBackground(t, phase)`

```
CHAOS:       bg = EGA.BLACK
ACTIVATION:  t=9.0–9.4 → bg = EGA.WHITE (flash)
             poté zpět na EGA.BLACK
ORDER:       Lerp EGA.BLACK → tmavší teal (0x0a1218) přes 6s
IDLE:        bg = 0x0a1218
```

### 10.3 `updateOperator(t, delta)`

**Totožná logika jako v3.1 ř. 482–507:**

```
t < 7:   předklon (opTorso.x=0.35, opHead.x=0.3, ruce na stole)
7–9:     lt = min(1, (t-7)/2) — lineární přechod
9–20:    rt = min(1, (t-9)/3) — zaklonění, ruce v týl, nohy zkřížené
20+:     idle, sinus pohyb hrnku + občasné pokývnutí

opSteam: vždy aktivní — Y posun = by + ((t*0.6 + ph) % 1.3) * 0.10
         opacity = 0.65 * (1 - ((t*0.6 + ph) % 1.3))
```

### 10.4 `updatePortal(t, delta)`

```
t < 8:   portalGroup.visible = false
8–10:    visible = true, scale 0.3→1 (ease)
         outer.rotation.y += 0.025, inner.rotation.y -= 0.035
         outer.emissiveIntensity = 0.3 + 0.4*sin(t*8)
         inner.emissiveIntensity = 0.4 + 0.5*sin(t*9)
         vortex: angle += speed * 0.03; x = cos(angle)*0.28; z = sin(angle)*0.28
10+:     visible = true, scale = 1
         emissiveIntensity = 0.12 (outer), 0.15 (inner)
         rotace pokračuje pomalu
```

### 10.5 `updateTeleport(t, delta)` — TERMINÁTOR EFEKT

```
t < 8:   teleportGroup.visible = false
8.0–9.5: visible = true

  SUB-FÁZE:
  8.0–8.3: fade in
    scale: 0.3 → 0.5 (lineární)
    core.opacity: 0 → 0.8
    shell.opacity: 0 → 0.4
    arcs.opacity: 0 → 0.6
    glow.opacity: 0 → 0.2

  8.3–9.0: aktivní fáze (pulzace)
    core.emissiveIntensity = 0.5 + 0.5*sin(t*12)
    shell.rotation.x += 0.02, shell.rotation.y += 0.03
    arcs: každý frame nové random koncové body
      for each arc pair:
        dir = Vector3(randomNormal).normalize()
        length = 0.2 + random*0.3
        positions[i*3] = 0; positions[i*3+1] = 0; positions[i*3+2] = 0
        positions[(i+1)*3] = dir.x * length
        positions[(i+1)*3+1] = dir.y * length
        positions[(i+1)*3+2] = dir.z * length
      arcs.geometry.attributes.position.needsUpdate = true
    glow.scale = 1 + 0.12*sin(t*8)
    scale: 0.5 → 1.2 (pomalý nárůst)

  9.0–9.5: fade out + expand
    scale: 1.2 → 3.0
    core.opacity: 0.8 → 0
    shell.opacity: 0.4 → 0
    arcs.opacity: 0.6 → 0
    glow.opacity: 0.2 → 0

  t ≥ 9.5: visible = false
```

### 10.6 `updateDataFlow(t, delta, phase)`

```
isOrdered = phase === 'ORDER' || phase === 'IDLE'
chaosSrc, chaosMid, chaosTgt — trojice Vector3

Pro každou dataParticle:
  speed *= timeScale (t=9–10 time dilation)
  progress += speed * delta * 60  // normalized speed
  if progress >= 1: progress -= 1; morphTime = -1
  pos = bezt(src, mid, tgt, progress)
  ico.position = pos; cube.position = pos
  ico.visible = !isOrdered (nebo morphTime < 0)
  cube.visible = isOrdered (a morphTime >= 0)
  color = isOrdered ? EGA.GREEN : EGA.RED
  ico.material.color = color

  label.position = (pos.x, pos.y+0.10, pos.z)
  if isOrdered: label = CNC_LABELS[phase], barva #22C55E
  else: label = čínský znak, barva #AA0000

  // BURST (při průchodu portálem v activaci)
  if phase === 'ACTIVATION' && morphTime < 0:
    dist = abs(progress - 0.5)
    if dist < (t-9)*0.3:
      burst 6 koulí z burstPool s random direction

  // TRAIL (zjednodušený)
  trailVerts[0..2] = pos
  trailVerts[3..5] = pos (offset zpět)
  trail.geometry.attributes.position.needsUpdate = true

Data labely:
  Pro každý label (CSS2DObject):
    if isOrdered: text = CNC_LABELS[i], color = '#22C55E'
    else: text = čínský znak, color = '#EF4444'

Burst pool:
  life += delta
  move direction * 0.04
  opacity = 0.8 * (1 - life*4)
  if life > 0.25: visible = false
```

### 10.7 `updateWallPanels(t, delta, phase)`

```
// SPAWN (jen v CHAOS fázi)
if phase === 'CHAOS' && t - lastPanelSpawn >= 0.65:
  spawnWallPanel(wallPanels.length > 0 && (wallPanels.length+1) % 3 === 0)
  lastPanelSpawn = t

// FLIGHT TO WALL
for each panel (if !arrived):
  flightTime += delta
  ft = flightTime / max(maxFlight, 0.5)
  ep = min(1, ft)
  ease = 1 - (1-ep)³
  position.lerpVectors(startPos, targetPos, ease)
  if ft >= 0.95: arrived = true; rotation.z = random(-0.25, 0.25)

// DEFRAG (při přechodu do ORDER fáze)
if phase === 'ORDER' && rearrangeStart < 0:
  rearrangeStart = t
  for each panel:
    origPos = position.clone(); origRot = rotation.z
    idealTarget = wallGridPos(targetCell)  // přesně do gridu

if rearrangeStart >= 0:
  rp = min(1, (t - rearrangeStart) / 3.5)
  // EaseOutBack:
  c3 = 2.70158
  ep = rp < 1 ? 1 + c3*(rp-1)³ + 1.70158*(rp-1)² : 1

  for each panel (if idealTarget):
    position.lerpVectors(origPos, idealTarget, ep)
    rotation.z = origRot * (1-ep)

    if rp > 0.5: defectMesh.visible = false (if exists)
    if rp > 0.4: warningLabel.visible = false (if exists)

    if rp > 0.1:
      cp = min(1, (rp-0.1)/0.9)
      defective: color lerp EGA.RED → EGA.GREEN
      OK:        color lerp EGA.BROWN → EGA.GREEN

  if rp >= 1: defragDone = true; all panels defective = false
```

### 10.8 `updateHUD(t, phase)`

```
// ENTROPY
if t < 8: entropy = 94
else if t <= 22: entropy = 94 - (t-8) * (88/14)
else: entropy = 6
eVal = Math.round(max(6, entropy))

Color interpolace:
  entropy ≥ 50: Color(0xFDE047).lerp(Color(0xEF4444), (entropy-50)/44)
  entropy < 50: Color(0x22C55E).lerp(Color(0xFDE047), (entropy-6)/44)

dosBar(val): █×filled + ░×(10-filled)

entropyDiv.textContent =
  "┌─ ENTROPY ─────────┐\n│ " + dosBar(eVal) + " " + eVal + "% │\n└────────────────────┘"
entropyDiv.style.color = "#" + ec.getHexString()

// STATUS
switch phase:
  CHAOS:     "UNSTRUCTURED DATA ▒" (bliká každých 0.5s), barva #EF4444
  ACTIVATION: "DECODING ⚡ ▒▒" / "░░" (bliká 0.25s), barva #FDE047
  ORDER:     "DETERMINISTIC OUTPUT ▒", barva #22C55E
  IDLE:      "ENGINE V20 · IDLE ▒", barva #22C55E

statusDiv.textContent = "┌─ STATUS ──────────┐\n│ " + st.padEnd(20) + "│\n└────────────────────┘"

// METRICS
doneCount = wallPanels.filter(p => p.arrived).length + orderedPanels.length
totalP = wallPanels.length + orderedPanels.length
savedMin = floor(t * 0.4)

metricsDiv.textContent =
  "┌─ METRICS ─────────┐\n│ T:" + floor(t) + "s P:" + doneCount + "/" + totalP + " S:" + savedMin + "│\n└────────────────────┘"
metricsDiv.style.color = t > 10 ? '#22C55E' : '#64748B'
```

### 10.9 `updateDollars(t, delta)`

```
// SPAWN
if phase === 'ORDER' || phase === 'IDLE':
  if t >= 15 && t - lastDollarSpawn >= 1.5 && dollars.length < DOLLAR_MAX:
    spawnDollar(); lastDollarSpawn = t
  if t > 22 && t - lastDollarSpawn >= 3 && dollars.length < DOLLAR_MAX:
    spawnDollar(); lastDollarSpawn = t

for each dollar:
  vy -= 0.00015 * delta * 60  // gravity
  y += vy; x += vx; z += vz
  if y <= GND + 0.1: y = GND + 0.1
  rotation.x += rotSpeed; rotation.y += rotSpeed * 0.6
  life = t - st
  if life > 8: opacity = max(0, 1 - (life-8)/2)
  if life > 10: main.remove; splice
```

### 10.10 `updatePlotter(t, delta)`

```
if t >= 10:
  plGantry.position.x = sin(t * 0.5) * 0.2
  plSpindle.position.y = GND + 0.10 + abs(sin(t * 2.5)) * 0.04

// Light color:
if phase === 'ORDER' || phase === 'IDLE': plLight.color = EGA.GREEN
else if phase === 'ACTIVATION': plLight.color = EGA.YELLOW
else: plLight.color = EGA.BROWN
```

### 10.11 `updateGuru(t)`

```
if t > 8.1 && t < 8.5 && !guruShown:
  guruShown = true
  updateMonitorTex(makeGuruTex())
  // Po 500ms: zpět na hex dump
  // Použít proměnnou guruTimer místo setTimeout
  // V dalším frame check: if guruTimer > 0.5: updateMonitorTex(makeHexTex()); guruTimer = -1
```

### 10.12 `updateIdleEffects(t, delta)`

```
if phase === 'IDLE' && Math.random() < 0.02:
  // Zelená tečka v prostoru plotru
  dot = M(box(0.03, 0.03, 0.03), EGA.GREEN)
  dot.position.set((rand-0.5)*0.5, GND+0.25, (rand-0.5)*0.5)
  main.add(dot)
  // Remove po 3s — pomocí timeToLive v userData
```

---

## 11. INIT

```javascript
function init() {
  createScene();
  createWall();
  createPC();
  createOperator();
  createPlotter();
  createPortal();
  createTeleport();       // místo createShockwave
  createDataFlow();
  createHUD();
  createToolpath();

  startT = performance.now();
  lastFrame = performance.now();
  animFrameId = requestAnimationFrame(animate);
}
```

---

## 12. DISPOSE

```javascript
function disposeAll() {
  // Zastavit animaci
  if (animFrameId) cancelAnimationFrame(animFrameId);

  // Uvolnit všechny geometrie
  _geos.forEach(g => g.dispose());
  // Uvolnit všechny materiály
  _mats.forEach(m => m.dispose());
  // Uvolnit všechny textury
  _texs.forEach(t => t.dispose());

  // Dispose rendererů
  renderer.dispose();
  lr.domElement.remove();

  // Odebrat event listenery
  window.removeEventListener('resize', onResize);
  document.removeEventListener('visibilitychange', onVisibilityChange);
  window.removeEventListener('beforeunload', disposeAll);
}

window.addEventListener('beforeunload', disposeAll);
```

---

## 13. PAGE VISIBILITY API

```javascript
document.addEventListener('visibilitychange', () => {
  if (document.hidden) {
    isPaused = true;
  } else {
    isPaused = false;
    lastFrame = performance.now();  // reset delta, zabrání skoku
  }
});
```

---

## 14. PROSTOROVÝ DIAGRAM (Y-UP)

```
  Y
  ↑
2.7 ─ HUD (CSS2D)
  │
1.7 ─ stěna horní okraj (Y=0.5+1.6=2.1 max)
  │
1.0 ─ hlavy postav
  │
0.5 ─ stěna střed (WALL_Y=0.5)
  │     plotr stůl (DSK_Y=0.22)
0.0 ─ GND
  │
-0.55 ─ podlaha (GND)
  │

  Z (směrem ke kameře)
  │
0.0 ─ plotr (Z=0)
  │     operátor (Z=1.1)
  │     PC (Z=0.5)
1.2 ─ kamera lookAt (Z=1.2)
  │
3.0 ─ stěna (WALL_Z=3.0)
  │
7.0 ─ kamera position (Z=7)

POZNÁMKA: V Three.js standard: Z+ je směrem ke kameře.
```

---

## 15. CHECKLIST PRO IMPLEMENTACI

### Povinné změny
- [ ] `WALL_Z = 3.0` (byl 1.5 — stěna byla před plotrem!)
- [ ] Odstranit `createLibrary()`, `dataClones`, `libraryBlockedCubes`
- [ ] `bizSymbols` spawn z plotru, levituje nad ním (místo letu do knihovny)
- [ ] Terminátor teleport místo shockwave sphere
- [ ] Detailnější PC stůl (Cylinder nohy, příčka, krk monitoru, šikmá klávesnice)

### Render loop & Dispose
- [ ] Delta time clamping (`Math.min(delta, 0.05)`)
- [ ] Page Visibility API pause/resume
- [ ] Registry `_geos`, `_mats`, `_texs`
- [ ] `disposeAll()` na `beforeunload`

### Vizuální upgrade
- [ ] EGA 16-color palette (převést všechny barvy)
- [ ] `Press Start 2P` font
- [ ] `setPixelRatio(1)`, `antialias: false`
- [ ] `NearestFilter` na všech CanvasTexture
- [ ] OrbitControls
- [ ] Toolpath line

### Obsah (beze změny z v3.1)
- [ ] Operátor s kšiltovkou, vousem, kávou + pára
- [ ] Dual torus portál + vortex
- [ ] Data flow 12 částic
- [ ] Question marks z plotru
- [ ] Wall panel spawn + flight + defrag easeOutBack
- [ ] DOS HUD s box-drawing znaky
- [ ] Guru meditation
- [ ] Dollars z plotru (ne knihovny)

---

*Konec specifikace. Připraveno pro implementační fázi.*
