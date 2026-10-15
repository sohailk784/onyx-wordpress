import json,sys
def page_json():
    return json.dumps([{"id":"a27c001","elType":"container","isInner":False,"settings":{"content_width":"full","css_classes":"s2-sec","_css_classes":"s2-sec","padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":True}},
      "elements":[{"id":"a27c002","elType":"widget","widgetType":"shortcode","isInner":False,"settings":{"shortcode":"[onyx_shop2]"},"elements":[]}]}],separators=(',',':'))
e=lambda x:x.replace('\\','\\\\').replace("'","\\'")
def sql(guid_base):
    j=e(page_json())
    meta=[('_elementor_data',j),('_elementor_edit_mode','builder'),('_wp_page_template','elementor_header_footer'),('_elementor_template_type','wp-page'),('_elementor_version','4.3.4'),('_elementor_page_settings','a:1:{s:10:"hide_title";s:3:"yes";}')]
    s=f"""-- ONYX "Peptides 2" (slug peptides-2) deploy27: create the page if missing, otherwise update it. Idempotent.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
SET @title := CONVERT('Peptides 2' USING utf8mb4) COLLATE utf8mb4_unicode_520_ci;
SET @content := CONVERT('[onyx_shop2]' USING utf8mb4) COLLATE utf8mb4_unicode_520_ci;
SET @pid := (SELECT ID FROM wp_posts WHERE BINARY post_name=BINARY 'peptides-2' AND BINARY post_type=BINARY 'page' AND post_status<>'trash' ORDER BY ID LIMIT 1);
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,comment_count)
SELECT 1,NOW(),UTC_TIMESTAMP(),@content,@title,'','publish','closed','closed','peptides-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page',0 FROM DUAL WHERE @pid IS NULL;
SET @created := ROW_COUNT();
SET @pid := IF(@pid IS NULL, LAST_INSERT_ID(), @pid);
UPDATE wp_posts SET guid=CONCAT('{guid_base}/?page_id=',@pid) WHERE ID=@pid AND guid='';
UPDATE wp_posts SET post_title=@title, post_content=@content, post_status='publish' WHERE ID=@pid AND NOT (BINARY post_title<=>BINARY @title AND BINARY post_content<=>BINARY @content AND post_status='publish');
SET @upd := ROW_COUNT();
SET @mchg := 0;
"""
    for k,v in meta:
        s+=f"""SET @v := CONVERT('{v}' USING utf8mb4) COLLATE utf8mb4_unicode_520_ci;
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'{k}',@v FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND BINARY meta_key=BINARY '{k}');
SET @mchg := @mchg + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND BINARY meta_key=BINARY '{k}' AND NOT (BINARY meta_value <=> BINARY @v);
SET @mchg := @mchg + ROW_COUNT();
"""
    s+="""DELETE FROM wp_postmeta WHERE (@created+@upd+@mchg)>0 AND post_id=@pid AND meta_key IN ('_elementor_css','_elementor_element_cache','_elementor_page_assets');
UPDATE wp_posts SET post_modified=NOW(), post_modified_gmt=UTC_TIMESTAMP() WHERE (@upd+@mchg)>0 AND @created=0 AND ID=@pid;
SELECT @pid AS page_id, @created AS page_created, @upd AS page_updated, @mchg AS meta_changed,
 (SELECT COUNT(*) FROM wp_posts WHERE BINARY post_name=BINARY 'peptides-2' AND post_type='page' AND post_status<>'trash') AS pages_with_slug,
 (SELECT LOCATE(BINARY '[onyx_shop2]',meta_value)>0 FROM wp_postmeta WHERE post_id=@pid AND BINARY meta_key=BINARY '_elementor_data') AS shortcode_present;
"""
    return s
open('/workspace/onyx/deploy27/onyx-peptides-2-deploy27.sql','w').write(sql('https://euro-nutra.com/onyxwp'))
open('/workspace/onyx/deploy27/work/local-test.sql','w').write(sql('http://localhost:8088'))
