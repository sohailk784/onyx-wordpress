DEPLOY21 - Bestsellers restyled as a reference-style carousel (section-only, page 712). Mockup, not deployed.
Same 4 products. The "Research use only" note under the carousel is REMOVED from this section (user request). Otherwise same ([products ids="270,272,268,255"]) and same texts. Cards: square #f2f2f2 image box (no border/shadow; white product backgrounds blend into the grey),
left-aligned name (small regular), tiny uppercase category, price, then a light "View Details" pill (#eaf1fc bg, #0f2a52 text, #d3e1f7 border).
Scroll-snap row: ~3.5 cards visible on desktop (only 4 products exist in this section), 2.5 on tablet, 1.5 on mobile. Swipe/trackpad works natively.
Pager "<  1/N  >" (JS in an Elementor HTML widget, scoped to the section; arrows disable at the ends; hidden if nothing to scroll).
FILES: onyx-home-v2-deploy21-wp-content.zip:
         themes/onyx-child/assets/css/home-v2.css = deploy20 CSS + carousel CSS
         themes/onyx-child/functions.php = deploy12 + a small hook that prints the product category label in loop cards on home-v2 only
       onyx-home-v2-deploy21-bestsellers.sql (replaces only "hv2-sec hv2-best")
ORDER: deploy17 -> deploy20 (zip+SQL) -> this zip -> this SQL -> Elementor Clear Files & Data -> purge Cloudflare -> hard refresh.
EXPECTED SQL: page_id=712 bestsellers_replaced=1 carousel_present=1 old_section_left=0 research_use_line_left=0 sections=10. Re-run: bestsellers_replaced=0.
TESTED locally: deploy14 state -> trust A -> deploy20 -> this SQL twice (1, then 0); data deep-equals expected; arrows: desktop 1/2->2/2->1/2, mobile 1/4->2/4->1/4.

The "research use only / research purposes only" wording stays elsewhere on Home V2 and was NOT changed: (1) the video section note "For laboratory standards and research purposes only." (2) the site-wide age-gate popup text "...supplied for research use only..." (theme/plugin, not page 712 data).
