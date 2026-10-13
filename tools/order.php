<?php $o=json_decode(file_get_contents('/workspace/onyx/import/order.json'),true);$i=0;
$map=[];foreach(wc_get_products(['limit'=>-1,'status'=>'any']) as $p){$map[trim($p->get_meta('_onyx_slug')?:$p->get_slug())]=$p->get_id();}
foreach($o as $r){$id=$map[$r['slug']]??0;if(!$id)continue;wp_update_post(['ID'=>$id,'menu_order'=>$i++]);update_post_meta($id,'_onyx_featured',$r['f']?'1':'0');}
echo $i;
