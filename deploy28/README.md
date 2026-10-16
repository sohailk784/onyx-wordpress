# deploy28 – "<Title> 2" pages (premium style), NOT deployed

Every page gets a styled copy named `<Original> 2` with slug `<slug>-2`, built the same way as Peptides 2. The text is identical to the original: the SQL copies `post_content` and the Elementor meta from the live original inside MySQL when it runs. The originals, Home (415), Home V2, Peptides 2 and the WooCommerce settings are not modified.

## Pages (22 copies; live and local inventories match, local was missing only b2b-login and it was recreated)
| original | new copy |
|---|---|
| cart, checkout, my-account, wishlist, b2b-login | cart-2, checkout-2, my-account-2, wishlist-2, b2b-login-2 |
| what-are-peptides, faq, payment-delivery, product-returns, contact, wholesale-application, independent-doctor-consultation | …-2 |
| terms-conditions, privacy-policy, refund-policy, shipping-policy, cookies-policy, accessibility-policy | …-2 |
| product-catalogue, check-code, analytical-reference-calculator, peptide-plans | …-2 |
Skipped: home (front page, must not be touched), peptides (the shop; Peptides 2 already exists), home-v2 and peptides-2 (already v2).
There is no "About" page on the live site.

## How the WooCommerce flow works without changing Woo settings (inc/pages2.php)
- A "v2 context" is any `-2` page, Peptides 2, or a URL carrying `?v2=1`. In that context only:
  - product links get `?v2=1`;
  - the cart URL goes to /cart-2/, the checkout URL to /checkout-2/ and the account URL to /my-account-2/.
- The `[woocommerce_checkout]` shortcode on checkout-2 is a working checkout. A hidden `onyx_v2` field marks the order (meta `_onyx_v2`), and its thank-you page opens at `/checkout-2/order-received/<id>/?key=…`. This is the same Woo endpoint, styled.
- Single product: it is a template, so the v2 style is applied only when `?v2=1` is present, which links from the v2 pages add. Without the flag the product page is unchanged.
- Everybody else (original pages, menus, direct links) gets exactly the current site. A later switch-over would just mean pointing Woo's Cart, Checkout and My account settings at the -2 pages (or renaming slugs).
- Styling: `assets/css/pages2.css`, enqueued with the self-hosted fonts only on v2 surfaces. It is scoped to `body.onyx-v2`. A small footer script tags Elementor boxes as card, tint or stat for the CSS; it changes no text.

## Files
- `onyx-pages-2-deploy28-wp-content.zip` (302 files, cumulative = the deploy27 set plus `inc/pages2.php`, `assets/css/pages2.css` and the new `functions.php`). Check: `work/zip-check.txt`.
- `work/onyx-pages-2-deploy28.sql`: create-or-update for all 22 pages. It is idempotent, uses `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci` and BINARY slug matching, and re-syncs a copy if its original changes.
  - Status line: `pages_created pages_updated meta_changed originals_missing v2_pages content_identical`.
  - Local runs: 1st run `22 0 124 0 22 22`; 2nd run `0 0 0 0 22 22`.
- `work/onyx-pages-2-deploy28-rollback.sql`: deletes only pages carrying the `_onyx_v2_source` marker, plus their meta. Tested twice (22 removed, then 0), with no orphan meta left and Peptides 2 kept.
- Rollback for files: re-upload the deploy27 zip (restores the previous functions.php; `inc/pages2.php` and `pages2.css` can stay, since they are unused).
- `crops/`: desktop (1440) and mobile (390) @2x parts for every page, plus `product-v2` (product page with ?v2=1) and `thankyou-v2`.

## Local test (add to cart → checkout → thank-you)
Peptides 2 → product?v2=1 → Add to cart → cart-2 → Checkout (links to /checkout-2/) → Place order with the local USDT gateway → `/checkout-2/order-received/735/…` styled. The original /cart/ still links to /checkout/. Test orders 734 and 735 exist on the LOCAL DB only.

