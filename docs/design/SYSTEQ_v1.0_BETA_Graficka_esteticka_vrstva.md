# PLAYBOOK: GRAFICKÁ A ESTETICKÁ Vrstva – SYSTEQ.CZ v1.0

**Verze:** 1.0  
**Určeno pro:** front-end implementaci dle `DEV_PLAN_SYSTEQ_v1.0_BETA.md`  
**Cíl:** Vytvořit subtilní, zapamatovatelný vizuální jazyk, který odráží „méně je více“ a organickou estetickou niku – nikoliv masový webový vkus. Inspirace čerpá z architektury, fotografie, kompozice a průmyslového designu.

---

## 0. Filozofie: Méně je více, ale precizně

> *„Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away.“* – Antoine de Saint-Exupéry

- **Každý pixel má důvod** – žádné dekorativní prvky bez funkce.
- **Vysoký SNR (Signal-to-Noise Ratio)** – odstranit veškerý vizuální šum (zbytečné stíny, přechody, rámečky).
- **Subtilní, nikoli nudný** – kontrast vzniká promyšlenou prací s prostorem, typografií a výjimečnými detaily.
- **Inspirace mimo web:**  
  - *Architektura*: Tadao Ando (holý beton, hra světla a stínu, geometrická čistota).  
  - *Fotografie*: Ansel Adams (kontrast, tonální rozsah), Michael Kenna (minimalismus, negativní prostor).  
  - *Průmyslový design*: Dieter Rams („Weniger, aber besser“ – méně, ale lépe).  
  - *Grafický design*: Josef Müller-Brockmann (grid, typografická hierarchie).

---

## 1. Typografický systém

Vychází z `DEV_PLAN` – kombinace **Space Grotesk** (pro delší texty a UI) a **JetBrains Mono** (pro kód, data, technické výstupy). Doplňujeme o fallbacky a přesné parametry.

### 1.1. Primární písma

| Role | Font | Fallback | Použití |
|------|------|----------|---------|
| **UI & tělový text** | Space Grotesk | system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif | Nadpisy, odstavce, tlačítka, navigace, karty |
| **Monospace (technický)** | JetBrains Mono | 'SF Mono', Monaco, 'Cascadia Code', 'Fira Code', monospace | Kód, JSON, API endpointy, číselné metriky, segmenty |
| **Akcentové krátké texty** (volitelně) | Space Grotesk (medium/bold) | – | Čísla v CEO view, trust badge |

> **Poznámka:** V původním plánu bylo `JetBrains Mono` pro vše – to vytváří příliš technický, nečitelný dojem pro delší texty. Kombinace s humanistickým Space Grotesk zajišťuje čistotu a moderní vzhled.

### 1.2. Velikosti a hierarchie (desktop)

| Prvek | Velikost (rem) | Váha (font-weight) | Řádkování (line-height) | Mezery písmen (letter-spacing) |
|-------|----------------|---------------------|-------------------------|-------------------------------|
| H1 (hero) | 3.5 – 5 rem (dle viewportu) | 600 (semi-bold) | 1.1 | -0.01em |
| H2 (sekce) | 2 rem | 500 (medium) | 1.2 | -0.005em |
| H3 (podnadpis) | 1.5 rem | 500 | 1.3 | normal |
| Tělový text (body) | 1 rem (16px) | 400 (regular) | 1.6 | normal |
| Small (metadata, tagy) | 0.875 rem | 400 | 1.4 | 0.01em |
| Monospace (data) | 0.9 rem | 400 | 1.5 | normal |
| Tlačítka | 1 rem | 500 | 1 | 0.02em |

**Mobil:** všechny velikosti snížit o 10–15 %, H1 maximálně 2.5rem.

### 1.3. CSS proměnné

```css
:root {
  --font-sans: 'Space Grotesk', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  --font-mono: 'JetBrains Mono', 'SF Mono', Monaco, 'Cascadia Code', 'Fira Code', monospace;

  --text-h1: 3.5rem;
  --text-h2: 2rem;
  --text-h3: 1.5rem;
  --text-body: 1rem;
  --text-small: 0.875rem;

  --line-height-tight: 1.1;
  --line-height-normal: 1.6;
  --letter-spacing-tight: -0.01em;
}
```

### 1.4. Principy typografie

