<?php 

session_start();
// var_dump(sha1("tes"));
include_once 'models/account.php';//contains class def of Account_Model
// var_dump(expression)
include_once 'models/users.php';//contains class def of Users_Model
$sha1=true;//if false, then login.php only use unhashed password(when you create password in database directly)
$debug=false;

if ($debug==true){//if debug is true, automatically set session. so go to users.php directly.
    $_SESSION['user']=2;
    header('Location: users.php');
}
  //isset(var) returns true if var exists and has value other than NULL, FALSE otherwise.
  if (isset($_SESSION['user'])) {
        /* redirect browser */
        echo("<p>session user set</p>");
        header('Location: users.php');
        /*Make sure that the code below does not get executed when we redirect.*/
        exit();
  }else{
        echo("<p>session user not set</p>");
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
      $error_message.=  $curr_userID."      ";
      if ($curr_userID!=0){
          $curr_user=new Account();
          $curr_user->load_account($curr_userID);
          // var_dump($curr_user);
          $error_message.=$curr_user->acc_password.'-my-'.sha1($password);

          $user_filled_password=($sha1?sha1($password):$password);

          if($curr_user->acc_password==$user_filled_password){
              $_SESSION['user']['id']=$curr_user->acc_id;
              $_SESSION['user']['login'] = $curr_user->acc_login;
              $_SESSION['user']['password'] = $curr_user->acc_password;
              $_SESSION['user']['name'] = $curr_user->acc_name;
              //redirect browser 
              header('Location: users.php');
              //Make sure that the code below does not get executed when we redirect.
              exit();
          }else{
              $error_message.='Password is incorrect. Please check the login and try again.<br />';
          }
      }else{
            $error_message.='Login is incorrect. Please check the login and try again.<br />';
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
