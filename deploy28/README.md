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
