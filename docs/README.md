# docs/

## `preview.svg`
Podgląd repo osadzony w głównym README (`![](docs/preview.svg)`). GitHub renderuje SVG natywnie — nic więcej nie trzeba robić.

## `social-preview.svg`
Obraz Open Graph (1200×630) — wyświetla się, gdy ktoś udostępni link do repo na LinkedIn, X, Slack, Facebook.

### Jak wgrać do GitHub (30 sekund, jednorazowo):

1. Otwórz `social-preview.svg` w Chrome/Firefox (dwuklik na plik lokalnie, albo online: `https://raw.githubusercontent.com/GronskiDeveloper/<REPO>/main/docs/social-preview.svg`).
2. Konwersja SVG → PNG:
   - **Najprościej:** [cloudconvert.com/svg-to-png](https://cloudconvert.com/svg-to-png) — upload SVG, download PNG 1200×630.
   - **W Firefoxie:** prawoklik na SVG → *Save Page As* → PNG.
   - **W Chrome DevTools:** F12 → Console → wklej ten oneliner przy otwartym SVG i klawisz Enter:
     ```js
     (async () => { const svg = document.querySelector('svg'); const s = new XMLSerializer().serializeToString(svg); const b = new Blob([s], {type: 'image/svg+xml'}); const u = URL.createObjectURL(b); const img = new Image(); img.src = u; await img.decode(); const c = document.createElement('canvas'); c.width = 1200; c.height = 630; c.getContext('2d').drawImage(img, 0, 0); const a = document.createElement('a'); a.download = 'social-preview.png'; a.href = c.toDataURL('image/png'); a.click(); })()
     ```
3. Wgraj wynikowy PNG w: **Settings → Social preview → Upload** (na stronie repo na GitHubie).

Efekt: link do repo shared'owany na LinkedIn / X / Slack pokazuje markowaną kartę zamiast szarej domyślnej GitHubowej.
