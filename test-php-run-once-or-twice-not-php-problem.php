<?php
return;
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
$flag_file_path='flag.csv';
//-------------------------------------------------------

try{
	if (file_exists($flag_file_path)) {
		echo '2nd time...';
		// exit;
	}else{
		write_log("lala", $flag_file_path);
	}

    echo 'Successfully run the first time. ';
} catch(Exception $e) {
    print_r($e);
}



function write_log($object,$flag_file_path)
{  
 error_log($object."\n", 3, $flag_file_path);
    return true;
}

