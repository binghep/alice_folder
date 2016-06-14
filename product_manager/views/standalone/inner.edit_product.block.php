<?php
$error_msg='';
$success_insert_into_database_msg='';
$new_image_path=null;
$product=null;

$callback_url="";
if (!is_null($_GET['callback_url'])) {
	$callback_url= $_GET['callback_url'];
}elseif(!is_null($_POST['callback_url'])){
	$callback_url=$_POST['callback_url'];
}
// var_dump($callback_url);
if (!empty($_POST['submit'])){
	//user clicked 'submit' button:
	// echo 'submitted';
	// var_dump($_POST);
	if (empty($_POST['name'])){
		$error_msg.="name cannot be empty<br>";
	}
	if (empty($_POST['sku'])){
		$error_msg.="sku cannot be empty<br>";
	}

	
	if (empty($error_msg)){
		// var_dump($_POST);
		$result=updateProductInDatabase($_POST['name'],$_POST['sku']);
		$result2=updateEavAttributesInDatabase($_POST);
		// var_dump($result);
		if ($result['status']===false || $result2['status']===false){
			// var_dump('here');
			$error_msg.=is_null($result['error_msg'])?'':$result['error_msg'];
			$error_msg.=is_null($result2['error_msg'])?'':$result2['error_msg'];
		}else{
			
			// var_dump('there');
			//insert success. retrieve it now:
			require_once 'models/standalone_product.php';
			// echo 'wawa';
			$product=new standalone_product();
			$product_id=$product->find_id($_POST['sku']);
			$load_result=$product->load_Product($product_id);
			// var_dump($product);




			if (!$load_result){//if product was gone due to error.
				$error_msg.='error. Failed to find the product in database. <br>';
			}else{
				$success_insert_into_database_msg="Successfully updated product info. Please click on ".'<a style="margin-right:4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;color: black;padding:2.5px 3px;border: 1px solid black;" href="'.$callback_url.'">This button</a>'." to go back.<br>您已成功更新产品信息。请点击 " .'<a style="background: #DEDEDE;text-decoration: none;color: black;padding:2.5px 3px;border: 1px solid black;" href="'.$callback_url.'">这个按钮</a>'. " 来返回上一页。";
			}
		}
	}
}else{
	// var_dump($_GET['id']);
	// var_dump($_GET);
	$product_id_to_edit=$_GET['id'];


	if (is_null($product_id_to_edit) || !is_numeric($product_id_to_edit)){
		echo "product id not valid! exiting.";
		exit;
	}
	require_once 'models/retrieved_standalone_product.php';
	require_once 'models/standalone_product.php';

	$helper=new retrieved_standalone_product("lala");
	$sku=$helper->get_product_sku_by_id($product_id_to_edit);
	// echo 'here';
	if ($sku!==false){
		// echo 'found sku by id:'.$sku;
	}else{
		echo 'cannot find sku by product id in $_GET variable. exiting.';
		exit;
	}

	$retrieved_standalone_product=new retrieved_standalone_product($sku);
}



function updateEavAttributesInDatabase($POST_Array){
	//update eav table:
	// require_once 'models/retrieved_standalone_product.php';
	// $retrieved_standalone_product=new retrieved_standalone_product($this->sku);
	// var_dump($retrieved_standalone_product);
	// return;
	// $sql_query = "UPDATE standalone_product_eav_attribute_values SET "


	$sku=$POST_Array['sku'];
	$attributes_on_form=array();
	foreach ($POST_Array as $key => $value) {
		if (strpos($key, "url_")===0 || strpos($key,"price_")===0 || strpos($key,"shipping_")===0){
			$attributes_on_form[$key]=$value;
		}
	}
	require 'database/dbcontroller.php';
	$db_handle=new DBController();

	// $update_success=true;
	foreach ($attributes_on_form as $key=>$value){
		if (empty($value)){
			continue;
		}
		$attribute_id=getAttributeId($key,$db_handle);
		if ($attribute_id===false){
			echo 'weired error!cant find this attribute in database';
		}else{
			if (getProductAttribute($sku,$attribute_id,$db_handle)!==false){//record of this attribute already exist in the standalone_product_eav_attribute_values table
				$result=updateAttribute($sku,$attribute_id,$db_handle,$value);
				// var_dump($result);
				if ($result['status']===false){
					echo 'Failed to update '.$attribute_id.' of '.$sku.' to '.$value.'<br>'.$result['msg'];
					// $update_success=false;
				}else{
				}
			}else{//does not exist in standalone_product_eav_attribute_values table
				insertAttribute($sku,$attribute_id,$db_handle,$value);
			}
		}
	}
}

