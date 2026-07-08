# AGENTS.md — Konvence pro LLM asistenty

Tento soubor definuje **výchozí kontext pro všechny LLM agenty** pracující na tomto repu.
Čti ho jako první při každé nové session.

---

## 1. Mapování repozitář → web hosting

Repozitář je zrcadlem FTP serveru (Webzdarma). **Neexistuje žádná `src/` na serveru.**

| Lokální repozitář | FTP / veřejný web |
|---|---|
| `src/index.html` | `/index.html` |
| `src/music.html` | `/music.html` |
| `projekty/index.html` | `/projekty/index.html` |
| `projekty/strecha_uvaly/xxx` | `/projekty/strecha_uvaly/xxx` |
| `projekty/dodavka_kuba/xxx` | `/projekty/dodavka_kuba/xxx` |
| `deploy/counter/xxx` | `/counter/xxx` |
| `Demo_Threejs/systeq_v3.html` | `/demo/systeq_v3.html` |

**Pravidlo:** Relativní cesty v HTML odkazech vždy vycházejí z FTP rootu, ne z `src/`.

---

## 2. Navigační struktura (web)

Tři pilíře — všechny jsou dostupné z header navigace každé stránky:

```
[SYSTEQ]  →  src/index.html        # B2B landing page (teal accent)
[Projekty] → projekty/index.html   # Fyzické projekty (brick accent)
[Ateliér] → src/music.html         # Audio tvorba (rust accent)
```

- **index.html** (SYSTEQ) → odkaz na `projekty/` a `music.html`
- **music.html** → odkaz na `projekty/` a `index.html`
- **projekty/index.html** → odkaz na `../index.html` a `../music.html`

---

## 3. Design system

- **Dark theme** vždy (#0a0e14 bg, #e2e8f0 text)
- **Font:** JetBrains Mono (Google Fonts CDN)
- **Scanline overlay:** `body::before` s repeating-linear-gradient
- **Single-file HTML** — žádný build step, vše inline (CSS + JS)
- **Žádný Node.js**, žádný bundler, žádný preprocesor

### Akcentní barvy podle pilíře:

| Pilíř | Akcent | CSS var |
|---|---|---|
| SYSTEQ | Teal #0d9488 | `--accent` |
| Ateliér můz | Rust #b7410e | `--rust` |
| Projekty | Brick #b8541a | `--accent` |

---

## 4. Git & deployment

- **Branch:** main
- **Remote:** origin/main (github.com/outpost2026/Systeq.cz_dev)
- **Deployment:** ručně přes FileZilla FTP na Webzdarma
- **Git ignoruje:** `.ai_state.json`, `music/audio/`, `music/video/`, `deploy/counter/data/`,
  `projekty/strecha_uvaly/`, `projekty/dodavka_kuba/`
- Audio a interní dokumenty projektů se nahrávají pouze na FTP (ne v git)

---

## 5. Důležité cesty (absolutní na disku)

| Účel | Cesta |
|---|---|
| Repo root | `c:\Users\PC\Documents\Repozitar_Dev\_github\web_integrace_systeq\` |
| Zdrojové HTML | `...\src\index.html`, `...\src\music.html` |
| Projekty | `...\projekty\index.html` |
| Projekt střecha | `...\Zakazky\Strecha_uvaly\` (mimo repo) |
| Projekt dodávka | `...\Zakazky\Dodavka_Kuba_Sladek\` (mimo repo) |

---

## 6. Pravidla pro úpravy

1. **Než něco změníš, zkontroluj AGENTS.md** — konvence se mohou vyvíjet
2. **Relativní cesty** v HTML vždy počítej od FTP rootu (ne od `src/`)
3. **Navigaci** udržuj konzistentně napříč všemi stránkami
4. **Interní dokumenty** (klient data, schémata, reporty) necommitovat do gitu
5. `.ai_state.json` aktualizuj při každé změně — je to perzistentní stav pro agenty
