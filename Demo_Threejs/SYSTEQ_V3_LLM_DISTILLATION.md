# SYSTEQ V3 — LLM Context Distillation

Single-file Three.js r160 scene (ES module, importmap). 4-phase entropy-to-order narrative for B2B CNC CAM demo.

## Tech Stack
- Three.js r160, CSS2DRenderer, OrbitControls, MeshStandardMaterial (flatShading)
- EGA-16 palette (0x000000–0xFFFFFF, 16 named colors)
- No build step, deploy via FTP, dev mode via `?dev` URL param + lil-gui
- Golden-master tests: Playwright + Pillow (pytest)

## Scene Structure (all at module scope)

```
scene (black bg + grid floor 12x24)
├── wallGroup      (params.wallY, WALL_Z=-2.5)   — defrag wall, 12 panel slots (3×4)
├── pcGroup        (params.pcX, GND, params.pcZ) — desk, tower, monitor, keyboard
├── operatorGroup  (params.opX, GND+opY, params.opZ) — 13-part character
├── plotterGroup   (params.plotterX, GND+plotterY, plotterZ) — CNC bed, gantry, spindle
├── portalGroup    (params.portalX, portalY, portalZ) — dual torus + vortex + shockwave
└── panels[]       — spawned on wall, 12 max, lerp from chaos→target position
```

## Operator Parts (all module-scoped variables)

| Variable | Type | Animations |
|----------|------|------------|
| opBody | Box (blue) | `scale.y` = breathing (sin) |
| opHead | Group | `rotation` = look-at target (yaw/pitch) |
| opEyeL/R | Box (black) | `scale.y` = blinking |
| opSmileMesh | Box (red) | `scale` + `opacity` = smile intensity |
| opLeftArm | Group | `rotation` per phase (mug-holding) |
| opRightArm | Group | `rotation` per phase |
| opLegL/R | Cyl (dkgray) | static |
| opFootL/R | Box (black) | static |
| opForearmL/R | Box (skin) | child of arm groups |

## Phase Timeline & Narrative

| Phase | simTime | Entropy | Status | Key Actions |
|-------|---------|---------|--------|-------------|
| CHAOS | 0–7s | 94.2%→max | UNSTRUCTURED_DATA | Gantry wobble, chaos cubes, ? marks, panels launch |
| ACTIVATION | 7–10s | 94→0% | DECODING_VCF... | White flash, shockwave, filaments portal→panel |
| ORDER | 10–22s | 64→6% | DETERMINISTIC_GCODE | Panels mount (easeOutBack), operator smiles, green laser |
| IDLE | 22s+ | 6.1% | PARSED ENGINE V3 - IDLE | AutoRotate camera, 99% yield, 137260 CZK saved |

## Params Object (37 keys — configures everything)

Camera: `camDist, camHeight, camTargetY`
Phase timing: `chaosEnd(7), activationEnd(10), orderEnd(22)`
Positions: `op{X,Y,Z}, plotter{X,Y,Z}, portal{X,Y,Z}, pc{X,Z}, wallY`
Scales: `wallScale, pcScale, opScale, plotterScale, portalScale`
Visibility: `show{Wall,PC,Operator,Plotter,Portal,Panels}`
Colors: `colCubeChaos[4], colCubeOrder, colPanelDefect/Ok/Healed`
Spawn rates: `cubeRate, qmRate, dollarRate, metricRate`
Panel anim: `panelLaunchSpeed, panelGridStep`
Operator emotion: `opBlinkRate(4), opBreathRate(4.5), opBreathAmp(0.02), opSlouchAngle(0.28), opBounceAmp(0.06), opSmileIntensity(1), opHeadTrackStrength(0.8)`
Control: `speed, paused, forceMounted, loopMode(none|full)`

## Spawners (all time-based, Math.random-driven)

| Spawner | Phase | Rate Param | Lifetime | Visual |
|---------|-------|------------|----------|--------|
| dataCubes[] | CHAOS | cubeRate | progress→1.0 | Colored boxes, lerp src→tgt + sin wiggle |
| dataCubes[] | ORDER | cubeRate×1.5 | same | Green boxes, go to portal |
| questionMarks[] | CHAOS | qmRate | 1.2s | CSS2D "?", float up+fade |
| goldenDollars[] | ORDER | dollarRate | 2.0s | Torus+cyl group, float+fade |
| floatingTexts[] | ORDER | metricRate | 2.2s | CSS2D metrics words |
| panels[] | CHAOS | fixed schedule | permanent | 12 panels, launch→chaos→grid-mount |

## HUD (CSS2D + DOM overlays)

- Top bar: Title + live clock
- Right sidebar: Cluster map (8×8 sectors, char-based), entropy, progress bar, status, yield, savings
- Bottom bar: F-key buttons (F2=Restart via triggerManualReset)
- CRT overlay: scanline gradient + flicker animation
- White flash overlay during ACTIVATION transition

## Cluster Map (non-deterministic)

64 sectors, colors change every `floor(t*3)` seconds:
- CHAOS: random red/magenta
- ACTIVATION: random cyan/magenta  
- ORDER: thresholdIndex = ratio × 64, green up to index, red/magenta after
- IDLE: all green

## Export Prod (buildDevGUI → stripped regex pipeline)

1. Normalize CRLF→LF
2. Replace params block with live values as template literal
3. Strip buildDevGUI function (anchored on @DEV_BUILDDEVGUI_END marker)
4. Strip lil-gui import, IS_DEV var, buildDevGUI call
5. Strip dev-toast/banner divs and CSS
6. Downloads as `systeq_v3_prod.html`

## Dev Test Hooks (behind `if (IS_DEV)`)

```js
window.__devSimTime()           → simTime
window.__devSetSimTime(v)       → set simTime + reset lastFrameT
window.__devPhase()             → getPhase(simTime)
window.__devSpeed.set(v)        → params.speed = v
window.__devPause.set(v)        → params.paused = v
window.__devResetAnimPhases()   → zero breath/blink/lookTarget/autoRotate
window.__devParts()             → {opBody, opHead, opLeftArm, ...}
```

Used by golden-master tests for deterministic simTime jumps.

## Golden Master Tests (pytest + Playwright)

```
Demo_Threejs/tests/
├── conftest.py     — server, browser, page fixtures, compare_or_update()
├── test_visual.py  — 9 tests: 4 phases × GM + sanity + operator parts
└── golden/*.png    — GM screenshots (CHAOS, ACTIVATION, ORDER, IDLE)
```

Usage: `python -m pytest Demo_Threejs/tests/ -v`
Update: `--update-golden` flag

## Key Technical Decisions

1. Single-file HTML — no build step, FTP deploy, dev via `?dev`
2. CRLF→LF normalization in export (Windows deployment)
3. 1 device pixel ratio — retro aesthetic, matches EGA pixel grid
4. All 3D helpers as module-level vars — enables per-frame animation
5. `requestAnimationFrame` with `simTime` — deterministic playback independent of frame rate
6. `lastFrameT` dual-write pattern: update inside `!paused` AND at loop reset
7. CSS2DRenderer for text (no texture atlas, no canvas text rendering)

## Known Non-Determinism

- Cluster map colors (Math.random per time interval)
- Spawn timings (Math.random rate checks per frame)
- Data cube spawn positions (Math.random wiggle offset)
- These are tolerated by 3% pixel-diff threshold in GM tests
