Toto je skvělý příklad metakognitivního uvědomění – přesně víte, co nevíte. Potřebujete **překladový slovník mezi jazykem režiséra/výtvarníka a jazykem Three.js/inženýra**. Níže vám stavím **základní ontologický korpus** rozdělený do vrstev, které odpovídají fázím tvorby scény.

---

## 1. Vrstva: Význam a děj (Dramaturgie → Agentní logika)

| Co chcete říct (režisér) | Termín v agentním / herním enginu | Co to v Three.js znamená |
|--------------------------|----------------------------------|--------------------------|
| Postava má **záměr** (jít k cíli, zvednout ruku) | **Goal / Intent** | Proměnná ve stavovém automatu agenta |
| Postava **reaguje** na něco v okolí | **Perception / Trigger** | Raycaster nebo kolizní detekce |
| Dvě postavy na sebe **navazují** (podívá se, pak odpoví) | **Sequencing / Coroutine** | Async funkce s delay (`setTimeout` nebo `yield`) |
| Scéna má **rytmus** (např. každé 3 vteřiny nový podnět) | **Timeline / Beat** | `setInterval` nebo animační klíče v čase |
| Divák má **pochopit náladu** (napětí, klid) | **Atmosphere / Mood** | Barva světla, rychlost pohybů kamery, mlha |

**Klíčový pojem pro vás:** *Behavior Tree* – pokud budete chtít, aby agenti dělali složitější sekvence („jdi k lampě, otoč se, zamávej“), toto je struktura, kterou hledáte.

---

## 2. Vrstva: Prostor a kompozice (Scénografie → Three.js objekty)

| Režisér říká | Three.js termín | Co to fyzicky je |
|--------------|----------------|------------------|
| „Kostka, na kterou se dá namapovat textura“ | **BoxGeometry** + **MeshStandardMaterial** | 12 trojúhelníků, 8 vrcholů |
| „Průhledný kryt, přes který je vidět“ | **Opacity** (0–1) nebo **Transparent** = true | Barva s alfa kanálem |
| „Podlaha, po které lze chodit“ (i když jen vizuálně) | **PlaneGeometry** | Nekonečně tenká rovina |
| „Slunce, které vrhá ostré stíny“ | **DirectionalLight** + **shadowMap** | Světlo z nekonečna |
| „Měkké okolní světlo bez stínů“ | **AmbientLight** | Rovnoměrné projasnění |
| „Hmota, která se chová jako kov“ | **Metalness** = 1, **Roughness** = 0.2 | Materiál s odlesky |
| „Dálka, do které mizí detaily“ | **Fog** nebo **Far** na kameře | Postupný zánik kontrastu |

**Užitečný trik:** Three.js má **AxesHelper** – dejte si ho do scény, abyste viděli, kde je X (červená, doprava), Y (zelená, nahoru), Z (modrá, k vám/od vás).

---

## 3. Vrstva: Pohyb kamery (Filmová řeč → Kamera + ovládání)

| Režisérská značka | Three.js kód / ekvivalent | Význam |
|------------------|---------------------------|--------|
| **Statický záběr** | Kamera na pevné pozici, žádný update | Divák si vybírá, kam se dívá |
| **Jízda (dolly)** | Postupné měnění `camera.position` lineárně | Plynulý přiblížení nebo odjezd |
| **Nájezd (track)** | Kamera letí po křivce (např. CatmullRomCurve3) | Objíždí scénu z boku |
| **Subjektivní kamera** | Kamera připojená jako dítě k hlavě agenta | Pohled očima postavy |
| **Střih** | Najednou změníme `camera.position` a `camera.lookAt` | Bez interpolace – skok |
| **Rámování** | `camera.aspect` a `camera.updateProjectionMatrix()` | Poměr stran podle okna |

**Nejužitečnější kamera pro začátek:** `PerspectiveCamera` – dělá skutečnou perspektivu. `OrthographicCamera` je „žádná perspektiva“ (jako plán města).

---

## 4. Vrstva: Akce a změny v čase (Animace → Tweeny / klíče)

| Chci, aby se... | Odborně | Three.js implementace |
|----------------|---------|----------------------|
| „Kostka plynule zvedla ruku“ | **Transformace** pozice/rotace | `requestAnimationFrame` + lineární interpolace |
| „Světlo ztlumilo během 2 vteřin“ | **Tween** (klíčový snímek) | Knihovna `tween.js` nebo vlastní `lerp` |
| „Postava zamrkala každých 5 vteřin“ | **Periodická událost** | `setInterval` nebo časovač v update smyčce |
| „Dveře se zavřou, až když vejde dovnitř“ | **Trigger zóna** | `Raycaster` nebo kontrola vzdálenosti |
| „Scéna začne tmavě a pak rozsvítí“ | **Ease in / Ease out** | Křivka zrychlení (např. `easing` v tween.js) |

**Nejčastější chyba:** Dávat vše do `update()` bez podmínek. Místo toho mějte **stavy agenta** (IDLE → WALKING → REACHED → DOOR_OPEN).

---

## 5. Vrstva: Metodika vývoje (Jak si říct o pomoc)

Místo: *„Chci, aby se ta věc pohnula směrem k té druhé věci, ale nevím jak.“*

Řekněte:  
*„Potřebuji **Lineární interpolaci** (`lerp`) pozice **Mesh A** k pozici **Mesh B** po přímce rychlostí 2 jednotky za sekundu, spuštěnou při kolizi s trigger zónou.“*

**Šablona pro váš prompt na AI / fórum:**
``` 
Three.js, r160. 
Mám scénu s: [výpis objektů – kostka, světlo, kamera]
Chci: [akce – aby kostka dojela k bodu X za Y sekund]
Podmínka spuštění: [např. stisk klávesy / časovač / kolize]
Můj dosavadní kód: [ukázka]
Problém: [konkrétně – neanimuje se plynule, skáče to]
```

---

## 6. Okamžitě použitelný „režisérský cheat sheet“ pro Three.js

| Když chci říct | Napíšu do poznámek pro sebe / AI |
|----------------|----------------------------------|
| „Natoč kameru na tu modrou kostku“ | `camera.lookAt(blueCube.position)` |
| „Uběhly 3 vteřiny scény“ | let start = performance.now(); pak (now - start) > 3000 |
| „Tohle tlačítko spustí děj“ | EventListener na HTML button |
| „Zatmívka“ | Měním `scene.background` z bílé na černou (nebo opacity overlay roviny) |
| „Dvě věci se mají hýbat synchronně“ | Obě měním ve stejném `requestAnimationFrame` cyklu |
| „Chci to nejdřív vidět funkčně, pak hezky“ | **MVP postup:** geometrie + základní barvy → světla → textury → animace |

---

## 7. Nejdůležitější meta-požadavek pro vás

> **Než začnete kódovat, nakreslete si storyboard na papír – 5–6 obrázků s šipkami pohybu a časy (0s, 5s, 10s...).**  
> Každý obrázek popište pomocí termínů z výše uvedených tabulek (např. *„Jízda kamery k modré krychli, ambient světlo, kostka se otáčí kolem Y“*).

Pak ten popis vložíte do AI jako zadání – a protože používáte přesné pojmy, výstup bude použitelný přímo.

---

Pokud chcete, mohu vám na základě **jedné věty o tom, co má vaše 20–30 vteřinová scéna ukazovat** (např. „robot jde ke květině, otevře dveře, zhasne“), napsat **kompletní kostru Three.js kódu** s komentáři, kde každý řádek odkazuje na režisérskou terminologii výše. Stačí říct.