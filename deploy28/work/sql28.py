#!/usr/bin/env python3
"""deploy28: create-or-update "<Title> 2" pages (slug <slug>-2) as exact copies of the CURRENT originals on the target DB.
Content is copied inside MySQL at run time (post_content + Elementor meta), so text is never re-typed. Originals are only read.
Idempotent: re-running re-syncs the copies; marker meta _onyx_v2_source=<slug> identifies deploy28 pages (used by rollback)."""
import sys
P = sys.argv[1] if len(sys.argv) > 1 else 'wp_'
SLUGS = ['cart','checkout','my-account','wishlist','b2b-login','what-are-peptides','faq','payment-delivery','product-returns','contact',
 'wholesale-application','independent-doctor-consultation','terms-conditions','privacy-policy','refund-policy','shipping-policy',
 'cookies-policy','accessibility-policy','product-catalogue','check-code','analytical-reference-calculator','peptide-plans']
META = ['_elementor_data','_elementor_edit_mode','_wp_page_template','_elementor_template_type','_elementor_version','_elementor_page_settings','_elementor_controls_usage']
o = ["SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;", "SET @c := 0, @u := 0, @m := 0, @missing := 0;"]
for s in SLUGS:
    n = s + '-2'
    o.append(f"""
-- {s} -> {n}
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM {P}posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY '{s}' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM {P}posts WHERE post_type='page' AND BINARY post_name=BINARY '{n}' ORDER BY ID LIMIT 1;
INSERT INTO {P}posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','{n}','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM {P}posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE {P}posts p JOIN {P}options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE {P}posts p JOIN {P}posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();""")
    for k in META + ['_onyx_v2_source']:
        val = f"'{s}'" if k == '_onyx_v2_source' else f"(SELECT meta_value FROM {P}postmeta WHERE post_id=@src AND meta_key='{k}' ORDER BY meta_id LIMIT 1)"
        o.append(f"""SET @v := {val};
DELETE FROM {P}postmeta WHERE post_id=@pid AND meta_key='{k}' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO {P}postmeta (post_id,meta_key,meta_value) SELECT @pid,'{k}',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM {P}postmeta WHERE post_id=@pid AND meta_key='{k}'); SET @m := @m + ROW_COUNT();
UPDATE {P}postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='{k}' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();""")
o.append(f"""
-- clear Elementor caches of the copies when anything changed
DELETE pm FROM {P}postmeta pm JOIN {P}postmeta mk ON mk.post_id=pm.post_id AND mk.meta_key='_onyx_v2_source'
 WHERE pm.meta_key IN ('_elementor_css','_elementor_page_assets','_elementor_element_cache') AND (@c+@u+@m)>0;
DELETE FROM {P}options WHERE option_name='_elementor_global_css' AND (@c+@u+@m)>0;
SELECT @c AS pages_created, @u AS pages_updated, @m AS meta_changed, @missing AS originals_missing,
 (SELECT COUNT(*) FROM {P}postmeta WHERE meta_key='_onyx_v2_source') AS v2_pages,
 (SELECT COUNT(*) FROM {P}posts p JOIN {P}postmeta m ON m.post_id=p.ID AND m.meta_key='_onyx_v2_source' JOIN {P}posts s ON s.post_type='page' AND s.post_status='publish' AND BINARY s.post_name=BINARY m.meta_value WHERE BINARY p.post_content=BINARY s.post_content) AS content_identical;""")
open('onyx-pages-2-deploy28.sql','w').write('\n'.join(o)+'\n')
open('onyx-pages-2-deploy28-rollback.sql','w').write(f"""SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
-- deploy28 rollback: removes ONLY the pages carrying _onyx_v2_source (the "<Title> 2" copies). Originals, Home V2 and Peptides 2 untouched. Idempotent.
CREATE TEMPORARY TABLE onyx_v2_del AS SELECT post_id AS id FROM {P}postmeta WHERE meta_key='_onyx_v2_source';
DELETE FROM {P}postmeta WHERE post_id IN (SELECT id FROM onyx_v2_del);
DELETE FROM {P}posts WHERE ID IN (SELECT id FROM onyx_v2_del) AND post_type='page';
SELECT (SELECT COUNT(*) FROM onyx_v2_del) AS pages_removed;
DROP TEMPORARY TABLE onyx_v2_del;
""")
