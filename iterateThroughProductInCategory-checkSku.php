<?php
/*
This file is used for printing the simples products that has sku ending in C

*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$category->load(414); //My cat id is 10
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$product_names=array();
$sizes=array();
foreach($prdIds as $prdId){
	$product_id = $prdId;

	$obj = Mage::getModel('catalog/product');
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id
	if (endsWith($_product->getSku,'C') && $_product->getTypeId()=='simple'){
	//if (endsWith($_product->getSku,'CC') && $_product->getTypeId()=='configurable'){

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
	}
}

echo count($product_names);
echo '<br>';
echo '<pre>';
//print the simples products that has sku ending in C
foreach ($product_names as $product_name){
	echo $product_name.'<br>';
}
echo '</pre>';



 function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}