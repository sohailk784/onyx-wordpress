<?php $m=json_decode(file_get_contents('/workspace/onyx/import/live-meta.json'),true);$n=0;
foreach(wc_get_products(['limit'=>-1,'status'=>'any']) as $p){ $s=trim($p->get_meta('_onyx_slug')?:$p->get_slug()); if(!isset($m[$s]))continue; $d=$m[$s];
 update_post_meta($p->get_id(),'_onyx_live_id',$d['live_id']);
 update_post_meta($p->get_id(),'_onyx_detail',wp_slash(wp_json_encode($d['detail'])));
 update_post_meta($p->get_id(),'_onyx_variants',wp_slash(wp_json_encode($d['variants'])));
 update_post_meta($p->get_id(),'_onyx_coa',wp_slash(wp_json_encode($d['coa'])));
 update_post_meta($p->get_id(),'_onyx_category',$d['category']);
 if($d['originalPrice']) update_post_meta($p->get_id(),'_onyx_original_price',$d['originalPrice']);
 $n++;}
echo $n;
