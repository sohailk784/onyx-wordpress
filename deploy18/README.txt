DEPLOY18 - CSS only (no SQL). Trust bar band recoloured to ONYX navy gradient #2a4f8f -> #1e3a6e. Icons light blue #b9d3ff (green #48b51f was only 3.0:1 on navy).
Titles #ffffff (8.0-11.1:1), lines #d6e2f5 (6.1-8.5:1), icons #b9d3ff (5.3-7.3:1); hover = white ring fill with navy icon. Hero button stays black.
Apply AFTER deploy17: unzip onyx-home-v2-deploy18-css-only.zip into /onyxwp/ (overwrites themes/onyx-child/assets/css/home-v2.css = deploy17 CSS + navy override),
then purge Cloudflare + hard refresh. The deploy12 functions.php (filemtime versioning) busts the CSS cache automatically.
