<?php // restore stripped html-widget content from edata dumps: wp --user=admin eval-file htmlfix.php
foreach(glob('/workspace/onyx/edata/*.json') as $f){$slug=basename($f,'.json');
$p=get_page_by_path($slug,OBJECT,['page','elementor_library']); if(!$p) continue;
$src=[];$w=function($x)use(&$w,&$src){foreach($x as $c){if(($c['widgetType']??'')==='html')$src[$c['id']]=$c['settings']['html']??'';$w($c['elements']??[]);}};
$w(json_decode(file_get_contents($f),true)?:[]);
$d=json_decode(get_post_meta($p->ID,'_elementor_data',true),true);$n=0;
$fix=function(&$x)use(&$fix,$src,&$n){foreach($x as &$c){if(($c['widgetType']??'')==='html'&&empty($c['settings']['html'])&&!empty($src[$c['id']])){$c['settings']['html']=$src[$c['id']];$n++;}if(!empty($c['elements']))$fix($c['elements']);}};
$fix($d);
if($n){update_post_meta($p->ID,'_elementor_data',wp_slash(wp_json_encode($d)));delete_post_meta($p->ID,'_elementor_element_cache');}
echo "$slug restored $n\n";}
