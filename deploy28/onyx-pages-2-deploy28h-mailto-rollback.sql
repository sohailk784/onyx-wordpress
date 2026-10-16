-- deploy28h: restore previous email link on accessibility-policy-2 only. Idempotent.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
UPDATE wp_postmeta m JOIN wp_posts p ON p.ID=m.post_id SET m.meta_value=REPLACE(m.meta_value,'href=\\"mailto:sales@onyxhealth.net\\"','href=\\"https:\\/\\/onyxhealth.netmailto:sales@onyxhealth.net\\"')
 WHERE m.meta_key='_elementor_data' AND p.post_type='page' AND p.post_name='accessibility-policy-2' AND LOCATE('href=\\"mailto:sales@onyxhealth.net\\"',m.meta_value)>0;
DELETE m FROM wp_postmeta m JOIN wp_posts p ON p.ID=m.post_id WHERE m.meta_key='_elementor_element_cache' AND p.post_name='accessibility-policy-2';
