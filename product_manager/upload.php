<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}


$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = 'uploads';   //2
 
if (!empty($_FILES)) {
     
    $tempFile = $_FILES['file']['tmp_name'];          //3             
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
     
    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
 
    move_uploaded_file($tempFile,$targetFile); //6
     
}
