<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
require_once '../../config.php';

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
		header('Location: ../../../library/fpdf/fpdf181/tutorial/view_pdf_report_for_recommended_website_products_api.php?start_date='.$from_date.'&end_date='.$to_date.'&secret_code=sadioiow8923inksk65xzdweXdj');
	}
}
// return;
?>
<head>
	<title>Recommended products on 1661USA.com/ 1661USA 推荐产品</title>
	<link rel="shortcut icon" type="image/x-icon" href="../../img/app_icon.png" />
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
<body>
<?php

echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';

?>






<h3>Recommended products on 1661USA.com/ 1661USA 推荐产品</h3>
<p>Please specify the time range (Beijing Time): / 请选择开始日期和结束日期 (美国西海岸时间):</p>

<div style="color:grey;">
	<div>Example 1: to view report for 5/18/2016, enter "05/18/2016" in both fields.</div>
	<div>Example 2: to view report for 5/18/2016 - 5/19/2016, enter "05/18/2016" in "From" field and enter "05/19/2016" in "To" field.</div>
</div>
<br>
<br>

<?php
	date_default_timezone_set('America/Los_Angeles');
	// date_default_timezone_set('Asia/Shanghai');	
	$today_date=date('m/d/Y');
?>

<form>
	<label for="from">From</label>
	<input type="text" id="from" name="from" value="<?php if (!isset($_GET['from'])){echo $today_date;}?>">
	<label for="to">to</label>
	<input type="text" id="to" name="to" value="<?php if (!isset($_GET['to'])){echo $today_date;}?>">

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


