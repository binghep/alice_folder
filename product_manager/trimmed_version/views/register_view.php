<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8">
	<title>Register</title> 
	<!-- <link href="Style.css" type="text/css" rel="stylesheet"> -->
	<link rel="stylesheet" type="text/css" href="livetools.uiparade.com.form.builder.css">
</head>

<body>
<!-- 	From this page, you can register to be able to add competitor prices to 1661 USA.com products. We will pay you based on the number of competitor prices you update.
	<form method='post'>
		<div class="silver">
			  <h2>Create New User</h2>
		      <label for="name">Name: </label>
		      <input type="text" name="name_for_new_user" value="" /> 
		      <br /><br />
		      <label for="login">Login: </label>
		      <input type="text" name="login_for_new_user" value="" /> 
		      <br /><br />
		      <label for="password">Password: </label>
		      <input type="password" name="password_for_new_user" value="" /> 
		      <br /><br />
		      <input type="submit" name="create" value="Create User" />
		      <br>
	    	  <a href="login.php">Log In</a>
		</div>
	</form> -->

    <div id="button-box">
        <div id="error_msg"> <?php echo $error_message; ?></div>
    
        <form method="post" class="form-container">
            <div class="form-title"><h2>Register / 注册</h2></div>
            
            <div class="form-title">Login / 用户名</div>
            <input class="form-field" type="text" name="login_for_new_user" /><br />
            
            <div class="form-title">Password / 密码</div>
            <input class="form-field" type="password" name="password_for_new_user" /><br />

            <div class="form-title">Full Name / 姓名</div>
            <input class="form-field" type="text" name="name_for_new_user" /><br />

            <div class="submit-container">
            <input class="submit-button" type="submit" name="create" value="Submit / 提交" />
            </div>
            <a href="login.php">Log In / 登录</a>
        </form>
    </div>
</body>
</html>
