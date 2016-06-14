<?php

/*
usage:
go to www.1661hk.com/alice/modify.php
where sku is the conf that you want to delete
then you will see all its children products, then itself (sku)
then the corresponding content will show up in magmi folder's csv. Pls use that csv in magmi to delete all the conf with their simples. 
-----------------------------------------------------------
Note the header line of log.csv should be written by hand: 
For example: 
store,sku,weight
admin,2912901300270408L,3
*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
/*
$sku=$_GET['sku'];

if (empty($sku)) {
	echo 'param not valid. exiting...';
	exit;
}
*/



$collection = Mage::getResourceModel('catalog/product_collection');
$collection->addAttributeToFilter('status',1); //only enabled product
$collection->addAttributeToSelect('name','small_image');
$needle="Headphone";
$collection->addAttributeToFilter('name', array(
        array('like' => '%'."buds".'%'),
        array('like' => '%'."Buds".'%'),
    ));


echo $collection->count(). " products found... <br>";

foreach($collection as $product) {
    print_r($product->getName());  // You can use any of the magic get functions on this object to get the value
    echo $product->getSmallImage();//'<img src="'.$product->getSmallImage().'">';
    print_r("<br>");
    // write_log('admin,'.$child->getSku().',1');
	write_log('admin,'.$product->getSku().',3');

}






function write_log($object){
	error_log($object."\n",3,'magmi_csv/modify-buds.csv');
	return true;
}





















//-----------------------------------------------------------------------------


// $product=Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);//shit. always doesn't work. brings down the whole page 500 error.
$product = Mage::getModel('catalog/product');

$id=$product->getIdBySku($sku);//will return "false" if not exist
//var_dump($id);

if ($id===false){
	echo 'no such sku exist. exiting...';
	exit;
}
echo '-----------found product with id: '.$id.'-------------<br>';
//product with this sku exist, now find its children:
//$product->load($id);
// var_dump($product);
/*
$childProductIDs = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($id);
var_dump($childProductIDs);
*/






$product = Mage::getModel('catalog/product')->load($id); 
$childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null,$product);

foreach($childProducts as $child) {
    print_r($child->getSku());  // You can use any of the magic get functions on this object to get the value
    print_r("<br>");
    write_log('admin,'.$child->getSku().',1');
}

echo $sku;
write_log('admin,'.$sku.',1');


