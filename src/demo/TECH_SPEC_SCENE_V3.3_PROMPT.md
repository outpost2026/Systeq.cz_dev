# SYSTEQ — Three.js Animace – Kompletní narativní prompt

## Kontext
Projekt **B2B parseru proprietárních CAM dat** (formát `*.vcf`).
Parser rozbije „černou skříňku“ neznámých hex‑binárek a vygeneruje čistá, strukturovaná data.  
Účelem animace je **vizuálně demonstrovat proces defragmentace a transformace dat** – od chaosu k řádu – v retro 8‑bitovém stylu (EGA‑16 paleta, CRT scan‑lines, font *Press Start 2P*).  
Scéna připomíná **Norton Commander / MS‑DOS** rozhraní z 90. let a symbolizuje **diskovou defragmentaci**.

---

## Hlavní příběh (co se má během 25 s stát)
1. **Úvod – Chaos (0‑7 s)**
   - **Nestrukturovaná, entropická data** proudí z PC do CNC plotru jako barevné částice.
   - Plotr **vypouští desky (panely)** každou sekundu: leží na vlně, dopadají na **zeď** přímo čelem k zobrazovači.  
   - Desky jsou **fragmentované** – rozptýlené po celé stěně, náhodně zarovnané v matici; **každá třetí panel** je poškozený (červený rámeček, varovná ikona).
   - **Symboly otazníků** (`?`) stříká z plotru každou sekundu – představují neznámé položky.
   - **Operátor** u PC je smutný: drsný ryšavý plnovous, červená kšiltovka, káva v ruce (levá ruka).  
   - Scéna je zahalena **černou, entropickou atmosférou** (CRT overlay, temné pozadí).

2. **Magický zásah – Parser (≈ 7‑8 s)**
   - Velký **záblesk světla** osvítí celou scénu – **symbolické zjevení parseru** (zvolte jednoduchý glowing orb nebo stylizovaný „magický klíč”).
   - **Energetické pole** (modré/neonové pulzující šípy) obklopí všechny objekty během jedné sekundy, poté utiší.
   - Po zásahu se data **čistí** – částice mění barvu na **čisté zelené** a proudí rychlejší.

3. **Transformace – Defragmentace (10‑25 s)**
   - **Plotr** přestává házet náhodné desky; místo toho **precizně ukládá panely** na zeď v **řadách a sloupcích** (jako soubory na disku).  
   - Už ve **prvních několika vteřinách** se fragmentované panely na stěně **automaticky zarovnají** do řádného gridu (použijte animaci eas‑out‑back).
   - **Otazníky** mizí a jsou nahrazeny **symbolickými objekty** (např. 3‑D kódy s textem):
     * "ČAS ZPRACOVÁNÍ"
     * "MARŽE"
     * "ERP"
     * "DOLLAR" – zlatý dolarový symbol
     * další klíčová slova (např. "AGREGOVANÁ ZAKÁZKA", "NEDOTAŽENÁ KŘIVKA").
   - **Operátor** se zvedne, odečte si horkou kávu a **usměje se** – jeho postoj se postupně přechodně narovná (rotace X → 0) a začne mírně vibrovat (sinus ≈ 0.05 rad).
   - **HUD** (retro DOS bar) zobrazuje **entropy %** (klesá z ~94 % na ~6 % během 25 s), **status** ("UNSTRUCTURED DATA" → "DECODING" → "DETERMINISTIC OUTPUT") a **metrics** (čas, počet panelů, úspora).

4. **Idle (po ~25 s)**
   - Scéna přejde do **stabilního idle‑stavu** – panelky jsou uspořádány, světla tlumená, operátor dívá se na monitor.
   - **Loop** pokračuje pouze v jemném otáčení kamery (auto‑rotate) a drobném pulzování HUDu.

---

