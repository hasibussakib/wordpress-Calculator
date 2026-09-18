# BatterySizing.xyz

Battery calculator and sizing platform — 12 calculators, guides, and reference
pages. This repo now ships an installable **WordPress theme** in `batterysizing/`
(the original React + Vite + Tailwind configs remain at the repo root).

## WordPress theme

Folder: [`batterysizing/`](batterysizing/) · details: [`THEME.md`](THEME.md)

1. Zip the `batterysizing` folder (or copy it into `wp-content/themes/`).
2. WordPress admin → **Appearance → Themes → Add New → Upload Theme** → Activate.
3. **Appearance → Batterysizing Setup → Import demo content**.

That creates every calculator, guide, FAQ, About, Contact, Privacy and Terms
page, assigns menus, and sets the static front page.

## React app setup (original)

```bash
npm install
npm run dev
```

Open the printed local URL (usually http://localhost:5173).

## Build

```bash
npm run build
npm run preview
```

`npm run build` also copies `dist/index.html` to `dist/404.html`, which is
needed for GitHub Pages (see Deploying below).

## Structure

- `src/components/` — one component per section/UI piece (Header, Hero,
  CalculatorCard, RuntimeCapacityCalculator, FAQ, Guides, Footer, etc.)
- `src/data/` — content data (calculators, guides, FAQ, chemistry, use cases,
  battery presets)
- `src/lib/` — shared logic: `batteryMath.ts` (all the formulas),
  `routes.ts` (registry of live routes), `setPageMeta.ts` (per-page title/description)
- `src/layouts/SiteLayout.tsx` — shared Header + Footer wrapper
- `src/pages/` — one file per route
- `src/App.tsx` — router setup + the cursor-spotlight effect

## Routes

**Calculators:** `/`, `/battery-calculator/`, `/battery-runtime-calculator/`,
`/battery-capacity-calculator/`, `/battery-backup-calculator/`,
`/battery-bank-calculator/`, `/solar-battery-calculator/`,
`/ups-battery-calculator/`, `/12v-battery-calculator/`,
`/24v-48v-battery-calculator/`, `/lifepo4-battery-calculator/`,
`/ah-to-wh-calculator/`, `/wh-to-ah-calculator/`, `/calculators/` (hub)

**Guides:** `/guides/` (hub), `/guides/ah-vs-wh/`,
`/guides/calculate-battery-runtime/`, `/guides/how-many-ah-do-i-need/`,
`/guides/depth-of-discharge/`, `/guides/battery-chemistry/`,
`/guides/battery-formulas/`, `/guides/examples/`

**Other:** `/faq/`, `/about/`, `/contact/`, `/privacy-policy/`,
`/terms-of-use/`

Every route is registered in two places that must stay in sync:
`src/lib/routes.ts` (`LIVE_ROUTES`, used by `SmartLink` to decide whether to
navigate client-side or fall back to a plain anchor) and `src/App.tsx`
(the actual `<Route>` definitions).

## Deploying

Routing uses `react-router-dom`'s `BrowserRouter`, giving clean URLs like
`/battery-calculator/` with no `#`. This is better for SEO and looks better
in links, but it means **your host must be configured to serve
`index.html` for any path** — otherwise a direct visit to
`/battery-calculator/` (or a page refresh on it) will 404, since the server
has no actual file at that path.

Config files for the common hosts are already included:

- **Netlify** — `public/_redirects` (copied into `dist/` automatically on build). No extra setup needed.
- **Vercel** — `vercel.json` at the project root. No extra setup needed.
- **Apache** — `public/.htaccess` (copied into `dist/` automatically on build). Requires `mod_rewrite` enabled on the server.
- **Nginx** — add this to your server block:
  ```nginx
  location / {
    try_files $uri $uri/ /index.html;
  }
  ```
- **GitHub Pages** — doesn't support custom rewrite rules, so `npm run build`
  copies `index.html` to `404.html`. GitHub Pages serves that on any unknown
  path while keeping the original URL in the address bar, which is enough
  for React Router to pick up the correct route on load.
- **Cloudflare Pages** — SPA fallback is automatic; no config needed.
- **A bare static server with no SPA support** (e.g. Python's
  `http.server`, opening files directly from disk) — will **not** work
  correctly with clean URLs, since nothing serves `index.html` for
  `/battery-calculator/`. If you need zero-config hosting like this, use
  `HashRouter` instead (see below) at the cost of URLs like
  `/#/battery-calculator/`.

### Switching back to HashRouter

If you ever need to host this somewhere that can't be configured with a
rewrite rule, swap `BrowserRouter` for `HashRouter` in `src/App.tsx`, and
set `base: './'` in `vite.config.ts` so built assets use relative paths.
This trades clean URLs for zero-config hosting.

Per-page `<title>`/meta description are updated client-side on route change
(see `src/lib/setPageMeta.ts`). For stronger SEO on individual pages beyond
what's already set, consider prerendering or SSR later — the current setup
works well for launch, but a crawler that doesn't execute JS will only see
the homepage's static meta tags in `index.html`.

## Notes

- The Appliance Load Estimator and What-If Runtime Comparison sections on
  the homepage are fully functional (editable rows, live totals, a live SVG
  chart).
- Shared runtime/capacity math lives in `src/lib/batteryMath.ts`; the 12V,
  24V/48V, and LiFePO4 calculator pages reuse the same
  `RuntimeCapacityCalculator` widget with different defaults.
- The Privacy Policy and Terms of Use pages use a placeholder contact email
  (`hello@batterysizing.xyz`) and a `[update this date]` placeholder — swap
  in your real details before publishing.
- Respects `prefers-reduced-motion` and disables cursor effects on touch
  devices.
