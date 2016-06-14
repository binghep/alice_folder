<?php
 require_once "../../../../../app/Mage.php";//if only 'require', then "=new product()" can work only once.
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
/* 
Return acc_name of the worker with worker_id
Return false if no worker is found. 
*/
// function get_worker_name($worker_id,$db_handle){
//     $query="select acc_name from workers where acc_id='{$worker_id}'";
//    // var_dump($query);

//     $result=$db_handle->runQuery($query);
//     if (is_null($result)){
//         return false;
//     }
//     $worker_name=$result[0]['acc_name'];
//     return $worker_name;    
// }


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
            $server_image_path="/usr/share/nginx/www/1661hk.com/media/catalog/product".$image_path;
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
        // var_dump($all_competitor_data);
        foreach($all_competitor_data as $comp_name=>$data){
            //output the transparent rectangle for this competitor
            $pdf->SetXY($cell_1_x,$cell_1_y);
            $pdf->SetFillColor(237,237,237);
            $pdf->SetAlpha(0.7);
            // $pdf->Rect($cell_1_x, $cell_1_y-2, 48, 5, 'DF');
            $pdf->Rect($cell_1_x-1, $cell_1_y, 50, 5, 'DF');
            $pdf->SetAlpha(1);

            //---------------------output amazon_icon.png or text----------------
            // if ($competitor_name=="amazon"){
            //     $icon_path = $this->getImagePath($competitor_name);
            //     $icon_x_offset=4;
            //     $pdf->Image($icon_path, $cell_1_x+$icon_x_offset, $cell_1_y+1, 4,4);
            // }else{
                $cell_y_for_competitor=$cell_1_y+3;
                $pdf->SetXY($cell_1_x,$cell_y_for_competitor);
                $pdf->Cell(0,0,$comp_name,$has_border,0,'L');
            // }
            
            //------output price with attached link for this competitor---------
            // if (!empty($data['link']) && !is_null($data['link'])){
                $cell_x_for_competitor=$cell_1_x+20;
                // if ($data['link']=="no such item"){
                //     $pdf->Cell(0,0,'No Such Item',$has_border,0,'L');
                // }else{
                    // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'','http://www.google.com');
                    // $pdf->Image("images/link_icon.png", $cell_1_x, $cell_1_y, 0,0,'',$data['link']);
                    $pdf->SetXY($cell_x_for_competitor,$cell_y_for_competitor);
                    $pdf->Cell(0,0,$this->sanitize_price($data['price']),$has_border,0,'L',0,$data['url']);    
                // }
            // }
            $cell_1_y-=5;//next row
            $cell_1_x=$temp_x;
            $count++;
            if ($count>7){
                break;//canot hold this much. even if we are piling from bottom to top.
            }
        }
        // echo 'here';

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
            // var_dump($object->get_name());
            $pdf->Cell(0,0,$i.'.'.$this->format_product_name($object->get_name()),$has_border,0,'L');

            $cell_1_y+=4;
            $pdf->SetXY($cell_1_x,$cell_1_y);
            $pdf->Cell(0,0,$object->get_sku(),$has_border,0,'L');
            // var_dump($object->get_sku());
            $cell_1_y+=4;
            $image_path=$object->get_image();
            // var_dump($image_path);
            $this->printProductImage($pdf,$image_path,$cell_1_x,$cell_1_y);
            //----------finish writing product image------------------
            $cell_1_y+=52;
            // $not_null_attributes=$this->buildArray($object->not_null_attributes);//$not_null_attributes['amazon']['link']
            //-------------------display all competitor--------------
            // var_dump($object->get_all_competitor_data());
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
    
    function sanitize_price($price){
        if(is_null($price) || $price=="RMB" || $price=="USD"){
            return "N/A";
        }else{
            return $price;
        }
    }
    // function doesRemoteImageExit($image_url){
    //     // $ch = curl_init("http://www.example.com/favicon.ico");
    //     $ch = curl_init("$image_url");

    //     curl_setopt($ch, CURLOPT_NOBODY, true);
    //     curl_exec($ch);
    //     $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     // $retcode >= 400 -> not found, $retcode = 200, found.
    //     // var_dump($retcode);
    //     curl_close($ch);
    // }
    function format_product_name($string){
        return substr($string, 0,21);
    }
    // function getImagePath($image_name){
    //     return "images/".$image_name."_icon.png";
    // }
}

/*
param: $price1: e.g. "$20.3", "￥20", "30.2"
param: $price2: e.g. "$3.99", "￥5.3", "8"
Two param Must be in the same currency (both start with $ or ￥，or start with nothing)
If two price are not valid prices in same currency, return false.
*/
/*
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
*/



require "product_interface.php";
class product implements product_interface{
    public $name,$image,$sku,$not_null_attributes,$all_competitor_data;//both do not contain No Match attributes.
    public $worker_id,$worker_name;
    public $i_recommend;
    public $cat_ids;
    public $error;
    public $db_handle;
    public function get_sku(){
        return $this->sku;
    }
    public function get_image(){
        return $this->image;
    }   
    public function get_name(){
        return $this->name;
    }    
    public function get_error(){
        return $this->error;
    }
    // public function get_not_null_attributes(){
    //     return $this->not_null_attributes;
    // }
  
