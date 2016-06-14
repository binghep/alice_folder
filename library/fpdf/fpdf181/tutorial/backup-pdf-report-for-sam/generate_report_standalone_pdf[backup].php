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

class product{
    public $name,$image,$sku,$all_competitor_names,$all_competitor_data;
    public $db_handle;
    function __construct($sku) {
		require_once("../../../../product_manager/database/dbcontroller.php");
		$this->db_handle=new DBController();
		$this->sku=$sku;
		// var_dump($this->db_handle);
		
		$status=$this->get_basic_product_info();
		if ($status===false){
			return false;//product with this sku is not found.
		}
		$this->all_competitor_data=$this->get_product_eav_attributes();

    }

    function get_basic_product_info(){
    	
    	// $sql_query = 'SELECT * FROM products WHERE id='.(int)$id.' LIMIT 1;';
    	// var_dump($this->sku);
    	$sql_query = 'SELECT * FROM products WHERE sku="'.$this->sku.'" LIMIT 1;';
		$results=$this->db_handle->runQuery($sql_query);
		if (is_null($results)){
			return false;//product with this sku is not found.
		}
		// $this->id = $row['id'];
		$row=$results[0];
		$this->name = $row['name'];
		$this->image = $row['image'];
		$this->sku = $row['sku'];
		return true;
    }
    /*
    returns all competitors names in an array, like ['amazon','jd']
   
    */
    function _get_all_competitor_names(){
		//show every attribute input text field in database table: 
		$results=$this->db_handle->runQuery('select * from standalone_product_eav_attributes');

		$competitors=array();
		foreach ($results as $row_id => $row) {
			$attribute_name=$row['attribute_name'];
			if (strpos($attribute_name, 'url_')===0){
				$competitor_name=substr($attribute_name,strlen('url_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}
			}else if (strpos($attribute_name,'price_')===0){
				$competitor_name=substr($attribute_name,strlen('price_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}
			}else{
				echo 'weird. ';
			}
		}
		return $competitors;
	}
	/*
	return all eav attributes of one product specified by sku:

	array("amazon"=>array("price"=>"$20","product_url"=>"http://www.amazon.com/water"),
			  "jd"=>array("price"=>"$20","product_url"=>"http://www.amazon.com/water")
	    );
	*/
	function get_product_eav_attributes(){
		$include_empty_price_and_url=false;
		$all_competitor_names=$this->_get_all_competitor_names();
		$this_product_eav_attributes=array();
		foreach ($all_competitor_names as $competitor_name) {
			$competitor=array();
			$competitor["price"]=$this->get_product_attribute_by_attribute_name("price_".$competitor_name);//string or false
			$competitor["product_url"]=$this->get_product_attribute_by_attribute_name("url_".$competitor_name);//string or false //can be empty. should not be false
			if (!$include_empty_price_and_url && empty($competitor["price"]) && empty($competitor["product_url"])){
			}else if(strtolower($competitor['product_url'])=="not available" || strtolower($competitor['product_url'])=="no such item"){
            }else{
				$this_product_eav_attributes[$competitor_name]=$competitor;
			}
		}
		return $this_product_eav_attributes;
	}
	/*
	get one eav attribute value for one product (specified with sku):
	*/
	function get_product_attribute_by_attribute_id($attribute_id){
		$query="select * from standalone_product_eav_attribute_values where attribute_id='".$attribute_id."' and sku='".$this->sku."'";
		// var_dump($query);
		$result=$this->db_handle->runQuery($query);
		if (is_null($result)){
			return false;
		}else{
			return $result[0]["value"];
		}
	}

	/*
	get one eav attribute value for one product (specified with sku):
	*/
	function get_product_attribute_by_attribute_name($attribute_name){
		$query="select * from standalone_product_eav_attributes where attribute_name='".$attribute_name."'";
		// var_dump($query);
		$result=$this->db_handle->runQuery($query);
		if (is_null($result)){
			return false;
		}else{
			$attribute_value=$this->get_product_attribute_by_attribute_id($result[0]["id"]);
			return $attribute_value;
		}
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
// $results=$db_handle->runQuery("select sku from products");
$results=$db_handle->runQuery("select  sku from products where update_timestamp>=\"$formated_start_date\" and update_timestamp<=\"$formated_end_date\"  ;");
if (empty($results) || $results==false || $results==-1){
    echo 'there are no records in the database yet. please add records first.';
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
// var_dump($eligible_products);
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
    include $magento_root.'alice/product_manager/config.php';
    $server_image_path=$magento_root."alice/product_manager/".$image_path;
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
    $competitors=$object->all_competitor_data;
    $temp_x=$cell_1_x;

    foreach($competitors as $competitor_name=>$competitor_data){
        //output amazon_icon.png etc. 
        // $icon_path = getImagePath($company);
        $icon_x_offset=2;
        $cell_1_x+=$icon_x_offset;
        $pdf->SetXY($cell_1_x,$cell_1_y);
        // $pdf->Image($icon_path, $cell_1_x, $cell_1_y, 5,5);
        $pdf->Cell(0,0,$competitor_name,$has_border,0,'L');
        //output price
        $cell_1_x+=19;
        $pdf->SetXY($cell_1_x,$cell_1_y);//move text down a bit to align with the image
        $pdf->Cell(0,0,$competitor_data['price'],$has_border,0,'L');    
        
      /*  if (!empty($value['link']) && !is_null($value['link'])){
            $cell_1_x+=20;//link on this row
            $pdf->SetXY($cell_1_x,$cell_1_y);
            // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'','http://www.google.com');
            $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'',$value['link']);
        }
      */
            // if ($i==28){
            //     echo '<pre>';
            //     var_dump($competitors);                
            //     // var_dump($competitor_data['product_url']);
            //     echo '</pre>';
            //     exit;	
            // }
            $cell_1_x+=20;//link on this row
            $pdf->SetXY($cell_1_x,$cell_1_y);
            // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'','http://www.google.com');
            $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y-3, 0,0,'',$competitor_data['product_url']);

        // $cell_1_x+=5;//next box on this row;
        // $pdf->SetXY($cell_1_x,$cell_1_y);
        // $pdf->Cell(0,0,$value['link'],$has_border,0,'L');

        $cell_1_y+=4;//next row
        $cell_1_x=$temp_x;
    }
    // break;
    // var_dump($object->name);
    // var_dump($i);

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






