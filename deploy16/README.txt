DEPLOY16 - Trust bar Option A recoloured with the globalpenpeptide.com palette (colours only, no assets/branding). Not deployed.
PALETTE (from that site's Shopify theme CSS, fetched 2026-10-14):
 #121212 band base. Source: --color-foreground / --color-button / --pp-heading / color-scheme-4 background (black buttons, announcement bar)
 #242833 band top of gradient. Source: color-scheme-3 background
 #48b51f icon stroke, ring, hover fill. Source: --pp-trust-dot / --pp-icon-check / --pp-item-dot (their green)
 #d0f0c9 title colour on hover. Source: rgba(208,240,201) light-green tint in their CSS
 #ffffff titles. Source: color-scheme-1 background / button text
 #bdbdbd description lines. Source: their grey scale, close to scheme-1 background-contrast #bfbfbf
 #d6d6d6 hairlines (at low alpha). Source: --pp-divider
 Not used: #ff6f00 orange (--pp-accent, eyebrow text) and #f3f3f3 (scheme-2 grey bg).
 Lime and blue appear only in their hero images, not in their CSS. Lime is represented by #48b51f.
CONTRAST (WCAG): white/#121212 18.7:1; #bdbdbd/#121212 10.0:1 (7.8:1 on #242833); #48b51f/#121212 7.1:1; hover #121212 icon on #48b51f 7.1:1. All AA, most AAA.
FILES: onyx-home-v2-trustbar-a-gpp-wp-content.zip (home-v2.css with the recolour, a superset of deploy14/15) + onyx-home-v2-trustbar-a.sql (= deploy15 option A).
ORDER: deploy14 zip+SQL -> this zip -> this SQL -> Elementor Clear Files & Data -> purge Cloudflare -> hard refresh.
EXPECTED SQL: first run trust_replaced=1, option_a_present=1, old_trust_left=0, sections=10. Re-run: trust_replaced=0.
If deploy15 option A SQL was already run, only upload the zip (SQL gives 0 = fine).
