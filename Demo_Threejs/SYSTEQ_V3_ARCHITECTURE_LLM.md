---
project: SYSTEQ B2B VCF Parser & CNC Defragmenter Three.js Demo
version: V3.3 (dev)
branch: feature/design_logo
last_commit: 21734eb
files:
  production: Demo_Threejs/systeq_v3.html (1574 lines, no dev code)
  dev:        Demo_Threejs/systeq_v3_dev.html (1508 lines, activated by `?dev`)
  preset:     Demo_Threejs/systeq_v3_dev.json (example GUI state export)
  server:     Demo_Threejs/systeq_v3.1_dev.bat (python -m http.server 8080 + chrome with ?dev)
  handoff:    Demo_Threejs/handoff_threejs_demo.txt
  spec_full:  docs/design/TECH_SPEC_SCENE_V3.1_REFACTOR.md
  spec_lite:  docs/design/TECH_SPEC_SCENE_V3.2_LITE.md
  spec_prompt: src/demo/TECH_SPEC_SCENE_V3.3_PROMPT.md
cdn:
  three: https://unpkg.com/three@0.160.0/build/three.module.js
  orbit: https://unpkg.com/three@0.160.0/examples/jsm/controls/OrbitControls.js
  css2d: https://unpkg.com/three@0.160.0/examples/jsm/renderers/CSS2DRenderer.js
  lil-gui: https://cdn.jsdelivr.net/npm/lil-gui@0.21/+esm
---

# ARCHITECTURE OVERVIEW

## 1. FILE STRUCTURE (single monolithic HTML)

```
<!DOCTYPE html>
├── <head>
│   ├── <style>                                     // CSS layers:
│   │   ├── Base reset + CRT scanline/flicker       //   #crt overlay
│   │   ├── .css-label                              //   CSS2D label style
│   │   ├── #hud-container, .dos-panel, .top-bar    //   DOS HUD layout
│   │   ├── .right-sidebar                          //   Cluster map, diagnostics
│   │   ├── .bottom-keys                            //   F-key bar (F1-F10)
│   │   ├── #dev-toast                              //   DEV: toast notification
│   │   ├── #dev-banner                             //   DEV: red top-left banner
│   │   └── #flash-overlay                          //   Activation white flash
│   └── <link> Press Start 2P font
├── <body>
│   ├── <div id="dev-toast">                        //   DEV only
│   ├── <div id="dev-banner">                       //   DEV only
│   ├── <div id="crt">                              //   CRT scanline overlay
│   ├── <div id="flash-overlay">                    //   White flash for activation
│   ├── <div id="hud-container">                    //   DOS HUD:
│   │   ├── .top-bar                                //     Title + live clock
│   │   ├── .right-sidebar                          //     Cluster map (64 sectors), legend, diagnostics, metrics
│   │   └── .bottom-keys                            //     F1-F10 key bar
│   ├── <script type="importmap">                   //   Three.js import map
│   └── <script type="module">                      //   ALL JavaScript (the core)
└── </html>
```

## 2. JavaScript MODULE LAYER (script type="module")

```
Is DEV? → IS_DEV = window.location.search.includes('dev')  // line 278
```

