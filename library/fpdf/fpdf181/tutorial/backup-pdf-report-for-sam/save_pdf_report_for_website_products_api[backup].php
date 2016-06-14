<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$date=$_GET['date'];

$error_msg='';
if (empty($date) ){
    $error_msg.="date cannot be empty<br>";
}
if (!empty($error_msg)){
    echo "<div style='color:red'>".$error_msg."</div>";
    exit;
}

$array_date=explode('/', $date);
$formated_date=$array_date[2].'-'.$array_date[0].'-'.$array_date[1];//03-01-2016


class product{
    public $name,$image,$sku,$not_null_attributes,$all_attributes;
    public $worker_id,$worker_name;
    public $db_handle;
    function __construct($sku) {
        require_once("../../../../product_manager/database/dbcontroller.php");
        $this->db_handle=new DBController();
        // require_once "/usr/share/nginx/www/1661hk/app/Mage.php";//if only 'require', then "=new product()" can work only once.
        require_once "../../../../../app/Mage.php";//if only 'require', then "=new product()" can work only once.
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product_id=Mage::getModel("catalog/product")->getIdBySku($sku);
        // var_dump($product_id);
        $product_model = Mage::getModel('catalog/product');
        // return;
        $product=$product_model->load($product_id);
        
        $this->sku=$sku;
        $this->name=$product->getName();
        $this->image=$product->getThumbnail();
        
        $this->all_attributes=array("amazon","jd","taobao","tmall","url_amazon","url_jd","url_taobao","url_tmall");
        foreach ($this->all_attributes as $attribute) {
            $attribute_value=$product->getData($attribute);
            // var_dump($attribute.'  '.$attribute_value.'<br>');
            if (is_null($attribute_value) || empty($attribute_value)){
                continue;
            }
            $this->not_null_attributes[$attribute]=$attribute_value;
        }
        $this->worker_id=$this->get_worker_id($this->sku);
        $this->worker_name=$this->get_worker_name($this->worker_id);
    }
    /*
    Return worker_id of this product.
    Return false if no info about this sku is found
    */
    function get_worker_id($sku){
        $query="select * from product_attibute_update_log where updated_sku='{$this->sku}' LIMIT 1; ";
        $result=$this->db_handle->runQuery($query);
        if (is_null($result)){
            return false;
        }
        $worker_id=$result[0]['worker_id'];
        return $worker_id;
    }
    /* 
    Return acc_name of the worker with worker_id
    Return false if no worker is found. 
    */
    function get_worker_name($worker_id){
        $query="select acc_name from workers where acc_id='{$worker_id}'";
       // var_dump($query);

        $result=$this->db_handle->runQuery($query);
        if (is_null($result)){
            return false;
        }
        $worker_name=$result[0]['acc_name'];
        return $worker_name;    
    }

}


// $product=new product("2912901415386940C");
// $product=new product("2912900187087780C");
// var_dump($product);
// return;

require_once("../../../../product_manager/database/dbcontroller.php");
require_once("../../../../product_manager/config.php");

$db_handle=new DBController();

$all_skus_to_display=array();
$results=$db_handle->runQuery("select distinct  updated_sku from product_attibute_update_log where update_timestamp>=\"$formated_date\" and update_timestamp<=\"$formated_date\"+ INTERVAL 1 DAY ;");
if (empty($results) || $results==false || $results==-1){
    echo 'there are no records for this date. please add records first.';
    exit;
}
// var_dump($results);
foreach ($results as $key => $value) {
    array_push($all_skus_to_display,$value['updated_sku']);
}


// var_dump($all_skus_to_display);
$workers=array();//contains products that have at least one non-empty attribute
foreach ($all_skus_to_display as $sku) {
    $product=new product($sku);
    // var_dump($product);
    if (!is_null($product->not_null_attributes)){
        $worker_id=$product->worker_id;
        $worker_name=$product->worker_name;
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
        // echo '<pre>';
        // var_dump($workers);
        // echo '</pre>';
        // break;
    }

}


// exit;

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

require('../fpdf.php');
// echo '<pre>';
// var_dump($eligible_products);
// echo '</pre>';

class configuration{
    public $cell_width,$cell_height,$x_start,$y_start,$cell_table,$x,$y;
    function __construct(){
        $this->cell_width=50;
        $this->cell_height=68;
        $this->x_start=5;
        $this->y_start=6;
        $this->cell_table=array();

        $this->x=$this->x_start;
        $this->y=$this->y_start;
        $this->cell_table=array();
        for ($index=0;$index<=15;$index++){
            $this->cell_table[$index]=array('x'=>$this->x, 'y'=>$this->y);
            $this->x+=$this->cell_width;
            if ($this->x>=$this->x_start+4*$this->cell_width){
                $this->x=$this->x_start;
                $this->y+=$this->cell_height;
            }
        }
    }
    public function printSummary($pdf, $summary){
        $pdf->addPage();
        $pdf->SetXY(50,140);
        $pdf->Cell(0,0,$summary,0,0,'L');
    }
    public function addPage($pdf){
        $pdf->addPage();
        $x_start=$this->x_start;
        $y_start=$this->y_start;
        $cell_height=$this->cell_height;
        $cell_width=$this->cell_width;
        //draw horizontal line for each y:
        for($y_temp=$y_start;$y_temp<=$y_start+4*$cell_height;$y_temp+=$cell_height){
            $pdf->Line($x_start,$y_temp,$x_start+$cell_width*4,$y_temp);
        }
        //draw vertical lines:
        for($x_temp=$x_start;$x_temp<=$x_start+4*$cell_width;$x_temp+=$cell_width){
            $pdf->Line($x_temp,$y_start,$x_temp,$y_start+$cell_height*4);
        }
    }
}
$pdf = new FPDF();
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
$summary='Date (Beijing Time): '.$date.': '.count($all_skus_to_display).' products were updated. ';
$config->printSummary($pdf,$summary);



