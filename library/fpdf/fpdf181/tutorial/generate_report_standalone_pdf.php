<?php

$start_date=$_GET['start_date'];
$end_date=$_GET['end_date'];

$error_msg='';
if (empty($start_date) || !isset($end_date)){
    $error_msg.="start date cannot be empty<br>";
}
if (empty($end_date) || !isset($end_date)){
    $error_msg.='end date cannot be empty<br>';
}
if (!empty($error_msg)){
    echo "<div style='color:red'>".$error_msg."</div>";
    exit;
}
// var_dump($start_date);//"03-01-2016" 
// var_dump($end_date);//"04-01-2016"
// return;
$array_start_date=explode('/', $start_date);
$formated_start_date=$array_start_date[2].'-'.$array_start_date[0].'-'.$array_start_date[1];
$array_end_date=explode('/', $end_date);
$formated_end_date=$array_end_date[2].'-'.$array_end_date[0].'-'.$array_end_date[1];
// var_dump($formated_start_date);
// var_dump($formated_end_date);
// return;






// $product=new product("2912901415386940C");
// $product=new product("2912900187087780C");
// var_dump($product);
// return;

require_once("../../../../product_manager/database/dbcontroller.php");
require_once("../../../../product_manager/config.php");
require_once('alice_helper.php');

$db_handle=new DBController();

$all_skus_to_display=array();
// $results=$db_handle->runQuery("select sku from products");
$results=$db_handle->runQuery("select  sku from products where update_timestamp>=\"$formated_start_date\" and update_timestamp<=\"$formated_end_date\"  ;");
if (empty($results) || $results==false || $results==-1){
    echo '<div style="margin: 20px 50px;padding: 10px;background: #F3F3F3;border-radius: 10px;border: 1px solid lightgrey;">there are no records matching this date range: '.$start_date.'-'.$end_date.'</div>';
    exit;
}
// var_dump($results);

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
$eligible_products=array();
// echo '<pre>';
foreach ($all_skus_to_display as $sku) {
	$product=new product($sku);
	// var_dump($product);
	$eligible_products[]=$product;

}
// echo '</pre>';

// return;
// var_dump(count($eligible_products));

// require('../fpdf.php');
require('../AlphaPDF.php');

// $pdf = new FPDF();
$pdf = new AlphaPDF();

$pdf_w=$pdf->w;//210
$pdf_h=$pdf->h;//297
$td_width=50;  //less than 1/4 of the pdf width
$td_height=55; //less than 1/5 of the pdf height
$has_border=0;

// $pdf->AddPage();
// $pdf->SetFont('Arial','B',10);
$pdf->SetFont('Arial','',10);
    // $pdf->SetXY(50,10);
    // $pdf->Cell(0,0,$pdf_w,0,0,'L');

    // $pdf->SetXY(110,10);
    // $pdf->Cell(0,0,$pdf_h,0,0,'L');

$config=new configuration();
$summary='From '.$start_date.' To '.$end_date.': '.count($eligible_products).' products were submitted.';
$config->printSummary($pdf,$summary);



