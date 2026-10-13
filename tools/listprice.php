<?php $n=0;foreach(glob('/workspace/onyx/live2/detail/*.json') as $f){$d=json_decode(file_get_contents($f),true);$s=trim($d['slug']);
$q=get_posts(['post_type'=>'product','post_status'=>'any','numberposts'=>1,'meta_key'=>'_onyx_slug','meta_value'=>$s]);if(!$q)$q=get_posts(['post_type'=>'product','post_status'=>'any','numberposts'=>1,'name'=>$s]);if(!$q)continue;
update_post_meta($q[0]->ID,'_onyx_list_price',$d['price']);$n++;}echo $n;
