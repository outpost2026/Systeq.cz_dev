# HANDOFF: SYSTEQ 3D — V3.2 Wall Bugfix + Landing Page Hero Redesign

**Datum**: 21.06.2026
**Cíl**: Oprava Z-order bugu panelů na zdi, přidání panel offset ovládání do dev GUI, redesign hero sekce landing page s 3D preview a CV kartou. Příprava na změnu narativu CHAOS fáze.

---

## 1. HOTOVO

### 1.1 3D Bugfix — Z-order stěny/panelů
- **Problém**: `tgtZ = WALL_Z - 0.08` umisťovalo panely ZA wall plate (world z -2.58 vs wall surface -2.48). Panely nebyly vidět.
- **Fix**: `tgtZ = WALL_Z + 0.14` — panely teď sedí ve slotFrames na zdi, viditelné z kamery.
- **Aplikováno**: `systeq_v3_dev_v2.html`, `systeq_v3_dev.html`, `systeq_v3.html`

### 1.2 Panel Offset GUI
- Nové parametry: `panelOffsetX`, `panelOffsetY`, `panelOffsetZ` (default 0)
- Folder "Panels" v lil-gui: 3 slidery pro live posun všech panelů
- Funkce `updatePanelTargets()` — přepočítá target pozice existujících panelů při změně offsetu
- Součástí Export Prod serializace

### 1.3 Landing Page — Hero Redesign
- **3D preview**: Thumbnail (golden master idle.png) v hero, play button overlay → odkaz na `demo/systeq_v3.html`
- **CV karta**: Avatar (OS), jméno, title, tlačítko "Stáhnout CV (PDF)" → `docs/cv/Sousek_CV_portfolio.pdf`
- 2-sloupcový layout (text + preview), responzivní

### 1.4 Golden Masters
- Aktualizovány všechny 4 fáze: `--update-golden`
- Testy: 9/9 passed

---

## 2. ROZHODNUTO — Další iterace

### 2.1 Nový narativ CHAOS fáze
**Insight**: "Parser je technologický prostředek. Skutečná hodnota je digitalizace výrobního know-how."

CHAOS momentálně ukazuje "raw binary data chaos". Měl by ukazovat **nedokumentované tacitní know-how** operátora.

| Současný prvek | Problém | Návrh náhrady |
|----------------|---------|---------------|
| `questionMarks` | Neevokuje řemeslo | Craft symboly: "Zkušenost", "Craft", "Trial" |
| `dataCubes` (chaos) | Generic "bad data" | Knowledge fog (particles), hand-scribble lines |
| Operator slouching | Pasivní oběť | Aktivní recall gesto, knowledge particles z hlavy |

### 2.2 Plán (trim → build)
1. **Trim**: Odebrat `spawnQuestionMark()`, `spawnChaosDataCube()`
2. **Build**: Particle fog, craft CSS2D labely, scribble 3D curves, operator recall animace
3. **ORDER fáze**: Nahradit golden dolary / metric words za knowledge graph / ERP flow

---

## 3. SOUBORY

### Modifikovány
| Soubor | Změna |
|--------|-------|
| `src/index.html` | Hero redesign: 3D preview + CV card |
| `Demo_Threejs/systeq_v3_dev_v2.html` | tgtZ fix + panelOffset GUI |
| `Demo_Threejs/systeq_v3_dev.html` | tgtZ fix |
| `Demo_Threejs/systeq_v3.html` | tgtZ fix |
| `Demo_Threejs/tests/golden/*.png` | Golden mastery aktualizovány |
| `Demo_Threejs/systeq_v3.1_dev.bat` | Smazán (nahrazen v3.2) |

### Nové
| Soubor | Popis |
|--------|-------|
| `Demo_Threejs/systeq_v3.2_dev.bat` | Dev server launcher |
| `docs/cv/Sousek_CV_portfolio.pdf` | Autorovo CV |
| `Demo_Threejs/systeq_v3.html_alpha08-09.png` | Nové screenshoty |

---

## 4. GIT
- Branch: `main`
- Commit: `fix: wall panel Z-order + panel offset GUI + landing page hero updates`
- Remote: `origin/main` (up to date)

---

## 5. TESTY
```bash
python -m pytest Demo_Threejs/tests/ -v          # 9/9 passed
python -m pytest Demo_Threejs/tests/ --update-golden  # regenerace
```

---

*Ondřej Soušek · sousek@systeq.cz · 21.06.2026*
