<?php
/*
This file is used for dumping product category-wise json
*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$category->load(429); //My cat id is 10
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$product_names=array();
$sizes=array();
$obj = Mage::getModel('catalog/product');
$i=2000;
foreach($prdIds as $prdId){
	 if ($i==3000) break;
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
	array_push($product_names,$_product->getName());
	array_push($sizes,$_product->getSize());
	$i++;
}

echo count($product_names);
echo '<br>';
echo '<pre>';
$i=0;
foreach ($product_names as $product_name){
	echo $product_name.'             '.$sizes[$i].'<br>';
	$i++;
}
echo '</pre>';