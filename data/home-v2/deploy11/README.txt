ONYX deploy11 – swap ONLY the hero of server Home V2 (page 712, currently deploy5/round-1 content) for the r6 hero
(white hero, five diagonal pens with needles, left text + buttons). All other sections stay byte-identical to deploy5.
NOT deployed – awaiting approval. (Use this INSTEAD of deploy6–10, which would replace the whole page.)

Files
  onyx-home-v2-hero-only-wp-content.zip
     wp-content/themes/onyx-child/assets/css/home-v2.css  = deploy5 CSS unchanged + appended r6-hero rules scoped to .hv2-hero2
     wp-content/uploads/2026/10/onyx-home-v2/hero-pens-v4.webp / .png  (hero image, 1600x1240, transparent)
  onyx-home-v2-hero-only-update.sql(.gz)
     UPDATE _elementor_data of page 712 (guard: ID 712 AND slug home-v2) = deploy5 JSON with section[0] replaced by the r6 hero;
     deletes _elementor_css, _elementor_element_cache, _elementor_page_assets for 712.
  functions.php is NOT included (deploy5 version already enqueues home-v2.css on slug home-v2).

Server steps
1. cPanel > File Manager > public_html/onyxwp/ > Upload onyx-home-v2-hero-only-wp-content.zip > Extract (overwrite home-v2.css).
2. phpMyAdmin > onyxwp > Import onyx-home-v2-hero-only-update.sql > Go  (expect updated_page_id = 712, data_len ≈ 35.8k).
3. WP Admin > Elementor > Tools > Clear Files & Data.
4. Ctrl+F5 https://euro-nutra.com/onyxwp/home-v2/

Local verification (page 711 put in deploy5 state, then this SQL+CSS applied):
  1440: pixel diff confined to the hero band (y 125–724); everything below identical.
  390:  diff only in the hero (taller/shorter hero shifts page); sections below identical.
  See before-vs-after-1440.png / -390.png, deploy5-state-*.png, deploy11-result-*.png.
Rollback: re-import deploy5/onyx-home-v2-page.sql is insert-only; instead restore via Elementor revisions, and re-upload the deploy5 home-v2.css.
