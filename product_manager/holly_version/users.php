<?php
$debug=false;
session_start();

//isset() determine if a variable is set and is not null
if(isset($_SESSION['user'])){//should be true if has logged in using login.php

}else{
  	/* redirect the user to the login page */
 	header('Location: login.php');
	exit();
}

include_once 'models/account.php';//contains class def of Account_Model
include_once 'models/users.php';//contains class def of Users_Model


if (! empty($_POST)) {
	// print("Post Array: <br />");
	if ($debug){
		print_r($_POST);
		print("<br />");
	}
	if (isset($_POST['edit'])){
		$_SESSION['user']['edit_mode']=true;
		$row_id_to_edit=(int)$_POST['edit'];
	}else if(isset($_POST['delete'])){
		delete_user();
	}else if(isset($_POST['update'])){
		update_user();
	}else if(isset($_POST['create'])){
		create_user();
	}
}




function delete_user($row_id_to_delete){
	$row_id_to_delete=(int)$_POST['delete'];
	
	$account=new Account();//Contains the current user info, account_model->curr_user_info
		
	$account->load_account($row_id_to_delete);
	if ($account->delete()){
		print("delete successfully");
	}else{
		print("error deleting account");
	}
}

function update_user($row_id_to_update){
	$error_message = '';
	$row_id_to_update=$_POST['update'];
	
	extract($_POST);	

	if(empty($temp_name)){
		$error_message .= 'Please enter a valid value for name field.<br />'; 
	}
	if (empty($temp_login)) {
		$error_message .= 'Please enter a valid value for Login Name field.<br />'; 
	}
	if(empty($temp_password)) {
		$error_message .= 'Please enter a valid value for Password field.<br />'; 
	}

    if (empty($error_message)){
    	//name, login, password passed validation:
    	$account=new Account();//Contains the current user info, account_model->curr_user_info
    	$account->set($row_id_to_update, $temp_name, $temp_login, $temp_password);
    	$account->update();
    }
    $_SESSION['user']['edit_mode']=false;
}

function create_user(){
	var_dump($_POST);
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
		var_dump($newuser);
		if ($newuser->add_new_account()){print("successfully added account");}
		else{print($error_message);}
	}
	// echo 'here';
}


include_once 'views/users_view.php';
?>