```
LAYER 1: CONSTANTS & EGA PALETTE      (lines 280-293)
─────────────────────────────────────
  GND=-0.55, DSK_Y=0.33, WALL_Z=-2.5, WALL_W=5.5, WALL_H=3.2
  EGA = { BLACK, BLUE, GREEN, CYAN, RED, MAGENTA, BROWN, LTGRAY,
          DKGRAY, LTBLUE, LTGREEN, LTCYAN, LTRED, LTMAGENTA, YELLOW, WHITE }

LAYER 2: DEV PARAMS OBJECT (tweakable) (lines 297-359)
─────────────────────────────────────
  44 fields: speed, camera (camDist/Height/TargetY),
  phase timing (chaosEnd/activationEnd/orderEnd),
  positions (opX/Y/Z, plotterX/Y/Z, portalX/Y/Z, pcX/Z, wallY),
  colors (colCubeChaos[4], colCubeOrder, colPanelDefect/Ok/Healed),
  spawn rates (cubeRate, qmRate, dollarRate, metricRate),
  panel mechanics (panelLaunchSpeed, panelGridStep, forceMounted),
  paused, frozenSimTime, loopMode,
  scales (wallScale, pcScale, opScale, plotterScale, portalScale),
  visibility (showWall/PC/Operator/Plotter/Portal/Panels)

LAYER 3: HELPERS                       (lines 370-374)
─────────────────────────────────────
  box(w,h,d), cyl(r,h,s), tor(R,r,rs,ts), mF(color), M(geo,color)

LAYER 4: STATE VARIABLES               (lines 376-397)
─────────────────────────────────────
  Scene: scene, camera, renderer, cssRenderer, controls
  Timing: startT, simTime, lastFrameT, lastSecond
  Groups: wallGroup, pcGroup, operatorGroup, plotterGroup, portalGroup
  Sub-objects: pcMonitorScreenMesh, monitorCanvas, monitorCtx
               plGantry, plSpindle, plLaserLight
               opHead, opLeftArm, opRightArm, opSmileMesh, opLegL, opLegR
               portalTorus1, portalTorus2, portalVortex, portalShockwave
               energeticFilamentsGroup
  Arrays: dataCubes[], questionMarks[], panels[], goldenDollars[], floatingTexts[]
  Other: clusterSectorColors[64]

LAYER 5: INIT / BOOTSTRAP              (lines 408-489)
─────────────────────────────────────
  function init()
    1. Create Three.js scene (ambient + directional + point lights)
    2. Create WebGLRenderer (antialias:false, pixelRatio:1, flat shading)
    3. Create CSS2DRenderer (for labels)
    4. Create OrbitControls
    5. createWall(), createPC(), createOperator(), createPlotter(), createPortal()
    6. Setup key handlers (Space=pause, R=reset, P=phase preview, L=loop)
    7. if (IS_DEV) buildDevGUI()
    8. start animation loop
  window.onload = init

LAYER 6: 3D GEOMETRY GENERATORS        (lines 492-784)
─────────────────────────────────────
  createWall()       → wallGroup at (0, wallY, WALL_Z), 4×3 slots, scale(v,v,1)
  createPC()         → pcGroup at (pcX, GND, pcZ), rotated 90°, desk+tower+CRT monitor
  createOperator()   → operatorGroup at (opX, GND+opY, opZ), blue torso+head+cap+beard+arms+mug+legs
  createPlotter()    → plotterGroup at (plotterX, GND+plotterY, plotterZ), bed+rails+gantry+spindle+laser
  createPortal()     → portalGroup at (portalX, portalY, portalZ), dual torus+vortex+shockwave+filaments

LAYER 7: SPAWNERS                      (lines 786-910)
─────────────────────────────────────
  spawnChaosDataCube(t)      → red/magenta/yellow/brown cube, PC→plotter
  spawnOrderedDataCube(t)    → green cube, PC→portal
  spawnQuestionMark()        → CSS2D "?" label, floats up from plotter
  spawnGoldenDollar()        → 3D gold $ group, floats from portal
  spawnFloatingMetricWord()  → CSS2D text (ČAS ZPRACOVÁNÍ, MARŽE...), floats from portal
  spawnPanelOnPlotter(defect)→ 3D panel group + optional wireframe warning, launches to wall

LAYER 8: RESET                         (lines 918-942)
─────────────────────────────────────
  function triggerManualReset()
    - Removes all arrays (dataCubes, questionMarks, panels, goldenDollars, floatingTexts)
    - Clears energeticFilamentsGroup
    - Resets operator pose, laser, cluster map (random red/darkgray)
    - Resets timer: simTime=0, startT=now, lastFrameT=now
    - Disables auto-rotate
    - Redraws PC screen to CHAOS state

LAYER 9: PHASE/ANIMATION ENGINE        (lines 951-1284)
─────────────────────────────────────
  function getPhase(t) → 'CHAOS' | 'ACTIVATION' | 'ORDER' | 'IDLE'
    CHAOS:      t < chaosEnd (default 7s)
    ACTIVATION: t < activationEnd (default 10s)
    ORDER:      t < orderEnd (default 22s)
    IDLE:       t >= orderEnd

  function animate()  [rAF loop]
    ├── Time update (handles pause, speed, loopMode)
    ├── Phase: CHAOS
    │   ├── spawn defective panels, data cubes, question marks
    │   ├── operator slouch, laser flicker, gantry moves
    │   └── white flash at t=chaosEnd+0.5..0.8
    ├── Phase: ACTIVATION
    │   ├── shockwave expands, filaments connect portal→panels
    │   ├── operator sits up, PC shows "PARSING..."
    │   └── gantry returns to center
    ├── Phase: ORDER
    │   ├── panels snap to grid (easeOutBack), defective heal (red→green)
    │   ├── green data cubes, $ symbols, metric words float up
    │   └── operator bounces, smile visible
    └── Phase: IDLE
        ├── all panels at targetPos
        ├── camera auto-rotate enabled
        └── residual glow
    ├── Data cube animation (lerp src→tgt with wiggle)
    ├── Panel launch animation (lerp startPos→chaosOffsetPos)
    ├── Question mark animation (float up + fade)
    ├── Golden dollar animation (float + fade)
    ├── Floating metric text animation (float + fade)
    ├── Force mounted debug (if params.forceMounted)
    ├── Visibility updates (apply show* params)
    ├── Scale updates (apply *Scale params)
    ├── HUD cluster map update (every second)
    ├── Controls update + render (WebGL + CSS2D)
    └── (DEV only) diagnostics update interval (200ms)

LAYER 10: DEV GUI (lil-gui)            (lines 1286-1501)
─────────────────────────────────────
  Only loaded when IS_DEV=true (i.e., URL includes ?dev).

  ▸ MASTER folder:
    Speed ×, Paused (Space), ↻ Restart, Loop mode (L)
    💾 Save (localStorage), 📂 Load (localStorage), 📋 Export (clipboard/JSON file)
    Jump → (CHAOS|ACTIVATION|ORDER|IDLE) with auto-reset
    🎬 Export Prod (see below)

  ▸ CAMERA folder:
    Distance, Height, Target Y with live onChange
    Presets: default, top-down, close-up, side

  ▸ PHASE TIMING folder:
    CHAOS→, ACTIVATION→, ORDER→ end times (seconds)

  ▸ OBJECT POSITIONS folder:
    Operator X/Y/Z, Plotter X/Y/Z, Portal X/Y/Z, Wall Y, PC Desk X/Z
    All have onChange → update corresponding group.position

  ▸ OBJECT DIMENSIONS folder:
    Wall (scale v,v,1), PC Desk, Operator, Plotter, Portal (uniform scale)
    All have onChange → update corresponding group.scale

  ▸ OBJECT VISIBILITY folder:
    Wall, PC Desk, Operator, Plotter, Portal, Panels (boolean)
    All have onChange → update .visible property

  ▸ EGA PALETTE folder:
    Cube Red/Magenta/Yellow/Brown (colCubeChaos[0-3])
    Cube Order, Panel Defect, Panel OK, Panel Healed (color strings → hex ints)
    Uses colorProxy object (lil-gui addColor needs string #RRGGBB)

  ▸ SPAWN RATES folder:
    Data cubes, Question mk, Dollars, Metrics (probability per frame)

  ▸ PANELS folder:
    Launch speed, Grid step, Force all mounted (debug)

  ▸ LIVE DIAGNOSTICS folder:
    Phase, Time (s), Panels, Data cubes, ? marks (auto-updates every 200ms)

  Auto-restores last preset from localStorage key 'systeq-dev' on init.

  ▸ EXPORT PROD (🎬 button):
    1. Fetch current HTML via fetch(window.location.href)
    2. Replace params block with current GUI values (template literal)
    3. Strip buildDevGUI() FIRST (prevents false regex matches)
    4. Strip: import GUI, IS_DEV, if(IS_DEV) call, devToast(), dev HTML/CSS
    5. Download as systeq_v3_prod.html via Blob + <a> click
```

