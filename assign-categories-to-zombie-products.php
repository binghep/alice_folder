<?php
/*
This file is used for dumping product category-wise json
*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//104292- 104443
echo 'here';
echo '<pre>';
for ( $product_id = 104292; $product_id <= 104443; $product_id ++) {
	$obj = Mage::getModel('catalog/product');
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id	
	//var_dump($_product);
	$_product->setCategoryIds(array(249,337,415));
	// optimize performance, tell Magento to not update indexes
	$_product
	    ->setIsMassupdate(true)
		->setExcludeUrlRewrite(true)
	;
	$_product->save();
	//exit;
}
echo '</pre>';
