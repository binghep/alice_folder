<?php
/*
This file creates a subcategory of Nutrition category (id: 80)
*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

// Mage::setIsDeveloperMode(true);
// ini_set('display_errors', 1);
// umask(0);
// Mage::app('admin');
// Mage::register('isSecureArea', 1);

//-------------------------------------------------------
// $new_cat_name="Minerals Liquid";
$new_cat_name="Food";
// $parentId = '654';
$parentId = '80';
$is_top_level_menu_item=0;
//-------------------------------------------------------
$flag_file_path='flag.csv';
//-------------------------------------------------------

try{
	if (file_exists($flag_file_path)) {
		echo '2nd time. aborting...';
		exit;
	}else{
		write_log("lala", $flag_file_path);
	}
	$category = Mage::getModel('catalog/category');
    $category->setName($new_cat_name);
    $category->setData("include_in_menu",$is_top_level_menu_item);//if 1, means is root level category
    // $category->setUrlKey('your-cat-url-key');
    $category->setIsActive(1);
	    // echo '<pre>';
	    // var_dump($category);
	    // echo '</pre>';
    $category->setDisplayMode('PRODUCTS');
    $category->setIsAnchor(1); //for active anchor
    $category->setStoreId(Mage::app()->getStore()->getId());

    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
    $category->setPath($parentCategory->getPath());
    $category->save();
    echo 'Successfully created new category. ';
} catch(Exception $e) {
    print_r($e);
}



function write_log($object,$flag_file_path)
{  
 error_log($object."\n", 3, $flag_file_path);
    return true;
}

