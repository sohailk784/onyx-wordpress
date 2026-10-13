<?php
require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/image.php';
$urls=json_decode(file_get_contents(getenv('ONYX_WORK').'/import/media-urls.json'),true);
$map=[];
foreach($urls as $u){
  if(str_ends_with($u,'.svg')) continue;
  $q=get_posts(['post_type'=>'attachment','numberposts'=>1,'meta_query'=>[['key'=>'_wc_attachment_source','value'=>$u]],'fields'=>'ids']);
  if(!$q) $q=get_posts(['post_type'=>'attachment','numberposts'=>1,'meta_query'=>[['key'=>'_onyx_source','value'=>$u]],'fields'=>'ids']);
  if($q){ $id=$q[0]; }
  else {
    $tmp=download_url($u,120); if(is_wp_error($tmp)){ echo "ERR $u ".$tmp->get_error_message()."\n"; continue; }
    $name=preg_replace('/-[A-Za-z0-9_]{8}\./','.',basename(rawurldecode(parse_url($u,PHP_URL_PATH))));
    $id=media_handle_sideload(['name'=>$name,'tmp_name'=>$tmp],0);
    if(is_wp_error($id)){ echo "ERR2 $u ".$id->get_error_message()."\n"; @unlink($tmp); continue; }
    update_post_meta($id,'_onyx_source',$u);
  }
  $map[$u]=['id'=>$id,'url'=>wp_get_attachment_url($id)];
}
file_put_contents(getenv('ONYX_WORK').'/import/media-map.json',json_encode($map,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo count($map)," mapped\n";
