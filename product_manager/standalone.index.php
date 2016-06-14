<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();


if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}

require "config.php";
if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}

//------------------------for dropZone------------------------
$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = 'uploads';   //2
 
if (!empty($_FILES)) {
     
    $tempFile = $_FILES['file']['tmp_name'];          //3             
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
     
    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
 
    move_uploaded_file($tempFile,$targetFile); //6
     
    exit;
}

//------------------------for dropZone------------------------


?>


<head>
	<title>Price Research Form - module 3 - External Products</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script> -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<!--<script src="http://code.jquery.com/jquery-1.5.js" type="text/javascript"></script>-->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<link href="css/dropzone.css" type="text/css" rel="stylesheet" >
	<script type="text/javascript" src="js/dropzone20.js"></script>
	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/form.css">	
</head>
<body>

<?php


echo '<div class="table_block">';
echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';
echo '<div>';
require_once '../../app/Mage.php';
require_once 'views/standalone/inner.form_block.php';
echo '</div>';
echo '</div>';
echo '</div>';
?>