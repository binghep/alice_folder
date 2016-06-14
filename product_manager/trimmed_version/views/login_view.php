<?php
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Login Page</title>
  <!-- <link rel="stylesheet" type="text/css" href="Style.css"> -->
  <link rel="stylesheet" type="text/css" href="livetools.uiparade.com.form.builder.css">
</head>
<body>
    <div id="button-box">
        <div id="error_msg"> <?php echo $error_message; ?></div>
  <!--
      <p>
        Please enter your user's login name and password. Both values are case sensitive.
       
      </p>
  -->

  <!--     <form method="post">
        <label for="login">Login: </label>
        <input type="text" name="login" value="" /> 
        <br /><br />
        <label for="password">Password: </label>
        <input type="password" name="password" value="" /> 
        <br /><br />
        <input type="submit" value="Submit" />
        <br>
        <a href="register.php">Register</a>
      </form> -->

        <form method="post" class="form-container">
            <div class="form-title"><h2>Log In / 登录</h2></div>
            <div class="form-title">Login / 用户名</div>
            <input class="form-field" type="text" name="login" /><br />
            <div class="form-title">Password / 密码</div>
            <input class="form-field" type="password" name="password" /><br />
            <div class="submit-container">
            <input class="submit-button" type="submit" value="Submit / 提交" />
            </div>
            <!-- <a href="register.php">Register / 注册</a> -->
        </form>
    </div>
</body>
</html>
