<?php
/* 
==========================================Usage:================================================
This file: 
when you go to : http://hk.1661hk.com/alice/data-extraction-for-xian-office.php
It will write a file to write_log folder.
The output csv is for xi'an office. 
The file name will be returned. 

The php file http://hk.1661hk.com/alice/PHPMailer/PHPMailer-5.2.14/sendToXianOffice.php
will use this file as api to get the output csv's name. then send to xi'an office as an email attachment.

========================================Usage:===============================================
This file is another api that generates all SF shipping labels for a batch (right after the first api is used to generate a csv file). 
Input: none
Output: pdf file. 
*/
// ini_set("memory_limit","40M");

function getRegion($region_id,$db_handle){
    $result=$db_handle->runQuery('select region_name from global_region where region_id=\''.$region_id.'\'');//returns null if empty resultset
    if (is_null($result)){
    	return 'region_name not found.';
    }else{
    	// var_dump($result[0]);
    	return $result[0]['region_name'];
    }
}
// $str1 = 'Begin to write_log .';
// echo $str1."<br>";
// write_log($str1);




// // set document information
// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetAuthor('Alice');
// $pdf->SetTitle('SF');
// $pdf->SetSubject('SF');

// // set default header data
// // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 008', PDF_HEADER_STRING);

// // set header and footer fonts
// // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
// // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// // set default monospaced font
// $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// // set margins
// // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
// $pdf->SetMargins(0, 0, 0);
// // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// $pdf->SetPrintHeader(false);
// $pdf->SetPrintFooter(false);

// // set auto page breaks
// // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// $pdf->SetAutoPageBreak(false, 0);

// // set image scale factor
// $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// ---------------------------------------------------------

//--------------------------------------------------
require('/usr/share/nginx/www/1661hk/alice_tools/fpdf181/font/chinese.php');

$pdf=new PDF_Chinese();
$pdf->AddBig5Font();
$pdf->AddPage();
$pdf->SetFont('Big5','',20);
// $data2 = mb_convert_encoding("你好nihao","GBK","UTF-8");
$nihao="你好nihao";
$a=iconv("UTF-8","BIG-5", $nihao);
$pdf->Write(10,$a);
$pdf->Output();
//-------------------------------------------------
exit;

// add a page

$users = getUserData();
if (is_null($users)) {
    echo 'no users registered today';
    exit;
}
// echo '<head><meta charset="UTF-8"></head><body>';
// var_dump($utf8text);
// exit;
// set color for text


foreach($users as $user){
    $pdf->AddPage();
    // write the text
    // $pdf->Write(5, "你好", '', 0, '', false, 0, false, false, 0);
    //"雷丹,15929265102,陕西咸阳秦都区陕西省咸阳市秦都区咸阳市第一人民医院,,3"
    $array=explode(',', $user);
    $product_name=null;
    switch ($user[4]) {
        case '1':
            $product_name="背包";
            break;
        case '2':
            $product_name="水杯";
            break;
        case '3':
            $product_name="毛巾";
            break;
        default:
            # code...
            $product_name="错误。用户没有获得可寄送奖品";
            break;
    }

    $recipient=$array[0];
    $recipient_address=$array[2];
    $recipient_mobile=$array[1];
    //------left border x value is 10 and 23
    $left_margin_x_small=12;
    $left_margin_x_large=23;
    $xing_ming_margin=90;
    $border=1;

    // $pdf->SetXY( 0, 0 );
    // $pdf->Cell( 0, 0, 'origin', $border, 0, 'L' );//(limit of width, limit of height, text to print, whether highlight border, [Indicates where the current position should go after the call, to the right or left or next line. default is to the right] , text align)

    $pdf->SetXY( $left_margin_x_large, 24 );
    $pdf->Cell( 0, 0, '1661HK', $border, 0, 'L' );
    $pdf->SetXY( $left_margin_x_large, 35 );
    $pdf->Cell( 0, 0, '陕西省西安市高新区高科尚都摩卡6栋2408室', $border, 0, 'L' );
    $pdf->SetXY( $left_margin_x_large, 41 );
    $pdf->Cell( 1, 0, '电话    029-88326923', $border, 0, 'L' );

    $pdf->SetXY( $xing_ming_margin, 46 );
    $pdf->Cell( 1, 0, '姓名   '.$recipient, $border, 0, 'L' );

    $pdf->SetXY( $left_margin_x_small, 57 );
    $pdf->Cell( 1, 0, '地址   '.$recipient_address, $border, 0, 'L' );

    $pdf->SetXY( $left_margin_x_large, 71 );
    $pdf->Cell( 1, 0, '电话  '.$recipient_mobile, $border, 0, 'L' );

    $pdf->SetXY( $left_margin_x_small, 86 );
    $pdf->Cell( 1.5, 0, '毛巾', $border, 0, 'L' );

    $pdf->SetXY( 150, 103.0 );
    $pdf->Cell( 1.5, 0, '852 671 0108', $border, 0, 'L' );

    $pdf->SetXY( $left_margin_x_large, 107.0 );
    $pdf->Cell( 1.5, 0, '1661HK', $border, 0, 'L' );
    break;
}





function getUserData(){
    require("../database/dbcontroller.php");
    $db_handle = new DBController();
    $result=$db_handle->runQuery('select * from alice_survey WHERE register_time <= CURDATE() + INTERVAL 1 DAY  and register_time > CURDATE() - INTERVAL 1 DAY;');//returns null if empty resultset
    if (is_null($result)) {
        // 'no record found.';
        return $result;
    }

    $to_print=array();
    foreach ($result as $k => $v) {
        if (is_numeric($k)) {
            // echo $result[$k]["id"];
            // echo $result[$k]["name"];
            // echo $result[$k]["prize"];
            // echo $result[$k]["mobile"];
            // echo $result[$k]["mobile"];
            // echo $result[$k]["register_time"];
            // echo $result[$k]["redeem_time"];
            $province=getRegion($result[$k]["region_province"],$db_handle);
            $city=getRegion($result[$k]["region_city"],$db_handle);
            $district=getRegion($result[$k]["region_district"],$db_handle);
            $address=$result[$k]["address"];
            $address_confirmed=($result[$k]["address_confirmed"]=="1"?"y":"");
            $detailed_address=$province.$city.$district.$address;

            $prize=$result[$k]["prize"];
            array_push($to_print, $result[$k]["name"].','.$result[$k]["mobile"].','.$detailed_address.','.$address_confirmed.','.$prize);
            // echo $result[$k]["register_time"];
            // echo $result[$k]["redeem_time"];
        }
    }
    return $to_print;
}


// function getFormatedHTMLforPage($recipient,$recipient_address,$recipient_mobile,$product){
//     $content="  1661HK<br>
// <br>
//       陕西省西安市高新区高科尚都摩卡6栋2408室<br>
// <br>
//   电话  029-88326923<br>
//                   姓名   ".$recipient."<br>
// <br>
// 地址  ".$recipient_address."<br>
// <br>
//   电话     ".$recipient_mobile." <br>
// <br>
// <br>
// <br>
//   
//   ".$product."<br>
// <br>
// <br>
// <br>
//   
// 1661HK";
//     return $content;
// }

