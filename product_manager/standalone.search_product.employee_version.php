<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();



if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}

include "config.php";
if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}

?>


<head>
	<title>Search Product by Name / 按产品名搜索</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script> -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<!--<script src="http://code.jquery.com/jquery-1.5.js" type="text/javascript"></script>-->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<!-- <link href="css/dropzone.css" type="text/css" rel="stylesheet" > -->
	<!-- <script type="text/javascript" src="js/dropzone19.js"></script> -->
	
	<style type="text/css">
		/*header block*/

table {
    border-collapse: collapse;
}

table, th, td {
    /*border: 1px solid #CECECE;*/
}

input[type=checkbox] {
  /* All browsers except webkit*/
  transform: scale(2.0);
  /* Webkit browsers*/
  -webkit-transform: scale(2.0);
}
.btn {
    width: 180px;
	height: 50px;
}

		div.header{
			background: hsla(231, 48%, 48%, 0.28);
			height: 24px;
			padding: 5px 10px;
		}
		.category_block{
			/*background: rgba(22, 118, 220, 0.14);*/
			background:rgba(5, 126, 255, 0.14);
			padding: 5px 10px;
		}
		.table_block{
			/*background: white;*/
			background: rgb(240,240,240);
		}
		input{
			margin: 5px 5px;
			border:1px solid black;
		}
		.amazon,.jd,.taobao,.tmall,.price{
			width:70px;
			float:right;

		}

		.special_price{
			width:70px;
			float:right;
		}
		.l{
			float:left;
		}
		input[type="text"]{
			float:right;
		}
		input.url_amazon,input.url_jd,input.url_taobao,input.url_tmall{
			width:260px;
		}
		span{
			/*margin: 5px 0px;*/
		}
		table,tr,td{
			padding:5px 5px;
			/*border:#B3B3B3 !important;*/
		}
		td.no_left_border{
			border-left:none !important;
		}
		td.no_right_border{
			border-right:none !important;
		}
		span.col_2_label{
			width:80px;
		}

		/**/
		#filter_toggle{
			float:right;
		}

		#filter_close_button_wrapper{
		    margin-right: 20px;
		    padding-bottom: 25px;
		}

		#filter_close_button{
			float: right;
		}
		#float_filter_block{
		    background: hsl(231, 100%, 86%);
		    position: absolute;
		    /*top: 109px; */
		    top: 105px; 
		    float: right;
		    margin-left: 37px;
		    display: none;
		    position: absolute;/*how to overlay two on div on other general html elements? make its position absolute*/
		    padding: 15px 15px;
		    z-index: 100;/*above the bootstrap pager*/
		}
		.header{
		    margin: 0 auto 0px auto;
		    width: 640px;
		}
		.category_block{
		    margin: 0 auto 0px auto;
		    width: 640px;
		}
		body{
		    background: none;
		    font-size: 15px !important;
		}
		#pager{
			width: 900px;
			text-align: center;
		    /*margin: 0 auto 10px auto;*/
		    margin:4px auto 4px auto;
		}
		ul.pagination{
			display: inline !important;
		}
		.glyphicon{
			padding: 2.5px 0px;
		}
		.container{
			width:100%;
			margin-right: 0px;
			margin-left:0px;
			padding-left: 0px;
			padding-right: 0px;
		}
		.pagination>li>a, .pagination>li>span {
			padding: 6px 8px; /*make the square smaller*/
		}
		#page_x_out_of_x_span{
			color: black;
			font-weight:bold;
			float:left;
			padding-top: 8px;
		}
		ul.pagination{
			margin:0px;
			 float: right;
		}
		.pagination>li:last-child>a, .pagination>li:last-child>span {
			border-top-right-radius: 0px !important;
    		border-bottom-right-radius: 0px !important;
    		float: left !important;
		}
		form ul#main_form_ul_part_b{
		    padding: 10px 10px 70px 10px;
		}
		form ul#main_form_ul{
			padding: 0px 10px;
		}
		form li.buttons {
			padding: 46px 1% 2px 1%;
		}
		.info {
			margin: 9px 0 0px 0 !important;
			padding:0 1% 8px 1% !important;
		}
		#logo{
			text-indent: 0px !important;
		}
		input.text, input.search, textarea.textarea {
		    border-top: 1px solid #7c7c7c !important;
		    border-left: 1px solid #7c7c7c !important;
		    border-right: 1px solid #7c7c7c !important;
		    border-bottom: 1px solid #7c7c7c !important;
		}
		#search_sku{
			float:left;
		}
		#search_sku_span{
			float:left;
		}
		#search_sku_button{
			float:right;
		}
	</style>
	<style type="text/css">
	.dropzone .dz-preview {
		border: 1px solid black;
	}
	</style>
	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	<style type="text/css">
	ul#main_form_ul>li.main_form_li>div>input{
		width: 70%;
	}
	</style>
	<style type="text/css">
	#container{
		width: 960px;
	}
	</style>
	<!-- jquery date picker -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<!--  -->
</head>
<body>

<?php

require_once '../../app/Mage.php';



// $cat_id = $_GET['cat_id'];
// if (empty($cat_id) || !is_numeric($cat_id)) {
// 	echo 'param not valid. exiting...';
// 	exit;
// }

// $p=1;
// if (!empty($_GET['p'])) {
// 	$p=$_GET['p'];
// }



//-----------------------------------------------------------------------------------------
include_once 'views/standalone/inner.search_product.employee_version.block.php';





// include_once 'views/standalone/manage_competitors_block.php';
?>