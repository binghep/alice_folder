<?php



function delete_file($file_path){
	// $file = "test.txt";
	if (strpos($file_path,"..")!==false){
		$error='the image path cannot contain ..<br>';
		return array('status'=>false,"error"=>$error);
	}else if(strpos($file_path, "usr/share/")!==false){
		$error='the image path cannot contain absolute server path<br>';
		return array('status'=>false,"error"=>$error);
	}else if(strpos($file_path,"uploads/")!==0){
		$error='the image path must start with the required folder name<br>';
		return array('status'=>false,"error"=>$error);
	}

	if (!unlink($file_path)){
		$error="Error deleting $file <br>";
		return array('status'=>false,"error"=>$error);
	}else{
	  	// echo ("Deleted $file_path");
	  	return array('status'=>true);
	}
}

$error_msg='';
$success_insert_into_database_msg='';
$new_image_path=null;
$product=null;
if (!empty($_POST['submit'])){
	//user clicked 'submit' button:
	// var_dump($_POST);
	if (empty($_POST['name'])){
		$error_msg.="name cannot be empty<br>";
	}
	if (empty($_POST['sku'])){
		$error_msg.="sku cannot be empty<br>";
	}
	// var_dump($_POST);

	// if (!empty($_POST['image'])){
	// 	$error_msg.="image cannot be empty<br>";
	// }
	//------------------------rename image ---------------------
	$hidden_uploaded_image_name=$_POST['hidden_uploaded_image_name'];
	if (!empty($hidden_uploaded_image_name)){
		$position_of_dot=strpos($hidden_uploaded_image_name,'.');
		$image_name_wo_extension=substr($hidden_uploaded_image_name, 0,$position_of_dot);
		// var_dump($image_name_wo_extension);
		$image_extension=substr($hidden_uploaded_image_name, $position_of_dot);
		// var_dump($image_extension);//.png

		$timestamp=time();
		$new_image_name=$image_name_wo_extension.'--'.$timestamp.$image_extension;
		// var_dump($new_image_name);

		//-----------create a copy of uploaded image with the new name (with timestamp)
		$image1="uploads/".$hidden_uploaded_image_name;
		$new_image_path="uploads/".$new_image_name;
		$result=copy($image1,$new_image_path);
		// $result=copy("uploads/tt.txt","uploads/tt2.txt");
		if ($result===false){
			$error_msg.='failed to create a copy of uploaded image. cp() failed. ';
		}else{
			$result2=delete_file($image1);
			if ($result2['status']===false){
				// $error_msg.=$result2['error'];
				//if deleting image failed, don't affect product saving. just print it out.
				echo $result2['error'];
			}
		}
		// var_dump($result);
		//-----------remove original image---------------------------

	}
	
	if (empty($error_msg)){
		// var_dump($_POST);
		$result=saveProductToDatabase($_POST['name'],$_POST['sku'],$new_image_path);
		$result2=saveEavAttributesToDatabase($_POST);
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
			if (!$load_result){
				$error_msg.='error. Failed to find the product in database. <br>';
			}else{
				$success_insert_into_database_msg="Successfully submitted new product info. Please click on ".'<a style="margin-right:4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;color: black;padding:2.5px 3px;border: 1px solid black;" href="standalone.index.php">This button</a>'." to upload next one.<br>您已成功提交新产品信息。请点击 " .'<a style="background: #DEDEDE;text-decoration: none;color: black;padding:2.5px 3px;border: 1px solid black;" href="standalone.index.php">这个按钮</a>'. " 来提交下一个产品。";
			}
		}
	}
}

