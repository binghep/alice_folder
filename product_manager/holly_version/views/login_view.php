
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Login Page</title>
  <link rel="stylesheet" type="text/css" href="Style.css">
</head>
<body>
  <div class="silver">
    <h1>Login Page</h1>
    
    <div style="color:red"> <?php echo $error_message; ?></div>

    <p>
      Please enter your user's login name and password. Both values are case sensitive.
     
    </p>


    <form method="post">
      <label for="login">Login: </label>
      <input type="text" name="login" value="" /> 
      <br /><br />
      <label for="password">Password: </label>
      <input type="password" name="password" value="" /> 
      <br /><br />
      <input type="submit" value="Submit" />
    </form>
    
  </div>
</body>
</html>
