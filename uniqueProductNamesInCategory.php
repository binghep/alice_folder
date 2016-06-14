<?php
/*
This file is used for dumping product category-wise json
*/
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$categoryId=429;
$category->load($categoryId); //My cat id is 10

$prodCollection = $category->getProductCollection();
echo 'here';

$product_names=array();
$unique_names=array();
echo 'there';

foreach ($prodCollection as $product) {
	$product_id = $product->getId();
	$obj = Mage::getModel('catalog/product');
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id

	$name=$_product->getName();
	array_push($product_names,$name);
	$processed_name=process($name);
	if (!in_array($processed_name, $unique_names)){
		array_push($unique_names,$processed_name);
	}
	//log2('logging');
	//break;
}


//exit();
//echo count($product_names);

log2('Number of all [conf and simple] products in category '.$categoryId.':'.count($product_names));
log2('Number of all [unique] products in category '.$categoryId.':'.count($unique_names));

/*
	returns the name without ', L' etc
*/
function process($name){
	$comma_pos=strpos($name,',');
	if ($comma_pos!==false){
		return substr($name,0,$comma_pos);
	}else{
		return $name;
	}
}


function log2($msg){
	$fp = fopen('log-unique-product-names-in-category.txt', 'a');//append
	fwrite($fp, $msg."\n");
	echo ($msg.'<br>');
	fclose($fp);
}

	//------------------------get a json array--------------------------
	//convert it to string
/*	$json = array(
		'id'	=>$_product->getId(),
	    'sku'   => $_product->getSku(),
	    'type'   => $_product->getTypeId(), //'simple' or 'configurable'
	    'categoryIds' => $categoryIds,
	    'name' => $_product->getName(),
	    'description' => $_product->getDescription(),
	    'shortDescription' => $_product->getShortDescription(),
	    'price' => $_product->getPrice(),
	    'specialPrice' => $_product->getData('special_price'),
	    'image'=>$_product->getImage(),
	    'small_image'=>$_product->getSmallImage(),
	    'thumbnail'=>$_product->getThumbnail(),
	    'weight'=>$_product->getWeight(),
	    //'imageUrl'=>$_product->getImageUrl()
	    );
*/