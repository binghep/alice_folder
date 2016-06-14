<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="../img/app_icon.png" />
  <title>Register</title>
</head>
<body>


<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// return;
$debug=false;
session_start();

//isset() determine if a variable is set and is not null
if(isset($_SESSION['user'])){//should be true if has logged in using login.php
 	header('Location: ../index.php?cat_id=415');
	exit();
}else{

}

include_once 'models/account.php';//contains class def of Account_Model
// include_once 'models/users.php';//contains class def of Users_Model

$error_message='';

if (isset($_POST['create'])) {
	// print("Post Array: <br />");
	if ($debug){
		print_r($_POST);
		print("<br />");
	}
	
	$error_message=create_user($error_message);
}




function create_user($error_message){
	// var_dump($_POST);
	extract($_POST);
	//empty($variable) returns true if it doesn't exist or if its value equals false 
	$error_message='';
	if (empty($login_for_new_user)) {
	  	$error_message .= 'Please enter a valid value for Login Name field.<br />'; 
	}
	if(empty($password_for_new_user)) {
		$error_message .= 'Please enter a valid value for Password field.<br />'; 
	}
	if(empty($name_for_new_user)) {
		$error_message .= 'Please enter a valid value for Name field.<br />'; 
	}

	if (empty($error_message))
	{
		$newuser=new Account();
		$account_id="";
		$newuser->set($account_id,$name_for_new_user,$login_for_new_user,$password_for_new_user);
		// var_dump($newuser);
		if ($newuser->add_new_account()){
			print("successfully created account");
			login($login_for_new_user,$error_message);
	 		header('Location: ../index.php?cat_id=415');
		}else{
			// print($newuser::$last_error);
			// print('<br>');
			// var_dump($newuser::$last_error);
			$error_message.="Error creating account: ".$newuser::$last_error."<br>";
		}
	}
	return $error_message;
}


function login($login,$error_message){
	  include 'models/users.php';//contains class def of Users_Model
	  echo 'here';
	  $users=new Users();//Once created, contains all the account info obtained from the mysql database
	  
	  $account_id=Account::find_acc_id($login);
      // if ($debug){
      //     $error_message.=  'User Id:　'.$account_id.".<br>      ";
      // }
	  
	  if ($account_id!=0){
	      $curr_user=new Account();
	      $curr_user->load_account($account_id);
          $_SESSION['user']['id']=$curr_user->acc_id;
          $_SESSION['user']['login'] = $curr_user->acc_login;
          // $_SESSION['user']['password'] = $curr_user->acc_password;
          $_SESSION['user']['name'] = $curr_user->acc_name;
          //redirect browser 
          header('Location: ../index.php?cat_id=415');
          //Make sure that the code below does not get executed when we redirect.
          exit();
	  }else{
	        $error_message.='Login does not exist. Please check the login and try again.<br />';
	  }
	  return $error_message;
}

include_once 'views/register_view.php';
?>
