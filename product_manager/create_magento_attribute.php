<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

echo '<head>
	<title>Price Research Form</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    
	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	</head><body>';

echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';

?>

<?php
require_once '../../app/Mage.php';
require_once 'config.php';
if ($_SESSION['user']['login']=="samip"){
	$is_admin=true;//update the $is_admin value in config.php
}
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);



// $sku = $_GET['sku'];
// if (empty($sku) || !isset($sku)) {
// 	echo 'sku not valid. exiting...';
// 	exit;
// }
?>

<form>
	<div>
		<h1>Add　Competitor / 添加竞争对手 </h1> 
		<hr>
		<h4>Q: Why adding new competitors?</h4> 
		A: After adding new competitor here, the form will have two more input fields (price and url) corresponding to this new competitor. 
		<br>
		<hr>
		<h4>问： 为什么要添加竞争对手？</h4>
		答： 添加竞争对手后,前台就会多出这个竞争对手的 链接 和 价格 文本框
		<br>
		<hr>
	</div>
	<div>
		The system currently has the following competitors: / 目前系统中收录 的竞争对手：<br><br>
		<?php
			require 'setting_manager.php';
			//-------------------------------------------------------------
			$setting=new setting_manager("database/setting.txt");
			
			$allowed_competitors=$setting->get_allowed_competitor_array();
			if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
				echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Please add one';
				// exit;
			}
			// var_dump($setting->get_allowed_competitor_array());//should have 100pm now
			foreach ($allowed_competitors as $competitor) {
				echo $competitor.'<br>';
			}
		?>
	</div>
	<hr>
	<br>
	<div style="text-align: center">
		<div style="width:100%;margin:10px auto;">
			<label class="desc">Please Enter Competitor Nickname / 请输入 竞争对手 昵称：</label>
			<input id="new_attribute_label" type="text">
			<button type="button" id="create_attribute_button">Add/添加</button>
		<p style="color:#349010">
			Please wait up to 30 seconds after clicking this button, don't close or refresh the pop-up window / 点击按钮后请稍后,不要刷新或者关闭跳出的小窗口
			<br>
			<br>
			Note: please enter English letters only. (e.g. amazon,wholefoods,amazonUSA)
			<br>
			注：请输入英文字母。(比如 amazon,wholefoods,amazonUSA)
		</p>
		</div>
	</div>
</form>

<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#create_attribute_button').click(function(){
		var new_attribute_label=$('input#new_attribute_label').val();
		if (!new_attribute_label) {
			alert('competitor name should not be empty. 竞争对手昵称不能为空');
		}else{
			// alert('here');
			var create_attribute_page_url="create_magento_attribute_api.php?new_attribute_label="+new_attribute_label;
			// window.open(create_attribute_page_url,'_blank');
			window.open(create_attribute_page_url,null,"height=200,width=400,status=yes,toolbar=no,menubar=no,location=no");
		}
	});
});
</script>

<!-- create_magento_attribute.php?new_attribute_label=hellokitty -->