## Fix: product images clipped on Peptides 2 (cards stay 3:4 portrait)
- Cause: premium images were drawn edge to edge (square image at 100% card width, scaled 1.04–1.08), and originals were scaled 1.3–1.34×. Pens or boxes touching the image edge were cut off on the left or right.
- Fix (`shop2.css` deploy28b block plus `shop2.php`):
  - all scale-ups removed; every image uses `object-fit: contain`;
  - premium images inset to 92% width (94% on mobile), with a soft 6% side and 14% top/bottom feather;
  - originals use 9% padding (7% on mobile);
  - each premium card's backdrop gradient is taken from that image's own edge colours (`assets/data/shop2-premium-bg.json`), so the inset has no square edge.
- Detection: `work/fix-clip/clip.mjs` shoots every card on every page at 1440 and 390; `edge.py` flags pixels touching the card edge.
  - Before: 78 cards flagged at desktop, 81 at mobile, 82 distinct products (`fix-clip-crops/_affected.txt`).
  - After: no product pixels touch an edge. The 13 remaining flags are symmetric backdrop tone, checked visually.
- Before/after crops: `fix-clip-crops/<product>-{desktop,mobile}-before-after.png` and `_sheet-*.png`.
- Zip rebuilt cumulatively (deploy27 + deploy28 + this fix): 303 files, check in `work/zip-check.txt`.

## Fix: sparkle icon beside "Contact" on Contact 2 removed
- Cause: the original Contact hero has an inline sparkle SVG inside the "Contact" eyebrow label (part of the page content, copied 1:1 into Contact 2). The v2 styling made it stand out as a Gemini-like star.
- Fix (`pages2.css` and `inc/pages2.php`): hides the leading glyph in eyebrow labels that sit directly before a heading on -2 pages, using CSS `:has(+ .elementor-widget-heading)` plus a JS tag as fallback.
  - The label text is untouched and the original /contact/ page is unchanged.
  - Only Contact 2 matches; the other -2 pages were checked with `work/hidden.mjs`.
  - Other icons (policy "Last updated", card icons) stay.
- Crops: `fix-sparkle-crops/contact-2-{desktop,mobile}-before-after.png`.
- Local note: the local DB copy had the SVG path data stripped by the import, so it was restored from live on local page 456 only, to reproduce the issue.

## Fix: "On this page" / sidebar card cut off → full card, sticky left sidebar
- Checked all 22 -2 pages (`work/side.mjs`). Pages with a sidebar card beside long content:
  - terms-conditions-2, privacy-policy-2, refund-policy-2, shipping-policy-2, cookies-policy-2, accessibility-policy-2 (the "On this page" table of contents);
  - product-catalogue-2 ("Categories" sidebar).
  - No other -2 page has one.
- Why it was cut off:
  - the v2 heading styles turned the small "On this page" label into a large serif h2;
  - the short-heading `nowrap` rule then pushed it under the content card;
  - the table of contents (taller than the viewport on Terms and Privacy) had no internal scroll.
