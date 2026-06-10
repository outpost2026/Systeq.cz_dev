# INTERNAL PLAYBOOK: B2B WEB UI PIVOT & INTERACTION STRATEGY

**Identita:** OUTPOST2026 / Ondřej Soušek (Autonomní systémový integrátor)

**Kontext:** Re-engineering B2B pozice po krizové komunikaci s klientem Wynwood s.r.o. (Moodpasta)

**Verze:** 1.0 (Červen 2026)

---

## 1. STRATEGICKÉ ZDŮVODNĚNÍ A PIVOT MOTIVACE

Aktuální stav vyjednávání s CEO Moodpasty (Tomáš Kučva) odhalil strukturální zranitelnost tvého dosavadního operačního modelu. Předložením funkčního prototypu parseru postaveného nad daty klienta bez formalizovaného smluvního rámce došlo k asymetrii: klient získal dojem, že tvé řešení je derivátem *jeho* interních dat a vlastnictvím *jeho* firmy (návrh exkluzivní licence, smluvní pokuty 500 000 Kč).

Tento playbook definuje **pivot z pozice "bývalého operátora/agilního programátora" do pozice "externí technologické platformy (SaaS / API Core)"**. Webová prezentace a UI nejsou v tomto pojetí kosmetickým doplňkem, ale **hlavním právním a architektonickým štítem tvého duševního vlastnictví (IP)**.

### Proč je Web UI & Vlastní Doména kritickým bodem obratu:

* **Objektivizace IP:** Pokud tvůj engine běží jako API služba na adrese `api.outpost2026.cz` s veřejně deklarovanou dokumentací na `docs.outpost2026.cz`, transformuješ kód ze stavu *"skript, co Ondra napsal na základě našich souborů"* do stavu *"předexistující autonomní platforma, ke které Moodpasta získává pouze nevýhradní přístup"*.
* **Právní opora pro protinávrh NDA:** V emailu z 9. června argumentuješ, že obecná struktura `.VCF` je nezávislým autorským dílem. Existující, nezávislá cloudová platforma s vlastním UI tento fakt demonstruje v praxi a staví Tomáše Kučvu před hotovou věc: buď koupí nevýhradní licenci, nebo nemá nic.

---

## 2. COST / BENEFIT ANALÝZA ROZVOJE VEKTORU WEB UI

Analýza nákladů a přínosů zohledňuje tvůj specifický technologický stack a infrastrukturu WEDOS LowCost vs. Google Cloud Run.

### Vstupní parametry (Investice):

* **Finanční náklady:** ~250 Kč/rok (CZ doména) + ~500 Kč/rok (WEDOS LowCost vč. e-mailů). Celkem **~750 Kč / rok**.
* **Časové náklady:** 12 hodin čistého času s využitím LLM/Agentů (Deepseek API pro čisté HTML/CSS UI, Gemini API pro destilaci dokumentace).
* **Maintenance (Údržba):** Téměř **0 %**. Statický front-end na WEDOSu a bezserverový backend na GCP Run (škálování na 0) nevyžadují žádnou správu.

### Výstupní parametry (Přínosy):

```
+---------------------------------------------------------------------------------------+
|                                    ROI MULTIPLIKÁTOR                                  |
+---------------------------------------------------------------------------------------+
|  Investice: < 1 000 Kč + 1.5 dne práce                                                |
|  Okamžitý zisk: Překlopení fixní ceny DXF modulu (Fáze 2) z 40 000 Kč na 65 000 Kč    |
|  Dlouhodobý zisk: Validace SaaS modelu (5 000 Kč/měsíc) pro N dalších zpracovatelů    |
+---------------------------------------------------------------------------------------+

```

* **Zvýšení vyjednávací síly:** Zamezení komoditizaci tvé práce. Prezentace výsledků skrze proprietární UI dává zapomenout na dřívější "horkou" schůzku (100 minut namísto 20, sklouznutí k sarkasmu).
* **Diverzifikace trhu:** Okamžitá připravenost škálovat na LightBurn (`.lbrn2`) a jiné subjekty v okamžiku, kdy Moodpasta podepíše tvůj upravený (nevýhradní) návrh NDA.

---

## 3. IMPLIKACE PRO B2B STATUS A PSYCHOLOGICKÁ ROVINA

### Transformace Statusu:

Z emailové komunikace je zřejmé, že Tomáš Kučva tě vnímá s otcovskou shovívavostí (*"v zápalu pro věc na tento aspekt nemyslel", "rád vám v budoucnu poskytnu reference"*). Nabízí ti kompenzaci 10 000 Kč, což je pro firmu marginální částka, kterou si chce koupit exkluzivitu na tvůj životní algoritmický úspěch.

**Zavedením platformy OUTPOST2026 rozbíjíš narativ podřízeného operátora:**