- **Maximální šířka řádku** pro tělový text: 70–80 znaků (na desktopu cca 45–55rem). Delší řádky unavují.
- **Hierarchie pouze pomocí velikosti a váhy** – žádné barevné podtrhávání nebo kapitálky.
- **Monospace pouze pro data, kód, číselné sloupce** – nikdy pro souvislý text.
- **Kontrast písma** – mezi nadpisem a textem minimálně 2x rozdíl velikosti.

---

## 2. Barevná paleta (nízká saturace, vysoký kontrast)

Odkláníme se od pestrých „startup“ barev. Cílem je industriální, technická elegance.

### 2.1. Neutrální základ

| Role | Barva (hex) | Příklad |
|------|-------------|---------|
| Pozadí (hlavní) | `#F9F9F9` (off-white) | Klasický papírový tón |
| Pozadí (sekundární, karty) | `#FFFFFF` | Čistě bílá |
| Pozadí (akcentní, dropzone) | `#F0F0F0` | Pro odlišení |
| Text primární (tělo) | `#1A1A1A` | Téměř černá – maximální čitelnost |
| Text sekundární (metadata, placeholdery) | `#6B6B6B` | Šedá 60 % |
| Text terciární (disabled, poznámky) | `#9A9A9A` | Šedá 70 % |
| Ohraničení (borders, rámečky) | `#E5E5E5` | Jemná linka |
| Stíny (jen kde nutné) | `rgba(0,0,0,0.05)` | Velmi subtilní |

### 2.2. Akcentová barva – jedna, výrazná, řídká

**Orange/Amber** – odkaz na průmyslové výstražné prvky, ale tlumená.  
Primární CTA, aktivní stavy, zvýrazněná čísla.

| Varianta | Hex | Použití |
|----------|-----|----------|
| Akcent primární | `#E67E22` | Hover tlačítek, aktivní tab, klíčové číslo |
| Akcent světlý | `#F39C12` | Hover stavy, border focus |
| Akcent tmavý | `#D35400` | Aktivní prvek (např. role switcher) |

### 2.3. Status barvy (informace, varování, chyba)

| Status | Hex | Použití |
|--------|-----|----------|
| Úspěch (success) | `#2E7D32` | Zelená pro waste factor <5% |
| Varování (warning) | `#F57C00` | Žlutá pro waste 5–15% |
| Chyba (error) | `#C62828` | Červená pro waste >15%, chybové stavy |
| Info | `#1976D2` | Modrá pro API info, tooltipy |

> **Zásada:** Barevné prvky používat střídmě – jeden akcent na obrazovku, status barvy pouze pro klíčové metriky.

---

## 3. Prostor a kompozice – geometrická logika

Inspirace architekturou – **jasný rastr, asymetrické napětí, negativní prostor**.

### 3.1. Grid systém (CSS Grid)

- **Desktop (≥1024px):** 12sloupcový grid, mezery 24px, okraje 32px.
- **Tablet (768–1023px):** 8 sloupců, mezery 20px, okraje 24px.
- **Mobil (<768px):** 4 sloupce, mezery 16px, okraje 16px.

**Definice v CSS:**

```css
.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 32px;
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 24px;
}

/* Responzivní úpravy */
@media (max-width: 1023px) {
  .container { grid-template-columns: repeat(8, 1fr); gap: 20px; padding: 0 24px; }
}
@media (max-width: 767px) {
  .container { grid-template-columns: repeat(4, 1fr); gap: 16px; padding: 0 16px; }
}
```

### 3.2. Topografická logika (vertikální rytmus)

- Základní jednotka: **8px** (používáme pro margin, padding, výšky prvků).
- Vertikální rytmus tělového textu: `line-height: 1.6` → každý řádek 16*1.6 = 25.6px, zaokrouhleno na násobek 8 → 24px nebo 32px? Lepší použít relativní jednotky.
- **Doporučení:** Nastavit `body` s `line-height: 1.6` a všechny mezery (margin-bottom odstavců, nadpisů) jako násobky základního řádkování. Např. `margin-bottom: 1.5rem` = 24px.

### 3.3. Principy kompozice z fotografie a architektury

| Princip | Aplikace v UI |
|---------|----------------|
| **Pravidlo třetin** | Důležité prvky (dropzone, CTA) umístit na průsečíky pomyslné sítě 3x3. |
| **Vedení linie (leading lines)** | Karty uspořádat do sloupců, které přirozeně vedou oko od hero k výsledkům. |
| **Negativní prostor** | Mezi sekcemi nechat 120–160px mezeru (na desktopu), aby každá sekce dýchala. |
| **Asymetrie** | Např. dropzone nalevo, file info napravo – necentrovat vše. |
| **Zlatý řez (1.618)** | Použít pro poměr šířky hlavního obsahu k postrannímu panelu (např. 8:4 sloupce v gridu). |
| **Vrstvení (layering)** | Jako v architektuře: pozadí (neutrální), střední vrstva (karty), popředí (interaktivní prvky) – každá s jemným odlišením (barva, stín). |

