<?php
$plist=json_decode(file_get_contents(getenv('ONYX_WORK').'/live/products.json'),true);
$n=0;
foreach($plist as $p){
  $vs=$p['variants']??[]; if(count($vs)<2) continue;
  $pid=wc_get_product_id_by_sku('ONYX-'.$p['id']); if(!$pid) continue;
  $prod=wc_get_product($pid);
  $attrs=[];
  foreach($prod->get_attributes() as $k=>$a){ $n=new WC_Product_Attribute(); $n->set_id($a->get_id()); $n->set_name($a->get_name()); $n->set_options($a->get_options()); $n->set_position($a->get_position()); $n->set_visible(true); $n->set_variation($a->get_name()==='pa_strength'); $attrs[$k]=$n; }
  $prod->set_attributes($attrs); $prod->save();
  update_post_meta($pid,'_product_attributes',array_map(fn($a)=>['name'=>$a->get_name(),'value'=>'','position'=>$a->get_position(),'is_visible'=>1,'is_variation'=>$a->get_variation()?1:0,'is_taxonomy'=>1],$attrs));
  foreach($vs as $i=>$v){
    $sku='ONYX-V'.$v['id'];
    $vid=wc_get_product_id_by_sku($sku);
    $var=$vid? wc_get_product($vid) : new WC_Product_Variation();
    $var->set_parent_id($pid);
    $term=get_term_by('name',$v['label'],'pa_strength');
    $var->set_attributes(['pa_strength'=>$term->slug]);
    if(!$vid) $var->set_sku($sku);
    $var->set_regular_price($v['price']);
    $var->set_menu_order($i);
    $var->set_stock_status(($v['outOfStock']||$v['comingSoon'])?'outofstock':'instock');
    if($v['stockQty']!==null){ $var->set_manage_stock(true); $var->set_stock_quantity($v['stockQty']); }
    $var->save(); $n++;
  }
  // default = first variant (live shows first)
  $t=get_term_by('name',$vs[0]['label'],'pa_strength');
  $prod=wc_get_product($pid); $prod->set_default_attributes(['pa_strength'=>$t->slug]); $prod->save();
  WC_Product_Variable::sync($pid);
}
// slugs + catalog visibility
foreach($plist as $p){
  $pid=wc_get_product_id_by_sku('ONYX-'.$p['id']); if(!$pid) continue;
  wp_update_post(['ID'=>$pid,'post_name'=>sanitize_title(trim($p['slug']))]);
}
echo "variations: $n\n";
