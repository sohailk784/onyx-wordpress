DEPLOY13 - Home V2: scientific-sources logo strip directly under the hero (server page 712). Section-only.

CONTENTS
- onyx-home-v2-logo-strip-wp-content.zip  -> unzip over the WordPress root (/onyxwp/). It holds:
    wp-content/uploads/2026/10/onyx-home-v2/logos/  nature.svg nih.svg pubchem.svg pubmed.svg sciencedirect.svg sciencedaily.png (new)
    wp-content/themes/onyx-child/assets/css/home-v2.css   = deploy11 CSS + appended scoped .hv2-logos rules (nothing else changed)
    wp-content/themes/onyx-child/functions.php            = deploy12 file (ver 1.4.1 + filemtime on home-v2.css), so the CSS URL changes and caches cannot serve the old copy. Already in place if deploy12 went up.
- onyx-home-v2-logo-strip.sql(.gz)  -> run on DB onyxwp. It inserts ONE section at position 2 of page 712's _elementor_data using JSON_ARRAY_INSERT. The other sections stay as they are.
  It runs only if page 712/home-v2 exists, its first section is the hero, and no strip exists yet, so it is safe to run twice. It clears page 712's Elementor CSS and cache meta.

STEPS
1. Back up: wp_postmeta row (post_id 712, meta_key _elementor_data) and themes/onyx-child/{functions.php,assets/css/home-v2.css}.
2. Upload and unzip the zip into /onyxwp/ (overwrite).
3. Import the SQL in phpMyAdmin (DB onyxwp). Expected output: rows_changed=1, section_2="hv2-sec hv2-logos", sections=10.
4. Elementor > Tools > Clear Files & Data. Purge the Cloudflare cache for /onyxwp/home-v2/ (or Purge Everything). Hard-refresh.
ROLLBACK: restore the _elementor_data backup (or delete the section in Elementor), and restore the two theme files.

BEHAVIOUR: Elementor HTML widget, CSS-only infinite marquee. Two identical logo sets are animated by -50%, which gives a seamless loop. It pauses on hover/focus.
prefers-reduced-motion: no animation, and the single set wraps centered (the duplicate is aria-hidden and hidden). Responsive: smaller tiles at 820px and below.
Not found in the live onyxhealth.net bundle (index-Bp280GFH.js). The caption comes from the reference image. The logos are official marks from Wikimedia Commons (Nature, PubChem, PubMed, ScienceDirect 2020, NIH mark) and sciencedaily.com/images/sd-logo.png. The NIH and NLM tiles are the NIH mark plus an HTML text label.
TESTED locally: deploy11 state + deploy12 functions + this SQL on page 711 -> data deep-equals the expected JSON, and a second run changes 0 rows. Screenshots: logo-strip-*@2x.png, server-state-*.png.