foreach ($workers as $worker) {
    $i=0;
    $product_count=count($worker['products']);
    $title=$worker['worker_name']." updated ".$product_count." products.";
    foreach ($worker['products'] as $object) {
        if ($i%16==0){
            // break;
            $config->addPage($pdf);
        }
        if ($i==0){
            $pdf->SetXY(70,2);//add title to the top of page
            $pdf->Cell(0,0,$title,$has_border,0,'L');
        }        

        $cell_table=$config->cell_table;
        $position=$cell_table[$i%16];//range from 0-15
        $cell_1_x=$position['x']+1;//offset a little from the border
        $cell_1_y=$position['y']+3;
        $pdf->SetXY($cell_1_x,$cell_1_y);
        $pdf->Cell(0,0,$i.'.'.format($object->name),$has_border,0,'L');

        $cell_1_y+=4;
        $pdf->SetXY($cell_1_x,$cell_1_y);
        $pdf->Cell(0,0,$object->sku,$has_border,0,'L');

        $cell_1_y+=4;
        // $pdf->SetXY($cell_1_x,$cell_1_y);
        // $image1 = "product1.png";
        $image_path=$object->image;
        // $server_image_path="/usr/share/nginx/www/1661hk/media/catalog/product".$image_path;
        include $magento_root.'alice/product_manager/config.php';
        $server_image_path=$magento_root."media/catalog/product".$image_path;
        // $full_image_path="http://ww1.1661hk.com/media/catalog/product".$image_path;

        // $full_image_path=$base_url."media/catalog/product".$image_path;
        // var_dump($full_image_path);
        // var_dump(file_exists($server_image_path));
        
    	if (is_null($image_path) || $image_path=='no_selection' || !file_exists($server_image_path)){
        	$pdf->Image("images/no_image.png", $cell_1_x, $cell_1_y, 30,30);
    		// echo 'skipping';
    	}else{
    		// $full_image_path="http://www.prosepoint.org/docs/no_image.png";//works
        	$error_msg=$pdf->Image($server_image_path, $cell_1_x, $cell_1_y, 30,30);
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
        $cell_1_y+=32;
        //started outputing attributes:
        // var_dump($object->not_null_attributes);
        $company=array("jd","amazon","tmall","taobao");
        $array=buildArray($object->not_null_attributes);
        $temp_x=$cell_1_x;

        foreach($array as $company=>$value){
            //output amazon_icon.png etc. 
            $icon_path = getImagePath($company);
            $icon_x_offset=4;
            $cell_1_x+=$icon_x_offset;
            $pdf->Image($icon_path, $cell_1_x, $cell_1_y, 5,5);
            //output price
            $cell_1_x+=5;
            $pdf->SetXY($cell_1_x,$cell_1_y+3);//move text down a bit to align with the image
            $pdf->Cell(0,0,format_2($value['price']),$has_border,0,'L');    
            
            if (!empty($value['link']) && !is_null($value['link'])){
                if ($value['link']=="no such item"){
                	$cell_1_x+=13;
                	$pdf->SetXY($cell_1_x,$cell_1_y+3);
                	$pdf->Cell(0,0,'No Such Item',$has_border,0,'L');
                }else{
    	            $cell_1_x+=20;//link on this row
    	            $pdf->SetXY($cell_1_x,$cell_1_y);
    	            // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'','http://www.google.com');
                	$pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'',$value['link']);
            	}
            }
            // $cell_1_x+=5;//next box on this row;
            // $pdf->SetXY($cell_1_x,$cell_1_y);
            // $pdf->Cell(0,0,$value['link'],$has_border,0,'L');

            $cell_1_y+=6;//next row
            $cell_1_x=$temp_x;
        }
        // break;
        // var_dump($object->name);
        // var_dump($i);

        $i++;
    }
}


// $pdf->Cell(100,10,'Hello World!');
// $pdf->Cell(140,10,'Hello World!');


// $pdf->Output();
// exit;


$save_pdf_on_server=false;
if ($save_pdf_on_server){
    $filename="price_comparison_report_for_website_products({$formated_date}).pdf";
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
	// var_dump($retcode);
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




