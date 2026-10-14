# Progress (identical-to-live task)
- DONE: home pen carousel [onyx_pen_carousel] + tabs [onyx_product_tabs]; hero gradient span CSS
- DONE: restored Elementor HTML widgets (SVG icons) stripped on save by WP-CLI w/o user (tools/htmlfix.php; edump now sets user 1)
- DONE: deploy2/onyx-db-update.sql(+.gz) from onyxmig staging (onyxmig = copy of onyxlocal w/ https://euro-nutra.com/onyxwp). Excludes wp_users/usermeta/wc_sessions.
- NEXT: zip changed files (/tmp/changed.txt via rsync vs wpc-base), screens2, git commit+push
- DONE: zip + screens2
- DONE: pushed 6762095, 3243a2d to sohailk784/onyx-wordpress main
## Round 3 (full parity) started
- hero overlay/blobs/scroll hint DONE (css+theme.js)
- home lab pills, research areas, quality/supply images DONE
- contact cards (html widgets) + fluent-skin.css (all forms) + contact form field layout DONE
- wholesale: form header html widget, password fields + WC account creation hook (inc/woocommerce.php), wholesale fluent CSS, trust bar icons restored DONE
- restored 20 stripped inline SVGs (tools/svgfix.mjs from live) DONE
- doctor: FAQ collapsible (.onyx-acc + theme.js), button alignment DONE (images slightly shorter than live)
- policy pages: onyx-toc/onyx-clause/onyx-sechead classes (tools/policyfix.py) + CSS DONE
- calc line-heights + check-code Verify (onyx-core plugin CSS) DONE
- mobile 390 shots done; fixes: hero letter-spacing em, fluent mobile stacking, doctor image radius
- checkcode mobile stacking; contact pill svg fix; country placeholder. NEXT: reshoot all local, sdiff_all, bundle, screens2, git
- bundle rebuilt (round3), screens2 refreshed (+mobile), pushed 5ba19f7
- sdiff re-run done (see sdiff/*.json). ROUND 3 COMPLETE.
## Round 4 (checkout parity) started

### Round 4 result (checkout parity) – done
- Classic WC checkout ported from live/React: onyx-child/inc/checkout.php, woocommerce/checkout/{form-checkout,payment,thankyou}.php, assets/js/onyx-checkout.js, CSS in onyx-tw-fixes.css.
- 4 sections (Contact, Shipping address w/ country flag + phone dial picker, Shipping method cards, Payment cards per USDT network), terms checkbox + hint, "Place order · £total" with lock; summary aside with qty badge, variant, coupon inline Apply (wc-ajax), totals, customs box, SSL row, trust badges. Live validation messages inline (client) + mapped server notices.
- Thank-you = React "Your order has been created" + PaymentProofForm (QR, copy, amount due, tx hash OR receipt, pending/under-review state); paid orders show "Payment successful".
- Crypto plugin: proof accepts tx hash alone; redirect back to order-received for guests.
- Tested guest order #709 locally (TRC20, file+txid proof, meta OK) then deleted it + proof file.
- deploy3/onyx-checkout-wp-content.zip (13 files, no SQL needed). screens3/ side-by-sides (thank-you shots redacted).
- Remaining: no Credit Card gateway locally; no "≈ USDT" conversion; header top-bar text rotates; onyx-app.css regenerated from new live CSS (index-CKgInHmG.css).
