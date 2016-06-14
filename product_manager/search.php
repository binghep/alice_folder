<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

if (!isset($_SESSION['user'])){
    header('Location: trimmed_version/login.php');
}
?>

<head>
	<title>Search Products</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>

    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
   	
   	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
   	<link rel="stylesheet" type="text/css" href="css/logo_bar2.css">
	<link rel="stylesheet" type="text/css" href="css/main_form4.css">

</head>
<body>

<?php
echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';
require_once '../../app/Mage.php';
require_once 'config.php';
require_once 'setting_manager.php';
$setting=new setting_manager("database/setting.txt");

if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);



$sku = $_GET['sku'];
if (empty($sku) || !isset($sku)) {
	echo 'sku not valid. exiting...';
	exit;
}
//===================this method does not work because adding this attribute to Demo Attribute Set always times out, so I use the similar method as in index.php=======
// class product{
//     public $product_object;
//     public $research_note;
//     function __construct($sku) {
//         $product_id=Mage::getModel("catalog/product")->getIdBySku($sku);
//         if (is_null($product_id) || $product_id==false){
//         	return false;
//         }
//         // var_dump($product_id);
//         $this->product_object = Mage::getModel('catalog/product')->load($product_id);
//         // var_dump($this->product_object->getData('research_note'));
//     }
// }

// $p=new product($sku);



// if ($p==false){
// 	echo 'there is no product with this sku. ';
// 	exit;
// }

// $product=$p->product_object;
//============================final method=========================================
?>

<?php
$productCollection = Mage::getResourceModel('reports/product_collection')
                        // ->addAttributeToSelect('*')
                        ->addAttributeToSelect('sku')
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('type_id')
                        // ->addAttributeToSelect('image_url')
                        // ->addAttributeToSelect('image')-->this one is not the same as the one you see in back end. it can be an image url ending in "/no_selection". or the image is totally from other products.
                        ->addAttributeToSelect('small_image')
                        ->addAttributeToSelect('image')
                        ->addAttributeToSelect('thumbnail')
                        ->addAttributeToSelect('image_external_url')
                        ->addAttributeToSelect('price')
                        ->addAttributeToSelect('special_price')
                        ->addAttributeToSelect('short_description')
                        ->addAttributeToSelect('research_note')
                        ->addAttributeToSelect('i_recommend');
//-------------------------------------------------------------
    require_once 'setting_manager.php';
    $setting=new setting_manager("database/setting.txt");

    $allowed_competitors=$setting->get_allowed_competitor_array();
    if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
        echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Exiting';
        exit;
    }
    // var_dump($allowed_competitors);
    
    //-------------------------------------------------------------
    foreach ($allowed_competitors as $competitor) {
        $productCollection->addAttributeToSelect('url_'.$competitor)
                          ->addAttributeToSelect('price_'.$competitor);
        
    }
    //-------------------------------------------------------------

$productCollection->addAttributeToFilter('status',1); //only enabled product
$productCollection->addAttributeToFilter('sku',$sku); //only this sku
$product=null;
foreach ($productCollection as $p) {
    $product=$p;
}
?>

<?php

include_once 'views/index_view_header_block.php';
require_once 'views/form_block.php';
