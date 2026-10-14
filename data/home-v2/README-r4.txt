ONYX deploy8 – Home V2 round 4: organised diagonal hero (parallel pens, aligned boxes).
NOT deployed – waiting for approval. Supersedes deploy6 + deploy7 (contains all deploy6 changes too).

Files
  onyx-home-v2-r4-wp-content.zip  functions.php, home-v2.css, uploads/2026/10/onyx-home-v2/* (incl. new hero-composite-v2.webp 1800x1400)
  onyx-home-v2-r4-update.sql(.gz) UPDATEs _elementor_data of page 712 (only if slug home-v2), clears its Elementor CSS/cache meta.

Server steps
1. cPanel > File Manager > public_html/onyxwp/ > Upload onyx-home-v2-r4-wp-content.zip > Extract (overwrite).
2. phpMyAdmin > onyxwp > Import onyx-home-v2-r4-update.sql > Go  (expect updated_page_id = 712).
3. WP Admin > Elementor > Tools > Clear Files & Data. 4. Ctrl+F5 https://euro-nutra.com/onyxwp/home-v2/