## 3. KEY PARAMETERS (defaults)

```
speed:          1.0       camDist:   7.0    camHeight:  1.4    camTargetY: 0.3
chaosEnd:       7.0       activationEnd: 10.0                 orderEnd: 22.0
op X/Y/Z:      -1.2/0.38/2.0
plotter X/Y/Z: -0.5/0.15/0.8
portal X/Y/Z:  0.0/0.8/-1.2
pc X/Z:        -2.4/1.2
wallY:         0.55
scales:        all 1.0
visibilities:  all true
cubeRate:      0.08      qmRate: 0.04     dollarRate: 0.02    metricRate: 0.015
panelLaunchSpeed: 0.02   panelGridStep: 0.008
```

## 4. DERIVED TIMING FUNCTIONS

```
flashStart()    = chaosEnd + 0.5
flashEnd()      = chaosEnd + 0.8
shockwaveStart()= chaosEnd + 0.5
shockwaveDur    = 2.0
filamentStart() = chaosEnd + 0.6
filamentEnd()   = chaosEnd + 2.5
```

## 5. SPATIAL LAYOUT (depth order)

```
Camera at (0, 1.4, 7) → target (0, 0.3, 1.2)
  ├── Plotter/PC/Operator at z ≈ 0.8–1.4
  ├── Portal at z = -1.2
  └── Wall at z = -2.5 (behind everything)
```

## 6. KNOWN ISSUES