$i=0;
foreach ($eligible_products as $object) {
    if ($i%16==0){
        // break;
        $config->addPage($pdf);
    }

    $cell_table=$config->cell_table;
    $position=$cell_table[$i%16];//range from 0-15
    $cell_1_x=$position['x']+1;//offset a little from the border
    $cell_1_y=$position['y']+3;
    $pdf->SetXY($cell_1_x,$cell_1_y);
    $pdf->Cell(0,0,$i.'.'.format($object->name),$has_border,0,'L');

    $cell_1_y+=4;
    $pdf->SetXY($cell_1_x,$cell_1_y);
    // $pdf->Cell(0,0,$object->sku,$has_border,0,'L');  //sku is too long. ommit it
    // $cell_1_y+=4;-->uncomment if you want sku

    // $pdf->SetXY($cell_1_x,$cell_1_y);
    // $image1 = "product1.png";
    $image_path=$object->image;
    // $server_image_path="/usr/share/nginx/www/1661hk/media/catalog/product".$image_path;
    require $magento_root.'alice/product_manager/config.php';
    $server_image_path=$magento_root."alice/product_manager/".$image_path;
    // var_dump(file_exists($server_image_path));
    
	if (is_null($image_path) || $image_path=='no_selection' || !file_exists($server_image_path)){
    	$pdf->Image("images/no_image.png", $cell_1_x, $cell_1_y, 45,45);
		// echo 'skipping';
	}else{
        $dimension_array = calculatePdfImageWidthParam($server_image_path);
        // $full_image_path="http://www.prosepoint.org/docs/no_image.png";//works
        // continue;
        //-------find the image width param ($resized_image_width) calculated by FPDF (ready to draw-----------
        $resized_image_width='';
        if ($dimension_array['width']==0 && $dimension_array['height']==0){
            $resized_image_width=0;//cannot read dimension, don't offset cell_1_x of image.
        }elseif ($dimension_array['width']==0){
            //calculate based on resized height:
            $resized_image_height=45;
            list($width,$height)=getimagesize($server_image_path);
            $ratio=$width/$height;
            $resized_image_width=$ratio*$resized_image_height;
        }elseif ($dimension_array['height']==0){
            $resized_image_width=45;
        }else{
            //both dimension less than 45
            $resized_image_width=$dimension_array['width'];
        }
        //----------calculate left offset to make image in middle of cell--------
        $horizontal_empty_length=$td_width-$resized_image_width;
        $image_left_offset=$horizontal_empty_length/2-1;//minus 1 to take the current left offset (almost 1) into consideration.

        // var_dump($horizontal_empty_length);
        // var_dump($image_left_offset);
        // continue;
        $error_msg=$pdf->Image($server_image_path, $cell_1_x+$image_left_offset, $cell_1_y, $dimension_array['width'],$dimension_array['height']);
            

		// $full_image_path="http://www.prosepoint.org/docs/no_image.png";//works
    	// $error_msg=$pdf->Image($server_image_path, $cell_1_x, $cell_1_y, 45,45);
    	if (!is_null($error_msg) && strpos($error_msg, "FPDF error: Not a JPEG file")!==false){
    		$pdf->SetXY($cell_1_x,$cell_1_y+20);
    		$pdf->Cell(0,0,'JPEG exists but not valid.',$has_border,0,'L');
    	}else if (!is_null($error_msg)){//other FPDF error/exceptions
    		$pdf->SetXY($cell_1_x,$cell_1_y+20);
    		// $pdf->Cell(0,0,$error_msg,$has_border,0,'L');
    		$pdf->Cell(0,0,'product image not valid',$has_border,0,'L');
    	}
   	}

   	//------------finish writing product image----------------------------------------
	// continue;
    $cell_1_y+=54;
    //started outputing attributes:
    $competitors=$object->all_competitor_data;
    $temp_x=$cell_1_x;
	
	$count=0;
    foreach($competitors as $competitor_name=>$competitor_data){
        //output amazon_icon.png etc. 
        // $icon_path = getImagePath($company);
    	$pdf->SetXY($cell_1_x,$cell_1_y);
        // $pdf->Image($icon_path, $cell_1_x, $cell_1_y, 5,5);
        
        $pdf->SetFillColor(222,222,255);
        $pdf->SetAlpha(0.7);
       	$pdf->Rect($cell_1_x, $cell_1_y-2, 48, 8, 'DF');
        // $pdf->Rect($cell_1_x, $cell_1_y, 47, 7);
        $pdf->SetAlpha(1);

       	$pdf->Cell(0,0,$competitor_name,$has_border,0,'L');
        //output price
        $cell_1_x+=7;
        $cell_1_y+=4;
        $pdf->SetXY($cell_1_x,$cell_1_y);

        $price_sentence="";
        $product_price=$competitor_data['price'];
        $product_shipping=$competitor_data['shipping'];
        if ($product_shipping===false){//not recorded
        	$price_sentence=$product_price;
        }elseif ($product_shipping=="Free Shipping"){//free shipping
        	$price_sentence=$product_price." (Free Shipping)";
        }else{//shipping is recorded and not Free shipping
        	$subtotal=getSumOfPrice($product_price,$product_shipping);
        	// var_dump($product_price.$product_shipping.$subtotal);
			if ($subtotal===false){//invalid or not unified price format.
				// $subtotal="N/A";
				$price_sentence=$product_price;
			}else{
				$price_sentence=$product_price.'+'.$product_shipping.'='.$subtotal;
			}
        }
        $pdf->Cell(0,0,$price_sentence,$has_border,0,'L',0,$competitor_data['product_url']); 
        // $pdf->Cell(0,0,$competitor_data['price'],$has_border,0,'L');    
        
            // $cell_1_x+=20;//link on this row
            // $pdf->SetXY($cell_1_x,$cell_1_y);
            // // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'','http://www.google.com');
            // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y-3, 0,0,'',$competitor_data['product_url']);

        $cell_1_y-=12;//next row
        $cell_1_x=$temp_x;
    	$count++;
		if ($count>7){
        	break;//canot hold this much. even if we are piling from bottom to top.
        }
    }
    // break;
    $i++;
}


// $pdf->Cell(100,10,'Hello World!');
// $pdf->Cell(140,10,'Hello World!');
$pdf->Output();
exit;


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
function format($string){
    return substr($string, 0,21);
}
function getImagePath($image_name){
    return "images/".$image_name."_icon.png";
}
function buildArray($attributes){
    $array=array();
    $array["amazon"]["price"]=$attributes["amazon"];
    $array["amazon"]["link"]=$attributes["url_amazon"];
    $array["jd"]["price"]=$attributes["jd"];
    $array["jd"]["link"]=$attributes["url_jd"];
    $array["taobao"]["price"]=$attributes["taobao"];
    $array["taobao"]["link"]=$attributes["url_taobao"];
    $array["tmall"]["price"]=$attributes["tmall"];
    $array["tmall"]["link"]=$attributes["url_tmall"];
    return $array;
}
function format_2($value){
    if(is_null($value) || $value=="RMB" || $value=="USD"){
        return "n/a";
    }else{
        return $value;
    }
}
