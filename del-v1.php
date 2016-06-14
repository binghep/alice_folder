<?php

require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$sku=$_GET['sku'];
// var_dump($sku);

if (empty($sku)) {
	echo 'param not valid. exiting...';
	exit;
}

// $product=Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);//shit. always doesn't work. brings down the whole page 500 error.
$product = Mage::getModel('catalog/product');

$id=$product->getIdBySku($sku);//will return "false" if not exist
//var_dump($id);

if ($id===false){
	echo 'no such sku exist. exiting...';
	exit;
}
//product with this sku exist, now find its children:
echo 'found product with id: '.$id.'<br>';
//$product->load($id);
// var_dump($product);

$childProductIDs = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($id);
var_dump($childProductIDs);