## Požadavky na vizuální úroveň (co refaktorovat)
- **Mesh** – každý objekt má **detailnější geometrii** (např. ozubené ozdobné rámy, více segmentů torusu, jemné zakřivení panelů).  
- **Portál** – místo jednoduchého toroidu použij kombinaci **dvou torusů + pulsující vortex + světelné line‑segments** (opakující se malé jasné částice kolem).  
- **Status bar & HUD** – stylizovaný jako **Norton Commander**: box‑drawing okraje, 8‑bitové písmo, paleta zelených/žlutých charakterních znaků.  
- **Defragmentace** – animace „přesunu“ panelů využívá **easing (ease‑out‑back)**, aby se zdálo, že se panel „vysouvá“ ze stěny a „zakládá“ na správnou pozici.  
- **Čistá data** – po zásahu parseru změň materiály na **flat‑shaded, vysoký kontrast, čistá zelená (EGA GREEN)** a **emissive** (pro text).  
- **Energetické pole** – krátkodobá **vlákna (LineBasicMaterial, neon‑modrá)**, rotující kolem středového bodu.  
- **CRT overlay** – nechte **scan‑line** s 0.08 α černým pruhem, aby zachoval retro pocit.
- **High SNR** – zajistěte, aby vše bylo **čitelné** i při malých rozlišeních (pixel‑art, `NearestFilter` na texturách, `setPixelRatio(1)`).

---

## Technické specifikace (co LLM potřebuje při generování kódu)
- **Three.js r160** – CDN import (`three@0.160.0`).
- **Moduly** – `OrbitControls` (z `three/addons/controls/OrbitControls.js`).
- **Renderer** – `antialias:false`, `pixelRatio:1`, `size:innerWidth/innerHeight`.
- **EGA 16‑color palette** – definována jako konstantní objekt `EGA` (viz níže).  
- **Helper funkce** – `box(w,h,d)`, `cyl(r,h)`, `tor(R,r)`, `mF(color)`, `M(geo,color)`.  
- **Scene setup** – podlaha, grid‑helper, ambient + directional + point světla (EGA DKGRAY, LTGRAY, YELLOW).  
- **Resize listener** – udržuje poměr stran.
- **Animation loop** – `requestAnimationFrame(animate)`, `delta = Math.min((now-last)/1000,0.05)`.  
- **Phase handling** – funkce `getPhase(t)` vrací `CHAOS`, `ACTIVATION`, `ORDER`, `IDLE` podle časových mezí.  
- **Export** – finální HTML musí být **jedním souborem** (`<script type="module">…</script>`), **< 100 KB** a **spustitelný v moderním prohlížeči**.

---

## Shrnutí promptu (pro LLM)
> *„Vytvoř kompletní HTML (single‑file) s Three.js r160, který během 25 s přehraje animaci popisující transformaci chaotických, entropických dat z PC do CNC plotru na retro 8‑bitovou scénu. Scéna má styl 90‑letého diskového defragmentátoru (Norton Commander), používá EGA‑16 paletu, CRT scan‑lines a font *Press Start 2P*. Na začátku jsou fragmentované, barevné desky a otazníky, po 7‑8 s se objeví magický parser (zářivý orb) a energetické pole, následně se data čistí, panelky se zarovnají do gridu a otazníky se změní na čitelné symboly ("ČAS ZPRACOVÁNÍ", „MARŽE“, …). Operátor má plnovous, červenou kšiltovku a kávu, po zásahu se usměje a mírně vibruje. HUD vypadá jako DOS‑bar s entropií a statusem. Implementuj detailnější geometrie, pulzující portál, eas‑out‑back animaci panelů a vysoký kontrast (high SNR). Soubor má být menší než 100 KB a běžet bez build‑step.“*

---

