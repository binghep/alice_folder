<?php
/*
This file is used for changing all conf products havinig skus ending in 'CC'. remove one C. 

*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$category->load(372);//414 

$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
foreach($prdIds as $prdId){
	$product_id = $prdId;

	$obj = Mage::getModel('catalog/product');
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id
		//$_product->setCategoryIds(array(249,337,415));
		//$_product->save();
		//echo $_product->getName().'<br>';
		//512
		$catIds=$_product->getCategoryIds();
		var_dump($catIds);
		echo ' is changed to ';
		$catIds[]="598";
		$_product->setCategoryIds($catIds);

		$_product->save();
		var_dump($_product->getCategoryIds());

		//echo "category changed <br>";
		//exit;
}

// echo count($product_names);
// echo '<br>';
// echo '<pre>';
// //print the simples products that has sku ending in C
// foreach ($product_names as $product_name){
// 	echo $product_name.'<br>';
// }
// echo '</pre>';




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