<?php
/*
This file is used for dumping product category-wise json
*/
return;
require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//$product_sku="2912902144222100S";
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);

$category = new Mage_Catalog_Model_Category();
$category->load(438); //My cat id is 10
$prodCollection = $category->getProductCollection();
$prdIds=array();
foreach ($prodCollection as $product) {
	$prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
}
//var_dump($prdIds);
//now I have all the product ids in this category
$product_names=array();
foreach($prdIds as $prdId){
	$product_id = $prdId;

	$obj = Mage::getModel('catalog/product');
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


echo count($product_names);
echo '<br>';
echo '<pre>';
//var_dump($unique_names);
echo '</pre>';

foreach($unique_names as $unique_name){
	$simple_names=getItsSimpleNames($product_names, $unique_name);
	$simples=null;
	echo '--------------its simple contains---------- <br>';
	if (is_null($simple_names)) {echo 'null<br>';}
	foreach ($simple_names as $simple_name){
		//$product=Mage::getModel('catalog/product')->loadByAttribute('name',$simple_name);
		//echo $product->getSku().'<br>';
		//array_push($simples,$product);
		echo $simple_name.'<br>';
	}
	echo '--------------------------------------------<br>';
	
}

function getItsSimpleNames($all_product_names,$unique_name){
	$itsSimpleNames=null;
	//var_dump($all_product_names);
	foreach ($all_product_names as $product_name){
		if (process($product_name)==$unique_name){
			//echo 'match<br>';
			$itsSimpleNames[]=$product_name;
		}else{
			//echo 'not match<br>';
		}
	}
	return $itsSimpleNames;
}

// $x="Arc'teryx Straibo Hooded Fleece Jacket - Men's Golden Palm";
// echo '<pre>';
// var_dump(getItsSimpleNames($product_names,$x));
// echo '</pre>';
