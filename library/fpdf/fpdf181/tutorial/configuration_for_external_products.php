<?php
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
    public function printSummary_one_line($pdf, $summary){
        $pdf->addPage();
        $pdf->SetXY(50,140);
        $pdf->Cell(0,0,$summary,0,0,'L');
    }
    public function printSummary($pdf, $top_txt, $bottom_txt=""){
        $pdf->addPage();
        $pdf->SetXY(20,80);
        // $pdf->Cell(0,0,$summary,0,0,'L');
        $height=7;
        $width=300;
        $border=0;//no border
        $align="L";//left alignment
        $fill=false;//no background
        $pdf->MultiCell($width,$height,$top_txt,$border,$align,$fill);
        // array_map('unlink', glob("/usr/share/nginx/www/ipzmall.com/media/alice_image_download/*"));
        if (!empty($bottom_txt)){
            $pdf->SetXY(60,150);
            $pdf->MultiCell($width,$height,$bottom_txt,$border,$align,$fill);
        }
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
    public function printProductImage($pdf,$image_path,$cell_1_x,$cell_1_y){
        // var_dump(glob("/usr/share/nginx/www/ipzmall.com/media/lll/*"));//empty array if lll does not exist;
       
        // $pdf_w=$pdf->w;//210
        // $pdf_h=$pdf->h;//297
        $td_width=50;  //less than 1/4 of the pdf width
        $td_height=55; //less than 1/5 of the pdf height
        $server_image_path='';
        // $server_image_path="media/catalog/product".$image_path;
        // $server_image_path="/usr/share/nginx/www/ipzmall.com/"."media/catalog/product".$image_path;
        if (is_null($image_path) ||$image_path=="no_selection"){//no_selection(already converted to null in another file) or null
            $pdf->Image("images/no_image.png", $cell_1_x, $cell_1_y, 30,30);
        }elseif (substr($image_path, 0,4)=="http"){//external image
        // $download_image_name="";
        // $download_image_path="";
        // $download_directory='/usr/share/nginx/www/ipzmall.com/media/alice_image_download/';
        // // $all_downloaded_image=array();
        // // foreach(glob($download_directory.'/*.*') as $file) {
        // //     array_push($all_downloaded_image,$file);
        // // }
        // $file_extension=substr($image_path, strrpos($image_path, "."));
        // do{
        //     $download_image_name=rand(1000,9999999).$file_extension;
        //     $download_image_path=$download_directory.$download_image_name;
        //     // var_dump(file_exists($download_image_path));//false
        // }while (file_exists($download_image_path));
            // var_dump($file_extension);
            // copy($image_path,$download_image_path);//failed 30 out of 43 operations.
            //------------------------------------
            // $image_binary=file_get_contents($image_path);
            // $save=file_put_contents($download_image_path, $image_binary);
            //     // var_dump(file_exists($download_image_path));//false
            //------------------------------------

        // var_dump("just downloaded $download_image_path");
        // $server_image_path="http://www.ipzmall.com/media/alice_image_download/".$download_image_name;
        // var_dump($server_image_path);
        }else{//internal image
            $server_image_path="/usr/share/nginx/www/1661hk.com/alice/product_manager/".$image_path;
        }
        // $server_image_path="http://www.backcountry.com/images/items/1200/DYL/DYL001R/FREWH.jpg";
        // $server_image_path="http://www.ipzmall.com/media/catalog/product/d/y/dylan-tribal--french-terry-vest---womens-fresh-whi-21ac94a4c0940e208bf35d121896de68.jpg";
        if (!is_null($image_path)){
            //=====================output unstretchy image==================
            $dimension_array = $this->calculatePdfImageWidthParam($server_image_path);
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
            // var_dump($dimension_array);

            // var_dump($server_image_path);
            $error_msg=$pdf->Image($server_image_path, $cell_1_x+$image_left_offset, $cell_1_y, $dimension_array['width'],$dimension_array['height']);
            
            //--------------------error handling-------------------------------------
            if (!is_null($error_msg) && strpos($error_msg, "FPDF error: Not a JPEG file")!==false){
                $pdf->SetXY($cell_1_x,$cell_1_y+14);
                $pdf->Cell(0,0,'JPEG exists but not valid.',$has_border,0,'L');
            }else if (!is_null($error_msg)){//other FPDF error/exceptions
                $pdf->SetXY($cell_1_x,$cell_1_y+14);
                // $pdf->Cell(0,0,$error_msg,$has_border,0,'L');
                // var_dump($error_msg);
                $pdf->Cell(0,0,'product image not valid',$has_border,0,'L');
            }
        }
    }
    public function printProductCompetitor($pdf,$all_competitor_data,$cell_1_x,$cell_1_y){
        $temp_x=$cell_1_x;
        $count=0;
        foreach($all_competitor_data as $competitor_name=>$competitor_data){
            //output amazon_icon.png etc. 
            // $icon_path = getImagePath($company);
            $pdf->SetXY($cell_1_x,$cell_1_y);
            // $pdf->Image($icon_path, $cell_1_x, $cell_1_y, 5,5);
            $pdf->SetFillColor(222,222,255);
            $pdf->SetAlpha(0.7);
            // $pdf->Cell(0,0,$competitor_name,$has_border,0,'L');
            $pdf->Rect($cell_1_x, $cell_1_y-2, 48, 8, 'DF');
            // $pdf->Rect($cell_1_x, $cell_1_y, 47, 7);
            $pdf->SetAlpha(1);

            $pdf->Cell(0,0,$competitor_name,$has_border,0,'L',0);
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
                $subtotal=$this->getSumOfPrice($product_price,$product_shipping);
                // var_dump($product_price.$product_shipping.$subtotal);
                if ($subtotal===false){//invalid or not unified price format.
                    // $subtotal="N/A";
                    $price_sentence=$product_price;
                }else{
                    $price_sentence=$product_price.'+'.$product_shipping.'='.$subtotal;
                }
            }
            $pdf->Cell(0,0,$price_sentence,$has_border,0,'L',0,$competitor_data['url']);    
            
        
                // $cell_1_x+=36;//link on this row
                // $pdf->SetXY($cell_1_x,$cell_1_y);
                // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y-3, 0,0,'',$competitor_data['product_url']);

            $cell_1_y-=12;//next row
            $cell_1_x=$temp_x;
            $count++;
            if ($count>7){
                break;//canot hold this much. even if we are piling from bottom to top.
            }
        }
    }
    public function printPDFSection($pdf,$products,$section_title){
        // require_once("../../../../product_manager/config.php");
        $i=0;
        $has_border=0;

// var_dump(count($products));
        foreach ($products as $object) {
            if ($i%16==0){
                // break;
                $this->addPage($pdf);
            }
            if ($i==0){
                $pdf->SetXY(70,2);//add title to the top of page
                $pdf->Cell(0,0,$section_title,$has_border,0,'L');
            }        

            $cell_table=$this->cell_table;
            $position=$cell_table[$i%16];//range from 0-15
            $cell_1_x=$position['x']+1;//offset a little from the border
            $cell_1_y=$position['y']+3;
            $pdf->SetXY($cell_1_x,$cell_1_y);
            $pdf->Cell(0,0,$i.'.'.$this->format_product_name($object->get_name()),$has_border,0,'L');

            $cell_1_y+=4;
            $pdf->SetXY($cell_1_x,$cell_1_y);
            $pdf->Cell(0,0,$object->get_sku(),$has_border,0,'L');

            $cell_1_y+=4;
            $image_path=$object->get_image();
            // var_dump($image_path);
            $this->printProductImage($pdf,$image_path,$cell_1_x,$cell_1_y);
            //----------finish writing product image------------------
            $cell_1_y+=52;
            // $not_null_attributes=$this->buildArray($object->get_not_null_attributes);//$not_null_attributes['amazon']['link']
            //-------------------display all competitor--------------
            $this->printProductCompetitor($pdf,$object->get_all_competitor_data(),$cell_1_x,$cell_1_y);
            $i++;
        }
    }
    function calculatePdfImageWidthParam($server_image_path){
        // var_dump($server_image_path);        
        list($width,$height)=getimagesize($server_image_path);
        // echo "width: " . $width . "     ";
        // echo "height: " .  $height . "<br />";
        if (!is_null($width) && !is_null($height)){
            if ($width<45 && $height<45){
            }elseif ($width>$height){
                $width=45;
                $height=0;//auto resize according to $width
            }else{
                $width=0;//auto resize according to $height
                $height=45;
            }
        }else{//can't get dimension of image.
            $width=45;
            $height=45;
        }
        return array("width"=>$width,"height"=>$height);
    }

    /*
    param: $price1: e.g. "$20.3", "￥20", "30.2"
    param: $price2: e.g. "$3.99", "￥5.3", "8"
    Two param Must be in the same currency (both start with $ or ￥，or start with nothing)
    If two price are not valid prices in same currency, return false.
    */
    function getSumOfPrice($price1,$price2){
        $price1_first_symbol=substr($price1, 0,1);
        $price1_first_3_symbol=substr($price1, 0,3);
        // $price1_first_symbol=substr($price1, 0,2);
        // $price1_first_symbol=substr($price1, 0,3);
        $price2_first_symbol=substr($price2, 0,1);
        $price2_first_3_symbol=substr($price2, 0,3);
        // var_dump($price1_first_3_symbol);
        // var_dump($price2_first_3_symbol);
        // var_dump($price1_first_3_symbol=="￥");//true
        if ($price1_first_symbol=="$" && $price2_first_symbol=="$"){
            if (is_numeric(substr($price1,1)) && is_numeric(substr($price2,1))){
                // var_dump($price1);
                // var_dump($price2);
                $sum=(float)substr($price1, 1)+(float)substr($price2, 1);
                return "$".$sum;
            }else{
                // var_dump("here");
                return false;
            }
        }else if ($price1_first_3_symbol=="￥" && $price2_first_3_symbol=="￥"){
            if (is_numeric(substr($price1,3)) && is_numeric(substr($price2,3))){
                // var_dump($price1);
                // var_dump(substr($price1,3));
                // var_dump($price2);
                $sum=(float)substr($price1, 3)+(float)substr($price2, 3);
                return "￥".$sum;
            }else{
                // var_dump("here");
                return false;
            }
        }else if (is_numeric($price1) && is_numeric($price2)){
            return $price1+$price2;
        }else{
            return false;
        }
    }
    function format_product_name($string){
        return substr($string, 0,21);
    }
}

