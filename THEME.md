# Batterysizing WordPress theme

Installable theme in `batterysizing/`. Recreates the BatterySizing.xyz site from this repo: 12 calculators, appliance load estimator, what-if runtime comparison, 7 guides, FAQ, About, Contact, Privacy and Terms.

## Install

1. Copy the `batterysizing` folder into `wp-content/themes/`.
2. Appearance → Themes → **Activate Batterysizing**.
3. Appearance → **Batterysizing Setup** → **Import demo content**.

That creates every page (matching the original routes), assigns menus, and sets the static front page.

## What maps to what

| Original React route | WordPress page |
| --- | --- |
| `/` | Front page (`front-page.php`) |
| `/battery-calculator/` … `/wh-to-ah-calculator/` | Calculator pages (`template-calculator.php`) |
| `/calculators/` | Hub |
| `/guides/…` | Guide pages (`template-guide.php`) |
| `/faq/` `/about/` `/contact/` `/privacy-policy/` `/terms-of-use/` | Matching pages |

Design tokens (navy `#0A1628`, brand `#2563EB`, Inter, card shadows, float & charge animations) live in `batterysizing/assets/css/theme.css`, ported from `tailwind.config.js`.

Shared math (the old `src/lib/batteryMath.ts`) is `batterysizing/assets/js/battery-math.js`.

## Preview without WordPress

Open `batterysizing/preview.html` (or serve the `batterysizing/` folder). It is a static extract of the homepage: hero, 12 calculator cards, load estimator, what-if comparison, guides, FAQ, CTA.