### 3.4. Whitespace – aktivní prvek

- **Hodnota:** Nebát se velkých prázdných ploch kolem klíčových prvků.
- **Karty:** Vnitřní padding 24px, mezi kartami 24px.
- **Hero sekce:** Margin-top alespoň 80px, margin-bottom 80px (na desktopu).

---

## 4. Kontrast – více než jen barva

Používejte všechny typy kontrastu pro zvýšení zájmu a čitelnosti.

### 4.1. Typy kontrastu

| Typ | Popis | Příklad v SYSTEQ |
|-----|-------|------------------|
| **Velikostní** | Rozdíl velikostí písma, prvků | H1 vs. tělový text (3x) |
| **Barevný** | Teplá vs. studená, světlá vs. tmavá | Akcentní oranžová na neutrálním šedém pozadí |
| **Tvarový** | Geometrické vs. organické | Karty s ostrými rohy vs. dropzone s jemně zaoblenými hranami (4px) |
| **Texturní** | Hladká plocha vs. jemná struktura | Pozadí hladké, monospace text připomíná dálnopisný pásek |
| **Konceptuální** | Staré vs. nové, ruční vs. digitální | VCF parser – analogový výstup (číselné hodiny) vs. moderní SVG grafy |

### 4.2. Implementace kontrastu

- **Velikostní:** V CEO view zobrazit hlavní metriku (čas řezu) fontem 3rem, vedlejší metriky 1.5rem.
- **Tvarový:** Všechny prvky mají `border-radius: 4px` (jemné zaoblení). Výjimka: monospace bloky mají `border-radius: 0` (ostré hrany) pro technický pocit.
- **Texturní:** Na pozadí `<body>` přidat velmi jemnou šumovou texturu (`background-image: repeating-linear-gradient(...)`) – ale jen pokud není rušivá. **Volitelné, default bez.**

---

## 5. Komponentní styl – přesné vizuály

Všechny komponenty vycházejí z `DEV_PLAN`.

### 5.1. Karty (tři typy)

| Typ | Pozadí | Ohraničení | Stín | Padding | Použití |
|-----|--------|------------|------|---------|----------|
| **Stack karta** | `#FFFFFF` | 1px solid `#E5E5E5` | `none` | 20px | /stack, technologie |
| **Parser karta** (výsledky) | `#FFFFFF` | `none` | `0 2px 8px rgba(0,0,0,0.04)` | 24px | CEO/CTO/Design view |
| **Solution karta** | `#F9F9F9` | `none` | `none` | 24px | /solutions |

**Hover efekt:** Pouze u tlačítek a interaktivních karet (např. v /solutions) – změna pozadí na `#F0F0F0`, plynulý přechod 150ms.

### 5.2. Dropzone (pro parser demo)

```css
.dropzone {
  border: 2px dashed #D1D1D1;
  background: #FAFAFA;
  border-radius: 8px;
  padding: 48px 24px;
  text-align: center;
  transition: all 0.2s ease;
}

.dropzone.drag-over {
  border-color: var(--accent-primary);
  background: #FEF5E7;
}

.dropzone.file-selected {
  border-style: solid;
  border-color: var(--accent-primary);
  background: #FFFFFF;
}
```

### 5.3. Tlačítka

- **Primární CTA:** Pozadí `#1A1A1A`, text `#FFFFFF`, padding 12px 24px, border-radius 4px. Hover: pozadí `#333333`.
- **Sekundární (outline):** Průhledné, border 1px solid `#1A1A1A`, text `#1A1A1A`. Hover: pozadí `#F0F0F0`.
- **Terciární (textové):** Žádný border, text `#6B6B6B`. Hover: text `#1A1A1A`.

Žádné stíny na tlačítkách.

### 5.4. Role switcher (CEO/CTO/Design)

- Taby uspořádané vodorovně, podtržení (border-bottom) aktivního tlačítka barvou `var(--accent-primary)`, tloušťka 2px.
- Mezery mezi taby: 32px.
- Hover neaktivního: změna barvy textu na `#1A1A1A`.

