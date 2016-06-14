<?php
class Account {
	public $acc_name;
	public $acc_id;
	public $acc_login;
	public $acc_password;
	public $is_admin;
	public static $last_error;

	function __construct() {
		$this->acc_id = 0;
		$this->acc_name = '';
		$this->acc_login = '';
		$this->acc_password = '';
		$this->is_admin = 0;
	}
	public function set($id, $name, $login, $password) {
		$this->acc_id = $id;
		$this->acc_name = $name;
		$this->acc_login = $login;
		$this->acc_password = $password;
	}
	public static function find_acc_id ( $login ) {
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';//static property can not use this->
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;

		} else {
			$sql_query = 'SELECT acc_id FROM workers WHERE acc_login=\''.$login.'\' LIMIT 1;';
			$result = $conn->query($sql_query);
			
			if ($row = $result->fetch_assoc()) {
				$conn->close();
				return $row['acc_id'];
			}
		}
		return 0; //something is wrong
	}

	public function clear_fields() { 
		$acc_name = '';
		$acc_id = 0;
		$acc_login = '';
		$acc_password = '';
	}

	public function load_account ( $acc_id ) {
		$this->clear_fields();
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
			if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			$sql_query = 'SELECT * FROM workers WHERE acc_id='.(int)$acc_id.' LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($row = $result->fetch_assoc()) {
			$conn->close();
			$this->acc_id = $row['acc_id'];
			$this->acc_name = $row['acc_name'];
			$this->acc_login = $row['acc_login'];
			$this->acc_password = $row['acc_password'];
			$this->is_admin = $row['is_admin'];
			return $this->acc_id;
			}
		}
		return 0; //something is wrong
	}

	public function delete () {
		if ($this->acc_id <=0 ) {
			self::$last_error = 'no accounts to delete';
			return 0;
		}
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = ''; 
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			$sql_query = 'DELETE FROM workers WHERE acc_id='.(int)$this->acc_id.' LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($result) {
				$conn->close();
				return $this->acc_id;
			} else {
				self::$last_error = $conn->connect_error;
			}
		}
		return 0; //something is wrong
	}

	public function update () {
		if ($this->acc_id <=0 ) {
			self::$last_error = 'no accoutns to update';
			return 0;
		}
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			//first check to see if LOGIN is not used by another user:
			$sql_query = 'SELECT acc_id FROM workers WHERE (acc_login=\''.$this->acc_login.'\')AND(acc_id<>'.(int)$this->acc_id.') LIMIT 1;';
			$result = $conn->query($sql_query);

			if ($row = $result->fetch_assoc()) {
				$conn->close();
				self::$last_error = 'This login is used by another user.';
				return 0;
			}
			$result->close();
			//update
			$sql_query = 'UPDATE workers SET acc_login=\''.$this->acc_login.'\', acc_name=\''.$this->acc_name.'\', acc_password=\''.sha1($this->acc_password).'\' WHERE acc_id='.(int)$this->acc_id.' 
			LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($result) {
				$conn->close();
				return $this->acc_id;
			} else {
				self::$last_error = $conn->connect_error;
			}
		}
		return 0; //something is wrong
	}

	public function add_new_account () {
		include 'database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			//first check to see if LOGIN is not used by another user:
			$sql_query = 'SELECT acc_id FROM workers WHERE (acc_login=\''.$this->acc_login.'\') LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($row = $result->fetch_assoc()) {
				$conn->close();
				self::$last_error = 'This login is already used by another user.';
				return 0;
			}
			$result->close();
			//insert
			$sql_query = 'INSERT INTO workers (acc_login, acc_name, acc_password, is_admin) VALUES (\''.$this->acc_login.'\', \''.$this->acc_name.'\', \''.sha1($this->acc_password).'\',0);';
			$result = $conn->query($sql_query); 
			if ($result) {
				$conn->close();
				return 1;
			} else {
				self::$last_error = $conn->connect_error;
			}
		}
		return 0; //something is wrong
	}
}
?>