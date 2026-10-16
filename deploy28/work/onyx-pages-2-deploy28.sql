SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
SET @c := 0, @u := 0, @m := 0, @missing := 0;

-- cart -> cart-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'cart' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'cart-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','cart-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'cart';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- checkout -> checkout-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'checkout' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'checkout-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','checkout-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'checkout';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- my-account -> my-account-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'my-account' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'my-account-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','my-account-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'my-account';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- wishlist -> wishlist-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'wishlist' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'wishlist-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','wishlist-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'wishlist';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- b2b-login -> b2b-login-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'b2b-login' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'b2b-login-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','b2b-login-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'b2b-login';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- what-are-peptides -> what-are-peptides-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'what-are-peptides' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'what-are-peptides-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','what-are-peptides-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'what-are-peptides';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- faq -> faq-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'faq' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'faq-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','faq-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'faq';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- payment-delivery -> payment-delivery-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'payment-delivery' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'payment-delivery-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','payment-delivery-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'payment-delivery';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- product-returns -> product-returns-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'product-returns' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'product-returns-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','product-returns-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'product-returns';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- contact -> contact-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'contact' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'contact-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','contact-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'contact';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- wholesale-application -> wholesale-application-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'wholesale-application' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'wholesale-application-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','wholesale-application-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'wholesale-application';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- independent-doctor-consultation -> independent-doctor-consultation-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'independent-doctor-consultation' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'independent-doctor-consultation-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','independent-doctor-consultation-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'independent-doctor-consultation';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- terms-conditions -> terms-conditions-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'terms-conditions' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'terms-conditions-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','terms-conditions-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'terms-conditions';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- privacy-policy -> privacy-policy-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'privacy-policy' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'privacy-policy-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','privacy-policy-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'privacy-policy';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- refund-policy -> refund-policy-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'refund-policy' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'refund-policy-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','refund-policy-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'refund-policy';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- shipping-policy -> shipping-policy-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'shipping-policy' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'shipping-policy-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','shipping-policy-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'shipping-policy';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- cookies-policy -> cookies-policy-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'cookies-policy' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'cookies-policy-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','cookies-policy-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'cookies-policy';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- accessibility-policy -> accessibility-policy-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'accessibility-policy' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'accessibility-policy-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','accessibility-policy-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'accessibility-policy';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- product-catalogue -> product-catalogue-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'product-catalogue' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'product-catalogue-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','product-catalogue-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'product-catalogue';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- check-code -> check-code-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'check-code' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'check-code-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','check-code-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'check-code';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- analytical-reference-calculator -> analytical-reference-calculator-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'analytical-reference-calculator' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'analytical-reference-calculator-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','analytical-reference-calculator-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'analytical-reference-calculator';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- peptide-plans -> peptide-plans-2
SET @src := NULL, @pid := NULL;
SELECT ID INTO @src FROM wp_posts WHERE post_type='page' AND post_status='publish' AND BINARY post_name=BINARY 'peptide-plans' ORDER BY ID LIMIT 1;
SET @missing := @missing + IF(@src IS NULL,1,0);
SELECT ID INTO @pid FROM wp_posts WHERE post_type='page' AND BINARY post_name=BINARY 'peptide-plans-2' ORDER BY ID LIMIT 1;
INSERT INTO wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count)
 SELECT s.post_author,NOW(),UTC_TIMESTAMP(),s.post_content,CONCAT(s.post_title,' 2'),'','publish','closed','closed','','peptide-plans-2','','',NOW(),UTC_TIMESTAMP(),'',0,'',0,'page','',0
 FROM wp_posts s WHERE s.ID=@src AND @pid IS NULL;
SET @new := IF(@pid IS NULL AND @src IS NOT NULL, LAST_INSERT_ID(), NULL);
SET @c := @c + IF(@new IS NULL,0,1);
SET @pid := COALESCE(@pid,@new);
UPDATE wp_posts p JOIN wp_options o ON BINARY o.option_name=BINARY 'home' SET p.guid=CONCAT(TRIM(TRAILING '/' FROM o.option_value),'/?page_id=',p.ID) WHERE p.ID=@new;
UPDATE wp_posts p JOIN wp_posts s ON s.ID=@src SET p.post_content=s.post_content, p.post_title=CONCAT(s.post_title,' 2'), p.post_status='publish', p.post_modified=NOW(), p.post_modified_gmt=UTC_TIMESTAMP()
 WHERE p.ID=@pid AND (BINARY p.post_content<>BINARY s.post_content OR BINARY p.post_title<>BINARY CONCAT(s.post_title,' 2') OR p.post_status<>'publish');
SET @u := @u + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_data' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_data',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_data'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_data' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_edit_mode' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_edit_mode',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_edit_mode'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_edit_mode' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_wp_page_template' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_wp_page_template',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_wp_page_template'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_wp_page_template' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_template_type' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_template_type',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_template_type'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_template_type' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_version' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_version',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_version'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_version' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_page_settings' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_page_settings',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_page_settings'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_page_settings' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := (SELECT meta_value FROM wp_postmeta WHERE post_id=@src AND meta_key='_elementor_controls_usage' ORDER BY meta_id LIMIT 1);
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_elementor_controls_usage',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_elementor_controls_usage'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_elementor_controls_usage' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();
SET @v := 'peptide-plans';
DELETE FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NULL; SET @m := @m + ROW_COUNT();
INSERT INTO wp_postmeta (post_id,meta_key,meta_value) SELECT @pid,'_onyx_v2_source',@v FROM DUAL WHERE @pid IS NOT NULL AND @v IS NOT NULL AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id=@pid AND meta_key='_onyx_v2_source'); SET @m := @m + ROW_COUNT();
UPDATE wp_postmeta SET meta_value=@v WHERE post_id=@pid AND meta_key='_onyx_v2_source' AND @v IS NOT NULL AND BINARY meta_value<>BINARY @v; SET @m := @m + ROW_COUNT();

-- clear Elementor caches of the copies when anything changed
DELETE pm FROM wp_postmeta pm JOIN wp_postmeta mk ON mk.post_id=pm.post_id AND mk.meta_key='_onyx_v2_source'
 WHERE pm.meta_key IN ('_elementor_css','_elementor_page_assets','_elementor_element_cache') AND (@c+@u+@m)>0;
DELETE FROM wp_options WHERE option_name='_elementor_global_css' AND (@c+@u+@m)>0;
SELECT @c AS pages_created, @u AS pages_updated, @m AS meta_changed, @missing AS originals_missing,
 (SELECT COUNT(*) FROM wp_postmeta WHERE meta_key='_onyx_v2_source') AS v2_pages,
 (SELECT COUNT(*) FROM wp_posts p JOIN wp_postmeta m ON m.post_id=p.ID AND m.meta_key='_onyx_v2_source' JOIN wp_posts s ON s.post_type='page' AND s.post_status='publish' AND BINARY s.post_name=BINARY m.meta_value WHERE BINARY p.post_content=BINARY s.post_content) AS content_identical;
