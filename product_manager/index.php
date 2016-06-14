<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

if (!isset($_SESSION['user']) && $_GET['secret_path']==="yes"){
	$_SESSION['user']=array();
	$_SESSION['user']['login']='ddd';
	$_SESSION['user']['id']=100;
	$_SESSION['user']['name']='ddd';
}

if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}


?>


<head>
	<title>Price Research Form - module 2 - 1661USA products</title>
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
if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


$product = Mage::getModel('catalog/product');

$cat_id = $_GET['cat_id'];
if (empty($cat_id) || !is_numeric($cat_id)) {
	echo 'param not valid. exiting...';
	exit;
}

$p=1;
if (!empty($_GET['p'])) {
	$p=$_GET['p'];
}

$type_id=isset($_GET['type_id'])?$_GET['type_id']:"smart";

include_once 'views/index_view_header_block.php';

$category = new Mage_Catalog_Model_Category();
$category->load($cat_id);//414 

// var_dump($_SESSION['user']['id']);


// ----------------------------------------------------------------------------------------
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
					    ->addAttributeToSelect('i_recommend')
					    // ->addAttributeToSelect('url_amazon')
					    // ->addAttributeToSelect('url_jd')
					    // ->addAttributeToSelect('url_tmall')
					    // ->addAttributeToSelect('url_taobao')

					    // ->addAttributeToSelect('amazon')
					    // ->addAttributeToSelect('jd')
					    // ->addAttributeToSelect('tmall')
					    // ->addAttributeToSelect('taobao')
					     ;
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

if ($type_id=="configurable" || $type_id=="simple"){
	$productCollection->addAttributeToFilter('type_id', $type_id);
}else if ($type_id=="smart"){
	$productCollection->addAttributeToFilter('visibility',4);//all visible products.
	// $productCollection->addAttributeToFilter(//both simple and configurable products
	// 	array(
	// 		array(
	// 			"attribute"=>"type_id",
	// 			'in'=>array("configurable"),
	// 		),
	// 		array(
	// 			"attribute"=>"type_id",
	// 			'in'=>array("simple"),
	// 		),
	// 	)
	// );
	$productCollection->addAttributeToFilter('type_id',array(
		'in'=>array("configurable","simple"),
		));
}else{//all products
	$productCollection->addAttributeToFilter('type_id',array(
		'in'=>array("configurable","simple"),
		));	
}



// //echo $data = $collect->getSelect(); 

/*   do not work for unset/NULL attirbutes (NULL)
$productCollection->addAttributeToFilter(
	array(
		array('attribute'=> 'url_amazon','like' => ''),
		array('attribute'=> 'url_jd','like' => ''),
		array('attribute'=> 'url_taobao','like' => ''),
		array('attribute'=> 'url_tmall','like' => ''),
	)	
);
*/


  //--------------------------------works------------------------------
$non_empty_attribute=$_GET['non_empty_attribute'];
$filter_options_non_empty_attribute=array("url_amazon"=>"need amazon url/只需 亚马逊 链接",
                    "url_jd"=>"need jd url/只需 京东 链接",
                    "url_taobao"=>"need taobao url/只需 淘宝 链接",
                    "url_tmall"=>"need tmall url/只需 天猫 链接",
                    "price_amazon"=>"need amazon price/只需 亚马逊 价格",
                    "price_jd"=>"need jd price/只需 京东 价格",
                    "price_taobao"=>"need taobao price/只需 淘宝 价格",
                    "price_tmall"=>"need tmall price/只需 天猫 价格",
                    "show_all"=>"show all/显示全部");


$in_array=false;
foreach ($filter_options_non_empty_attribute as $key => $value) {
	// var_dump($key);var_dump($non_empty_attribute);echo '<br>---------------<br>';
    if ($key==$non_empty_attribute){
        $in_array=true;
        break;
    }
}

if ($in_array==false){//default to show_all
    $non_empty_attribute="show_all";
}

// var_dump($non_empty_attribute);

if ($in_array ==true && $non_empty_attribute!=="show_all"){
	$productCollection->addAttributeToFilter($non_empty_attribute,array('null'=>true),'left');//show all products with no amazon_url = NULL. 
}
//----------------------------------------------------------------------
/* works:
// Add OR condition: if any of these fields are not null
$productCollection->addAttributeToFilter(
	array(
		array('attribute'=> 'url_amazon','notnull'=>true),
		array('attribute'=> 'url_jd','notnull'=>true),
		array('attribute'=> 'url_taobao','notnull'=>true),
		array('attribute'=> 'url_tmall','notnull'=>true),
	)	
);
*/

// $productCollection->addAttributeToFilter(
//  	array('url_amazon', 'url_jd'),
//     array(
//         array('like'=>'htt'), 
//         array('like'=>'')
//     )
// );

// $productCollection->getSelect()->where("at_url_amazon is NULL");

// echo $data = $productCollection->getSelect(); 
// var_dump($data);







//-----------------------------------------------------------------------------------------
$productCollection->addCategoryFilter($category);

$number_of_products_to_display=$productCollection->getSize();
if ($_SESSION['user']['login']=='ddd'){
	$product_per_page=$product_per_page_for_test_account;
}
$page_total=ceil($number_of_products_to_display/$product_per_page);
// ----------------------------------------------------------------------------------------
$productCollection->setPageSize($product_per_page)
				    ->setCurPage($p)
					;