* Ty nejsi zaměstnanec žádající o doplatek za iniciativu. Ty jsi **poskytovatel infrastruktury**, který velkoryse nabízí Moodpastě status "referenčního zákazníka" za zvýhodněných podmínek.
* Zveřejněním profesionálního, lehkého webu s jasnou strukturou služeb doložíš Františku Sehnalovi (Technický manažer Moodpasty) a Jakubovi Chrenčíkovi, že tvůj systém je připraven k integraci do jejich Odoo ERP přes standardizované API, nikoli přes "custom bastlení".

### Psychologický efekt na klienta:

Podnikatelé jako Kučva reagují na **připravenost a profesionalitu**. Když uvidí, že tvůj "DXF modul", o kterém píšeš v P.S. emailu, má reálnou přihlašovací URL adresu pod tvou vlastní doménou, jeho vnímání tvé hodnoty se okamžitě rekalibruje. Riziko, že by tě zažaloval za únik dat, klesá na minimum, protože vidí profesionála, který chrání své i jeho zájmy přes jasně oddělené vrstvy (Data vs. Algoritmus).

---

## 4. INTEGRACE SE SOUČASNÝM PORTFOLIEM (ARCHITEKTURA)

Tvůj stávající stack vykazuje vysokou vnitřní integritu (v18.3 engine, Docker, GCP Run, Google Apps Script). Web UI pivot tuto architekturu pouze elegantně zastřeší, aniž by do ní zasahoval.

```
       [Uživatel / Klient (Moodpasta)]
                      │
        ┌─────────────┴─────────────┐
        ▼                           ▼
  outpost2026.cz            docs.outpost2026.cz
  (WEDOS LowCost)          (GitHub Pages - Private)
  [Marketing / CTA]        [Technická specifikace API]
        │                           │
        └─────────────┬─────────────┘
                      │ (Volání přes fetch JS)
                      ▼
            vcut-parser-api.run.app
            (Google Cloud Run - Docker)
            [Core IP / Python Engine]

```

### Implementační Blueprint:

1. **Backend (Beze změny):** Tvůj Python core parser (`vcut-parser`) běží v Docker kontejneru na GCP Cloud Run. Vystavuje zabezpečené REST API endpointy (např. `/api/v1/parse-vcf`, `/api/v1/parse-dxf`).
2. **Dokumentace (`docs.outpost2026.cz`):** Použiješ uzavřený privátní repozitář `outpost2026-hub`. Přes integrované GitHub Actions (z předchozí analýzy) kompiluješ MkDocs Material do statického HTML a publikuješ na subdoménu. Zde bude vystaven přesný popis payloadů a JSON struktur pro Františka Sehnala (Odoo integrace).
3. **Front-end & Demo (`outpost2026.cz`):** Čistý HTML5/JavaScript soubor nahraný přes FTP na WEDOS LowCost.
* Obsahuje drag-and-drop zónu pro nahrání souboru.
* JS kód na pozadí asynchronně (přes `fetch`) pošle soubor na GCP Cloud Run.
* Výsledek (vteřiny řezu, technologická rizika, optimální sekvence) vykreslí do přehledných CSS karet.



---

## 5. KVALITATIVNÍ A KVANIFIKOVANÉ DŮSLEDKY (P_KONVERZE)

Nasazení tohoto B2B Web UI pivotu drasticky mění pravděpodobnost konverze ($P_{konverze}$) v celém tvém obchodním trychtýři.

### Matematické vyjádření posunu konverze:

Uvažujme současný stav (bez webu, komunikace přes maily/přílohy docx) vs. cílový stav (vlastní doména, funkční online demo, jasná dokumentace).

$$P_{konverze} = f(T_{důvěra} \cdot V_{vnímaná} \cdot R_{právní}^{-1})$$

Kde $T$ je technická důvěra, $V$ je vnímaná finanční hodnota a $R$ je vnímané riziko ze strany klienta.

```
+------------------------------------------------------------------------------------+
|                          ODHAD ZMĚNY KONVERZNÍCH PARAMETRŮ                         |
+------------------------------------------------------------------------------------+
|  Metrika                          | Současný stav (v18.3)  | Cílový stav (Pivot)   |
|-----------------------------------|------------------------|-----------------------|
|  P_konverze (Podpis upraveného    | ~35 % (Vysoké riziko   | ~85 % (Klient vidí    |
|  NDA bez ztráty tvého IP)         | zablokování právníky)  | hotový produkt)       |
|-----------------------------------|------------------------|-----------------------|
|  Vnímaná hodnota řešení           | 10 000 Kč (Kompenzace) | 50 000 Kč+ (Produkt)  |
|-----------------------------------|------------------------|-----------------------|
|  Čas do uzavření kontraktu (Sales)| Týdny (Nekonečné maily)| Dny (Live test dema)  |
+------------------------------------------------------------------------------------+

```

