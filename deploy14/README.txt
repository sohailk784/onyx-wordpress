DEPLOY14 - replaces deploy13. Server page 712 (home-v2), starting from the deploy11+deploy12 state:
 (1) inserts the scientific-sources logo strip as section 2, right after the hero
 (2) removes the hero "Verify a batch code" link widget. "Shop research peptides" stays.
FILES: onyx-home-v2-deploy14-wp-content.zip (identical to the deploy13 zip: logos, home-v2.css with the strip CSS, deploy12 functions.php)
       onyx-home-v2-deploy14.sql(.gz)
STEPS: back up page 712's _elementor_data and the two theme files -> unzip into /onyxwp/ -> import the SQL into onyxwp -> Elementor > Tools > Clear Files & Data -> purge Cloudflare -> hard refresh.
EXPECTED SQL RESULT (first run):   page_id=712 strip_inserted=1 link_removed=1 verify_link_left=0 section_2=hv2-sec hv2-logos sections=10
RE-RUN (idempotent):               strip_inserted=0 link_removed=0 verify_link_left=0 (no change)
If deploy13 SQL was already run: strip_inserted=0 link_removed=1.
Guards: page 712/home-v2 exists, the first section is the hero, the strip is not already present, and the widget being removed has the text "Verify a batch code".
TESTED locally: page 711 in the deploy11 state + this SQL deep-equals the expected JSON; the second run changes 0 rows. Screenshots: server-state-1440.png / server-state-390.png.