function getAttributeId($attribute_name,$db_handle){
	$query="select * from standalone_product_eav_attributes where attribute_name='".$attribute_name."'";
	// var_dump($query);
	$result=$db_handle->runQuery($query);
	if (is_null($result)){
		return false;
	}else{
		return $result[0]['id'];
	}
}

function updateProductInDatabase($name,$sku){
	require 'models/standalone_product.php';
	// echo 'xxx';
	$product_model=new standalone_product();
	$product_model->id=$product_model->find_id($sku);
	// var_dump($product_model->id);
	if ($product_model->id==0){
		 $error_msg.='there is no product with this sku. <br>';
	    return array('status'=>false,'error_msg'=>$error_msg);
	}
	// $product_model->set($name,$sku,$image);//image can be null
	$product_model->name=$name;
	$product_model->sku=$sku;
	// var_dump($product_model);
	$update_result=$product_model->update();
	if ($update_result===true){
	    // echo 'update product in database success.';
		return array('status'=>true);
	}else{
	    $error_msg.=$product_model::$last_error.'<br>';
	    return array('status'=>false,'error_msg'=>$error_msg);
	    //e.g: sku already exist, connection error. insert error. 
	}
}

/*
	$sku already exist and is not allowed to be modified. so use it to identify records for the same product id.
*/
function updateAttribute($sku,$attribute_id,$db_handle,$value){
	$worker_id=$_SESSION['user']['id'];
	if (is_null($worker_id)){
		header('Location: trimmed_version/login.php');
	}
	//--------------------------------------------
	$query="select * from standalone_product_eav_attribute_values where sku='{$sku}' LIMIT 1;";
	require_once 'database/dbcontroller.php';
	$db_handle=new DBController();
	$result=$db_handle->runQuery($query);
	if (is_null($result)){//should not be false because the product record already exist in previous function.
		// echo 'weird ya.';
		return array("status"=>false,"msg"=>"weird. product record not found. rare.");
	}
	//--------------------------------------------
	$record_worker_id=$result[0]['worker_id'];

// var_dump($worker_id);
// var_dump($record_worker_id);
	if($worker_id!==$record_worker_id){
		// echo 'this product is not submitted by you. you are not allowed to modify it.exiting...';
		return array("status"=>false,"msg"=>"This product is uploaded by others. You cannot modify it.");
	}

	$query="update standalone_product_eav_attribute_values SET value='".$value."' where attribute_id='".$attribute_id."' and sku='".$sku."'";
	// var_dump($query);
	$result=$db_handle->runQuery($query);
	// echo "The result of updating existing rows in database: ".$result."<br>";

	// return $result;
	return array("status"=>$result,"msg"=>"This is database update result.");
	// var_dump($result);
}
function insertAttribute($sku,$attribute_id,$db_handle,$value){
	$worker_id=$_SESSION['user']['id'];
	if (is_null($worker_id)){
		header('Location: trimmed_version/login.php');
	}
	$query="insert into standalone_product_eav_attribute_values (sku,attribute_id,value,worker_id) VALUES ('".$sku."','".$attribute_id."','".$value."','".$worker_id."')";
	// var_dump($query);
	$result=$db_handle->runQuery($query);
	// var_dump($result);
}
?>
<div id="container" class="ltr">
	<h1 id="logo">
		 <div style="font-size:20px;padding: 10px 10px;">1661USA.com
		 <span style="font-size:15px;float: right;margin-top: -7px;">
		 	Hi, <?php echo $_SESSION['user']['name'];?> &nbsp;
		 	<a style="height:19px;" href="<?php echo $product_manager_url;?>trimmed_version/logout.php">Log Out</a>
		 </span>
		 </div>
		<!-- <a href="http://www.wufoo.com/?t=o2j5o6" title="Powered by Wufoo">Wufoo</a> -->
	</h1>
	<div style="
    padding: 3px 10px 0px 10px;
    background: rgba(63, 81, 181, 0.06);
">
  		<?php
  		$admin_names=array("samip","justin","francis");
  		$has_search_priveledge=in_array($_SESSION['user']['login'],$admin_names);
  		if ($has_search_priveledge){
  			
  		}
  		?>

  		
  		<a style="height:19px;" href="<?php //var_dump($_GET['callback_url']);
  		echo $callback_url;?>">Back To Search</a>
  	</div>
	
	<!-- <form id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="https://475461387.wufoo.com/forms/mab0co1dsh5gt/"> -->
	<form style="border:none;" id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="standalone.edit.php">
  