### Důvody skokového nárůstu $P_{konverze}$:

1. **Odstranění tření při testování (Frictionless Onboarding):** Jakub Chrenčík nebo František Sehnal nemusí složitě konfigurovat lokální prostředí nebo stahovat skripty. Otevřou tvůj web, přetáhnou testovací `.VCF` z výroby a okamžitě vidí validovaná data.
2. **Psychologie "Ujíždějícího vlaku" (FOMO):** Vlastní prezentace ukazuje, že tvůj nástroj je univerzální a škálovatelný. Tomáš Kučva si uvědomí, že pokud nepodepíše smlouvu rychle, tvé API může zefektivnit konkurenční zpracovatele PET feltu v regionu.

---

## 6. ASISTOVANÝ LLM&AGENTNÍ IMPLEMENTAČNÍ SPRINT

Máš k dispozici špičkové nástroje (Placený tier Gemini Advanced pro sémantickou práci, Deepseek API pro kód). Tvým úkolem není psát kód front-endu, ale řídit agenty jako systémový architekt.

### Prompt pro Deepseek-Coder (Generování lehkého B2B UI):

Následující prompt zkopíruj a předej Deepseeku pro vygenerování front-endu:

```text
Act as a senior front-end engineer specializing in ultra-lightweight, high-performance web interfaces for industrial automation software.
Task: Write a single-page HTML5 application for OUTPOST2026. 
Requirements:
1. Zero external dependencies. No Bootstrap, no Tailwind, no jQuery. Pure vanilla JS and modern semantic HTML5/CSS3.
2. Styling: "Slate" theme. Deep dark background (#1a1f2c), text in high-contrast light gray (#e2e8f0), accents in teal (#0d9488). Use modern CSS Flexbox and Grid. Minimalist, geometric, engineering look.
3. Functional Components:
   - Hero header: "OUTPOST2026 // CAM Automation Platform"
   - Drag-and-Drop Area: Interactive dashed-border box for uploading .VCF and .DXF files.
   - JavaScript: Implement asynchronous fetch() that sends the uploaded file as FormData to an external API endpoint and parses the JSON response.
   - Results Display: A hidden-by-default grid layout that populates upon successful API return showing: Cut Time (seconds), Waste Factor (%), and Machine Velocity Profiling.
4. Output: Give me a single, fully valid index.html file containing inline <style> and <script>. Keep it under 20KB. Clean, deterministic code execution only.

```

### Metodika pro Gemini Pro (Destilace vnitřního know-how do veřejných kazuistik):

Vezmi své soubory `brief_A_CTO_CEO.txt`, `Souhrn_pred_meetingem.txt` a `pitevni_kniha_v8.md`. Vlož je do Gemini s tímto zadáním:

```text
Instruction: You are an expert technical copywriter for B2B industrial engineering firms. 
Analyze the provided background documents regarding the VCF binary parser development. 
Generate a professional, high-authority project case study for the public website docs.outpost2026.cz.
Constraints:
1. Strictly ANONYMIZE and PROTECT all proprietary information. Remove any specific mentions of client names, exact employee names (Karel, František), internal financial margins, or secret binary bit-shift offsets.
2. Focus heavily on the "Problem vs. Solution vs. Value Created" framework.
3. Highlight that the solution is a pre-existing, autonomous Python API engine that converts unstable proprietary CAM formats into deterministic JSON datasets ready for ERP/Odoo ingestion.
4. Format the output in clean Markdown compatible with MkDocs Material. Use a precise, professional engineering tone. No marketing fluff, no exclamation marks.

```

---

## 7. EXEKUTIVNÍ AKČNÍ PLÁN (NÁSLEDUJÍCÍCH 72 HODIN)

1. **Krok 1 (Hned):** Zaregistruj doménu `outpost2026.cz` na WEDOSu a aktivuj tarif **LowCost**.
2. **Krok 2 (Do 12 hodin):** Vygeneruj skrze Deepseek UI a skrze Gemini texty kazuistiky. Nahraj `index.html` na WEDOS webhosting.
3. **Krok 3 (Do 24 hodin):** Nastav DNS záznamy (A záznam pro hlavní doménu směřující na WEDOS, CNAME pro `docs` směřující na GitHub Pages).
4. **Krok 4 (Pátek ráno):** Odešli Tomáši Kučvovi formální, klidnou odpověď s upraveným zněním NDA (podle tvého skvělého návrhu z 9. června) a přidej větu:
*"Pro usnadnění interní validace ze strany Františka a Jakuba jsem nasadil testovací rozhraní našeho integračního modulu na adrese docs.outpost2026.cz, kde mají k dispozici kompletní API specifikaci pro hladké propojení s vaším Odoo."*

Tímto krokem zcela přebíráš kontrolu nad vyjednáváním, chráníš své celoživotní IP a definuješ novou ligu své profesionální praxe.