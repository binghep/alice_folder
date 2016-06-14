<?php
/*
This file is used for dumping product category-wise json
*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$categoryId=444;
$category = new Mage_Catalog_Model_Category();
$category->load($categoryId); //My cat id is 10
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$product_array=array();
//echo '<pre>';
foreach($prdIds as $prdId){
	$product_id = $prdId;

	$obj = Mage::getModel('catalog/product');
	$_product = $obj->load($product_id); // Enter your Product Id in $product_id

	//----------------load the categories of this product---------------
	$categoryIds=array();//249 337 415
	$categoryCollection = $_product->getCategoryCollection();
	foreach ($categoryCollection as $cat) {
	    array_push($categoryIds,(int)$cat->getData('entity_id'));
	}
	//----------------get size of simple product------------------------
	//var_dump($_product);
	//echo $_product->getAttributeText('size').'<br>';

	//$optionId = $this->_getOptionIDByCode('size', 'M'); 
	//$product->getSize($optionId);
	//------------------------get a json array--------------------------
	//convert it to string
	$json = array(
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
	    'size'=>$_product->getAttributeText('size'),
	    //'imageUrl'=>$_product->getImageUrl()
	    );
	//print_r($json);
	//print_r(json_encode($json));
	array_push($product_array,$json);

	//convert it to array again ,and get one element out of it.

	//echo $back['categoryIds'][0];
}

/*$back=(array)json_decode(json_encode($product_array));

print_r($back);
*/


//----------------------------------------------------
//echo json_encode($product_array,JSON_PRETTY_PRINT);
//echo json_encode($product_array);


$output=json_encode($product_array,JSON_PRETTY_PRINT);
file_put_contents('backup/'.$categoryId.'.json', $output);
echo 'finished. see '.'<a href="/alice/backup/'.$categoryId.'.json">created json file for category '.$categoryId.'</a>';
//echo '</pre>';

