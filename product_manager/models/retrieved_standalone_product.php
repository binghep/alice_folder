
<?php
class retrieved_standalone_product{
    public $id,$name,$image,$sku,$all_competitor_names,$all_competitor_data;
    public $db_handle;
    function __construct($sku) {
		require_once("database/dbcontroller.php");
		$this->db_handle=new DBController();
		$this->sku=$sku;
		// var_dump($this->db_handle);
		
		$status=$this->get_basic_product_info();
		if ($status===false){
			return false;//product with this sku is not found.
		}
		$this->all_competitor_data=$this->get_product_eav_attributes();

    }
    // helper function:
    function get_product_sku_by_id($id){
		require_once("database/dbcontroller.php");
		$this->db_handle=new DBController();
    	$query="select * from products where id='{$id}'";
    	$result=$this->db_handle->runQuery($query);
    	if (is_null($result)){
    		return false;
    	}
    	return $result[0]['sku'];
    }

    function get_basic_product_info(){
    	
    	// $sql_query = 'SELECT * FROM products WHERE id='.(int)$id.' LIMIT 1;';
    	// var_dump($this->sku);
    	$sql_query = 'SELECT * FROM products WHERE sku="'.$this->sku.'" LIMIT 1;';
		$results=$this->db_handle->runQuery($sql_query);
		if (is_null($results)){
			return false;//product with this sku is not found.
		}
		// $this->id = $row['id'];
		$row=$results[0];
		$this->id=$row['id'];
		$this->name = $row['name'];
		$this->image = $row['image'];
		$this->sku = $row['sku'];
		return true;
    }
    /*
    returns all competitors names in an array, like ['amazon','jd']
   
    */
    function _get_all_competitor_names(){
		//show every attribute input text field in database table: 
		$results=$this->db_handle->runQuery('select * from standalone_product_eav_attributes');

		$competitors=array();
		foreach ($results as $row_id => $row) {
			$attribute_name=$row['attribute_name'];
			if (strpos($attribute_name, 'url_')===0){
				$competitor_name=substr($attribute_name,strlen('url_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}
			}else if (strpos($attribute_name,'price_')===0){
				$competitor_name=substr($attribute_name,strlen('price_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}
			}else if (strpos($attribute_name,'shipping_')===0){
				$competitor_name=substr($attribute_name,strlen('shipping_'));
				if (!in_array($competitor_name, $competitors,true)){
					array_push($competitors,$competitor_name);
				}	
			}else{
				echo 'weird. ';
			}
		}
		return $competitors;
	}
	/*
	return all eav attributes of one product specified by sku:

	array("amazon"=>array("price"=>"$20","product_url"=>"http://www.amazon.com/water"),
			  "jd"=>array("price"=>"$20","product_url"=>"http://www.amazon.com/water")
	    );
	*/
	function get_product_eav_attributes(){
		$include_empty_price_and_url=false;
		$all_competitor_names=$this->_get_all_competitor_names();
		$this_product_eav_attributes=array();
		foreach ($all_competitor_names as $competitor_name) {
			$competitor=array();
			$competitor["price"]=$this->get_product_attribute_by_attribute_name("price_".$competitor_name);//string or false
			$competitor["product_url"]=$this->get_product_attribute_by_attribute_name("url_".$competitor_name);//string or false //can be empty. should not be false
			$competitor['shipping']=$this->get_product_attribute_by_attribute_name("shipping_".$competitor_name);//string or false
			if (!$include_empty_price_and_url && empty($competitor["price"]) && empty($competitor["product_url"]) ){
			}else if(strtolower($competitor['product_url'])=="not available" || strtolower($competitor['product_url'])=="no such item"){
            }else{
				$this_product_eav_attributes[$competitor_name]=$competitor;
			}
		}
		return $this_product_eav_attributes;
	}
	/*
	get one eav attribute value for one product (specified with sku):
	*/
	function get_product_attribute_by_attribute_id($attribute_id){
		$query="select * from standalone_product_eav_attribute_values where attribute_id='".$attribute_id."' and sku='".$this->sku."'";
		// var_dump($query);
		$result=$this->db_handle->runQuery($query);
		if (is_null($result)){
			return false;
		}else{
			return $result[0]["value"];
		}
	}

	/*
	get one eav attribute value for one product (specified with sku):
	*/
	function get_product_attribute_by_attribute_name($attribute_name){
		$query="select * from standalone_product_eav_attributes where attribute_name='".$attribute_name."'";
		// var_dump($query);
		$result=$this->db_handle->runQuery($query);
		if (is_null($result)){
			return false;
		}else{
			$attribute_value=$this->get_product_attribute_by_attribute_id($result[0]["id"]);
			return $attribute_value;
		}
	}

}

?>