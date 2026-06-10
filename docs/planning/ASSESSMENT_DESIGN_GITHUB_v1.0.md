# ASSESSMENT: Design koncepce & GitHub integrace

---

## ČÁST 1: POSOUZENÍ DESIGN KONCEPCE

**Hodnocený artefakt:** `SYSTEQ_v1.0_BETA_Graficka_esteticka_vrstva.md`

### 1.1. Celkový verdikt

**Koncepce je validní a vhodná pro projekt systeq.cz.** Design je promyšlený, vychází z reálných architektonických a typografických principů (Tadao Ando, Dieter Rams, Müller-Brockmann). Není to "kolekce hezkých barviček" — má vnitřní logiku.

### 1.2. Hodnocení dimenzí

| Dimenze | Hodnocení | Odůvodnění |
|---------|-----------|------------|
| **Filosofická konzistence** | 9/10 | "Méně je více" je aplikováno důsledně napříč všemi vrstvami. |
| **Typografický systém** | 8/10 | Space Grotesk + JetBrains Mono je validní pár. Dobře definované velikosti a hierarchie. |
| **Barevná paleta** | 7/10 | Světlý background + oranžový akcent je neobvyklá (a tím zapamatovatelná) volba pro B2B tech. Funkční, ale kontrast oranžové (#E67E22) na off-white (#F9F9F9) je na hraně WCAG AA pro malé texty. |
| **Grid a kompozice** | 9/10 | 12/8/4 responzivní grid + 8px rytmus + pravidlo třetin = solidní architektonický základ. |
| **Komponentní systém** | 8/10 | Tři typy karet, definované stavy (hover/active/focus), dropzone s 3 stavy. |
| **Responzivita** | 7/10 | Breakpointy definovány, chybí detailnější specifikace pro tablet. |

### 1.3. Kritické odchylky od existujícího stavu (beta 0.2)

Tento design představuje **kompletní vizuální pivot** oproti aktuálně live verzi na systeq.cz:

| Prvek | Beta 0.2 (live) | Tento design | Dopad |
|-------|-----------------|--------------|-------|
| **Barevné schéma** | Dark (slate #0a0e14) | Light (off-white #F9F9F9) | Kompletní přepis všech CSS proměnných |
| **Akcent** | Teal (#0d9488) | Orange/Amber (#E67E22) | Mění vizuální identitu — z "cold tech" na "warm industrial" |
| **Pozadí** | Tmavé panely #1a1f2c | Bílé karty #FFFFFF | Zásadní změna atmosféry |
| **Font** | JetBrains Mono (vše) | Space Grotesk (UI) + Mono (data) | Zvyšuje čitelnost dlouhých textů |
| **Radius** | 3px (ostré) | 4px (jemné zaoblení) | Subtilní, ale znatelný posun |
| **Stíny** | Žádné | Subtile (jen parser karty) | Mění vizuální váhu karet |

### 1.4. Silné stránky koncepce

1. **Nízká saturace = vysoká profesionalita** — světlý background s řídkým akcentem působí jako architectural studio, ne jako startup.
2. **Prostor jako prvek** — 120-160px mezi sekcemi dodává dech a hierarchii.
3. **Oranžový akcent** — nečekaná volba v B2B tech. Pamatovatelná. Evokuje průmyslové výstražné prvky, těžkou techniku, safety.
4. **Typografická párování** — Space Grotesk (humanistický, čitelný) + JetBrains Mono (technický, přesný) je lepší než mono-for-all.

### 1.5. Slabé stránky a rizika

1. **Kontrast oranžové na off-white** — #E67E22 na #F9F9F9 dává kontrastní poměr ~3.5:1. WCAG AA vyžaduje 4.5:1 pro malý text. **Řešení:** Používat tmavší oranžovou (#D35400) pro malé texty, světlejší (#E67E22) jen pro velké nadpisy a CTA.
2. **Světlý background + monospace data** — dlouhé JSON bloky na bílém pozadí jsou únavné pro oči. Zvaž u datových bloků tmavý panel (jako GitHub code blocks).
3. **12sloupcový grid na desktopu** — 12 sloupců je vhodný pro komplexní layouty, ale pro jednoduché stránky (landing, /author) může vytvářet zbytečný overhead. Zvaž 6sloupcový grid jako výchozí, 12 pouze pro komplexní views.
4. **Absence dark modu** — B2B vývojáři často preferují tmavé IDE. Pokud je cílová audience technická, chybějící dark mode je missed opportunity.

### 1.6. Implementační doporučení

1. **Ponechat beta 0.2 live** během vývoje — neničit existující URL.
2. **Vyvíjet v novém adresáři** na FTP (např. `/v1.0-beta/`) nebo na GitHub Pages.
3. **Implementovat CSS proměnné** pro barevné schéma — umožní později přidat dark mode bez přepisování.
4. **Otestovat kontrast** oranžového akcentu na všech podkladech (bílá, šedá, off-white) nástrojem WebAIM Contrast Checker.
5. **Zvážit přechodové období** — pokud je dark/teal identita již komunikována CEO, náhlá změna na light/orange může působit nekonzistentně.

### 1.7. Závěr

**Design koncepce je validní, implementovatelná, a zvyšuje profesionální úroveň oproti beta 0.2.** Hlavní rozhodnutí: zda opustit dark/teal vizuální identitu (která byla součástí brandového rozhodnutí) a přejít na light/orange. To není technické, ale brandové rozhodnutí.

---

## ČÁST 2: POSOUZENÍ GITHUB INTEGRACE

**Otázka:** Měl by být web SYSTEQ vyvíjen integrovaně s GitHub (nové private repo)?

### 2.1. Verdikt

**Ano — založit nové private repo je vhodné a doporučené.**

### 2.2. Argumenty pro

| Argument | Váha | Zdůvodnění |
|----------|------|------------|
| **Version control** | Kritická | Iterativní vývoj beta 0.1 → 0.2 → 1.0 vyžaduje historii. Bez Gitu nelze vrátit změny. |
| **Rollback** | Vysoká | Při chybném deploy se vrátíš k poslednímu commitu. |
| **CI/CD** | Střední | GitHub Actions může auto-deployovat na Webzdarma FTP při push do main. |
| **Kontext pro LLM** | Vysoká | Při dalších session můžeš nahrát repo jako kontext. Otevře se ti celá historie. |
| **Dokumentace vedle kódu** | Střední | README.md s deploy instrukcí, CHANGELOG.md s verzemi. |
| **Portfolio** | Nízká | Pokud změníš na public, je to další položka do portfolia. |
| **Bezpečnost** | Vysoká | Private repo = API klíče, endpointy, obchodní logika neuniknou. |
| **Autorova zvyklost** | Střední | Již máš 7 repozitářů na github.com/outpost2026 — workflow máš zažitý. |

### 2.3. Doporučená struktura repa

```
outpost2026/systeq-web (private)
├── README.md
├── CHANGELOG.md
├── .gitignore
├── .github/
│   └── workflows/
│       └── deploy-webzdarma.yml    (volitelně: auto-deploy)
├── public/                          # → obsah FTP rootu
│   ├── index.html
│   ├── stack.html
│   ├── author.html
│   ├── contact.html
│   ├── parser/
│   ├── solutions/
│   ├── style.css
│   └── js/
└── docs/                            # dokumentace k deploy
    └── DEPLOY.md
```

### 2.4. Rizika a mitigace

| Riziko | Mitigace |
|--------|----------|
| **FTP přístup v repu** | Nikdy necommitovat FTP heslo. Použít GitHub Secrets pro Actions. |
| **Veřejný endpoint v kódu** | API URL dát do JS proměnné, nehardcodovat. Nahradit placeholderem v repu, reálnou hodnotu nasadit přes env. |
| **Velikost repa** | .gitignore zahrnuje node_modules, .env, *.jpg (assets se stahují zvlášť). Web je lightweight — riziko minimální. |

### 2.5. První kroky

```
gh repo create outpost2026/systeq-web --private --description "SYSTEQ B2B web — systeq.cz"
git init
git add .
git commit -m "init: systeq.cz v0.2 beta — landing page structure"
git branch -M main
git remote add origin https://github.com/outpost2026/systeq-web.git
git push -u origin main
```

---

## SHRNUTÍ

1. **Design koncepce:** ✅ Validní. Kvalitní, profesionální, implementovatelná. Hlavní riziko: kontrast oranžové na off-white (nutno otestovat) a rozhodnutí zda opustit dark/teal vizuální identitu.
2. **GitHub integrace:** ✅ Doporučeno. Založit `outpost2026/systeq-web` jako private repo. Přináší version control, rollback, CI/CD potenciál a bezpečné uchování API konfigurace.
