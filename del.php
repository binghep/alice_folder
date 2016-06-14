<?php

/*
usage:
go to www.1661hk.com/alice/del.php?sku=xxxxxx
where sku is the conf that you want to delete
then you will see all its children products, then itself (sku)
then the corresponding content will show up in magmi folder's csv. Pls use that csv in magmi to delete all the conf with their simples. 
-----------------------------------------------------------
Note the header line of log.csv should be written by hand: 
For example: 
store,sku,magmi:delete
admin,2912901300270408L,1
*/

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



function write_log($object){
	error_log($object."\n",3,'magmi_csv/log.csv');
	return true;
}