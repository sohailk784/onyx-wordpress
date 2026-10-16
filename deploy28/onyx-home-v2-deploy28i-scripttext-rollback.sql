-- deploy28i rollback: restore Home V2 _elementor_data from _onyx_elementor_data_bak28i (if present). Idempotent.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
UPDATE wp_postmeta m JOIN wp_postmeta b ON b.post_id=m.post_id AND b.meta_key='_onyx_elementor_data_bak28i' SET m.meta_value=b.meta_value WHERE m.meta_key='_elementor_data' AND m.post_id=(SELECT ID FROM wp_posts WHERE post_type='page' AND post_name='home-v2' LIMIT 1);
DELETE FROM wp_postmeta WHERE meta_key IN ('_elementor_element_cache','_elementor_css') AND post_id=(SELECT ID FROM wp_posts WHERE post_type='page' AND post_name='home-v2' LIMIT 1);
