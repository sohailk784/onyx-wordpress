<?php
// Tailwind "mx-auto max-w-X px-4" = max-width includes the padding; Elementor boxed width excludes it.
// Shrink boxed_width by the container's horizontal padding so content widths match the React layout.
function onyx_fix(&$els,&$n){foreach($els as &$e){ if(($e['elType']??'')==='container'&&($e['settings']['content_width']??'')==='boxed'){
  foreach([''=>'','_tablet'=>'_tablet','_mobile'=>'_mobile'] as $suf){ $bw=$e['settings']['boxed_width'.$suf]??null; $pad=$e['settings']['padding'.$suf]??($e['settings']['padding']??null);
   if($bw&&isset($bw['size'])&&$bw['unit']==='px'&&$pad&&empty($e['settings']['_onyx_boxfix'.$suf])){ $h=(float)$pad['left']+(float)$pad['right']; if($h>0){$e['settings']['boxed_width'.$suf]['size']=$bw['size']-$h;$e['settings']['_onyx_boxfix'.$suf]='1';$n++;}}}}
  if(!empty($e['elements'])) onyx_fix($e['elements'],$n);}}
$ids=get_posts(['post_type'=>['page','elementor_library','elementor-hf'],'post_status'=>'any','numberposts'=>-1,'fields'=>'ids','meta_key'=>'_elementor_data']);$t=0;
foreach($ids as $id){$d=json_decode(get_post_meta($id,'_elementor_data',true),true);if(!is_array($d))continue;$n=0;onyx_fix($d,$n);if($n){update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($d)));delete_post_meta($id,'_elementor_element_cache');$t+=$n;echo get_post_field('post_name',$id)." $n\n";}}
\Elementor\Plugin::$instance->files_manager->clear_cache(); echo "total $t\n";
