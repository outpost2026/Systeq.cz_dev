**✅ Aktualizovaná doporučení na základě analýzy poskytnutého `index.html`**

Prohlédl jsem si kompletní kód stránky. Je to **solidní, čistý a technicky dobře zpracovaný** landing page s moderním dark-mode stylem, JetBrains Mono fontem a dobrým použitím CSS variables.

### Shrnutí aktuálního stavu stránky
- **Silné stránky**: Čistý design, dobrá responzivita, konzistentní barevný systém, přehledná struktura sekcí, dobré použití gridů a hover efektů.
- **Slabiny**: Některé konverzní prvky jsou slabší, než by mohly být; chybí některé CTA; vizuální hierarchie by mohla být ostřejší; lead generation je zatím spíš pasivní.

---

### 1. Konkrétní vylepšení **konverzních prvků**

**Aktuální stav** je slušný, ale lze ho výrazně posílit:

1. **Přímý kontakt**  
   - **Aktuálně**: Jen v patičce.  
   - **Vylepšení**: Přidat **"Book Discovery Call"** button s Calendly (nebo jednoduchý inline formulář) hned pod hero sekcí a po každé benefit sekci (zvláště po "cenotvorba" a "vizuální audit").  
   - Text: *"Chcete vidět parser na vašich datech? Domluvte si 15min call"*

2. **Chatbot**  
   - **Aktuálně**: Jen NotebookLM link v Lab sekci.  
   - **Vylepšení**: Přidat **plnohodnotný embedded chatbot** (např. Tidio, Chatbase nebo vlastní s Vercel AI) přímo do hero sekce jako floating bubble nebo samostatnou kartu "Ask SYSTEQ".  
   - Přidat CTA: *"Zeptejte se na váš konkrétní výrobní problém"*

3. **Audio esej**  
   - **Aktuálně**: Jen odkaz.  
   - **Vylepšení**: Přidat **inline audio player** s waveform vizualizací (použij např. Wavesurfer.js) přímo v Lab sekci. Přidat transcript snippet.

4. **Lead magnet** (nový silný prvek)  
   - Přidat **"Free VCF Analysis"** — uživatel nahraje soubor → dostane automatický report (chyby, časový odhad, waste factor).  
   - Umístit jako hlavní CTA v hero sekci.

5. **API specifikace**  
   - **Aktuálně**: Text v kontaktní sekci.  
   - **Vylepšení**: Přidat **interaktivní API tester** (jednoduchý form + button "Send request") nebo aspoň copy-paste ready curl příklady s reálnými response.

---

### 2. Konkrétní vylepšení **vizuálního stylu**

Stránka je už teď nadprůměrná, ale lze ji posunout na **premium úroveň**.

**Prioritní změny:**

1. **Hero sekce** (nejdůležitější):
   - Zvětšit a zlepšit 3D preview (přidat loading state + fallback image).
   - Přidat **subtle particle / scanline** animaci na pozadí hero (jemně, ne rušivě).
   - Přidat druhý CTA button: "Request Live Demo" vedle "Spustit demo".

2. **Barvy a akcenty**:
   - Aktuální teal (`--accent: #0d9488`) je dobrý, ale přidej lehce teplejší oranžovou (`#D35400`) jako sekundární akcent pro CNC/manufacturing feeling (už ji máš v logu).

3. **Typografie a spacing**:
   - Zvětšit `line-height` u delších textů na 1.75–1.8.
   - Použít více **bold weightů** u klíčových benefitů.

4. **Nové vizuální prvky**:
   - Přidat **progress bars / animated counters** u metrik (33+, 100%, 0%).
   - V benefit sekcích přidat ikony nebo malé SVG ilustrace (např. check, warning, clock).
   - Přidat **subtle grid pattern** na pozadí (jemný technický motiv).

5. **Mobilní optimalizace**:
   - Hero na mobilu je dobrý, ale zajisti, aby 3D preview nespomalovalo načítání (použij lazy loading + fallback).

**Celkový doporučený směr vizuálu**:
- Tmavý, technický, přesný, s mírným "industrial cyber" nádechem (tmavé pozadí + teal/oranžové akcenty + čisté linie).

---

**Shrnutí priorit (co udělat nejdříve)**:
1. Přidat **lead magnet** (Free VCF Analysis) + Calendly button.
2. Vylepšit **hero sekci** (dva CTA + lepší vizualizace).
3. Přidat **embedded chatbot** a **inline audio player**.
4. Zlepšit **metriky** animacemi a **benefit karty** ilustracemi.

Chceš, abych ti připravil konkrétní HTML/CSS snippety pro některé z těchto změn (např. nový hero, lead magnet form, nebo audio player)?