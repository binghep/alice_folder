<?php
class Users {
	public $list; //will hold list of all users
	public $last_error;
	
	function __construct() {
		$this->last_error = '';
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		// Create connection
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			$this->last_error = $conn->connect_error;
		} else {
			//Query Database for login name
			$sql_query = 'SELECT acc_id, acc_name, acc_login, acc_password FROM tbl_accounts ORDER BY 
			acc_name;';
			$result = $conn->query($sql_query);
			$this->list = array();
			while ($row = $result->fetch_assoc()) {
				$user = new Account();
				$user->set((int)$row['acc_id'], $row['acc_name'], $row['acc_login'], $row['acc_password']);
				$this->list[ $row['acc_id'] ] = $user;
			}
			//close connection
			$conn->close();
		}
	}
}
?> 