include_once 'views/index_view_category_block.php';
// include_once 'views/index_view_table_block.php';
include_once 'views/form_block_container.php';
// echo '<span id="curr_page" style="display:none">'.$p.'</span>';
function outputFilterPopUp($cat_id,$filter_options_non_empty_attribute,$non_empty_attribute){
	  // echo '<link rel="stylesheet" href="alice_style.css">';
    // ---------------------------filters---------------
    echo '<div id="light" class="white_content">
            <div id="filter_body">
            <div id="filter_close_button_wrapper">
                <a href="javascript:void(0)" id="close_pop_up">Close</a>
            </div>';

    // --------filter_1: NULL product attribute: default to url_amazon---------
    foreach ($filter_options_non_empty_attribute as $key => $value) {
    	if ($non_empty_attribute==$key){
    		echo '<div style="font-weight:bold;">'.$value.'</div>';
    	}else{
        	echo '<div><a style="text-decoration: none;color:blue;" href="index.php?cat_id='.$cat_id.'&type_id=smart&non_empty_attribute='.$key.'">'.$value.'</a></div>';
    	}
    }
    echo '<hr>';
    // ---------------------------filter_2: mode: default to smart mode---------------
    $filter_options=array("smart"=>"smart/智能模式",
                        "configurable"=>"show configurable products only/只显示configurable商品",
                        "simple"=>"show simple products only/只显示简单产品",
                        "all"=>"show all/显示全部");
	
	$in_array=false;
    foreach ($filter_options as $key => $value) {
    	if ($key==$type_id){
    		$in_array=true;
    		break;
    	}
    }
    if ($in_array==false){
    	// echo 'type_id is not valid...default to "smart" mode';
    	$type_id="smart";
    }

	foreach ($filter_options as $key=>$value) {
	    if ($key == $type_id) {
	        // echo 'found!';
			echo '<div style="font-weight:bold;">'.$value."</div>";
	    }else{
	    	// echo 'not match';
			echo "<div><a style='text-decoration: none;color:blue;' href='index.php?cat_id=".$cat_id."&type_id=".$key."'>".$value."</a></div>";
	    }
	}
    
    echo '</div></div>';
    // ------------------------the black overlay---------------
	echo '<div id="fade" class="black_overlay"></div>';
}

function outputPager($page_total,$curr_page,$cat_id,$type_id,$non_empty_attribute){
	require "config.php";
	echo '<div id="pager">';
		echo '<div class="container">';

		// echo '<span id="search_sku_span"><input id="search_sku" type="text"><button type="button" id="search_sku_button">Search SKU</button></span>';
	    echo '<span id="search_sku_span">
				    <input id="search_sku" type="text" class="TextInput">
				    <button type="button" class="btn btn-default" id="search_sku_button">
			          <span class="glyphicon glyphicon-search"></span> Search SKU
			        </button>
        	   </span>';
       	//-----------------open pop up----------------------
	    echo '<ul class="pagination">';
	    echo '<li><a href="javascript:void(0)" id="open_pop_up"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span></a></li>';
	    echo '</ul>';
       	//-----------------open pop up----------------------
		// echo '<span id="page_x_out_of_x_span">Page '.$curr_page.' out of '.$page_total.'</span>';
		echo '<ul class="pagination">';
		echo '<li> <a href="'.$product_manager_url.'index.php?cat_id='.$cat_id.'&p=1&type_id='.$type_id.'&non_empty_attribute='.$non_empty_attribute.'"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a></li>';
	// glyphicon glyphicon-search
		if ($curr_page>1){
			echo '<li> <a href="'.$product_manager_url.'index.php?cat_id='.$cat_id.'&p='.($curr_page-1).'&non_empty_attribute='.$non_empty_attribute.'"><span class="glyphicon glyphicon-step-backward"></span></a></li>';
		}
		
		for($i=$curr_page-2;$i<=$curr_page+2;$i++){
			if ($i<1 || $i>$page_total){
				continue;
			}
			if ($i==$curr_page){
				echo "<li> <a style='background: lightgoldenrodyellow;' href='#' >$i</a></li>";
			}else{
				echo '<li> <a href="'.$product_manager_url.'index.php?cat_id='.$cat_id.'&p='.$i.'&type_id='.$type_id.'&non_empty_attribute='.$non_empty_attribute.'">'.$i.'</a></li>';
			}
		}
		if ($curr_page<$page_total){
			echo '<li> <a href="'.$product_manager_url.'index.php?cat_id='.$cat_id.'&p='.($curr_page+1).'&non_empty_attribute='.$non_empty_attribute.'"><span class="glyphicon glyphicon-step-forward"></span></a></li>';
		}
		echo '<li> <a href="'.$product_manager_url.'index.php?cat_id='.$cat_id.'&p='.$page_total.'&type_id='.$type_id.'&non_empty_attribute='.$non_empty_attribute.'"><span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span></a></li>';


		echo '</ul>';
		echo '</div>';
	echo '</div>';
//--------------------------
// 	echo '<div class="container">                
//   <ul class="pagination">
//     <li><a href="#">«</a></li>
//     <li><a href="#">1</a></li>
//     <li><a href="#">2</a></li>
//     <li><a href="#">3</a></li>
//     <li><a href="#">4</a></li>
//     <li><a href="#">5</a></li>
//     <li><a href="#">6</a></li>
//     <li><a href="#">»</a></li>
//   </ul>
// </div>';
}
