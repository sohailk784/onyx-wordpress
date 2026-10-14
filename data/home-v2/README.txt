ONYX deploy5 – "Home V2" (new, separate Elementor page; existing homepage / front page untouched)

Files
  onyx-home-v2-wp-content.zip  3 files: themes/onyx-child/functions.php (enqueues home-v2.css only on slug home-v2),
                               themes/onyx-child/assets/css/home-v2.css, uploads/2026/10/onyx-home-v2/wholesale-hero.webp
  onyx-home-v2-page.sql(.gz)   INSERTs one page (title "Home V2", slug home-v2) + 6 postmeta rows (Elementor data).
                               Only INSERT/UPDATE of the new row; skips everything if slug home-v2 already exists.

Server steps
1. cPanel > File Manager > public_html/onyxwp/ : upload onyx-home-v2-wp-content.zip > Extract (overwrite functions.php).
2. cPanel > phpMyAdmin > database onyxwp > Import (or SQL tab) > onyx-home-v2-page.sql > Go.
   Result shows new_home_v2_page_id.
3. WP Admin > Elementor > Tools > Regenerate CSS & Data (Clear Files & Data).
4. Open https://euro-nutra.com/onyxwp/home-v2/ ; edit via Pages > Home V2 > Edit with Elementor.
Do NOT change Settings > Reading (front page stays "Home").

Rollback: delete page "Home V2" in WP Admin (Trash + delete permanently), delete assets/css/home-v2.css; the
functions.php enqueue is harmless without the page.
Assumes server IDs match local (products 270,272,268,255; attachment 269) – true since deploy2 DB copy.
