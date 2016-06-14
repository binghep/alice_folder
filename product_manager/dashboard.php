<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}


?>


<head>
	<title>Dashboard</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script> -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/logo_bar2.css">
	<link rel="stylesheet" type="text/css" href="css/dashboard.css">
	<link rel="stylesheet" type="text/css" href="css/main_form4.css">

</head>
<body>

<?php

require_once '../../app/Mage.php';
require_once 'config.php';
if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


$product = Mage::getModel('catalog/product');

include_once 'views/index_view_header_block.php';
include_once 'views/dashboard.php';

