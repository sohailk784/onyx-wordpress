# Home V2 deploy24: luxury polish of deploy23 (mockup, NOT deployed)
Same 11 sections and same content as deploy23. Visual changes only:
- Typography: Cormorant Garamond serif for headings, product titles, tile names and accordion rows; Manrope for body text. Both load from Google Fonts on home-v2 only. Eyebrows are small caps with 0.28em tracking and a fine champagne hairline under them.
- Palette: ivory (#faf8f4) section backgrounds, deep ink (#14161c), the ONYX light-blue gradient kept on the pen section, and champagne/gold (#c8b48a / #8a7650) used sparingly for hairlines, eyebrows, ticks and pills.
- Spacing: roughly 112px section padding on desktop (72px on mobile); centred section heads.
- Category tiles: 22px-radius cards with a soft studio-gradient backdrop, a contact shadow under each product, and a hover lift with image zoom. Images are now full-size uploads (1024–2048px) with WordPress srcset/sizes. The Dual Chamber tile uses the real BPC + TB pen cutout.
- Pen section: the pen sits on a rounded studio card with a drop shadow; refined accordion; champagne pills.
- Weight-loss grid: product images are now 600px plus srcset instead of 300px thumbnails (functions.php filter, home-v2 only). Rounded cards on a studio backdrop, serif titles, outline "View details" buttons that fill ink on hover.
- Bestsellers carousel: serif titles and rounded cards; the approved light "View details" button is kept.
- Comparison table: rounded card with a soft shadow and ink header; the pen column is lightly tinted blue.
- FAQ: rounded, bordered cards with serif questions.
- Motion: sections fade and rise in on scroll (IntersectionObserver); cards and buttons lift on hover. All motion is disabled under prefers-reduced-motion, and content stays visible if JS doesn't run.
No new products, images of people, stats or reviews.
