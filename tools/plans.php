<?php $order=array_map(fn($p)=>$p['slug'],json_decode(file_get_contents('/workspace/onyx/live2/plans.json'),true));
foreach(glob('/workspace/onyx/live2/plans/*.json') as $f){$d=json_decode(file_get_contents($f),true);
$q=get_posts(['post_type'=>'product','post_status'=>'any','numberposts'=>1,'name'=>$d['slug']]);if(!$q){$q=get_posts(['post_type'=>'product','post_status'=>'any','numberposts'=>1,'meta_key'=>'_onyx_slug','meta_value'=>$d['slug']]);} if(!$q){echo "missing {$d['slug']}\n";continue;}
update_post_meta($q[0]->ID,'_onyx_plan',wp_slash(wp_json_encode($d)));update_post_meta($q[0]->ID,'_onyx_plan_order',array_search($d['slug'],$order));echo $d['slug'].' '.$q[0]->ID."\n";}
