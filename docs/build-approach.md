# Build approach

The live site (onyxhealth.net) is a React 19 + Vite + Tailwind v4 SPA backed by a Slim 4 PHP API.
Because the live site is newer than the supplied source archive, the **live site is the source of truth**.

1. **Catalogue** – `scripts/build/fetch-live.sh` snapshots the public API (`/products`, `/products/{slug}`,
   `/categories`, `/plans`). `scripts/build/mkcsv.py` turns it into a WooCommerce CSV
   (`data/products/onyx-products-woocommerce.csv`, 123 products / 149 rows). `scripts/lib/import-products.php`
   imports it and `scripts/lib/create-variations.php` builds the strength variations (`pa_strength`).
2. **Media** – `scripts/build/media_urls.py` collects image URLs from DOM dumps; `scripts/lib/import-media.php`
   sideloads them into the media library and writes a URL→attachment map.
3. **Pages** – `scripts/build/dump.mjs` (Playwright) renders each route at 1440/820/390 px and records the DOM
   tree with computed styles. `scripts/build/convert.py` converts it into Elementor *container* JSON with
   desktop/tablet/mobile settings; `build_pages.py` applies per-page overrides (forms → Fluent Forms
   shortcodes, FAQ → accordion, product grids → `[products]`, calculator / check-code → shortcodes).
   `build_hf.py` builds the header, footer and shop hero templates.
4. **Import** – `scripts/lib/import-elementor-pages.php` and `import-templates.php` save the JSON through
   Elementor's document API, so every page is fully editable in Elementor.
5. **Round trip** – after editing in wp-admin, `scripts/export-elementor.php` writes the JSON back to
   `data/elementor/` with `{{SITE_URL}}` placeholders.

Build-time work files live in `$ONYX_WORK` (default `./build-work`, git-ignored).
