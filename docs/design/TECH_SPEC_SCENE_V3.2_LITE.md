# SYSTEQ THREE.JS — ZJEDNODUŠENÁ SPECIFIKACE (v3.2 LITE)

**Cíl:** Monolitický HTML soubor (`systeq_lite.html`) s Three.js.  
**Délka animace:** 25 sekund, poté idle.  
**Styl:** Retro pixelart (EGA paleta, CRT scanlines, `Press Start 2P` font).  
**Vtip:** Operátor s kšiltovkou a kávou, který reaguje na „defektní" data.

---

## 1. ZÁKLADNÍ STRUKTURA

```html
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>SYSTEQ LITE</title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <style>
    body { margin: 0; background: #000; overflow: hidden; font-family: 'Press Start 2P', monospace; }
    canvas { display: block; }
    #crt { position: fixed; inset: 0; pointer-events: none; z-index: 999;
           background: repeating-linear-gradient(0deg, rgba(0,0,0,0.08) 0px, transparent 1px, transparent 3px); }
  </style>
</head>
<body>
  <div id="crt"></div>
  <script type="importmap">
    { "imports": {
      "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
      "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
    }}
  </script>
  <script type="module">
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

    // === KONSTANTY ===
    const GND = -0.55;
    const DSK_Y = 0.22;
    const WALL_Z = 3.0;
    const WALL_W = 5.5;
    const WALL_H = 3.2;
    const WALL_Y = 0.5;

    // === EGA PALETA ===
    const EGA = {
      BLACK: 0x000000, BLUE: 0x0000AA, GREEN: 0x00AA00, CYAN: 0x00AAAA,
      RED: 0xAA0000, MAGENTA: 0xAA00AA, BROWN: 0xAA5500, LTGRAY: 0xAAAAAA,
      DKGRAY: 0x555555, LTBLUE: 0x5555FF, LTGREEN: 0x55FF55, LTCYAN: 0x55FFFF,
      LTRED: 0xFF5555, LTMAGENTA: 0xFF55FF, YELLOW: 0xFFFF55, WHITE: 0xFFFFFF
    };

    // === POMOCNÉ FUNKCE ===
    const box = (w, h, d) => new THREE.BoxGeometry(w, h, d);
    const mF = (c) => new THREE.MeshStandardMaterial({ color: c, flatShading: true, roughness: 0.85 });
    const M = (g, c) => new THREE.Mesh(g, mF(c));

    // === SCÉNA ===
    let scene, camera, renderer, controls;
    let operator, monitor, plotter, portal;

    function init() {
      scene = new THREE.Scene();
      scene.background = new THREE.Color(EGA.BLACK);
      scene.fog = new THREE.Fog(EGA.BLACK, 15, 35);

      camera = new THREE.PerspectiveCamera(45, innerWidth / innerHeight, 0.1, 40);
      camera.position.set(0, 1.5, 7);

      renderer = new THREE.WebGLRenderer({ antialias: false });
      renderer.setPixelRatio(1);
      renderer.setSize(innerWidth, innerHeight);
      document.body.appendChild(renderer.domElement);

      controls = new OrbitControls(camera, renderer.domElement);
      controls.target.set(0, 0.3, 1.2);
      controls.enableDamping = true;
      controls.autoRotate = true;
      controls.autoRotateSpeed = 0.2;
      controls.enablePan = false;
      controls.enabled = false;

      // Světla
      scene.add(new THREE.AmbientLight(EGA.DKGRAY));
      const dir = new THREE.DirectionalLight(EGA.LTGRAY, 0.8);
      dir.position.set(2, 6, 4);
      scene.add(dir);

      // Podlaha
      const floor = M(box(8, 0.02, 3), EGA.BLACK);
      floor.rotation.x = -Math.PI / 2;
      floor.position.y = GND - 0.01;
      scene.add(floor);

      // Grid
      const grid = new THREE.GridHelper(8, 16, EGA.DKGRAY, EGA.BLACK);
      grid.position.y = GND;
      scene.add(grid);

      createWall();
      createPC();
      createOperator();
      createPlotter();
      createPortal();

      window.addEventListener('resize', () => {
        camera.aspect = innerWidth / innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(innerWidth, innerHeight);
      });

      animate();
    }

    // === ZEĎ ===
    function createWall() {
      const group = new THREE.Group();
      group.position.set(0, WALL_Y, WALL_Z);

      // Rám
      const frame = M(box(WALL_W + 0.12, WALL_H + 0.12, 0.08), EGA.LTGRAY);
      frame.position.z = -0.04;
      group.add(frame);

      // Pozadí
      const bg = M(box(WALL_W, WALL_H, 0.02), 0x0F172A);
      group.add(bg);

      scene.add(group);
    }

    // === PC ===
    function createPC() {
      const group = new THREE.Group();
      group.position.set(-3, 0, 0.5);

      // Stůl
      const table = M(box(0.95, 0.06, 0.65), EGA.DKGRAY);
      table.position.y = DSK_Y + 0.02;
      group.add(table);

      // Nohy
      for (let x of [-0.4, 0.4]) {
        for (let z of [-0.28, 0.28]) {
          const leg = M(new THREE.CylinderGeometry(0.04, 0.04, 0.55, 8), EGA.BLUE);
          leg.position.set(x, DSK_Y - 0.28, z);
          group.add(leg);
        }
      }

      // Monitor
      monitor = M(box(0.55, 0.38, 0.03), EGA.GREEN);
      monitor.position.set(0, DSK_Y + 0.32, 0.10);
      group.add(monitor);

      // Klávesnice
      const kb = M(box(0.40, 0.03, 0.15), EGA.DKGRAY);
      kb.position.set(0, DSK_Y + 0.035, 0.32);
      kb.rotation.x = -0.15;
      group.add(kb);

      scene.add(group);
    }

    // === OPERÁTOR ===
    function createOperator() {
      operator = new THREE.Group();
      operator.position.set(0, 0, 1.1);

      // Tělo
      const body = M(box(0.52, 0.42, 0.28), EGA.BLUE);
      body.position.y = 0.5;
      operator.add(body);

      // Hlava
      const head = M(box(0.40, 0.40, 0.40), 0xF5D0A9);
      head.position.y = 1.0;
      operator.add(head);

      // Kšiltovka
      const cap = M(box(0.44, 0.08, 0.34), 0xD35400);
      cap.position.set(0, 1.25, 0);
      operator.add(cap);

      // Vous
      const beard = M(new THREE.ConeGeometry(0.14, 0.09, 6), 0x92400E);
      beard.position.set(0, 0.85, 0.20);
      operator.add(beard);

      // Hrnek
      const mug = M(new THREE.CylinderGeometry(0.09, 0.09, 0.16, 8), 0x78350F);
      mug.position.set(0.3, 0.6, 0.2);
      operator.add(mug);

      scene.add(operator);
    }

    // === PLOTR ===
    function createPlotter() {
      const group = new THREE.Group();
      group.position.set(0, 0, 0);

      // Stůl
      const table = M(box(2.5, 0.06, 1.2), EGA.CYAN);
      table.position.y = DSK_Y + 0.03;
      group.add(table);

      // Gantry
      plotter = M(box(0.12, 0.16, 1.32), EGA.LTBLUE);
      plotter.position.y = DSK_Y + 0.11;
      group.add(plotter);

      // Spindle
      const spindle = M(box(0.20, 0.14, 0.20), EGA.DKGRAY);
      spindle.position.y = DSK_Y + 0.18;
      group.add(spindle);

      scene.add(group);
    }

    // === PORTÁL ===
    function createPortal() {
      portal = new THREE.Group();
      portal.position.set(-2, 0.4, 0.3);
      portal.visible = false;

      // Vnější torus
      const outer = new THREE.Mesh(
        new THREE.TorusGeometry(0.35, 0.07, 16, 32),
        new THREE.MeshStandardMaterial({
          color: 0xFEF3C7, emissive: 0xFEF3C7, emissiveIntensity: 0.35,
          flatShading: true, roughness: 0.7
        })
      );
      outer.rotation.x = Math.PI / 2;
      portal.add(outer);

      // Vnitřní torus
      const inner = new THREE.Mesh(
        new THREE.TorusGeometry(0.22, 0.05, 12, 24),
        new THREE.MeshStandardMaterial({
          color: 0xFDE047, emissive: 0xFDE047, emissiveIntensity: 0.5,
          flatShading: true, roughness: 0.7
        })
      );
      inner.rotation.x = Math.PI / 2;
      portal.add(inner);

      scene.add(portal);
    }

    // === ANIMACE ===
    let startT = performance.now();

    function animate() {
      requestAnimationFrame(animate);
      const t = (performance.now() - startT) / 1000;

      // Fáze
      if (t > 8 && t < 10) {
        portal.visible = true;
        portal.scale.setScalar(0.3 + (t - 8) * 0.35);
        portal.children[0].rotation.y += 0.025;
        portal.children[1].rotation.y -= 0.035;
      } else if (t >= 10) {
        portal.visible = true;
        portal.scale.setScalar(1);
        portal.children[0].rotation.y += 0.005;
        portal.children[1].rotation.y -= 0.007;
      }

      // Pohyb plotru
      if (t >= 10) {
        plotter.position.x = Math.sin(t * 0.5) * 0.2;
      }

      // Operátor
      if (t > 7 && t < 9) {
        operator.rotation.x = 0.35 * (1 - (t - 7) / 2);
      } else if (t >= 9) {
        operator.rotation.x = -0.2 + Math.sin(t * 0.3) * 0.05;
      }

      controls.update();
      renderer.render(scene, camera);
    }

    init();
  </script>
</body>
</html>
```

---

## 2. KLÍČOVÉ PRVKY

- **EGA paleta:** Všechny barvy musí být z 16 barev EGA.
- **Pixelart:** `flatShading: true`, `NearestFilter` na texturách, `setPixelRatio(1)`.
- **CRT overlay:** Scanlines přes celou obrazovku.
- **Font:** `Press Start 2P` pro HUD a labely.
- **Animace:** Portál se objeví v 8s, plotr začne jet v 10s, operátor se narovná.

---

## 3. ROZŠÍŘENÍ (volitelné)

- Přidat HUD s entropií a statusem.
- Přidat částice (data flow) z PC do plotru přes portál.
- Přidat defektní panely na zeď (červené, s varováním).
- Přidat dolary vyskakující z plotru.

---

*Hotovo. Soubor by měl mít < 100 KB a běžet 25 sekund.*