- Why it wasn't sticky:
  - the Elementor sidebar column stretches to the full grid height, so sticky has no room to move;
  - no `overflow: hidden` ancestor (body uses `overflow-x: clip`, which doesn't break sticky).
- Fix (`pages2.css` and `inc/pages2.php`):
  - JS tags the sidebar column (`onyx-v2-sticky`), found from the heading, and sets `--onyx-v2-hdr` to the sticky header's height;
  - CSS: `position: sticky; top: header + 24px; align-self: start; max-height: 100vh - header - 48px; overflow-y: auto`;
  - "On this page" becomes a small uppercase label inside a white card; Categories keeps its own card;
  - the text is unchanged.
- Mobile/tablet: these sidebars are hidden on tablet and mobile by the original page's own Elementor settings, so they are not shown there. The settings were left as authored.
- Crops: `fix-toc-crops/{before,after}-<page>-{desktop,mobile}-{start,mid,late}.png`. Card position per scroll step: `_before-visibility.txt` and `_after-visibility.txt` (after: the card is pinned at top ≈149px at mid-scroll on every page).

## Fix: sidebar label size and spacing (7 pages)
- The "On this page" label and the Product Catalogue 2 "Categories" label are now 12.5px uppercase with 0.22em letter-spacing in slate, matching the eyebrow labels. Each sits inside its white card with 24px/18px padding.
- The sidebar-to-content column gap is forced to 40px; this overrides the v2 grid gap, which was 18px on the catalogue.
- Checked at 1025, 1100, 1200, 1280, 1366 and 1440 on all 7 pages (`fix-toc-label-crops/_gap-label-check.txt`): gap 40px everywhere, label fully inside its card. At 1024 and below the original pages hide the sidebar.
- After crops: `fix-toc-label-crops/after-{terms-conditions-2,privacy-policy-2,product-catalogue-2}-{1440,1280}-{start,mid}.png`.

## deploy28f
- Peptides 2: every card uses one neutral backdrop (#f7f6f9, sampled from globalpenpeptide.com). The products' own white-background packshots are multiplied onto it. Blue-backdrop studio renders are dropped, except Bacteriostatic Water (its own photo is dark), which keeps its render under a soft radial mask. Dark-photo products (6 vials and 6 programmes) show full-bleed. All card hover motion is removed.
- -2 pages: Woo product grids use a CSS grid. "View Details" is a contained pill (width minus 28px, 14px inset), so nothing overlaps from 390 to 1440. Card hover effects are removed. The plan-card image overlays are hidden.
- `onyx-pages-2-deploy28f-draft3.sql` / `-rollback.sql`: set independent-doctor-consultation-2, product-catalogue-2 and analytical-reference-calculator-2 to draft, or back to publish. No -2 page, header or footer links to them.
- Home V2 hero (neutral backdrop, plain badge) is BUILD ONLY, in `optional-home-v2/home-v2.css`. It is NOT in the zip.
- Peptide Plans 2: no image is missing (all 6 files exist and return 200). The specs are in `peptide-plans-imgs/PROMPTS.txt`.

## deploy28g
- Home V2 hero: one uniform #f7f5f0 background across the text and the pens, with no panel, orb, stripes, gradient or seam. The badge is plain type. This change is now IN the zip (CSS only, scoped to `.hv2-hero2`).
- Peptides 2 grid matches GPP: 1100px max, 4 columns, 8px column gap, 2:3 image area, 3 columns at 750–989px, 2 columns below 750px. 20 products per page.
- `onyx-pages-2-deploy28g-wording.sql` / `-rollback.sql`: 15 spelling, grammar and punctuation fixes on -2 pages only (guarded REPLACE, idempotent). The list is in the run report.

## deploy28h
- Home V2 "Shop by category": cards match Peptides 2/GPP. Grid is 1100px max with 8px column gaps: 6 columns on desktop, 3 at 750–1023px, 2 on mobile. Each card has a 2:3 #f7f6f9 image area with the full image visible, the name below as plain type, and no hover. The ink arrow circle is hidden. Text unchanged.
- The category tiles (`uploads/2026/10/onyx-home-v2/tiles/onyx-tile-*.webp`) had blue studio backdrops. They are replaced in place (same filenames) by neutral cut-outs made from each product's own white-background photo. The dual-chamber tile uses just the pen from the product's diagram photo. No page data changes.
- Bestsellers: 4 cards across 1100px with 8px gaps (2 on mobile). Each has a 2:3 #f7f6f9 image area, a full-width outline View Details button, and no hover.
- `onyx-pages-2-deploy28h-mailto.sql` / `-rollback.sql`: the accessibility-policy-2 email link now points to mailto:sales@onyxhealth.net. Only the -2 page is changed; the original keeps its link.
- Server note: a stray folder `public_html/onyxwp/wp-conte/` exists. Nothing in this repo or zip relates to it.
