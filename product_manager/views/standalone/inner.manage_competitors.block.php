
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

$error_msg='';
$success_insert_into_database_msg='';

if (!empty($_POST['submit'])){
	//add this competitor's two attributes to standalone_product_eav_attributes
	$competitor_name=$_POST['name'];
	if (!empty($competitor_name)){
		if (!hasOnlyLetters($competitor_name)){
			$error_msg.='Format of new competitor nickname is NOT OK. it can only contain English letters (Example: amazonUSA, taobao) 
				<br>
				您输入的竞争对手昵称不符合以下要求：只能包含英文字母。 (比如： amazonUSA, taobao)<br>';
		}
	}else{
		$error_msg.="The competitor nickname field cannot be empty / 竞争对手昵称不能为空<br>";
	}

	// return;
	if (empty($error_msg)){
		// var_dump($_POST);
		$result=createThreeAttributesForThisCompetitor($competitor_name);
		if ($result['status']===false){
			$error_msg.="Error: ".$result['error'];
		}else{
			$success_insert_into_database_msg.="Sucessfully added this competitor in database. <br> 成功创建此竞争对手的三个属性：price_".$competitor_name.", url_".$competitor_name.", and shipping_".$competitor_name.".";
		}
	}
}
function createThreeAttributesForThisCompetitor($competitor_name){
	require '../../database/dbcontroller.php';
	$db_handle=new DBController();
	// var_dump($db_handle);
	$price_attribute="price_".$competitor_name;
	$url_attribute="url_".$competitor_name;
	$shipping_attribute="shipping_".$competitor_name;
	$result1='';
	$result2='';
	$result3='';
	$error1='';
	$error2='';
	$error3='';
	//----------------------create one attribute----------------------:
	$q2="select * from standalone_product_eav_attributes where attribute_name='".$url_attribute."'";
	$result_search=$db_handle->runQuery($q2);
	// var_dump($result_search);
	if (is_null($result_search)){
		$insert_query2="insert into standalone_product_eav_attributes (attribute_name)  values ('$url_attribute')";
		$result2=$db_handle->runQuery($insert_query2);
	}else{
		$error2.=$url_attribute." attribute already exist. ";
	}
	//----------------------create second attribute----------------------:
	$q1="select * from standalone_product_eav_attributes where attribute_name='".$price_attribute."'";
	$result_search=$db_handle->runQuery($q1);

	// var_dump($result_search);
	if (is_null($result_search)){
		$insert_query1="insert into standalone_product_eav_attributes (attribute_name)  values ('$price_attribute')";
		$result1=$db_handle->runQuery($insert_query1);
	}else{
		$error1.=$price_attribute." attribute already exist. ";
	}
	//----------------------create third attribute----------------------:
	$q3="select * from standalone_product_eav_attributes where attribute_name='".$shipping_attribute."'";
	$result_search=$db_handle->runQuery($q3);

	// var_dump($result_search);
	if (is_null($result_search)){
		$insert_query3="insert into standalone_product_eav_attributes (attribute_name)  values ('$shipping_attribute')";
		$result3=$db_handle->runQuery($insert_query3);
	}else{
		$error3.=$shipping_attribute." attribute already exist. ";
	}
	//----------------------return--------------------------------------:
	if ($result1===true && $result2===true && $result3===true){
		return array('status'=>true);
	}else{
		return array('status'=>false,'error'=>$error1.$error2.$error3);
	}
}

function hasOnlyLetters($str) {
   // return preg_match('/^[a-zA-Z_]+$/i',$str); //-->working. letter and underscore only
   return preg_match('/^[a-zA-Z]+$/i',$str);
}

?>

<head>
	<title>manage competitors / 管理竞争对手</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script> -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<!--<script src="http://code.jquery.com/jquery-1.5.js" type="text/javascript"></script>-->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
	<!-- <link href="css/dropzone.css" type="text/css" rel="stylesheet" > -->
	<!-- <script type="text/javascript" src="js/dropzone17.js"></script> -->
	
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
		table{
		    margin: 20px;
		}
		table,tr,td{
			padding:5px 5px;
			border:1px solid #B3B3B3 !important;
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
		}
		#pager{
			width: 640px;
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
	<link href="../Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	<style type="text/css">
	.wufoo li {
	    width: 80%;
	}
	</style>
</head>
<body>



<div id="container" class="ltr">
	<h1 id="logo">
		 <div style="font-size:20px;padding: 10px 10px;">1661 USA.com</div>
		<!-- <a href="http://www.wufoo.com/?t=o2j5o6" title="Powered by Wufoo">Wufoo</a> -->
	</h1>
	
	<!-- <form id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="https://475461387.wufoo.com/forms/mab0co1dsh5gt/"> -->
	<form   id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off"  method="post" novalidate="" action="">
  
<header id="header" class="info">
	<h2>Manage Competitors / 管理竞争对手</h2>
</header>






<!-- <table>
	<tr>
		<td>amazon</td>
		<td>Visible: Yes No</td>
	</tr>
	<tr>
		<td>ebay</td>
		<td>Visible: Yes No</td>
	</tr>
</table> -->

	<!-- eav attributes -->
	<?php
		require_once '../../database/dbcontroller.php';
		$db_handle=new DBController();
		// var_dump($db_handle);
		$results=$db_handle->runQuery('select * from standalone_product_eav_attributes');
		// var_dump($results);

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
			}else if (strpos($attribute_name,'shipping_')===0){
				$competitor_name=substr($attribute_name,strlen('shipping_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}
			}else{
				echo 'weird. ';
			}
		}

		echo '<table>';
		foreach ($competitors as $competitor) {
			echo '<tr>';
			echo '<td>';
			echo $competitor;
			echo '</td>';
			// echo '<td>';
			// echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	?>

<header id="header" class="info">
	<h2>Add Competitor / 添加竞争对手</h2>
</header>

<div style="color:red;"><?php echo $error_msg;?></div>
<div style="line-height: 2;color: black;background: #DBFFDB;"><?php echo $success_insert_into_database_msg;?></div>

<ul id="main_form_ul">
	<li class="main_form_li">
		<label class="desc">competitor name(must be composed of English letters only)<br>竞争对手名字 （只能由英文字母组成）</label>
		<div>
			<input id="name" name="name" value=<?php if (!is_null($_POST['name'])) {echo $_POST['name'];} ?>>
		</div>
	</li>
</ul>

<input type="submit" style="margin-left: 19px;height: 40px;width: 120px;" value="Add / 添加" name="submit">

</form>
<a style="background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 16px;margin-top: 10px;border: 1px solid black;" href="../../index-standalone.php">Go Back / 返回</a>
</div>


