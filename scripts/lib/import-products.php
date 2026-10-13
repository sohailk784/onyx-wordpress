<?php
include_once WC_ABSPATH.'includes/admin/importers/class-wc-product-csv-importer-controller.php';
include_once WC_ABSPATH.'includes/import/class-wc-product-csv-importer.php';
$file=getenv('ONYX_WORK').'/import/onyx-products-woocommerce.csv';
$params=['parse'=>true,'update_existing'=>false,'prevent_timeouts'=>false,'lines'=>-1,
 'mapping'=>[]];
$ctrl_map=function($h){ return $h; };
$imp=new WC_Product_CSV_Importer($file,['parse'=>true,'update_existing'=>false,'prevent_timeouts'=>false,'lines'=>-1,
 'mapping'=>array_map(function($h){
   $m=['ID'=>'id','Type'=>'type','SKU'=>'sku','Name'=>'name','Published'=>'published','Is featured?'=>'featured','Visibility in catalog'=>'catalog_visibility','Short description'=>'short_description','Description'=>'description','Tax status'=>'tax_status','In stock?'=>'stock_status','Stock'=>'stock_quantity','Backorders allowed?'=>'backorders','Weight (kg)'=>'weight','Regular price'=>'regular_price','Sale price'=>'sale_price','Categories'=>'category_ids','Tags'=>'tag_ids','Images'=>'images','Parent'=>'parent_id','Upsells'=>'upsell_ids','Position'=>'menu_order'];
   if(isset($m[$h])) return $m[$h];
   if(preg_match('/^Attribute (\d+) (name|value\(s\)|visible|global)$/',$h,$x)) return 'attributes:'.['name'=>'name','value(s)'=>'value','visible'=>'visible','global'=>'taxonomy'][$x[2]].$x[1];
   if(str_starts_with($h,'Meta: ')) return 'meta:'.substr($h,6);
   return $h; }, (function($f){$h=fgetcsv(fopen($f,'r'));return array_combine($h,$h);})($file))]);
$r=$imp->import();
foreach($r as $k=>$v) echo $k.': '.count($v)."\n";
var_dump(array_slice($r["failed"],0,2));