## Minimalistický kódový “skeleton” (vzor, který LLM může rozvinout)
```html
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><title>SYSTEQ Lite</title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <style>
    body{margin:0;background:#000;overflow:hidden;font-family:'Press Start 2P',monospace;}
    canvas{display:block;}
    #crt{position:fixed;inset:0;pointer-events:none;z-index:999;
        background:repeating-linear-gradient(0deg,rgba(0,0,0,0.08)0px,transparent1px,transparent3px);
    }
  </style>
</head>
<body>
  <div id="crt"></div>
  <script type="importmap">{ "imports":{ "three":"https://unpkg.com/three@0.160.0/build/three.module.js", "three/addons/":"https://unpkg.com/three@0.160.0/examples/jsm/" } }</script>
  <script type="module">
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

    // ==== CONSTANTS ====================================================
    const GND=-0.55, DSK_Y=0.22, WALL_Z=3.0, WALL_W=5.5, WALL_H=3.2, WALL_Y=0.5;
    const PHASE={ CHAOS:0, ACTIVATION:8, ORDER:10, IDLE:22 };
    const EGA={BLACK:0x000000, BLUE:0x0000AA, GREEN:0x00AA00, CYAN:0x00AAAA,
               RED:0xAA0000, MAGENTA:0xAA00AA, BROWN:0xAA5500, LTGRAY:0xAAAAAA,
               DKGRAY:0x555555, LTBLUE:0x5555FF, LTGREEN:0x55FF55, LTCYAN:0x55FFFF,
               LTRED:0xFF5555, LTMAGENTA:0xFF55FF, YELLOW:0xFFFF55, WHITE:0xFFFFFF};

    // ==== HELPERS ======================================================
    const box=(w,h,d)=>new THREE.BoxGeometry(w,h,d);
    const mF=c=>new THREE.MeshStandardMaterial({color:c,flatShading:true,roughness:0.85});
    const M=(g,c)=>new THREE.Mesh(g,mF(c));

    // ==== SCENE ========================================================
    let scene,camera,renderer,controls,startT;
    function init(){
      scene=new THREE.Scene();scene.background=new THREE.Color(EGA.BLACK);
      camera=new THREE.PerspectiveCamera(45,innerWidth/innerHeight,0.1,40);
      camera.position.set(0,1.5,7);
      renderer=new THREE.WebGLRenderer({antialias:false});renderer.setPixelRatio(1);renderer.setSize(innerWidth,innerHeight);document.body.appendChild(renderer.domElement);
      controls=new OrbitControls(camera,renderer.domElement);
      controls.target.set(0,0.3,1.2);controls.enableDamping=true;controls.autoRotate=true;controls.autoRotateSpeed=0.2;controls.enablePan=false;controls.enabled=false;
      // lights
      scene.add(new THREE.AmbientLight(EGA.DKGRAY));
      const dir=new THREE.DirectionalLight(EGA.LTGRAY,0.8);dir.position.set(2,6,4);scene.add(dir);
      // floor & grid
      const floor=M(box(8,0.02,3),EGA.BLACK);floor.rotation.x=-Math.PI/2;floor.position.y=GND-0.01;scene.add(floor);
      const grid=new THREE.GridHelper(8,16,EGA.DKGRAY,EGA.BLACK);grid.position.y=GND;scene.add(grid);
      // objects (wall, PC, operator, plotter, portal) – placeholders, LLM rozvine dál
      createWall();createPC();createOperator();createPlotter();createPortal();
      window.addEventListener('resize',()=>{camera.aspect=innerWidth/innerHeight;camera.updateProjectionMatrix();renderer.setSize(innerWidth,innerHeight);});
      startT=performance.now();
      animate();
    }
    // ---- placeholder functions (LLM rozšíří) -----------------------
    function createWall(){ /* detailní rám, textura, panelky */ }
    function createPC(){   /* stůl, monitor, klávesnice */ }
    function createOperator(){ /* postava s vousy, kšiltovkou, kávou */ }
    function createPlotter(){ /* CNC stůl, gantry, spindle */ }
    function createPortal(){ /* duální torus + vortex + line‑segments */ }

    // ==== ANIMATION ====================================================
    function getPhase(t){
      if(t<PHASE.ACTIVATION) return 'CHAOS';
      if(t<PHASE.ORDER) return 'ACTIVATION';
      if(t<PHASE.IDLE) return 'ORDER';
      return 'IDLE';
    }
    function animate(){
      requestAnimationFrame(animate);
      const now=performance.now();
      const t=(now-startT)/1000; // seconds
      const phase=getPhase(t);
      // –‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑‑
      // LLM doplní animace pro: data‑flow, panel‑defrag, portal‑pulse, operator‑reactions, HUD update
      // -------------------------------------------------------------------
      controls.update();
      renderer.render(scene,camera);
    }
    init();
  </script>
</body>
</html>
```

---

**Finální instrukce pro LLM:** Vezmi výše popsaný narativ, technické požadavky a skeleton a **generuj kompletní, funkční HTML** (single‑file) s detailními funkcemi, mesh‑geometriemi a animacemi dle popisu. Dodrž **EGA‑palette**, **high SNR** a zachovej velikost < 100 KB.

---

*Soubor připraven k vložení do LLM promptu, poskytuje kompletní kontext, výtvarný směr a technické detaily.*