function saveEavAttributesToDatabase($POST_Array){
	
	$sku=$POST_Array['sku'];
	$attributes_on_form=array();
	foreach ($POST_Array as $key => $value) {
		if (strpos($key, "url_")===0 || strpos($key,"price_")===0 || strpos($key,"shipping_")===0){
			$attributes_on_form[$key]=$value;
		}
	}
	require 'database/dbcontroller.php';
	$db_handle=new DBController();

	foreach ($attributes_on_form as $key=>$value){
		if (empty($value)){//don't process or save empty fields
			continue;
		}
		$attribute_id=getAttributeId($key,$db_handle);
		if ($attribute_id===false){
			echo 'weired error!cant find this attribute in database';
		}else{
			if (getProductAttribute($sku,$attribute_id,$db_handle)!==false){//record of this attribute already exist in the standalone_product_eav_attribute_values table
				updateAttribute($sku,$attribute_id,$db_handle,$value);
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

function saveProductToDatabase($name,$sku,$image){
	require 'models/standalone_product.php';
	// echo 'xxx';
	$product_model=new standalone_product();
	$product_model->set($name,$sku,$image);//image can be null
	// var_dump($product_model);
	$insert_result=$product_model->add_new_Product();
	if ($insert_result===1){
	    // echo 'inserting product in database success.';
	}else{
	    $error_msg.=$product_model::$last_error.'<br>';
	    // var_dump($product_model::$last_error);
	    return array('status'=>false,'error_msg'=>$error_msg);
	    //e.g: sku already exist, connection error. insert error. 
	}
	return array('status'=>true);
}

function updateAttribute($sku,$attribute_id,$db_handle,$value){
	$query="update standalone_product_eav_attribute_values SET value='".$value."' where attribute_id='".$attribute_id."'' and sku='".$sku."'";
	// var_dump($query);
	$result=$db_handle->runQuery($query);
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
  			echo '<a style="height:19px;" href="'.$product_manager_url.'standalone.search_product.admin_version.php">Search Product by Name (Admin Only)</a>';
  			echo '<br>';
  		}
  		?>
  		<a style="height:19px;" href="<?php echo $product_manager_url;?>standalone.view-history.php">View History</a>
  		<br>
  		<a style="height:19px;" href="<?php echo $product_manager_url;?>standalone.search_product.employee_version.php">Edit My Products</a>
  	</div>
	
	<!-- <form id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="https://475461387.wufoo.com/forms/mab0co1dsh5gt/"> -->
	<form  class="dropzone" style="border:none;" id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="standalone.index.php">
  
<header id="header" class="info">
	<h2>Submit New Product Info / 提交 新产品信息</h2>
	<?php

	// var_dump($is_admin);
	if ($is_admin){
		echo '<div><a href="standalone.report.php" target="_blank">View Product Update Report</a></div>';
	}
	?>
	<div style="color: grey;">For products that are not in our database, you can use this form to submit competitor prices. <br>如果这个产品不在我们网站中，您可以使用下面的表格提交新产品信息。(注意：产品一旦提交无法修改)</div>	
</header>


<div style="color:red;"><?php echo $error_msg;?></div>
<div style="line-height: 2;color: black;background: #DBFFDB;"><?php echo $success_insert_into_database_msg;?></div>



<ul id="main_form_ul">
	<li class="main_form_li">
		<label class="desc">Product Name / 产品名称</label>
		<div>
			<input id="name" name="name" value=<?php if (!is_null($product)) {echo "'".$product->name."'";} ?>>
		</div>
		<a href="#" id="search_on_amazon">Amazon</a>
		<a href="#" id="search_on_google">Google</a>
		<a href="#" id="search_on_walmart">Walmart</a>
		<a href="#" id="search_on_target">Target</a>
	</li>

	<li class="main_form_li">
		<label class="desc">Product Sku / 产品sku</label>
		<div>
			<input id="sku" name="sku" value=<?php if (!is_null($product)) {echo "'".$product->sku."'";} ?>>
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
		echo '</ul>';
		echo '</div>';

		foreach ($competitors as $competitor) {
			// var_dump($row['attribute_name']);
			$attribute_one_name="url_".$competitor;
			$attribute_two_name="price_".$competitor;
			$attribute_three_name="shipping_".$competitor;
			$attribute_one_id=getAttributeId($attribute_one_name,$db_handle);
			$attribute_two_id=getAttributeId($attribute_two_name,$db_handle);
			$attribute_three_id=getAttributeId($attribute_three_name,$db_handle);
			$attribute_one_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_one_id,$db_handle);
			$attribute_two_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_two_id,$db_handle);
			$attribute_three_value=empty($_POST['submit'])?'':getProductAttribute($_POST['sku'],$attribute_three_id,$db_handle);
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

	<header id="header" class="info">
	</header>

	<li class="main_form_li">
		<label class="desc">Product Image / 产品图片</label>
		<input style="display:none" name="hidden_uploaded_image_name"  id="hidden_uploaded_image_name" >
		<div>
			<span id="image">
			<?php

			$image=null;
			if (!is_null($product)) {
				$image=$product->image;
			}
            if (!is_null($image) && !empty($image)){
	            echo '<img width="125px" src="'.$product_manager_url.$image.'">';
            	// echo "Image path: ".$image;
            }else{
            	// echo 'No image';
            }
            ?>
            </span>
		</div>
	</li>
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
<a href="views/standalone/inner.manage_competitors.block.php" style="margin-right: 4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;">Manage Competitors in Database<br>管理数据库中的竞争对手</a>
<a style="margin-right:4px;margin-top: 8px;background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;" href="<?php echo $product_manager_url;?>standalone.index.php">New Product<br>新建</a>
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



		//------------for search on Amazon conveniently---------
		$('#search_on_amazon').click(function(){
			var name=$('#name').val();
			if (name){//if string is not empty
				// alert($name);
				window.open("http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords="+encodeURIComponent(name));
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on Google conveniently---------
		$('#search_on_google').click(function(){
			var name=$('#name').val();
			if (name){//if string is not empty
				// alert($name);
				window.open("https://www.google.com/search?q="+encodeURIComponent(name));
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on Walmart conveniently---------
		$('#search_on_walmart').click(function(){
			var name=$('#name').val();
			if (name){//if string is not empty
				// alert($name);
				window.open("http://www.walmart.com/search/?query="+encodeURIComponent(name));
			}else{
				alert('Name cannot be empty');
			}
		});
		//------------for search on Target conveniently---------
		$('#search_on_target').click(function(){
			var name=$('#name').val();
			if (name){//if string is not empty
				// alert($name);
				window.open("http://www.target.com/s?searchTerm="+encodeURIComponent(name)+"&category=0%7CAll%7Cmatchallpartial%7Call+categories");
			}else{
				alert('Name cannot be empty');
			}
		});

});
</script>



