DEPLOY20 - Home V2 video section redesign (section-only, page 712). Not deployed.
Layout follows the reference: #f3f3f3 band; left 55% the ONYX YouTube video W1-7GmGogEg (youtube-nocookie embed, 16:9, rounded corners);
right: orange #e8590c eyebrow "INSIDE THE ONYX LABORATORY", light 2-line headline, the existing paragraph, an "Analytical control" row between hairlines, and a black pill "View Research Materials ->" linking to /peptides/.
Below: 3 green-dot uppercase labels (High-tech equipment / Controlled production / Strict quality control) between hairlines, then the note "For laboratory standards and research purposes only."
Mobile: video on top, full-width button, labels stacked and left-aligned in a centred column.
FILES: onyx-home-v2-deploy20-wp-content.zip  (home-v2.css = deploy19 CSS + .hv2-lab2 rules; CSS only)
       onyx-home-v2-deploy20-video-section.sql (replaces only the "hv2-sec hv2-lab" section, matched by class)
ORDER: deploy17 (zip+SQL) -> deploy19 zip (or skip it: this zip already contains the deploy19 CSS) -> this zip -> this SQL -> Elementor Clear Files & Data -> purge Cloudflare -> hard refresh.
EXPECTED SQL: page_id=712 video_section_replaced=1 new_section_present=1 old_section_left=0 sections=10. Re-run: video_section_replaced=0.
TESTED locally: deploy14 state -> trust A SQL -> this SQL twice (1 then 0); the data deep-equals the expected JSON.