<header id="header" class="info">
	<h2>Update Product Info / 更新产品信息</h2>
</header>


<div style="color:red;"><?php echo $error_msg;?></div>
<div style="line-height: 2;color: black;background: #DBFFDB;"><?php echo $success_insert_into_database_msg;?></div>
<?php 
if (!empty($success_insert_into_database_msg)){
	exit;
}
?>


<ul id="main_form_ul">
	<li class="main_form_li">
		<label class="desc">Product Name / 产品名称</label>
		<div>
			<input id="name" name="name" value="<?php if (!is_null($product)) {
					echo addslashes($product->name);
				}else{

					echo addslashes($retrieved_standalone_product->name);
					
				} ?>
				">
		</div>
	</li>
<?php //var_dump( $retrieved_standalone_product->name);?>
	<li class="main_form_li">
		<label class="desc">Product Sku / 产品sku</label>
		<div>
			<input id="sku" name="sku" disabled value=<?php if (!is_null($product)) {echo "'".$product->sku."'";}else{echo $retrieved_standalone_product->sku;} ?>>
			<!-- do a second input field to add sku to $_POST array: the one above is disabled so do not show up in $_POST array. -->
			<input style="display:none" name="sku" value=<?php if (!is_null($product)) {echo "'".$product->sku."'";}else{echo $retrieved_standalone_product->sku;} ?>>
		</div>
	</li>

	<li class="main_form_li">
		<label class="desc">Product Image / 产品图片</label>
		<div>
			<span id="image">
			<?php

			$image=null;
			// var_dump($product);
			if (!is_null($retrieved_standalone_product)) {
				$image=$retrieved_standalone_product->image;
			}
            if (!is_null($image) && !empty($image)){
	            echo '<img width="125px" src="'.$product_manager_url.$image.'">';
            	// echo "Image path: ".$image;
            }else{
            	echo 'No image';
            }
            ?>
            </span>
		</div>
	</li>
	
	<header id="header" class="info" style="font-weight: bold;color: #009626;color: black;background: lightgrey;">
	Competitor Prices
	</header>


	<!-- list all eav attributes -->
	<?php
		require_once 'database/dbcontroller.php';
		$db_handle=new DBController();
		// var_dump($db_handle);
		//show every attribute input text field in database table: 
		$results=$db_handle->runQuery('select * from standalone_product_eav_attributes');

		$competitors=array();
		foreach ($results as $row_id => $row) {
			$attribute_name=$row['attribute_name'];
			if (substr($attribute_name,0,4)=="url_"){
				$competitor=substr($attribute_name, 4);
				if (!in_array($competitor,$competitors)){
					array_push($competitors, $competitor);
				}
			}else if (substr($attribute_name, 0,6)=="price_"){
				$competitor=substr($attribute_name, 6);
				if (!in_array($competitor,$competitors)){
					array_push($competitors, $competitor);
				}
			}
		}
		// var_dump($competitors);
		// var_dump($results);
		echo '<div id="floating_nav" style="position:fixed;top:50;left:15;background:white;padding:15px;">';
		echo '<ul>';
		foreach ($competitors as $competitor) {
			echo '<li><a href="#'.$competitor.'">'.$competitor.'</a></li>';
		}

		echo '<input type="submit" name="submit" Value="Submit/提交">';
		echo '</ul>';
		echo '</div>';

		// var_dump($_GET);
		// var_dump($_POST);

		echo '<input style="display:none" name="callback_url" value="'.$callback_url.'">';
		foreach ($competitors as $competitor) {
			// var_dump($row['attribute_name']);
			$attribute_one_name="url_".$competitor;
			$attribute_two_name="price_".$competitor;
			$attribute_three_name="shipping_".$competitor;
			$attribute_one_id=getAttributeId($attribute_one_name,$db_handle);
			$attribute_two_id=getAttributeId($attribute_two_name,$db_handle);
			$attribute_three_id=getAttributeId($attribute_three_name,$db_handle);
			// $attribute_one_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_one_id,$db_handle);
			$attribute_one_value=getProductAttribute($retrieved_standalone_product->sku,$attribute_one_id,$db_handle);
			// $attribute_two_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_two_id,$db_handle);
			$attribute_two_value=getProductAttribute($retrieved_standalone_product->sku,$attribute_two_id,$db_handle);
			// $attribute_three_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_three_id,$db_handle);
			$attribute_three_value=getProductAttribute($retrieved_standalone_product->sku,$attribute_three_id,$db_handle);
			// var_dump("attribute value is: ");
			// var_dump($attribute_one_value);
			// var_dump($attribute_two_value);
			
			// if ($attribute_value===false){
			// 	$attribute_value='';
			// }
			echo '<div style="border:1px dashed #888;" id="'.$competitor.'">';
			//---------------url------------------
			echo '<li class="main_form_li">
				  <label class="desc">';
			echo $attribute_one_name;//url_xxx
			echo '</label>';
			echo '<div>';
			echo '<input  style="width:80%" value="'.$attribute_one_value.'" name="'.$attribute_one_name.'" id="'.$attribute_one_name.'">';
			echo '</div>';
			echo '</li>';
			//---------------price-----------------
			echo '<li class="main_form_li">
				  <label class="desc">';
			echo $attribute_two_name;//price_xxx
			echo '<span style="font-weight:normal;front-size:13px;color:grey;"> (Please add ￥ or $ / 请注明 ￥ 或者 $ )</span>';
			echo '</label>';
			echo '<div>';
			echo '<input  value="'.$attribute_two_value.'" name="'.$attribute_two_name.'" id="'.$attribute_two_name.'">';
			echo '<button type="button" class="fill_usd_button">$</button>';
			echo '<button type="button" class="fill_rmb_button">￥</button>';
			echo '<button type="button" class="clear_price_field_button">Clear</button>';
			echo '</div>';
			echo '</li>';
			//-----------shipping price-------------
			echo '<li class="main_form_li">
				  <label class="desc">';
			echo $attribute_three_name;//shipping_xxx
			echo '<span style="font-weight:normal;front-size:13px;color:grey;"> (Please add ￥ or $ / 请注明 ￥ 或者 $ )</span>';
			echo '<a href="#" data-toggle="tooltip" data-placement="right" title="If Not Available, just leave it empty."><img height="20px" src="'.$product_manager_url.'icons/info_icon.png"></a>';
			echo '</label>';
			echo '<div>';
			echo '<input  value="'.$attribute_three_value.'" name="'.$attribute_three_name.'" id="'.$attribute_three_name.'">';
			echo '<button type="button" class="fill_usd_button">$</button>';
			echo '<button type="button" class="fill_rmb_button">￥</button>';
			echo '<button type="button" class="free_shipping_buttons">Free Shipping</button>';
			echo '<button type="button" class="clear_free_shipping_field_button">Clear</button>';

			echo '</div>';
			echo '</li>';
			//--------------------------------------
			echo '</div>';

		}

		function getProductAttribute($sku,$attribute_id,$db_handle){
			$query="select * from standalone_product_eav_attribute_values where attribute_id='".$attribute_id."' and sku='".$sku."'";
			// var_dump("here");
			// var_dump($query);
			$result=$db_handle->runQuery($query);
			if (is_null($result)){
				return false;
			}else{
				return $result[0]["value"];
			}
		}
	?>


	
