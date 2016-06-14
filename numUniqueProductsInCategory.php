<?php
/*
This file is used for dumping product category-wise json
*/
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$categoryId=438;
$category->load($categoryId); //My cat id is 10
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$unique_names=array();
$obj = Mage::getModel('catalog/product');
//$i=0;
foreach($prdIds as $prdId){
	//if ($i<6000) {$i++;continue;}
	//if ($i>6000) break;
	$product_id = $prdId;
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id
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
	$processed_name=process($_product->getName());
	if (!in_array($processed_name, $unique_names)){
		array_push($unique_names,$processed_name);
	}
	$i++;
}

//-----------------------------------------get unique product names---------------------------

//echo count($product_names);
//echo 'Number of all [conf and simple] products in category '.$categoryId.':'.count($product_names);

echo 'Number of all [unique] products in category '.$categoryId.':'.count($unique_names);

echo '<br>';
echo '<pre>';

//var_dump($unique_names);
echo '</pre>';

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