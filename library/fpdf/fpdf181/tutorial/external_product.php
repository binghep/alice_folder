<?php
require_once "product_interface.php";

class external_product implements product_interface{
    public $name,$image,$sku,$not_null_attributes,$all_competitor_data;
    public $all_competitor_names;
    public $worker_id,$worker_name;
    public $error;
    public $db_handle;
    public function get_sku(){
        return $this->sku;
    }
    public function get_image(){
        return $this->image;
    }   
    public function get_name(){
        return $this->name;
    }    
    public function get_error(){
        return $this->error;
    }
    public function get_i_recommend(){
        return null;
    }
    //------------------------------------------

    function __construct($sku,$db_handle) {
        // require_once("../../../../product_manager/database/dbcontroller.php");
        // $this->db_handle=new DBController();
        $this->db_handle=$db_handle;
        $this->sku=$sku;
        
        $status=$this->get_basic_product_info();
        if ($status===false){
            $this->error="cannot get basic product info, product with this sku is not found.";
        }
        // $this->all_competitor_data=$this->get_not_null_attributes();
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
        $this->name = $row['name'];
        $this->image = $row['image'];
        $this->sku = $row['sku'];
        $this->worker_id=$this->get_worker_id();//false on error
        $this->worker_name=$this->get_worker_name();
        return true;
    }
    /*
    Return worker_id of this product.
    Return false if no info about this sku is found
    */
    public function get_worker_id(){
        if (is_null($this->worker_id)){
            $query="select * from standalone_product_eav_attribute_values where sku='".$this->sku."' LIMIT 1;";
            // var_dump($query);
            $result=$this->db_handle->runQuery($query);
            if (is_null($result)){
                return false;
            }
            $this->worker_id=$result[0]['worker_id'];
            return $this->worker_id;
        }else{
            return $this->worker_id;
        }
    }
    /* 
    Return acc_name of the worker with worker_id
    Return false if no worker is found. 
    */
    public function get_worker_name(){
        $query="select acc_name from workers where acc_id='".$this->get_worker_id()."'";
        $result=$this->db_handle->runQuery($query);
        if (is_null($result)){
            return false;
        }
        $worker_name=$result[0]['acc_name'];
        return $worker_name;    
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

    array("amazon"=>array("price"=>"$20","url"=>"http://www.amazon.com/water","shipping"=>"$3.99"),
              "jd"=>array("price"=>"$20","url"=>"http://www.amazon.com/water","shipping"=>"Free Shipping")
        );
    */
    public function get_all_competitor_data(){
        $include_empty_price_and_url=false;
        $all_competitor_names=$this->_get_all_competitor_names();
        $all_competitor_data=array();
        foreach ($all_competitor_names as $competitor_name) {
            $competitor=array();
            $competitor["price"]=$this->get_product_attribute_by_attribute_name("price_".$competitor_name);//string or false
            $competitor["url"]=$this->get_product_attribute_by_attribute_name("url_".$competitor_name);//string or false //can be empty. should not be false
            $competitor['shipping']=$this->get_product_attribute_by_attribute_name("shipping_".$competitor_name);//string or false
            if (!$include_empty_price_and_url && empty($competitor["price"]) && empty($competitor["url"]) ){
            }else if(strtolower($competitor['url'])=="not available" || strtolower($competitor['url'])=="no such item"){
            }else{
                $all_competitor_data[$competitor_name]=$competitor;
            }
        }
        return $all_competitor_data;
    }
    /*
    get one eav attribute value for one product (specified with sku):
    */
    function get_product_attribute_by_attribute_id($attribute_id){
        $query="select * from standalone_product_eav_attribute_values where attribute_id='".$attribute_id."' and sku='".$this->get_sku()."'";
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