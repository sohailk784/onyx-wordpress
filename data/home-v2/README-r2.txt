ONYX deploy6 – Home V2 round 2 (premium redesign of the existing page slug home-v2, server ID 712). Front page untouched.

Files
  onyx-home-v2-r2-wp-content.zip  8 files: themes/onyx-child/functions.php (home-v2.css enqueue + 600px product
                                  images on home-v2 only), themes/onyx-child/assets/css/home-v2.css (rewritten),
                                  uploads/2026/10/onyx-home-v2/{pen-sema,pen-bpc-tb,pen-ghk}.png, coa-bpc157.webp,
                                  manufacture.webp (video poster), wholesale-hero.webp
  onyx-home-v2-r2-update.sql(.gz) UPDATEs _elementor_data of post 712 (only if ID 712 has slug home-v2),
                                  deletes its _elementor_css/_elementor_element_cache/_elementor_page_assets.

Server steps
1. cPanel > File Manager > public_html/onyxwp/ > Upload onyx-home-v2-r2-wp-content.zip > Extract (overwrite).
2. phpMyAdmin > onyxwp > Import onyx-home-v2-r2-update.sql > Go  (expect updated_page_id=712, data_len≈33752).
3. WP Admin > Elementor > Tools > Clear Files & Data (and Elementor > Settings > Performance: element cache, if enabled).
4. Hard-refresh https://euro-nutra.com/onyxwp/home-v2/ (CSS URL uses ?ver=1.4.0 – Ctrl+F5 / purge any cache plugin).
Rollback: re-run deploy5/onyx-home-v2-page.sql is NOT an update; instead restore via Pages > Home V2 > Edit with Elementor > History/Revisions.
