<?php wp_set_current_user(1); // dump/load elementor data: wp eval-file edump.php dump|load slug...
$mode=$args[0];foreach(array_slice($args,1) as $slug){$p=get_page_by_path($slug,OBJECT,['page','elementor_library']);if(!$p){$q=get_posts(['post_type'=>'elementor_library','name'=>$slug,'numberposts'=>1]);$p=$q?$q[0]:null;} if(!$p){echo "no $slug\n";continue;}
$f="/workspace/onyx/edata/$slug.json";
if($mode==='dump'){file_put_contents($f,json_encode(json_decode(get_post_meta($p->ID,'_elementor_data',true),true),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));echo "dumped $slug {$p->ID}\n";}
else{$d=json_decode(file_get_contents($f),true);update_post_meta($p->ID,'_elementor_data',wp_slash(wp_json_encode($d)));delete_post_meta($p->ID,'_elementor_element_cache');delete_post_meta($p->ID,'_elementor_css');echo "loaded $slug\n";}}
if($mode==='load' && class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
