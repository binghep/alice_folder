<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$worker_id=$_GET['worker_id'];
if (!isset($worker_id) || !is_numeric($worker_id)){
    die("pls specify valid worker id you want to view report from.");
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$start_date=$_GET['start_date'];//05/09/2016
$end_date=$_GET['end_date'];
$secret_code="sadioiow8923inksk65xzdweXdj";
ini_set('memory_limit','512M');

$error_msg='';
if ($_GET['secret_code']!==$secret_code){
    die("pls use correct secret key.");
}

if (empty($start_date) ){
    $error_msg.="start date cannot be empty<br>";
}
if (empty($end_date) ){
    $error_msg.="end date cannot be empty<br>";
}

if (!empty($error_msg)){
    die( "<div style='color:red'>".$error_msg."</div>");
}

$array_start_date=explode('/', $start_date);
$array_end_date=explode('/', $end_date);
$formated_start_date=$array_start_date[2].'-'.$array_start_date[0].'-'.$array_start_date[1];//2016-05-09
$formated_end_date=$array_end_date[2].'-'.$array_end_date[0].'-'.$array_end_date[1];//2016-05-09

// $product=new product("2912901415386940C");
// $product=new product("2912900187087780C");
// var_dump($product);
// return;

require_once("../../../../product_manager/database/dbcontroller.php");
require_once("../../../../product_manager/config.php");

require_once('alice_helper.php');

$db_handle=new DBController();

$possible_skus_to_display=array();

$worker_filter_string=" and worker_id = {$worker_id}";
$query="select distinct  updated_sku from product_attibute_update_log where update_timestamp>=\"$formated_start_date\" and update_timestamp<=\"$formated_end_date\"+ INTERVAL 1 DAY {$worker_filter_string};";
$results=$db_handle->runQuery($query);
// var_dump($query);
// var_dump($results);
if (is_null($results)){
    die('there are no records for this date. please add records first.');
}
// var_dump($results);
// return;
foreach ($results as $key => $value) {
    array_push($possible_skus_to_display,$value['updated_sku']);
}


// var_dump($possible_skus_to_display);
//-------------------build $magento_attributes array-----------------
$magento_attributes=array();
// $magento_attributes=array("price_amazon","price_jd","price_taobao","price_tmall","url_amazon","url_jd","url_taobao","url_tmall");
require_once '../../../../product_manager/setting_manager.php';
$setting=new setting_manager("../../../../product_manager/database/setting.txt");
$allowed_competitors=$setting->get_allowed_competitor_array();
if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
    die( 'Cant read settings. or setting.txt is empty. no allowed competitors found.<br>');
}
// var_dump($allowed_competitors);
foreach ($allowed_competitors as $competitor) {
    array_push($magento_attributes, "price_".$competitor);
    array_push($magento_attributes, "url_".$competitor);
}
// var_dump($magento_attributes);
// return;
// echo '<pre>';
//-----------------------build $all_elegible_products array---------
$all_eligible_products=array();//save double work on product->load()
foreach ($possible_skus_to_display as $sku) {
    $product=new product($sku,$db_handle,$magento_attributes);//$magento_attributes: all magento attributes
    // var_dump($product);
    // break;
    //---for .myself.php file only. cause the most recent worker might not be me.---
    if ($product->get_worker_id()!==$_GET['worker_id']){
        continue;
    }
    //-------------------------------------------------------------------------------
    if (is_null($product->error)){
        array_push($all_eligible_products, $product);
    }
}
// echo '<pre>';
// var_dump($all_eligible_products);
// return;
// echo '<pre>';
// return;
//-------------------build $workers array-----------------
// var_dump($possible_skus_to_display);
$workers=array();//key represents worker, value contains products that have at least one non-empty attribute submitted by this worker.
foreach ($all_eligible_products as $product) {
    $worker_id=$product->get_worker_id();
    $worker_name=$product->get_worker_name();
    // var_dump($worker_id);
    // var_dump($worker_name);
    // break;
    if ($worker_id===false || $worker_name===false){continue;}
    $worker_created=isWorkerCreated($worker_id,$workers);
    if (!$worker_created){
        // var_dump($worker_name);
        $workers[]=array("worker_id"=>$worker_id,"worker_name"=>$worker_name,"products"=>array());
    }
    //----------add current product to this worker-------------
    $new_workers=addProductToExistingWorker($workers,$product);
    // var_dump($new_workers);
    if ($new_workers!==false && !empty($new_workers)){//should be!
        $workers=$new_workers;
    }
}
// return;
// var_dump($workers);
if (empty($workers)){//price or url was updated but was updated to empty in latest update. 
    die('there are no records for this date. please add records first.');	
}
// return;
// require('../fpdf.php');
require('../AlphaPDF.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// $pdf = new FPDF();
$pdf = new AlphaPDF();



// $pdf->SetFont('Arial','B',10);
$pdf->SetFont('Arial','',10);
$pdf->SetTitle('Price Research Report for 1661USA.com products');
$config=new configuration();
$summary="Price Research Report for 1661USA Website Products\n{$start_date} ~ {$end_date} (Beijing Time)\n".count($all_eligible_products)." products were updated and have competitor price. \n\n Note: if someone else update the same product after you during this date range, the product will \nonly show up in his report.";
// $config->printSummary($pdf,$summary);

//--------------------build a $recommended_products array--------------------------
$recommended_products=array();
foreach ($all_eligible_products as $object) {
    if ($object->i_recommend=="1"){
        array_push($recommended_products, $object);
    }//else //"0" or NULL
}
// var_dump($recommended_products);
//------------------build summary_bottom string-------------------------------------
$summary_bottom="I recommend ".count($recommended_products)." products.\nThe following are the number of products updated by me in this date range:\n";
foreach ($workers as $worker) {
    $product_count=count($worker['products']);
    $summary_bottom.=$worker['worker_name'].": ".$product_count." products.\n";
}
// var_dump($summary_bottom);
// var_dump($summary);

$config->printSummary($pdf,$summary,$summary_bottom);


//--------------------print recommended products section----------------------
if (!empty($recommended_products)){
    $section_title="Recommended Products: ".count($recommended_products)." products";
    // var_dump($recommended_products);
    $config->printPDFSection($pdf, $recommended_products, $section_title);
}
// echo 'here';
//--------------------print today's updated products by employee----------------------
foreach ($workers as $worker) {
    $product_count=count($worker['products']);
    $section_title=$worker['worker_name']." updated ".$product_count." products.";
    // var_dump($worker['products']);
    $config->printPDFSection($pdf, $worker['products'], $section_title);
}


// $pdf->Cell(100,10,'Hello World!');
// $pdf->Cell(140,10,'Hello World!');


$save_pdf_on_server=false;
if ($save_pdf_on_server){
    $filename="price_research_report_for_[1661USA_Website_Products]({$formated_start_date}~{$formated_end_date}).pdf";
    // var_dump($filename);
    // return;
    $pdf->Output($filename,'F');
    echo "Success. PDF is located at: /alice/library/fpdf/fpdf181/tutorial/".$filename;
}else{
    $pdf->Output();
}
return;


// ---------------------helper functions-------------------------

function isWorkerCreated($worker_id,$workers){
    foreach ($workers as $index => $worker_info) {
        if ($worker_info['worker_id']==$worker_id){
            //this worker already exists in $workers. just need to add products
            return true;
        }
    }
    return false;
}
function addProductToExistingWorker($workers,$product){
    $new_workers=$workers;
    foreach ($workers as $index => $worker_info) {
        if ($worker_info['worker_id']==$product->worker_id){
            //this worker already exists in $workers. just need to add products
            $products=$worker_info['products'];
            $products[]=$product;
            $new_workers[$index]['products']=$products;
            return $new_workers;
        }
    }
    return false;
}
/*
$left_margin_x_large=10;
   
$pdf->SetXY( $left_margin_x_large, 24 );
$pdf->Cell( 0, 0, '1661HK', $border, 0, 'L' );
$pdf->SetXY( $left_margin_x_large, 35 );
$pdf->Cell( 0, 0, iconv_helper('陕西'), $border, 0, 'L' );
// $pdf->Cell( 0, 0, iconv_helper('陕西省西安市高新区高科尚都摩卡').'6栋2408室', $border, 0, 'L' );
$pdf->SetXY( $left_margin_x_large, 41 );
$pdf->Cell( 1, 0, iconv_helper('电话    029-88326923'), $border, 0, 'L' );

$pdf->SetXY( 150, 103.0 );
$pdf->Cell( 1.5, 0, '852 671 0108', $border, 0, 'L' );

$pdf->SetXY( $left_margin_x_large, 107.0 );
$pdf->Cell( 1.5, 0, '1661HK', $border, 0, 'L' );


$pdf->Output();

*/
