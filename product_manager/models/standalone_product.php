<?php
class standalone_product {
	public $sku;
	public $id;
	public $name;
	public $image;
	public static $last_error;

	function __construct() {
		$this->id = 0;
		$this->name = '';
		$this->image = '';
		$this->sku = '';
	}
	public function set($name, $sku, $image) {
		$this->name = $name;
		$this->image = $image;
		$this->sku = $sku;
	}
	public static function find_id ( $sku ) {
		include 'models/database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';//static property can not use this->
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			$sql_query = 'SELECT id FROM products WHERE sku=\''.$sku.'\' LIMIT 1;';
			$result = $conn->query($sql_query);
			
			if ($row = $result->fetch_assoc()) {
				$conn->close();
				return $row['id'];
			}
		}
		return 0; //something is wrong
	}

	public function clear_fields() { 
		$name = '';
		$id = 0;
		$image = '';
		$sku = '';
	}

	public function load_Product ( $id ) {
		$this->clear_fields();
		include 'models/database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = '';
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
			if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			$sql_query = 'SELECT * FROM products WHERE id='.(int)$id.' LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($row = $result->fetch_assoc()) {
			$conn->close();
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->image = $row['image'];
			$this->sku = $row['sku'];
			return $this->id;
			}
		}
		return 0; //something is wrong
	}

	public function delete () {
		if ($this->id <=0 ) {
			self::$last_error = 'no Products to delete';
			return 0;
		}
		include 'models/database.php'; //do not use include_once inside function (local scope)
		//connect to mySQL
		self::$last_error = ''; 
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			$sql_query = 'DELETE FROM products WHERE id='.(int)$this->id.' LIMIT 1;';
			$result = $conn->query($sql_query);
			if ($result) {
				$conn->close();
				return $this->id;
			} else {
				self::$last_error = $conn->connect_error;
			}
		}
		return 0; //something is wrong
	}
	/*
		update $name and $competitor info of existing product. ($id and $sku not editable/changable)
	*/
	public function update () {
		if ($this->id <=0 ) {
			self::$last_error = 'no products to update';
			return 0;
		}
		require 'models/database.php'; //do not use include_once inside function (local scope)
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
			// $sql_query = 'SELECT id FROM products WHERE (id=\''.$this->id.'\')AND(id<>'.(int)$this->id.') LIMIT 1;';
			
			/*
			// $sql_query = 'SELECT * FROM products WHERE (sku=\''.$this->sku.'\') LIMIT 1;';
			// $result = $conn->query($sql_query);

			// if ($row = $result->fetch_assoc()) {
			// 	$conn->close();
			// 	self::$last_error = 'This new sku is already used by another product.';
			// 	return 0;
			// }
			// $result->close();
			*/
			//update product table:
			$sql_query = 'UPDATE products SET name=\''.$this->name.'\' WHERE id='.(int)$this->id.' 
			LIMIT 1;';//image is not allowed to update
			// var_dump($sql_query);
			$result = $conn->query($sql_query);

			if ($result) {
				$conn->close();
				// return $this->id;
				return true;
			} else {
				self::$last_error = $conn->connect_error;
			}
		}
		return 0; //something is wrong
	}

	public function add_new_Product () {
		require 'models/database.php'; //do not use include_once inside function (local scope)
		// var_dump($db_servername);
		//connect to mySQL
		self::$last_error = '';
		$conn = new mysqli($db_servername, $db_username, $db_password, $db_name, $db_port);
		// Check connection
		if ($conn->connect_error) {
			self::$last_error = $conn->connect_error;
			$conn->close();
			return 0;
		} else {
			//first check to see if sku is not used by another user:
			$sql_query = 'SELECT id FROM products WHERE (sku=\''.$this->sku.'\') LIMIT 1;';
			// var_dump($sql_query);
			$result = $conn->query($sql_query);
			if ($row = $result->fetch_assoc()) {
				// var_dump($row);
				$conn->close();
				self::$last_error = 'This sku is already used by another product.';
				return 0;
			}
			$result->close();
			//insert
			$sql_query = 'INSERT INTO products (image, name, sku) VALUES (\''.$this->image.'\', \''.$this->name.'\', \''.$this->sku.'\');';
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