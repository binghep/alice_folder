<?php
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


/*
this will output bool(false) if the product in json array is not in database
*/
function checkIfJsonProdcutExists(){
	$string = file_get_contents("to_do.json");
	$back=(array)json_decode($string);

	echo "<pre>";
	//var_dump($back);
	/*
	var_dump($back[2]); //->this is an object! 
	var_dump($back[0]->categoryIds); //->this is an array 
	*/
	//var_dump($back[0]->sku);



	// var_dump($back[0]['categoryIds']);


	foreach ($back as $product){
		//echo $product->sku;
		$product_load = Mage::getModel('catalog/product')->loadByAttribute('sku',$product->sku);
		
		var_dump($product_load);
		echo "<br><br>";
		echo "</pre>";

		//exit;
		// $childProducts = Mage::getModel('catalog/product_type_configurable')
	 //                    ->getUsedProducts(null,$product);

		// foreach($childProducts as $child) {
		//     print_r($child->getName());  // You can use any of the magic get functions on this object to get the value
		// }
	}
}