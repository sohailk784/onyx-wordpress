# deploy22 – Home V2 full-page restyle (globalpenpeptide.com system), MOCKUP – not deployed

What I took from GPP: system-ui type stack; 400-weight large headings (40px, about 1.18 line-height); 1200px page width; about 88px section rhythm;
black 40px-radius pill buttons (#121212, hover #242833) and black-outline secondary pills; flat #f3f3f3 grey bands and #f2f2f2 media boxes;
hairline (#d6d6d6) dividers; FAQ as white shadowed cards with a chevron on the right; green #48b51f dots and checks.
ONYX touch kept: light-blue trust bar, ONYX-blue (#2a4f8f) eyebrows, numbered steps in light-blue circles, faint blue (#eef4fc) newsletter band. No GPP text, logos or images.

## Per section
1. Hero (pens): heading switched to regular weight in the GPP type, ink colour, muted sub-copy. Black pill kept. "Verify a batch code" stays removed.
2. Logo marquee: caption ink + medium weight (GPP caption style); grey band kept.
3. Trust bar: unchanged (light blue, approved).
4. Video section: unchanged layout (it already follows GPP). Headline size tuned to 34px so it sits on 2 lines.
5. Bestsellers carousel: GPP collection header (regular 32px heading, outline pill "View all peptides"); COA badge hidden for the flat GPP card look; carousel unchanged.
6. How we test: GPP "Easy to use" pattern. Big regular heading, black + outline pills, the 4 steps as one hairline-divided row (no boxed cards), numbers in light-blue circles.
7. Wholesale (B2B): the dark navy block became a GPP-style light grey band. Ink text, hairline checklist with green checks, rounded photo, black + outline pills.
8. FAQ: GPP pattern. Big heading on the left; questions as white shadowed cards with a right chevron.
9. Newsletter: GPP "subscribe" form (6px input + black pill) on a faint ONYX-blue band.
10. Removed: the placeholder "What our customers say" section that is live on page 712. It only holds example reviews ("Example name · Verified buyer"), and real data only applies.
Text: all ONYX text unchanged (same sections and strings as live, minus the removed placeholder reviews).

## Questions for the user (GPP patterns with no ONYX content, not invented)
- GPP "Shop by categories" tile grid: OK to add one using the existing WooCommerce categories? We would need a category image for each (none suitable locally).
- GPP "Pen vs standard vials" comparison table: is there an approved ONYX comparison (rows and claims)? Without one it is left out.
- GPP "Peptide Blog" cards: does ONYX have blog or articles to feature? None exist on onyxhealth.net or WP.
- GPP 4-icon features strip above the footer (delivery / payment / formulas / support): the ONYX trust bar already covers this. Add a second one?
- Reviews: add back only with verified reviews (e.g. a Trustpilot widget). Do you have a source?
- "How we test" COA card is marked "EXAMPLE LAYOUT" (batch ONX-XXXX). Replace it with a real COA image?

## Files
- before-full-desktop-1440.png (before = current approved state rendered locally, 1440 wide @1x)
- after-full-desktop@2x.png (2880 wide) + after-full-desktop-part1..5@2x.png
- after-full-mobile@2x.png (780 wide) + after-full-mobile-part1..5@2x.png
- onyx-home-v2-deploy22-wp-content.zip: home-v2.css (deploy21 CSS + GPP layer = work/gpp22.css), functions.php (deploy21), logos (everything from deploy17)
- onyx-home-v2-deploy22-full-page.sql(.gz): replaces the WHOLE _elementor_data of page 712 (slug-guarded, idempotent),
  SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci, binary comparisons only (fixes the deploy21 "illegal mix of collations").
  Expected: page_id=712 page_replaced=1 sections=9 carousel_present=1 reviews_left=0 verify_link_left=0. Re-run: page_replaced=0.
  This one SQL replaces deploy17/20/21 SQLs (any server state).
- Steps: back up page 712 _elementor_data and the theme files -> unzip into /onyxwp/ -> import the SQL -> Elementor Clear Files & Data -> purge Cloudflare -> hard refresh.
Note: the YouTube video shows black in the screenshots (headless browser, embed not loaded); it is the same embed as deploy20.
Local env note: the box was reset. I rebuilt the local WP from manual/onyx-database.sql.gz (MariaDB + PHP installed again, php -S on :8088), recreated page 711 (home-v2),
and set the HFE header/footer templates (432/434) to draft so the theme header and footer render like on live.
