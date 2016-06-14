
<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8">
	<title>Users Page</title> 
	<link href="Style.css" type="text/css" rel="stylesheet">
</head>

<body>
	<div class="silver">
		<p>Welcome <?php echo $_SESSION['user']['name']; ?></p>
		<a href="logout.php">Log Out</a>
		<p>This page is protected from the public, and you can see a list of all users defined in the database.</p>
	</div>


	

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
		</div>

		<div class="silver">
			<h1>List of Users</h1>
			<table class="myTable">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Login</th>
					<th>New Password</th>
					<th>Action</th>
				</tr>
		        <?php
		   //      	include_once 'models/account.php';//contains class def of Account_Model
					// include_once 'models/users.php';//contains class def of Users_Model

		        	$users=new Users();
		        	$list_=$users->list;

		        	foreach ( $list_ as $key => $value ){
		        		// print ($row_id_to_edit); 
		        		if (!empty($row_id_to_edit) && $key==(int)$row_id_to_edit){
			        		print( "<tr>" );
			               //print first 3 colums in this account: 
			               print( "<td>$value->acc_id</td><td><input type='text' name='temp_name' value=''></td><td><input type='text' name='temp_login' value=''></td>" );
			               //print New Password Column element on this row:
			               print("<td><input type='text' name='temp_password' value=''></td>");
			               
			               //print Action column on this row:
			               print("<td>");
			               print("<button name='update' value=$key>Update</button>");
			               print("<button name='cancel' value=$key>Cancel</button>");
			               print("</td>");

			               print( "</tr>" );
		        		}else{
			               //print( "<td>$value</td>" );
			               // build table to display results
			               print( "<tr>" );
			               //print first 3 colums in this account: 
			               print( "<td>$value->acc_id</td><td>$value->acc_name</td><td>$value->acc_login</td>" );
			               //print New Password Column element on this row:
			               print("<td></td>");
			               
			               //print Action column on this row:
			               print("<td>");
			               print("<button name='edit' value=$key>Edit</button>");
			               print("<button  name='delete' value=$key>Delete</button>");
			               print("</td>");

			               print( "</tr>" );
		           		}
		            }
		        ?><!-- end PHP script -->
	  		</table>

	  		<p>This database has <?php echo count($list_) ; ?> rows/accounts.</p>
		</div>
	
</form>

</body>
</html>
