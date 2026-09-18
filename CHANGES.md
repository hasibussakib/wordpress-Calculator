# Fix history

## Round 2: why the last fix broke deploys on both Netlify and Vercel

**Root cause: a Puppeteer-based prerender step, added in round 1, launched a
real headless Chromium during the build.** That's a very common way to break
CI builds on sandboxed platforms like Netlify/Vercel — Chromium can fail to
launch under their build-container sandboxing, and/or the ~170MB Chromium
download during `npm install` can fail or time out. Since both platforms
failed the same way, this was almost certainly it.

**Compounding factor:** round 1 added `puppeteer` to `package.json` but
`package-lock.json` was never regenerated (no network access in the sandbox
this was built in). If either platform's install step uses `npm ci` — which
strictly requires the lockfile to match `package.json` — that mismatch alone
would fail the build immediately, before Puppeteer ever got a chance to run.

## The actual fix: remove the browser dependency entirely

Puppeteer and the old `scripts/prerender.mjs` browser-automation approach are
gone. Prerendering is now done with **React's own server-rendering API**
(`react-dom/server`), which:
- needs **no new dependency** — `react-dom` and `react-router-dom` are
  already in `package.json`, and both ship server-rendering entry points
  (`react-dom/server`, `react-router-dom/server`).
- launches **no browser, no subprocess, no port, no timing race** — it's a
  synchronous function call in plain Node.
- can't hit the sandboxing/Chromium-download issues that broke the previous
  approach, because there's no browser involved at all.

### New/changed files

- **`src/routesConfig.tsx`** (new) — the single source of truth pairing each
  route's path, page component, and page metadata (title/description) in
  one array. Both the client app and the build-time renderer read from this,
  so there's no way for the two to drift out of sync.
- **27 page files in `src/pages/`** — each now exports its title/description
  as `export const meta = { title, description }` instead of an inline
  object literal inside `setPageMeta({...})`. Purely mechanical change,
  content is byte-for-byte identical to before; this just makes the same
  data importable at build time.
- **`src/App.tsx`** — now builds its `<Routes>` from `routesConfig` via
  `.map()` instead of a hand-written list of 27 `<Route>` elements.
- **`src/lib/routes.ts`** — now derives `LIVE_ROUTES` from `routesConfig`
  instead of maintaining its own separate hardcoded list.
- **`src/entry-server.tsx`** (new) — a build-time-only entry point. Renders
  any given route to an HTML string with `renderToStaticMarkup` +
  `StaticRouter`. Never shipped to the browser.
- **`scripts/prerender.mjs`** (rewritten, no more Puppeteer) — after the
  client build and a new SSR build, this script renders every route via
  `entry-server.js`, injects the route's own title/description/canonical/OG
  tags into a copy of `dist/index.html`, and writes the result to
  `dist/<route>/index.html`.
- **`package.json`** — `puppeteer` removed. `build` now runs:
  `tsc -b && vite build && vite build --ssr src/entry-server.tsx --outDir dist-server && node scripts/prerender.mjs && cp dist/index.html dist/404.html`
  (split into `build:client` / `build:ssr` scripts for clarity).
- **`package-lock.json`** — back in sync with `package.json` (verified
  programmatically) since no new dependency was introduced this round.

### Why this is safe to deploy
- No new dependencies → nothing new for `npm ci`/`npm install` to fail on.
- No browser/subprocess/network calls during build → nothing for a
  sandboxed CI container to block.
- Checked every component for `window`/`document` access outside of
  `useEffect`/event handlers (the only things that would break server
  rendering) — none found; the codebase is SSR-safe as-is.
- All 9 HTML-tag replacement patterns in `prerender.mjs` were tested against
  the real `index.html` template and matched correctly.
- All 27 page files verified after the automated refactor: brace-balanced,
  no leftover unconverted `setPageMeta({...})` calls, exact title/description
  text preserved (including the one page with an apostrophe in its title).

## What you need to do

1. `npm install` (safe now — no new/changed dependencies, but do this if you
   haven't since round 1 to make sure `puppeteer` is actually removed from
   your local `node_modules`/lockfile state).
2. `npm run build` locally and confirm it completes. You should see:
   - normal `vite build` output (client)
   - a second, smaller `vite build` pass for `dist-server`
   - "Prerendering 27 routes..." followed by 27 checkmark lines
3. Spot-check: `grep -E "canonical|<title>" dist/battery-backup-calculator/index.html`
   should show that page's own title and a canonical URL ending in
   `/battery-backup-calculator/`.
4. Push and redeploy on Netlify/Vercel. If either still fails, send me the
   actual build log this time — with the browser dependency gone, any
   further failure is very likely something more mundane (Node version,
   TypeScript error, etc.) that the log will show directly.

## Round 1 recap (unchanged from before)

- `src/lib/setPageMeta.ts` sets canonical, `og:url`, and twitter tags in
  addition to title/description, auto-derived from the current route.
- `vercel.json` checks the filesystem for a matching static file before
  falling back to `index.html`, so the new per-route static files won't be
  overridden by the SPA fallback rewrite.
