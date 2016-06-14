<?php
return;
/*
This file creates a subcategory of Nutrition category (id: 80)
*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

//-------------------------------------------------------
$category_to_move=646;//bars
$new_sister_id=645;// move above category to make it a sister of 645 men's clothing. 
//-------------------------------------------------------
$flag_file_path='change_category_parent_flag.csv';
//-------------------------------------------------------
try{
	if (file_exists($flag_file_path)) {
		echo '2nd time. aborting...';
		exit;
	}else{
		write_log("lala", $flag_file_path);
	}
    $category = new Mage_Catalog_Model_Category();
    $category->load($category_to_move);
   
    $parentCategory = Mage::getModel('catalog/category')->load($new_sister_id);
    $category->setPath($parentCategory->getPath());
    $category->save();
    echo 'Successfully moved category. ';
} catch(Exception $e) {
    print_r($e);
}




function write_log($object,$flag_file_path)
{  
	error_log($object."\n", 3, $flag_file_path);
    return true;
}

