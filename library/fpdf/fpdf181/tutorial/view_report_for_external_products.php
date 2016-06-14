<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$start_date=$_GET['start_date'];//05/09/2016
$end_date=$_GET['end_date'];
$secret_code="sadioiow8923inksk65xzdweXdj";

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
require_once('alice_helper_for_external_products.php');

$db_handle=new DBController();

$all_skus_to_display=array();
// $results=$db_handle->runQuery("select sku from products");
$query="select  sku from products where update_timestamp>=\"$formated_start_date\" and update_timestamp<=\"$formated_end_date\"+ INTERVAL 1 DAY ;";
// var_dump($query);
// return;
$results=$db_handle->runQuery($query);

if (empty($results) || $results==false || $results==null){
    die('there are no records for this date. please add records first.');
}


foreach ($results as $key => $value) {
    array_push($all_skus_to_display,$value['sku']);
}

/*
// var_dump($all_skus_to_display);
$eligible_products=array();//has at least one non-empty attribute
foreach ($all_skus_to_display as $sku) {
    // var_dump($sku);
    $new_product=new product($sku);
    if (!is_null($new_product->not_null_attributes)){
        $eligible_products[] = $new_product;
    }
    // echo 'finished';    
}
*/

// var_dump($all_skus_to_display);
// return;
$workers=array();
foreach ($all_skus_to_display as $sku) {
	$product=new external_product($sku,$db_handle);
// echo '<pre>';
// 	var_dump($product);
// echo '</pre>';
// return;
	$worker_id=$product->get_worker_id();
	$worker_name=$product->get_worker_name();
	// var_dump($worker_id);
	// var_dump($worker_name);
	if ($worker_id===false || $worker_name===false){continue;}
	$worker_created=isWorkerCreated($worker_id,$workers);
	if (!$worker_created){
		$workers[]=array("worker_id"=>$worker_id,"worker_name"=>$worker_name,"products"=>array());
	}
	//----------add current product to this worker-------------
	$new_workers=addProductToExistingWorker($workers,$product);
	if ($new_workers!==false && !empty($new_workers)){//should be!
		$workers=$new_workers;
	}
}

// echo '<pre>';
// var_dump($workers);
// echo '</pre>';
// return;
function isWorkerCreated($worker_id,$workers){
	foreach ($workers as $index => $worker_info) {
		if ($worker_info['worker_id']==$worker_id){
			//this worker already exists in $workers. just need to add products
			return true;
		}
	}
	return false;
}
/*
$workers array: 

array(
	0=>array(
		"worker_id"=>21,
		"worker_name"=>"Bertha",
		"products"=>array(0=>$product1, 1=>$product2)
		),
	1=>array(
		"worker_id"=>4,
		"worker_name"=>"Alice",
		"products"=>array(0=>$product5, 1=>$product6)
		),
	2=>array(
		"worker_id"=>2,
		"worker_name"=>"Grace",
		"products"=>array(0=>$product4, 1=>$product5)
		),		
	)
)
----------------------
if add success returns the new $workers array
if worker is not created, do nothing. return false
*/

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


require('../AlphaPDF.php');

$pdf = new AlphaPDF();
// echo 'here4';
// return;

$pdf->SetFont('Arial','',10);
// $pdf->SetFont('Arial','B',10);
$pdf->SetTitle('Price Research Report for External products');


// $pdf_w=$pdf->w;//210
// $pdf_h=$pdf->h;//297
$td_width=50;  //less than 1/4 of the pdf width
$td_height=55; //less than 1/5 of the pdf height
$has_border=0;
// $pdf->SetXY(50,10);
// $pdf->Cell(0,0,$pdf_w,0,0,'L');

// $pdf->SetXY(110,10);
// $pdf->Cell(0,0,$pdf_h,0,0,'L');

$config=new configuration();
$summary="Price Research Report for External Products\n{$start_date} ~ {$end_date} (Beijing Time)\n".count($all_skus_to_display)." products were submitted.";
// $summary_b="";
// foreach ($workers as $index => $worker_info) {
// 	$summary_b.=$worker_info['worker_name']." submitted ".count($worker_info['products'])." products.";
// }
//---------------build $summary_bottom string------------------
$summary_bottom="The following are the number of products updated by each employee in this date range:\n";
foreach ($workers as $worker) {
    $product_count=count($worker['products']);
    $summary_bottom.=$worker['worker_name'].": ".$product_count." products.\n";
}
$config->printSummary($pdf,$summary,$summary_bottom);
//------------------print products by employees-----------------
foreach ($workers as $worker) {
    $product_count=count($worker['products']);
    $section_title=$worker['worker_name']." submitted ".$product_count." products.";
    $config->printPDFSection($pdf, $worker['products'], $section_title);
    // var_dump($worker['products']);
}



$save_pdf_on_server=false;
if ($save_pdf_on_server){
	$filename="price_comparison_report_for_[external_products]({$formated_date}).pdf";
	// var_dump($filename);
	// return;
	$pdf->Output($filename,'F');
	echo "Success. PDF is located at: /alice/library/fpdf/fpdf181/tutorial/".$filename;
}else{
	$pdf->Output();
}
return;


function doesRemoteImageExit($image_url){
	// $ch = curl_init("http://www.example.com/favicon.ico");
	$ch = curl_init("$image_url");

	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// $retcode >= 400 -> not found, $retcode = 200, found.
	var_dump($retcode);
	curl_close($ch);
}



