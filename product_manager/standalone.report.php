<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();


// var_dump($_GET);

$error_msg='';
if ($_GET['submit']=="Submit"){
	$from_date=$_GET['from'];
	$to_date=$_GET['to'];
	if (empty($from_date)){
		$error_msg.="From date cannot be empty. <br>";
	}
	if (empty($to_date)){
		$error_msg.="To date cannot be empty. <br>";
	}
	if (!empty($error_msg)){
		echo '<div style="color:red">'.$error_msg.'</div>';
	}else{
		// header('Location: ../library/fpdf/fpdf181/tutorial/generate_report_pdf.php?start_date=03-01-2016&end_date=04-01-2016');
		header('Location: ../library/fpdf/fpdf181/tutorial/generate_report_standalone_pdf.php?start_date='.$from_date.'&end_date='.$to_date);
	}
}
// return;

echo '<head>
	<title>View External Product Report / 查看外部产品报告</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        a{
            display:none;
        }
    </style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>



	</head>

	<body>';

echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';

?>

<?php
// require_once '../../app/Mage.php';
require_once 'config.php';
// Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);



// $sku = $_GET['sku'];
// if (empty($sku) || !isset($sku)) {
// 	echo 'sku not valid. exiting...';
// 	exit;
// }

// class product{
//     public $product_object;
//     function __construct($sku) {
//         require_once "/usr/share/nginx/www/1661hk/app/Mage.php";//if only 'require', then "=new product()" can work only once.
//         Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//         $product_id=Mage::getModel("catalog/product")->getIdBySku($sku);
//         if (is_null($product_id) || $product_id==false){
//         	return false;
//         }
//         // var_dump($product_id);
//         $this->product_object = Mage::getModel('catalog/product')->load($product_id);
//     }
// }

// $p=new product($sku);
// if ($p==false){
// 	echo 'there is no product with this sku. ';
// 	exit;
// }

// $product=$p->product_object;
?>

<head>
  <!-- <meta charset="utf-8"> -->
  <!-- <title>jQuery UI Datepicker - Default functionality</title> -->
  <!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->

</head>
<body>



<h3>View External Product PDF Report / 查看外部产品PDF报告</h3>
<p>Please specify the time range: / 请选择开始时间和结束时间:</p>
 

<form>
	<label for="from">From</label>
	<input type="text" id="from" name="from">
	<label for="to">to</label>
	<input type="text" id="to" name="to">

	<input type="submit" name="submit" value="Submit">
</form>
<script>
$(function() {
$( "#from" ).datepicker({
  defaultDate: "+1w",
  changeMonth: true,
  numberOfMonths: 1,
  onClose: function( selectedDate ) {
    $( "#to" ).datepicker( "option", "minDate", selectedDate );
  }
});
$( "#to" ).datepicker({
  defaultDate: "+1w",
  changeMonth: true,
  numberOfMonths: 1,
  onClose: function( selectedDate ) {
    $( "#from" ).datepicker( "option", "maxDate", selectedDate );
  }
});
});
</script>
<script type="text/javascript">
	

</script>
</body>
