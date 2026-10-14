ONYX deploy10 – Home V2 round 6: pens-only cascade hero with a pen needle attached to each pen.
NOT deployed – waiting for approval. Supersedes deploy6–9 (contains all deploy6 changes too).

Files
  onyx-home-v2-r6-wp-content.zip  functions.php, home-v2.css, uploads/2026/10/onyx-home-v2/* (incl. new hero-pens-v4.webp + hero-pens-v4.png 1600x1240 transparent)
  onyx-home-v2-r6-update.sql(.gz) UPDATEs _elementor_data of page 712 (only if slug home-v2), clears its Elementor CSS/cache meta.

Server steps
1. cPanel > File Manager > public_html/onyxwp/ > Upload onyx-home-v2-r6-wp-content.zip > Extract (overwrite).
2. phpMyAdmin > onyxwp > Import onyx-home-v2-r6-update.sql > Go  (expect updated_page_id = 712).
3. WP Admin > Elementor > Tools > Clear Files & Data. 4. Ctrl+F5 https://euro-nutra.com/onyxwp/home-v2/
