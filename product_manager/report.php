

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
		header('Location: ../library/fpdf/fpdf181/tutorial/generate_report_pdf.php?start_date='.$from_date.'&end_date='.$to_date);
	}
}
// return;

echo '<head>
	<title>View Report / 查看报告</title>
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
require_once 'config.php';

?>





<h3>View Report / 查看报告</h3>
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