    public function get_i_recommend(){
        return $this->i_recommend;
    }  
    public function get_worker_id(){
        return $this->worker_id;
    }  
    /**
    * magento_attributes: array("url_amazon","price_amazon"...)
    */
    function __construct($sku,$db_handle,$magento_attributes) {
        // require_once "/usr/share/nginx/www/1661hk/app/Mage.php";//if only 'require', then "=new product()" can work only once.
        $this->db_handle=$db_handle;
        $product_id=Mage::getModel("catalog/product")->getIdBySku($sku);
        // var_dump($product_id);
        if ($product_id===false){
            $this->error="cannot load by id";
        }
        $product_model = Mage::getModel('catalog/product');
        // return;
        $product=$product_model->load($product_id);
        if ($product===false) {
            $this->error.="<br>cannot load by id";
        }

        $this->image=$product->getImage();
        if ($image=="no_selection") {$image=null;}
        $this->sku=$product->getSku();
        $this->cat_ids=$product->getCategoryIds();
        $this->name=$product->getName();
        $this->i_recommend=$product->getData("i_recommend");
        $this->worker_id=$this->init_worker_id($this->sku);

        // var_dump($product->getImage());//---->the only way you can find the image path downloaded by the external image extension. others are impossible even if you see highly upvoted forum posts on stackoverflow.
        //--------------------------------------------
        // $_resource = Mage::getResourceSingleton('catalog/product');//not working for some products. weird.
        // $optionValue = $_resource->getAttributeRawValue($product_id,  [ATTRIBUTE_ID/ATTRIBUTE_CODE], Mage::app()->getStore());
        // var_dump(Mage::app()->getStore());
        //--------------------------------------------
        $this->not_null_attributes=array();
        foreach ($magento_attributes as $attribute_code) {
            // $attribute_value = $_resource->getAttributeRawValue($product_id, $attribute_code, 0);
            $skip_values=array("RMB","USD","No Match","USDNo Match","RMBNo Match");
            $attribute_value=$product->getData($attribute_code);
            // var_dump($attribute_value);
            if (is_null($attribute_value) || empty($attribute_value) || in_array($attribute_value, $skip_values)){
                continue;
            }
            if(substr($attribute_value, 0,3)=="USD"){
                $attribute_value="$".substr($attribute_value, 3);
            }elseif(substr($attribute_value, 0,3)=="RMB"){
                $attribute_value=substr($attribute_value, 3);
            }
            $this->not_null_attributes[$attribute_code]=$attribute_value;
        }
        if (empty($this->not_null_attributes)){
            $this->error.="<br> No valid not_null_attributes. don't need to display. skip this product";
        }
        //--------------------------------------------
        
        // $this->sku=$_resource->getAttributeRawValue($product_id, "sku", 0);
        // $image=$_resource->getAttributeRawValue($product_id, "small_image", 0);

        // $this->i_recommend=$_resource->getAttributeRawValue($product_id, "i_recommend", 0);
        
        //-----------------------------------------------------
        // $special_price=$_resource->getAttributeRawValue($product_id, "special_price", 0);
        $special_price=$product->getSpecialPrice();
        // $price=$_resource->getAttributeRawValue($product_id, "price", 0);
        $price=$product->getPrice();
        $ipz_price=(is_null($special_price) || empty($special_price))?$price:$special_price;
        $ipz_url="https://www.1661usa.com/en/catalog/product/view/id/".$product_id;
        

        $this->not_null_attributes["price_1661"]="$".$this->format_price($ipz_price);
        $this->not_null_attributes["url_1661"]=$ipz_url;

        // $this->worker_name=$this->get_worker_name($this->worker_id,$this->db_handle);
    }
    /*
    return following format:
    array("amazon"=>array("price"=>"$20","url"=>"http://www.amazon.com/water","shipping"=>"$3.99"),
              "jd"=>array("price"=>"$20","url"=>"http://www.amazon.com/water","shipping"=>"Free Shipping")
        );
    */
    public function get_all_competitor_data(){
        $array=array();
        $all_competitor_attribute_names=array_keys($this->not_null_attributes);//already sanitized by constructor $error variable. 
        $comp_names=array();
        // var_dump($all_competitor_attribute_names);
        //---build an array $comp_names containing competitor names to display---
        foreach ($all_competitor_attribute_names as $attribute) {
            if (substr($attribute, 0,6)=="price_"){
                $competitor=substr($attribute, 6);
                if (!in_array($competitor, $comp_names)){
                    array_push($comp_names, $competitor);
                    // echo 'pushing';
                }
            }elseif (substr($attribute, 0,4)=="url_"){
                $competitor=substr($attribute, 4);
                if (!in_array($competitor, $comp_names)){
                    array_push($comp_names, $competitor);
                    // echo 'pushing';
                }
            }else{
                // echo 'aaaa';
            }
        }
        //----------------------build the format pdf needs-----------------------
        foreach ($comp_names as $competitor) {
            $array[$competitor]["price"]=$this->not_null_attributes["price_".$competitor];
            $array[$competitor]["url"]=$this->not_null_attributes["url_".$competitor];
        }
        // var_dump($array);
        return $array;
    }
    /*
    Return worker_id of this product.
    Return false if no info about this sku is found
    */
    public function init_worker_id($sku){
        // $query="select * from product_attibute_update_log where updated_sku='{$this->sku}' LIMIT 1; ";
        //$query="select * from product_attibute_update_log where record_id in (
//select max(record_id) from product_attibute_update_log where updated_sku='{$this->sku}');";//select the most recent update record to grab worker from
        $query="select * from product_attibute_update_log 
where updated_sku='{$sku}' 
ORDER BY record_id DESC
LIMIT 1;";
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
    public function get_worker_name(){
        if (is_null($this->worker_name)){
            $query="select acc_name from workers where acc_id='".$this->get_worker_id()."'";
           // var_dump($query);
            $result=$this->db_handle->runQuery($query);
            if (is_null($result)){
                return false;
            }
            $this->worker_name=$result[0]['acc_name'];
            return $this->worker_name;    
        }else{
            return $this->worker_name;
        }
    }
    //--------------helper function------------
    public function format_price($price){
        return number_format($price,2);
    }
}