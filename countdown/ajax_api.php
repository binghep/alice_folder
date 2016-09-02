<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

$secret_key=$_GET['secret_key'];
if ($secret_key!=="uiweui35s653kjsd923*2w"){
	return;
}

require '/usr/share/nginx/www/1661hk.com/lib/alice/dbcontroller.php';
$db_handle=new DBController();

$query="SELECT * FROM `countdown_dates` WHERE `beijing_datetime` >= NOW() ORDER BY `beijing_datetime` LIMIT 1;";
$result=$db_handle->runQuery($query);

if (!is_null($result)){
	$response['status']=true;
	$response['data']['beijing_datetime']=$result[0]['beijing_datetime'];
	$response['data']['holiday_chinese_name']=$result[0]['holiday_chinese_name'];
	$response['data']['holiday_english_name']=$result[0]['holiday_english_name'];
}

die (json_encode($response, JSON_FORCE_OBJECT));