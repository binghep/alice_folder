<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="../img/app_icon.png" />
  <title>Login</title>
</head>
<body>

<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

session_start();
// var_dump(sha1("tes"));
require_once 'models/account.php';//contains class def of Account_Model
// var_dump(expression)
require_once 'models/users.php';//contains class def of Users_Model
$sha1=true;//if false, then login.php only use unhashed password(when you create password in database directly)
require_once '../config.php';//contains $debug variable. if true, then auto log in using dummy user id =2 (set session directly)
if ($debug==true){
    $_SESSION['user']['id']=2;
    header('Location: ../index.php?cat_id=415&p=1');
}
  //isset(var) returns true if var exists and has value other than NULL, FALSE otherwise.
  if (isset($_SESSION['user'])) {
        /* redirect browser */
        
        // echo("<p>session user set</p>");
        header('Location: ../index.php?cat_id=415&p=1');
        /*Make sure that the code below does not get executed when we redirect.*/
        exit();
  }else{
        // echo("<p>session user not set</p>");
  }

  $error_message = '';

  if (! empty($_POST)) {
      extract( $_POST );
      
      //empty($variable) returns true if it doesn't exist or if its value equals false 
      if (empty($login)) {
          $error_message .= 'Please enter a valid value for Login Name field.<br />'; 
      }
      if(empty($password)) {
          $error_message .= 'Please enter a valid value for Password field.<br />'; 
      }
     
   
    if (!empty($login) && !empty($password))
    {
      /* input validation 2 ---part 1: query database for usernames and passwords */
      /* input validation 2 ---part 2: if the login/username is valid(i.e. in the database)*/
      $users=new Users();//Once created, contains all the account info obtained from the mysql database
    //  print_r($users);

      $curr_userID=Account::find_acc_id($login);
      if ($debug){
          $error_message.=  'User Id:　'.$curr_userID.".<br>      ";
      }
      if ($curr_userID!=0){
          $curr_user=new Account();
          $curr_user->load_account($curr_userID);
          // var_dump($curr_user);
          if ($debug) {
              $error_message.=$curr_user->acc_password.'-my-'.sha1($password);
          }
          $user_filled_password=($sha1?sha1($password):$password);

          if($curr_user->acc_password==$user_filled_password){
              $_SESSION['user']['id']=$curr_user->acc_id;
              $_SESSION['user']['login'] = $curr_user->acc_login;
              // $_SESSION['user']['password'] = $curr_user->acc_password;
              $_SESSION['user']['name'] = $curr_user->acc_name;
              //redirect browser 
              header('Location: ../index.php?cat_id=415');
              //Make sure that the code below does not get executed when we redirect.
              exit();
          }else{
              $error_message.='Password is incorrect. Please check the login and try again.<br />';
          }
      }else{
            $error_message.='Login does not exist. Please check the login and try again.<br />';
      } 
  }else{
    //post is empty, do nothing.
  }
}
?>
<?php
  include_once 'views/login_view.php';
  //$wd_was=getcwd();
  // chdir("/home/pengx077/.www/assg8/views/");
  // if ((include 'login_view.php')=='OK'){
  //     echo 'OK';
  // }
  // chdir($wd_was);
?>
