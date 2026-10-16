SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
-- deploy28 rollback: removes ONLY the pages carrying _onyx_v2_source (the "<Title> 2" copies). Originals, Home V2 and Peptides 2 untouched. Idempotent.
CREATE TEMPORARY TABLE onyx_v2_del AS SELECT post_id AS id FROM wp_postmeta WHERE meta_key='_onyx_v2_source';
DELETE FROM wp_postmeta WHERE post_id IN (SELECT id FROM onyx_v2_del);
DELETE FROM wp_posts WHERE ID IN (SELECT id FROM onyx_v2_del) AND post_type='page';
SELECT (SELECT COUNT(*) FROM onyx_v2_del) AS pages_removed;
DROP TEMPORARY TABLE onyx_v2_del;