### 5.5. Loading stav

- **Spinner:** SVG kruh s animací rotace, barva `var(--accent-primary)`. Bez textu – pouze vizuální indikátor.
- **Progress bar:** Tenký pruh (2px) na horním okraji dropzone, šířka se postupně zvyšuje (simulace). Po dokončení zmizí.

### 5.6. Tabulky (CTO view segment breakdown)

- Bez vertikálních linek, pouze horizontální oddělovače.
- Hlavička: `font-weight: 600`, barva `#6B6B6B`, text-align left.
- Řádky: `border-bottom: 1px solid #E5E5E5`, padding 12px 8px.
- Zebra stínování? **Ne** – ruší čistotu.

### 5.7. SVG canvas (Design view)

- Pozadí `#F9F9F9`, ohraničení 1px solid `#E5E5E5`.
- Souřadnicový systém: výchozí 0,0 v levém horním rohu.
- Barvy toolpathů:  
  - Cut: `#1A1A1A`  
  - Engrave: `#E67E22`  
  - Travel: `#D1D1D1`  
  - Scoring: `#1976D2`

---

## 6. Animace a přechody – méně je více

Pouze účelové, žádné „wau“ efekty.

| Akce | Doba trvání | Křivka | Použití |
|------|-------------|--------|----------|
| Hover (tlačítka, karty) | 150ms | ease | plynulá změna barvy pozadí |
| Fade-in výsledků | 200ms | ease | po úspěšném parsování |
| Loading spinner | lineární | infinite | rotace |
| Přepínání rolí (CEO/CTO/Design) | 100ms | ease | výměna obsahu bez animace (instantní) – žádné slide |

**Zákaz:** blikání, bounce, pulse (kromě loading stavu), 3D transformace, paralaxa.

---

## 7. Responzivita – mobilní adaptace principů

Na mobilu platí stejná filozofie, ale s úpravami:

- **Odstranit sloupcový grid** → přejít na jednosloupcový layout.
- **Dropzone** má padding 32px 16px.
- **Role switcher** umístit pod dropzone, taby na celou šířku (display: flex; justify-content: space-around).
- **CEO view metriky** zobrazit jako 2x2 grid, ne 4 v řadě.
- **Tabulky** – převést na karty? Nebo horizontální scroll – povolit `overflow-x: auto`.
- **Typografie:** H1 na 2rem, tělový text na 0.9rem.

---

## 8. Inspirační zdroje (pro další studium)

**Weby s podobnou estetikou:**  
- [Stripe](https://stripe.com) – čistá typografie, prostor.  
- [Linear](https://linear.app) – tmavý režim, přísný grid, monospace akcenty.  
- [PlanetScale](https://planetscale.com) – kombinace serifu a sansu, asymetrie.  
- [Every](https://every.to) – newsletter s architektonickým pojetím.

**Architektura:**  
- Tadao Ando – Church of the Light (kontrast světla a tmy).  
- Louis Kahn – Salk Institute (symetrie, linie, materiál).

**Fotografie:**  
- Michael Kenna – dlouhé expozice, minimalismus.  
- Hiroshi Sugimoto – mořské krajiny, rovnováha.

**Grafický design:**  
- Müller-Brockmann – „Grid Systems“.  
- Emil Ruder – „Typographie“.

---

## 9. Shrnutí implementačních pravidel (checklist pro developera)

Při psaní CSS dodržuj:

- [ ] Všechny barvy definuj v `:root` jako CSS proměnné.
- [ ] Nepoužívej `!important` – řeš specifičnost selektorů.
- [ ] Žádné vložené styly (`style` attribut) – pouze externí CSS.
- [ ] Pro grid používej `display: grid`, ne `float` nebo `flex` tam, kde jde o strukturu.
- [ ] Velikosti fontů v `rem`, margin/padding v `rem` nebo `px` (pro jemné ladění).
- [ ] Každý stav (hover, focus, active) má explicitně definovaný styl.
- [ ] Otestuj kontrast textu (WCAG AA minimálně) – nástroj [Contrast Checker](https://webaim.org/resources/contrastchecker/).
- [ ] Responzivní breakpoints: 768px, 1024px.
- [ ] Žádné zbytečné media dotazy – preferuj flexibilní jednotky.

---

**Tento playbook je závazný pro implementaci vizuální vrstvy SYSTEQ v1.0. Jakákoli odchylka musí být zdůvodněna a schválena.**