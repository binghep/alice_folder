<?php
return;
/*
This file is used for changing all conf products havinig skus ending in 'CC'. remove one C. 

*/
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

//'578065180%'  ： long sleeve: finished
//'578536042%'  ： 灰： ing
//"587148821%"  : 深蓝：  少了 4 6 7
// "Real-mardird-white": 白短袖： 
$exist_skus=array('real-madrid-prize-%');//  '587148821%' and its 4 children are deleted//white,dark blue, grey, and long sleeve

$obj=Mage::getModel('catalog/product');

foreach($exist_skus as $sku){
	$productCollection = Mage::getModel('catalog/product')->getCollection()
	    ->addFieldToFilter('sku', array('like' => $sku));

	    echo 'here';
	    //var_dump($productCollection);
	$i=0;
	foreach($productCollection as $product){
		echo $i;
		//var_dump($product);
		//continue;
		$_product = $obj->load($product->getId()); // Enter your Product Id in $product_id
		//512
		$catIds=$_product->getCategoryIds();
		var_dump($catIds);
		echo $_product->getName();
		echo '<br>';
		// var_dump($catIds);
		//$catIds=array(610,611,612,629,598);//duty fee 2 : 598
		//if (!in_array(631, $catIds)){
			//$catIds=array_push($catIds, 631);
			$catIds=array("631","598");
			var_dump($catIds);
			$_product->setCategoryIds($catIds);
			$_product->save();
			echo $_product->getName()." is added to the promotion gifts catagory. <br>";
			// break;
		//}
		$i++;
	}
}
/*echo ' is changed to ';
$catIds[]="598";
$_product->setCategoryIds($catIds);

$_product->save();
var_dump($_product->getCategoryIds());
*/
//echo($_product->getName());

/*
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
//	if ($_product->getTypeId()=='configurable'){
		//if (strpos($_product->getName(),'Rain')!==false){
			//------------------------get a json array--------------------------
			//convert it to string

			//$_product->setCategoryIds(array(249,337,415));
			//$_product->save();
			echo $_product->getName().'<br>';
			//echo "category changed <br>";
			//exit;
		//}
//	}
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