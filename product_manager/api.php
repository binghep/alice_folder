<?php
//http://www.1661hk.com/alice/product_manager/api.php?class=research_note&worker_id=14&new_value=testtest&sku=2912900288325540C
// if output of this file contains 'Failed.', then we display the error message in a newly created span in that cell the user is trying to update. 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}

require_once '../../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
header('Content-type: application/json');


$attribute = $_GET['class'];
$sku = $_GET['sku'];
$worker_id=(int)$_GET['worker_id'];
$new_value = $_GET['new_value'];

if (empty($attribute)) {
	// echo 'Failed. param not valid. exiting...';
	$response=array(
				"status"=>"error",
				"error_details"=>"attribute is not specified",
				"selected_attribute"=>$attribute,
				"worker_id"=>$worker_id,
				"sku"=>$sku,
				"new_value"=>$new_value
				);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}
if (empty($sku)) {
	$response=array(
				"status"=>"error",
				"error_details"=>"sku is not specified",
				"selected_attribute"=>$attribute,
				"worker_id"=>$worker_id,
				"sku"=>$sku,
				"new_value"=>$new_value
				);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}
if (empty($worker_id)) {
	$response=array(
				"status"=>"error",
				"error_details"=>"worker_id is not specified",
				"selected_attribute"=>$attribute,
				"worker_id"=>$worker_id,
				"sku"=>$sku,
				"new_value"=>$new_value
				);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}

// $numeric_fields=array("amazon","jd","taobao","tmall","special_price","price");
// if (in_array($attribute, $numeric_fields)){
// 	if (is_numeric($new_value) || empty($new_value)){//new_value must be numeric.
// 		//valid
// 	}else{
// 		$response=array(
// 					"status"=>"error",
// 					"error_details"=>"new_value is set but is not numeric",
// 					"selected_attribute"=>$attribute,
// 					"worker_id"=>$worker_id,
// 					"sku"=>$sku,
// 					"new_value"=>$new_value
// 				  );
// 		echo json_encode($response, JSON_FORCE_OBJECT);
// 		exit;
// 	}
// }

if (strpos($attribute, "price_")===0 || strpos($attribute,"url_")===0 ){
	updateAttribute($attribute,$sku,$new_value);
 	$result=log_record($attribute,$sku,$new_value,$worker_id);
 	
 	$response=array(
			"status"=>"success",
			"selected_attribute"=>$attribute,
			"worker_id"=>$worker_id,
			"sku"=>$sku
			);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}else if ($attribute=="i_recommend"){
	if ($new_value=="1"||$new_value=="0"){
		updateAttribute($attribute,$sku,(int)$new_value);
	 	$result=log_record($attribute,$sku,$new_value,$worker_id);
	 	
	 	$response=array(
				"status"=>"success",
				"selected_attribute"=>$attribute,
				"worker_id"=>$worker_id,
				"sku"=>$sku
				);
		echo json_encode($response, JSON_FORCE_OBJECT);
		exit;
	}else{
		$response=array(
			"status"=>"error",
			"error_details"=>"i_recommend cannot accept a non-numeric new value",
			"selected_attribute"=>$attribute,
			"worker_id"=>$worker_id,
			"sku"=>$sku,
			"new_value"=>$new_value
			);
		echo json_encode($response, JSON_FORCE_OBJECT);
		exit;
	}
}elseif ($attribute=="research_note"){
	// $new_value=addslashes($new_value);
	// echo 'here';
	updateAttribute($attribute,$sku,$new_value);
 	$result=log_record($attribute,$sku,$new_value,$worker_id);
 	
 	$response=array(
			"status"=>"success",
			"selected_attribute"=>$attribute,
			"worker_id"=>$worker_id,
			"sku"=>$sku
			);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}else{
	$response=array(
			"status"=>"error",
			"error_details"=>"Attribute not in allowed list. fall through to default in switch",
			"selected_attribute"=>$attribute,
			"worker_id"=>$worker_id,
			"sku"=>$sku,
			"new_value"=>$new_value
			);
	echo json_encode($response, JSON_FORCE_OBJECT);
	exit;
}






function updateAttribute($attribute,$sku,$new_value){
	// $product = Mage::getModel('catalog/product');
	$id=Mage::getModel("catalog/product")->getIdBySku( $sku );
	// var_dump($id);
	$array_product=array($id);
	// var_dump($array_product);
	Mage::getSingleton('catalog/product_action')->updateAttributes($array_product, array($attribute => $new_value), 0);
	// echo 'finished';
}
/*
write this action as one row in global_link_distribution database, product_attribute_update log.
*/
function log_record($attribute,$sku,$new_value,$worker_id){
	require 'database/dbcontroller.php';
	$db_handle=new DBController();
	// $query="select * from product_attibute_update_log";
	$query="insert into `product_attibute_update_log` (`worker_id`,`updated_attribute`,`value`,`updated_sku`) values ('".$worker_id."','".$attribute."','".$new_value."','".$sku."');";
	$result=$db_handle->runQuery($query);
	// return $db_handle->getError();
	return $result;
	// return $db_handle;
}
