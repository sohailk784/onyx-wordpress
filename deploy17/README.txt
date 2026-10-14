DEPLOY17 (final) - for the server in the deploy14 state. Not deployed.
1. Back up page 712 _elementor_data + themes/onyx-child/{functions.php,assets/css/home-v2.css}.
2. Unzip onyx-home-v2-deploy17-wp-content.zip into /onyxwp/ (overwrite). It holds the final home-v2.css (logo strip + trust bar A in gpp colours + black hero button), the logos and the deploy12 functions.php.
3. Import onyx-home-v2-deploy17.sql into onyxwp (trust bar option A, section-only).
   Expected: page_id=712 trust_replaced=1 option_a_present=1 old_trust_left=0 sections=10. Re-run: trust_replaced=0.
4. Elementor > Tools > Clear Files & Data, purge Cloudflare, hard refresh.
Hero button: #121212, hover/focus #242833 with a green #48b51f ring (CSS only, scoped to .hv2-hero2).
