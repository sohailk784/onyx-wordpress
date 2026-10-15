# deploy27: NEW page "Peptides 2" (slug peptides-2), mockup, NOT deployed
Layout follows the globalpenpeptide.com collection page:
- breadcrumb, then the title and intro, then category chips
- a filter bar (Filter: Format / Availability / Price dropdowns · Sort by · product count)
- a 4-column grid (2 columns on mobile, where the filters collapse into "Filter and sort"), then pagination

It's styled in the Home V2 deploy26 language: paper background, Cormorant/Hanken self-hosted fonts, slate eyebrows, light-blue accents and 24px rounded cards.
- Live data via the custom shortcode [onyx_shop2] (inc/shop2.php). It reads real products, categories, prices and badges, and uses the same base list as /peptides/ (programs excluded, 116 products, 16 per page). The intro text is the existing ONYX /peptides/ copy (≥99% unchanged).
- Filters and sorting use GET parameters (s2_cat, s2_form, s2_avail, s2_min, s2_max, s2_sort, s2_pg), so they work without JS.
- /peptides/ (archive-product.php) and the WooCommerce shop are untouched. The CSS and fonts load on peptides-2 only.
- Premium images: products with a premium studio image (currently 270, 48, 55, 62, 70, 73) show it on this page only. Featured images are never changed.
  - After ChatGPT images land in /workspace/onyx/chatgpt-out2/<product_id>.png, run homev2/make27.sh once.
  - Sources for that work: /workspace/onyx/chatgpt-src2/ (110 PNGs + READY.txt, named by product ID like chatgpt-out).

Files:
- onyx-peptides-2-deploy27.sql: creates the page if slug peptides-2 is missing, otherwise updates it. Idempotent, with the collation fix.
- onyx-peptides-2-deploy27-wp-content.zip: theme files (functions.php, inc/shop2.php, shop2.css, onyx-fonts.css and the woff2 fonts, home-v2.css, shop2-premium.json), premium webps, and every custom home-v2 asset including the pen-bpc-tb.png that deploy26 missed. The verification report is in work/zip-check.txt.
- Screenshots:
  - before-peptides-desktop@2x.png (+ -top)
  - after-peptides2-{desktop,mobile}-partN@2x.png
  - after-peptides2-filtered-pens-* (example: Dual Chamber Pens, price low→high)

## Update (Oct 15, 9:40 PM PKT): premium images, batch 1
- chatgpt-out2 has 57 PNGs (the _rejected/ folder is ignored). 63 of 116 products now use premium images on Peptides 2: these 57 plus the 6 earlier ones. The other 53 keep their originals.
- Every card now uses one cool light-blue studio backdrop, so original photos (multiplied onto it) sit consistently next to the premium images. Previously the originals were on a warm beige backdrop.
- The zip was rebuilt and re-verified: 216 files, all custom references present (see work/zip-check.txt).
- Mixed-grid spot checks: after-p2-page4-mixed-*, after-p2-page7-mixed-*.
- When more images arrive: run homev2/make27.sh again.
