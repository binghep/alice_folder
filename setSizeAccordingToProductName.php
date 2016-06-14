<?php
/*
This file is used for dumping product category-wise json
*/
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$category->load(429); 
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$obj = Mage::getModel('catalog/product');
foreach($prdIds as $prdId){
	$product_id = $prdId;

	$_product = $obj->load($product_id); // Enter your Product Id in $product_id
	if ($_product->getTypeId()=='configurable') {continue;}
	$size=getSize($_product->getName());
	//echo $_product->getName().'        '.'"'.$size.'"'.'<br>';
	if ($size==false) {continue;}//no L, or S at the end of product name, don't change anything
	$optionId=_getOptionIDByCode('size',$size);
	if ($_product->getSize()!=$optionId){
		log2('setting '.$_product->getSize().' to '. $optionId);
		$_product->setSize($optionId);
		$_product->save();
	}else {
		//echo 'pass<br>';
	}
}
log2('finished');


 function log2($msg){
	$fp = fopen('log_setSize.txt', 'a');//append
	fwrite($fp, $msg."\n");
	fclose($fp);
}

/*
	returns the size in product name
*/
function getSize($name){
	$comma_pos=strpos($name,',');
	if ($comma_pos!==false){
		return substr($name,$comma_pos+2);
	}else{
		return false;
	}
}



 function _getOptionIDByCode($attrCode, $optionLabel) 
{
	$attrModel   = Mage::getModel('eav/entity_attribute');

	$attrID      = $attrModel->getIdByCode('catalog_product', $attrCode);
	$attribute   = $attrModel->load($attrID);

	$options     = Mage::getModel('eav/entity_attribute_source_table')
		->setAttribute($attribute)
		->getAllOptions(false);

	foreach ($options as $option) {
		if ($option['label'] == $optionLabel) {
			return $option['value'];
		}
	}

	return false;
}
