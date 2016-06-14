<?php
	/*
		1. example url: http://www.1661hk.com/alice/_getProductNamesJson.php?catId=415  which displays all product names in 415 category using json
		2. example url: http://www.1661hk.com/alice/_getProductNamesJson.php?catId=415&unique=true  which displays all unique product names(will be convertied to conf products) in 415 category using json
		
		3.a. example url: http://www.1661hk.com/alice/_getProductNamesJson.php?catId=415&simple_only=true  which only diplays all simples products in category
		3.b. example url: http://www.1661hk.com/alice/_getProductNamesJson.php?catId=415&configurable_only=true  which only diplays all configurable products in category
		
		displays the json of all products if $_GET['unique'] is not "true"
	*/
		return;
	require_once '../app/Mage.php';
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	if (empty($_GET['catId'])){
		return;
	}

	$catId=(int)$_GET['catId'];
	$category = new Mage_Catalog_Model_Category();
	//$category->load(414); 
	$category->load($catId); 		
	$prodCollection = $category->getProductCollection();
	$prdIds=array();
	if (!empty($_GET['simple_only']) && $_GET['simple_only']=='true'){
		foreach ($prodCollection as $product) {
			if ($product->getTypeId()=='simple'){
				$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
			}
		}
	}else if (!empty($_GET['configurable_only']) && $_GET['configurable_only']=='true'){
		foreach ($prodCollection as $product) {
			if ($product->getTypeId()=='configurable'){
				$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
			}
		}
	}else{
		foreach ($prodCollection as $product) {
			$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
		}
	}
	//var_dump($prdIds);
	//now I have all the product ids in this category
	$product_names=array();
	$obj = Mage::getModel('catalog/product');
	foreach($prdIds as $prdId){
		$product_id = $prdId;
		$_product = $obj->load($product_id); // Enter your Product Id in $product_id

		array_push($product_names,$_product->getName());
	}

	//-----------------------------------------get unique product names---------------------------
	$unique_names=array();
	foreach ($product_names as $name){
		$processed_name=process($name);
		if (!in_array($processed_name, $unique_names)){
			array_push($unique_names,$processed_name);
		}
	}
	//var_dump($unique_names);

	//echo count($product_names);
	//echo '<br>';
	//echo '<pre>'; //if you add <pre> json can't decode
	if (!empty($_GET['unique']) && $_GET['unique']=="true"){
		echo json_encode($unique_names, JSON_PRETTY_PRINT);
	}else{
		echo json_encode($product_names,JSON_PRETTY_PRINT);
	}
	//echo '</pre>';




	/*
		returns the name without ', L' etc
	*/
	function process($name){
		$comma_pos=strpos($name,',');
		if ($comma_pos!==false){
			return substr($name,0,$comma_pos);
		}else{
			return $name;
		}
	}

	$fp=null;
	public function log($msg){
		//$fp = fopen('log.txt', 'a');//append
		fwrite($fp, $msg."\n");
		//fclose($fp);
   	}
