<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$start_date=$_GET['start_date'];//05/09/2016
$end_date=$_GET['end_date'];//05/09/2016

ini_set('memory_limit','512M');

$error_msg='';
if (empty($start_date) || empty($end_date) ){
    $error_msg.="date cannot be empty<br>";
}
if (!empty($error_msg)){
    echo "<div style='color:red'>".$error_msg."</div>";
    exit;
}

$array_start_date=explode('/', $start_date);
$formated_start_date=$array_start_date[2].'-'.$array_start_date[0].'-'.$array_start_date[1];//2016-05-09

$array_end_date=explode('/', $end_date);
$formated_end_date=$array_end_date[2].'-'.$array_end_date[0].'-'.$array_end_date[1];//2016-05-09
// $product=new product("2912901415386940C");
// $product=new product("2912900187087780C");
// var_dump($product);
// return;

require_once("../../../../product_manager/database/dbcontroller.php");
require_once("../../../../product_manager/config.php");
require_once('alice_helper.php');

$db_handle=new DBController();

$all_skus_to_display=array();
$results=$db_handle->runQuery("select distinct  updated_sku from product_attibute_update_log where update_timestamp>=\"$formated_start_date\" and update_timestamp<=\"$formated_end_date\"+ INTERVAL 1 DAY ;");
if (empty($results) || $results==false || $results==-1){
    echo 'there are no records for this date. please add records first.';
    exit;
}
//-------------------------------------------------------------
require_once "../../../../../app/Mage.php";//if only 'require', then "=new product()" can work only once.
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$mage_product_model=Mage::getModel("catalog/product");
$_resource = Mage::getResourceSingleton('catalog/product');
//------------------build $recommended_skus array-------------------
$recommended_skus=array();
// return;
foreach ($results as $key => $value) {
	$sku=$value['updated_sku'];
    //now record the skus having is recommend=="1":
	$product_id=$mage_product_model->getIdBySku($sku);
    $i_recommend=$_resource->getAttributeRawValue($product_id, "i_recommend", 0);
    // var_dump($i_recommend);
    if ($i_recommend=="1"){
	    array_push($recommended_skus,$sku);
		// var_dump($product_id);
    }
}

// var_dump($recommended_skus);


//-------------------build $magento_attributes array-----------------
$magento_attributes=array();
// $magento_attributes=array("price_amazon","price_jd","price_taobao","price_tmall","url_amazon","url_jd","url_taobao","url_tmall");
require_once '../../../../product_manager/setting_manager.php';
$setting=new setting_manager("../../../../product_manager/database/setting.txt");
$allowed_competitors=$setting->get_allowed_competitor_array();
if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
    echo 'Cant read settings. or setting.txt is empty. no allowed competitors found.<br>';
}
// var_dump($allowed_competitors);
foreach ($allowed_competitors as $competitor) {
    array_push($magento_attributes, "price_".$competitor);
    array_push($magento_attributes, "url_".$competitor);
}
// var_dump($magento_attributes);
// return;
//-----------------------build $all_eligible_products array---------
$recommended_products=array();//containing "category_id" as key, and array of products under this category as value.
foreach ($recommended_skus as $sku) {
    $product=new product($sku,$db_handle,$magento_attributes);//$magento_attributes: all magento attributes
    // var_dump($product->sku);
    if ($product!==false){//has at least one non-empty competitor:
        $id=$mage_product_model->getIdbySku($product->sku);
    	if ($id===false) {
    		continue;
    	}
    	$mage_product_model = Mage::getModel('catalog/product');
        $mage_product=$mage_product_model->load($id);
    	// var_dump($mage_product);
    	if ($mage_product->getData("entity_id")===false){
    		continue;
    	}
    	$cat_ids=$mage_product->getCategoryIds();
        //---------for 1661 only--------------
        $except_duty_cat_id=$cat_ids;
        if (($key = array_search('597', $except_duty_cat_id)) !== false) {
            unset($except_duty_cat_id[$key]);
        }
        if (($key = array_search('598', $except_duty_cat_id)) !== false) {
            unset($except_duty_cat_id[$key]);
        }
    	$key=implode(",", $except_duty_cat_id);
        //------------------------------------
		// var_dump($key);
    	if (is_null($recommended_products[$key])){
    		// echo 'is null. Adding this key.<br>';
    		$recommended_products[$key]=array();
    	}
    	// echo '-----------------<br>';
    	$new_value=$recommended_products[$key];
    	$new_value[]=$product;
    	$recommended_products[$key]=$new_value;
    	// echo '-----------------<br>';
    }
}
// echo '<pre>';
// var_dump($recommended_products);
// echo '</pre>';
// return;
// var_dump($workers);
// require('../fpdf.php');
require('../AlphaPDF.php');

// $pdf = new FPDF();
$pdf = new AlphaPDF();

// $pdf->SetFont('Arial','B',10);
$pdf->SetFont('Arial','',10);

$config=new configuration();
$summary="[1661USA] Here are the recommended products:\n{$start_date} ~ {$end_date} (PST)\n".count($recommended_skus)." products were recommended.";
$config->printSummary($pdf,$summary);

//-----------------------print all category sections----------------
foreach ($recommended_products as $cat_id_key => $products) {
    $cat_ids=explode(",", $cat_id_key);
    // $inner_most_id=end($cat_ids);
    $cat_names=array();
    foreach ($cat_ids as $cat_id) {
        // var_dump($inner_most_id);
        $cat_obj=getCatObject($cat_id);
        if ($cat_obj===false){
            echo 'error';
            continue;
        }else{
            array_push($cat_names,$cat_obj->getName());
        }
    }
    $flatted_cat_names=implode("->", $cat_names);
    // var_dump($flatted_cat_names);
    // continue;
    $section_title="Category: ".$flatted_cat_names." ( ".count($products)." products )";
    // var_dump($section_title);
    $config->printPDFSection($pdf, $products, $section_title);
}

function getCatObject($cat_id)
{
    $category = new Mage_Catalog_Model_Category();
    $category->load($cat_id);//414 
    if(!$category->getId()) {
        return false;
    }else{
        return $category;
    }
}


$save_pdf_on_server=false;
if ($save_pdf_on_server){
    $filename="[1661USA]Recommended_products_by_category({$formated_start_date}~{$formated_end_date}).pdf";
    $pdf->Output($filename,'F');
    echo "Success. PDF is located at: /alice/library/fpdf/fpdf181/tutorial/".$filename;
}else{
    $pdf->Output();
}