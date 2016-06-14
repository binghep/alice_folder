
<?php
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

	function debug_to_console( $data ) {
	    if ( is_array( $data ) )
	        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
	    else
	        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

	    echo $output;
	}
?>

<?php
	session_start();
	/* clear events in session */
	if (isset($_SESSION['user'])){debug_to_console("was set");}
	unset($_SESSION['user']);
	//session_unset();
	//session_destroy();
	debug_to_console("unsetting user");
	/* redirect the user to the login page */
	header('Location: login.php');
	exit();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset='utf-8'>
	<title>Logout Page</title>
	<link rel="stylesheet" type="text/css" href="Style.css">
</head>

<body>
	<p>
		Logging Out
	</p>
</body>
</html>