DEPLOY15 - Home V2 trust bar redesign (section-only, server page 712). AWAITING OPTION CHOICE. Not deployed.
Wording (facts only): 24h dispatch / Shipped within 24 hours; Third-party tested / HPLC & LC-MS analysis; >=99% purity / Third-party reported;
 COA per batch / Certificate of analysis; Authenticity codes / Verify packs online; UK & EU made / Certified facilities. Inline SVG line icons (Lucide-style).
OPTION A: navy proof band, 6 items in one row, icon rings. Mobile: swipe row with scroll-snap.
OPTION B: light band with white icon cards and gradient icon chips. Mobile: clean 2x3 grid.
Images: before-vs-after-desktop.png, option-{a,b}-{desktop,mobile}@2x.png, before-*.png, server-state-a-*.png
FILES: onyx-home-v2-trustbar-wp-content.zip. It holds home-v2.css (deploy13 CSS + trust bar CSS for both options), the logos and the deploy12 functions.php, and is a superset of the deploy14 zip.
       onyx-home-v2-trustbar-option-a.sql / -option-b.sql. Run only the chosen one.
ORDER: run deploy14 first (zip + SQL), then this zip (overwrite) + the chosen SQL. Then Elementor > Tools > Clear Files & Data, purge Cloudflare, hard refresh.
EXPECTED: first run trust_replaced=1, option_x_present=1, old_trust_left=0, sections=10. Re-run: trust_replaced=0. Running the other option's SQL later switches A<->B.
It also works if deploy14 has not been run (it matches positions 1-3). TESTED locally: deploy11 -> deploy14 SQL -> A,A,B,B,A, and the final data deep-equals the expected JSON.
