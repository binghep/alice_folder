<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();

echo '<head>
	<title>Product Research Form</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
    <style>
        a{
            display:none;
        }
    </style>
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

// function format_ok($string){
// 	// if (preg_match('/[^A-Za-z0-9]/', $string)) // '/[^a-z\d]/i' should also work.     
// 	//As the first char inside a [] group, ^ means "Not", so "NOT a-zA-Z", not "[a-zA-Z] at start of line" as you were trying.
// 	if (preg_match('[A-Za-z0-9]+', $string)) // '/[^a-z\d]/i' should also work.     
// 	{
// 	  // string contains only english letters, digits, or _
// 		return true;
// 	}else{
// 		return false;
// 	}
// }

$new_attribute_label=$_GET['new_attribute_label'];
$new_attribute_code=strtolower($new_attribute_label);
if (empty($new_attribute_label) || !isset($new_attribute_label)){
	echo 'Please enter a non-empty value, like "amazon" or "amazonUSA". Exiting. ';
	echo '<br>';
	echo '错误：请输入非空的属性名称，比如 "amazon"或者"amazonUSA". 正在退出. ';
	exit;
}

if (!hasOnlyLetters($new_attribute_label)){
	echo  'Format of new attribute label is NOT OK. it can only contain English letters (Example: amazonUSA, taobao) 
		<br>
		您输入的产品属性名称不符合以下要求：只能包含英文字母。 (比如： amazonUSA, taobao)';
	exit;
}

function hasOnlyLetters($str) {
   // return preg_match('/^[a-zA-Z_]+$/i',$str); //-->working. letter and underscore only
   return preg_match('/^[a-zA-Z]+$/i',$str);
}

// var_dump(preg_match('[A-Za-z0-9_]+', "iew933()"));
// var_dump(preg_match('[A-Za-z0-9_]+', "url_amazon"));
// var_dump(hasOnlyLetters("jdjksdfjkl_"));
// var_dump(hasOnlyLetters("jdjksdfjkl"));
// var_dump(hasOnlyLetters("jdjksdfj_jdjjf"));
// var_dump(hasOnlyLetters("jdjksdf565"));


// $new_attribute_label="hellokitty";
function helper($new_attribute_label){
	$new_attribute_code=strtolower($new_attribute_label);
	$new_attribute_type="text";
	$result=createAttribute($new_attribute_code, $new_attribute_label, $new_attribute_type, "");
	if ($result===true){
		echo '1. Successfully created attribute with code: '.$new_attribute_code.'<br>';
	}else{
		echo $result;//"Error ...."
		exit;
	}
	addToDemoAttributeSet_Prices_Group($new_attribute_code);
	echo "2. Finished adding the attribute to the Demo Attribute Set.";
}

helper("price_".$new_attribute_label);
echo '<hr>';
helper("url_".$new_attribute_label);
echo '<hr>';
//-------------------------------------------------------------
include 'setting_manager.php';
$setting=new setting_manager("database/setting.txt");
$allowed_competitors=$setting->get_allowed_competitor_array();
if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
	echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Now I am creating a default one first...<br>';
}
$result=$setting->addCompetitor("amazon");
$result=$setting->addCompetitor("jd");
$result=$setting->addCompetitor("taobao");
$result=$setting->addCompetitor("tmall");
echo 'Adding '.$new_attribute_code.' to setting.txt<br>';
$result=$setting->addCompetitor($new_attribute_code);
// var_dump($result);//true
// var_dump($setting->get_allowed_competitor_array());

if ($result===false){
	echo '<hr>Result/结果: <div style="color:red"> Failure/失败</div>. Please contact IT department/请联系IT部门.<br>';
	exit;
}
//-------------------------------------------------------------
echo "<hr>Result/结果: <div style='color:green;font-weight:bold;font-size:20px;'>Success/成功</div>
	<a href='index.php?cat_id=415&type_id=smart'>Back to Home Page/返回主页</a>";
$allowed_competitors=$setting->get_allowed_competitor_array();

echo 'Now all competitors in this system include / 现在本应用中存在 以下竞争对手: <br>';
echo '<ul>';
foreach ($allowed_competitors as $competitor) {
	echo '<li>'.$competitor.'</li>';
}
echo '</ul>';

/*
$attribute type: please choose from: 
1. text   -->100% choose this one
2. textarea
3. date
4. boolean
5. multiselect
6. select
7. price
8. media_image
9. weee
*/
function createAttribute($code, $label, $attribute_type, $product_type)
{		
	$_attribute_data = array(
		// 'attribute_code' => 'old_site_attribute_'.(($product_type) ? $product_type : 'joint').'_'.$code,
		'attribute_code' => $code,
		'attribute_set'=>"Demo Attribute Set",
		'is_global' => '1',
		'frontend_input' => $attribute_type, //'text',
		'default_value_text' => '',
		'default_value_yesno' => '0',
		'default_value_date' => '',
		'default_value_textarea' => '',
		'is_unique' => '0',
		'is_required' => '0',
		'apply_to' => array($product_type), //array('grouped')
		'is_configurable' => '0',
		'is_searchable' => '0',
		'is_visible_in_advanced_search' => '0',
		'is_comparable' => '0',
		'is_used_for_price_rules' => '0',
		'is_wysiwyg_enabled' => '0',
		'is_html_allowed_on_front' => '1',
		'is_visible_on_front' => '0',
		'used_in_product_listing' => '0',
		'used_for_sort_by' => '0',
		'frontend_label' => array($label)
	);
 
 
	$model = Mage::getModel('catalog/resource_eav_attribute');
 
	if (!isset($_attribute_data['is_configurable'])) {
		$_attribute_data['is_configurable'] = 0;
	}
	if (!isset($_attribute_data['is_filterable'])) {
		$_attribute_data['is_filterable'] = 0;
	}
	if (!isset($_attribute_data['is_filterable_in_search'])) {
		$_attribute_data['is_filterable_in_search'] = 0;
	}
 //change the code if the attributes set is user defined (might have different code for the same attribute)
	if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
		$_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
	}
 
	// $defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
	// if ($defaultValueField) {
	// 	$_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
	// }
 
 
	$model->addData($_attribute_data);
 
	$model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
	$model->setIsUserDefined(1);
 
 
	try {
		$model->save();
		// echo 'successfully created '.$label;
		return true;
	} catch (Exception $e) { 
		// echo '<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; 
		return "Error ".$e->getMessage();
	}
}

/*
Add the attribute to the price group under "Demo Attribute Set"
*/
function addToDemoAttributeSet_Prices_Group($new_attribute_code){
	//now add this attribue to "Demo Attribute Set"
	$attribute_set_name = 'Demo Attribute Set';
	$group_name = 'Prices';
	$attribute_code = $new_attribute_code;
	// echo 'here';
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	// echo 'bbb';

	//-------------- add attribute to set and group-----------------------
	$attribute_set_id=$setup->getAttributeSetId('catalog_product', $attribute_set_name);
	$attribute_group_id=$setup->getAttributeGroupId('catalog_product', $attribute_set_id, $group_name);
	$attribute_id=$setup->getAttributeId('catalog_product', $attribute_code);

	$result=$setup->addAttributeToSet($entityTypeId='catalog_product',$attribute_set_id, $attribute_group_id, $attribute_id);
	// var_dump($result->_conn);//a lot of unuseful vardump. i can't find true or false easily
	// echo 'finished';
}