```
1. triggerManualReset() uses locally-defined helper functions inside
   forEach (qmLbl, dcGeo) → ReferenceError in module strict mode.
   Fix: just use scene.remove(qm) directly.

2. White flash in ACTIVATION only lasts 300ms (t=7.5-7.8);
   spec wants ≈1s burst.

3. Operator right arm: no subtle typing animation in ORDER/IDLE.

4. Cluster map starts all dark gray ░; should be chaotic red/magenta.

5. F-key buttons (F4 VCF_Parse, F5 G-Code) not wired.

6. Camera auto-rotate in IDLE: controls.enabled=false in init,
   needs true for autoRotate to work.

7. No question-mark → symbol replacement; spec wants CSS metric
   symbols to replace ? objects, not just spawn alongside.

8. HUD sidebar breaks on ultrawide monitors (absolute positioning).
```

## 7. GIT STATE

```
Branch: feature/design_logo
Ahead of origin/feature/design_logo by 8 commits:
  21734eb docs: update handoff with dev panel status, Export Prod fix, next steps
  cc14ba3 fix: reorder Export Prod regex (buildDevGUI strip first)
  842d711 feat: add Export Prod button
  0607c4b add: visibility toggles for all object layers
  226dfce add: object dimensions scale controls + toast
  cc2e986 add: save/load/export presets + PC Desk position
  4362da2 fix: pause/resume, phase looping, key handlers
  793575b add: dev build with lil-gui panel
Remote push currently times out in bash tool (network issue).
```

## 8. DEV WORKFLOW

```
1. Start server: python -m http.server 8080
2. Open browser: http://localhost:8080/Demo_Threejs/systeq_v3_dev.html?dev
3. Edit systeq_v3_dev.html, refresh browser to see changes
4. Tweak params in lil-gui panel, use Save/Load for persistence
5. When ready: click 🎬 Export Prod → downloads systeq_v3_prod.html
6. Rename to systeq_v3.html to promote to production
```

## 9. EXPORT PROD — REGEX STRIPPING ORDER (critical!)

```js
.replace(/const params = \{[\s\S]*?\n    \};/, newParams)   // 1. Replace params with tweaked values
.replace(/    function buildDevGUI\(\) \{[\s\S]*?\n    }\n/, '') // 2. STRIP buildDevGUI FIRST!
.replace(/\n    import GUI from 'lil-gui';/, '')               // 3. Strip import
.replace(/\n    const IS_DEV = .*?;/, '')                      // 4. Strip IS_DEV
.replace(/\n[\t ]+if \(IS_DEV\) buildDevGUI\(\);?/, '')        // 5. Strip conditional call
.replace(/    function devToast[\s\S]*?\n    }\n/, '')          // 6. Strip devToast
.replace(/<div id="dev-toast"><\/div>\n/, '')                   // 7. Strip dev HTML
.replace(/<div id="dev-banner">.*?<\/div>\n/, '')               // 8. Strip banner HTML
.replace(/    #dev-toast[\s\S]*?show \{ opacity: 1; }\n/, '')  // 9. Strip dev CSS
```

> Step 2 MUST come before steps 3-9 to prevent false positive regex matches
> from the literal string patterns inside the Export Prod button code.

## 10. COLOR SYSTEM

```
EGA palette: 16 colors stored as hex integers (0xRRGGBB)
params colors: stored as hex integers, except colCubeChaos is array of 4 ints
lil-gui color pickers: use #RRGGBB string format via colorProxy object
Conversion: hex(n) = '#' + n.toString(16).padStart(6, '0')
```

## 11. HUD COMPONENTS

```
Top bar:     "╔════ SYSTEQ CAM-DEFRAG V3.3 ══╗" + live clock
Right sidebar:
  ├── Cluster map (64 monospace chars, 8×8 grid)
  │     Colors: 'red' (#ff5555), 'green' (#55ff55), 'magenta' (#ff55ff), 'darkgray' (#555555)
  ├── Legend (4 items: red=CHAOS, magenta=ACTIVATION, green=ORDER, gray=IDLE)
  ├── Diagnostics: ENTROPY, PROGRESS, STATUS (update per phase)
  ├── B2B Metrics: YIELD, TIME, SAVINGS ($198,420.00)
  └── Perf metrics: PANELS, DATA CUBES, RECONSTRUCT
Bottom bar:  F1-F10 key buttons (F2=Reset, F9=Help, F10=Exit=Reload)
```

## 12. CSS2D LABEL SYSTEM

```js
function makeCssLabel(txt, color) → CSS2DObject with .css-label class
  Used by: question marks (?), warning labels (! BAD DATA !), metric words, dollar labels
  Font: 'Press Start 2P', 16px, with text-shadow, transition on color/transform
```
