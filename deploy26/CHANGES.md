# Home V2 deploy26: pre-live fixes on deploy25 (mockup, NOT deployed)
1. Category tiles: every name now sits on a frosted, rounded plate (blur and 78% white) inset in the tile, so it never collides with the photo. On the Dual Chamber Pens tile the pen photo is scaled into the area above the plate and edge-blended into a matching blue backdrop.
2. Performance:
   - Fonts are self-hosted as woff2 in onyx-child/assets/fonts: Cormorant Garamond normal and italic, Hanken Grotesk, latin and latin-ext. They load through assets/css/onyx-fonts.css with font-display: swap and are enqueued on home-v2 only.
   - The serif and sans latin files are preloaded in the head. No request goes to Google Fonts any more, and Manrope has been dropped.
   - Every image below the hero now has loading="lazy" and decoding="async": logos, tiles, the pen image and all WooCommerce loop images. The first three carousel images used to be left eager; a functions.php filter now makes them lazy on home-v2 only.
   - The hero image stays eager with fetchpriority="high".
   - Every image now has width and height attributes (the logos, tiles and pen image were missing them) to prevent layout shift.
3. The SQL saves the current page 712 _elementor_data to the meta key _onyx_backup_elementor_data_deploy26 before replacing it. It only does this if that key is not already set, so re-runs keep the original backup.
   - onyx-home-v2-deploy26-ROLLBACK.sql restores the page from that key.
4. Mobile trust bar: a compact 2-column grid with icons above the text. There are 6 facts, so it shows 3 rows of 2.
Purity text unchanged (≥99%). No product pages touched.