</ul>


<!-- testing dropzone -->
<!-- <form action="upload.php" class="dropzone"> -->
	<!-- <span>Product Name</span>
	<div>
		<input type="text" value="yoyo">
	</div>
	<span>Product Sku</span>
	<div>
		<input type="text" value="yoyo">
	</div> -->
<!-- </form> -->
<!--  finished testing dropzone-->
</form>
<a style="margin-top: 8px;background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;" href="<?php echo $product_manager_url;?>index.php?cat_id=415">Product already in website? Click here.<br>产品已经在网站中?点这里</a>
<a href="views/standalone/manage_competitors_block.php" style="margin-right: 4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;">Manage Competitors in Database<br>管理数据库中的竞争对手</a>
<a style="margin-right:4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;" href="<?php echo $product_manager_url;?>index-standalone.php">New Product<br>新建</a>
</div><!--container-->

<script>
	$(document).ready(function(){
    	$('[data-toggle="tooltip"]').tooltip();   
		//--------------------for both price fields--------------------
		$(".fill_usd_button").click(function(){
			// alert('clicked');
			$(this).prev('input').val("$").focus();
		});
		$(".fill_rmb_button").click(function(){
			// alert('clicked');
			$(this).prev('button').prev('input').val("￥").focus();

		});
		$(".clear_price_field_button").click(function(){
			// alert('clicked');
			$(this).prev('button').prev('button').prev('input').val("").focus();
		});
		//--------------------for shipping price field------------
		$(".free_shipping_buttons").click(function(){
			// alert('clicked');
			$(this).prev('button').prev('button').prev('input').val("Free Shipping");
		});
		$(".clear_free_shipping_field_button").click(function(){
			// alert('clicked');
			$(this).prev('button').prev('button').prev('button').prev('input').val("").focus();
		});
	});
</script>



