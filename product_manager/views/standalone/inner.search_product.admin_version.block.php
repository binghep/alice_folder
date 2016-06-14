
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
?>
<div id="container" class="ltr">
	<h1 id="logo">
		 <div style="font-size:20px;padding: 10px 10px;">1661USA.com
		 <span style="font-size:15px;float: right;margin-top: -7px;">
		 	Hi, <?php echo $_SESSION['user']['name'];?> &nbsp;
		 	<a style="height:19px;" href="<?php echo $product_manager_url;?>trimmed_version/logout.php">Log Out</a>
		 </span>
		 </div>
		<!-- <a href="http://www.wufoo.com/?t=o2j5o6" title="Powered by Wufoo">Wufoo</a> -->
	</h1>
	<div style="
    padding: 3px 10px 0px 10px;
    background: rgba(63, 81, 181, 0.06);
">
  		<a style="height:19px;" href="<?php echo $product_manager_url;?>standalone.index.php">Go Back</a>
  	</div>
	
	<!-- <form id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="https://475461387.wufoo.com/forms/mab0co1dsh5gt/"> -->
	<form  style="border:none;" id="form3" name="form3" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" action="standalone.search_product.admin_version.php" method="get" novalidate="" >
  
		<header id="header" class="info">
			<h2>Search Product by Name / 按产品名搜索 (Admin Version) </h2>
		</header>

		<label>Find product with Name keyword:</label>
		<input style="float:none" type="text" name="search_keywords" value="<?php if (!empty($_GET['search_keywords'])){echo $_GET['search_keywords'];}?>">
		<input type="submit" name="submit" value="Submit">
	</form>
 <style>
body{
	/*font-weight: bold;*/
    font-family: tahoma;
}

/* add your own style on td:hover*/
.table-hover>tbody>tr>td:hover, .table-hover>tbody>tr>td:hover{
        background-color: #EEEEEE!important; // Or any colour you like
    }

/* reset the default bootstrap style on tr:hover*/
.table-hover>tbody>tr:hover, .table-hover>tbody>tr:hover {
    background-color: white;
}

.td_elements{
	position: relative;
}
.stick_to_bottom{
	position: absolute;
	width: 93%;
    bottom: 6px;
    color: #fff;
    /*background-color: #5bc0de;*/
    /*border: 1px solid #46b8da;*/
    /*background-color: #E2E2E2;*/
    background-color: rgba(237,237,237,0.7);
    border: 1px solid #D2D2D2;
    padding: 2px;
    text-align: left;
    border-radius: 4px;
	padding-left: 4px;
}
/*.stick_to_bottom:hover{
	background-color: #CECECE;
	border: 1px solid rgba(97, 97, 97, 0.3);
}*/
table#product_table td{
	height: 235px;
}
table#product_table{
	margin-bottom:0!important;/*overwrite bootstrap forced margin 20px*/
}
.img_div{
	position: absolute;
    top: 45px;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}
.desc_div{
	position: absolute;
    top: 5px; 
    font-size: 13.5px;
}
.container{
	/*margin: 20px 14px 0 14px;*/
    /*padding: 0 0 20px 0;*/
}
  </style>
<style type="text/css">
	.thirty_percent_span{
		width: 30%;
    	float: left;
	    font-size: 15px;
	}
	.seventy_percent_span{
		width: 70%;
    	float: right;
	}
</style>


<?php
class product{
    public $name,$image,$sku,$all_competitor_names,$all_competitor_data;
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

<?php
if (empty($_GET['submit'])){
	return;
}
if (!empty($_GET['submit']) && !empty($_GET['search_keywords'])){
	$search_keywords=$_GET['search_keywords'];
	require_once("database/dbcontroller.php");
	require_once("config.php");

	$db_handle=new DBController();

	$all_skus_to_display=array();
	$curr_page=1;
	if (!empty($_GET['curr_page']) && is_numeric($_GET['curr_page'])){
		$curr_page=(int)$_GET['curr_page'];
	}
	$num_product_per_page=32;
	//--------get total num of results of the search--------------
	$query="select count(*) as total_num_result from products where name LIKE '%{$search_keywords}%'";
	$result=$db_handle->runQuery($query);
	if (is_null($result)){
		echo 'weird';
		exit;
	}
		// var_dump($count_result[0]);
	$total_num_result=$result[0]['total_num_result'];
	//---calculate total page number -----
	$total_page_number=ceil($total_num_result/$num_product_per_page);
	//------------------------------------------------------------
	// var_dump($page_number);
	$start_row_index=$num_product_per_page*($curr_page-1);
	$query="select * from products where name LIKE '%{$search_keywords}%' LIMIT $start_row_index,$num_product_per_page;";

	// var_dump($query);
	$results=$db_handle->runQuery($query);
	// var_dump(count($results));
	if (is_null($results)){
		echo '<div style="margin: 20px 50px;padding: 10px;background: #F3F3F3;border-radius: 10px;border: 1px solid lightgrey;">';
		echo 'There are no products matching this search keyword. Please search another keyword that might be in product name. <br>';
		echo '</div>';
		return;
	}
	foreach ($results as $result) {
		// var_dump($result['sku']);
		// var_dump($result['name']);
		// echo '<br>';
		array_push($all_skus_to_display, $result['sku']);
	}

	// var_dump($all_skus_to_display);
	// return;
	$eligible_products=array();
	foreach ($all_skus_to_display as $sku) {
		$product=new product($sku);
		// echo '<pre>';
		// var_dump($product);
		// echo '</pre>';
		// break;
		$eligible_products[]=$product;
	}
	// var_dump(count($eligible_products));
	// return;
}
?>
<?php
function outputPager($page_total,$curr_page,$search_keywords){
	require "config.php";
	echo '<div id="pager">';
		echo '<div class="container">';
		// echo '<span id="search_sku_span"><input id="search_sku" type="text"><button type="button" id="search_sku_button">Search SKU</button></span>';

		// echo '<span id="page_x_out_of_x_span">Page '.$curr_page.' out of '.$page_total.'</span>';
		echo '<ul class="pagination">';
		echo '<li> <a href="'.$product_manager_url.'standalone.search_product_by_name.php?curr_page=1&search_keywords='.$search_keywords.'&submit=Submit"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a></li>';
	// glyphicon glyphicon-search
		if ($curr_page>1){
			echo '<li> <a href="'.$product_manager_url.'standalone.search_product_by_name.php?curr_page='.($curr_page-1).'&search_keywords='.$search_keywords.'&submit=Submit"><span class="glyphicon glyphicon-step-backward"></span></a></li>';
		}
		
		for($i=$curr_page-2;$i<=$curr_page+2;$i++){
			if ($i<1 || $i>$page_total){
				continue;
			}
			if ($i==$curr_page){
				echo "<li> <a style='background: lightgoldenrodyellow;' href='#' >$i</a></li>";
			}else{
				echo '<li> <a href="'.$product_manager_url.'standalone.search_product_by_name.php?curr_page='.$i.'&search_keywords='.$search_keywords.'&submit=Submit">'.$i.'</a></li>';
			}
		}
		if ($curr_page<$page_total){
			echo '<li> <a href="'.$product_manager_url.'standalone.search_product_by_name.php?curr_page='.($curr_page+1).'&search_keywords='.$search_keywords.'&submit=Submit"><span class="glyphicon glyphicon-step-forward"></span></a></li>';
		}
		echo '<li> <a href="'.$product_manager_url.'standalone.search_product_by_name.php?curr_page='.$page_total.'&search_keywords='.$search_keywords.'&submit=Submit"><span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span></a></li>';


		echo '</ul>';
		echo '</div>';
	echo '</div>';



//--------------------------
// 	echo '<div class="container">                
//   <ul class="pagination">
//     <li><a href="#">«</a></li>
//     <li><a href="#">1</a></li>
//     <li><a href="#">2</a></li>
//     <li><a href="#">3</a></li>
//     <li><a href="#">4</a></li>
//     <li><a href="#">5</a></li>
//     <li><a href="#">6</a></li>
//     <li><a href="#">»</a></li>
//   </ul>
// </div>';
}

echo '<div style="margin: 0 20px 15px 20px;color: grey;"><font face="verdana">Your search gives '.$total_num_result." results. </font></div>";
outputPager($total_page_number,$curr_page,$search_keywords);

/*
param: $price1: e.g. "$20.3", "￥20", "30.2"
param: $price2: e.g. "$3.99", "￥5.3", "8"
Two param Must be in the same currency (both start with $ or ￥，or start with nothing)
If two price are not valid prices in same currency, return false.
*/
function getSumOfPrice($price1,$price2){
	$price1_first_symbol=substr($price1, 0,1);
	$price1_first_3_symbol=substr($price1, 0,3);
	// $price1_first_symbol=substr($price1, 0,2);
	// $price1_first_symbol=substr($price1, 0,3);
	$price2_first_symbol=substr($price2, 0,1);
	$price2_first_3_symbol=substr($price2, 0,3);
	// var_dump($price1_first_3_symbol);
	// var_dump($price2_first_3_symbol);
	// var_dump($price1_first_3_symbol=="￥");//true
	if ($price1_first_symbol=="$" && $price2_first_symbol=="$"){
		if (is_numeric(substr($price1,1)) && is_numeric(substr($price2,1))){
			// var_dump($price1);
			// var_dump($price2);
			$sum=(float)substr($price1, 1)+(float)substr($price2, 1);
			return "$".$sum;
		}else{
			// var_dump("here");
			return false;
		}
	}else if ($price1_first_3_symbol=="￥" && $price2_first_3_symbol=="￥"){
		if (is_numeric(substr($price1,3)) && is_numeric(substr($price2,3))){
			// var_dump($price1);
			// var_dump(substr($price1,3));
			// var_dump($price2);
			$sum=(float)substr($price1, 3)+(float)substr($price2, 3);
			return "￥".$sum;
		}else{
			// var_dump("here");
			return false;
		}
	}else if (is_numeric($price1) && is_numeric($price2)){
		return $price1+$price2;
	}else{
		return false;
	}
}
?>
<div class="container">          
  <table id="product_table" class="table table-bordered table-hover">
   <!--  <thead>
      <tr>
        <th></th>
        <th>Lastname</th>
        <th>Email</th>
      </tr>
    </thead> -->
    <tbody>
	<?php
	// var_dump(count($eligible_products));
	$i=0;
	echo '<tr>';
	foreach ($eligible_products as $object) {
		if ($i!==0 && $i%4==0){
			echo "</tr><tr>";
		}

		echo '<td class="td_elements">';
			echo '<div class="img_div"><img style="margin: auto;max-width: 150px;max-height: 150px;" src="'.$product_manager_url.$object->image.'" ></div>';
			
			echo '<div class="desc_div">';
			echo $i.'.';
			$max_length=60;
			if (strlen($object->name)>$max_length){
				echo substr($object->name,0,$max_length)."...";
			}else{
				echo $object->name;
			}
			echo '</div>';

			$all_competitor_names_uploaded=array_keys($object->all_competitor_data);
			// echo '<button class="stick_to_bottom" type="button" class="btn btn-info">'.implode(",", $all_competitor_names_uploaded).'</button>';
			echo '<div class="stick_to_bottom" style="color: black;">';
			// echo implode(", ", $all_competitor_names_uploaded);
			foreach ($all_competitor_names_uploaded as $key) {
				// var_dump($object->all_competitor_data);
				$product_price=$object->all_competitor_data[$key]['price'];
				$product_url=$object->all_competitor_data[$key]['product_url'];
				$product_shipping=$object->all_competitor_data[$key]['shipping'];
				$subtotal="";
				$price_sentence="";
				// var_dump($product_shipping);
				if ($product_shipping===false){//not recorded
					$price_sentence=$product_price;
				}else if ($product_shipping=="Free Shipping"){//free shipping
					// echo 'here';
					$price_sentence=$product_price."&nbsp;<span style='color:blue;'>(Free Shipping)</span>";
				}else{//shipping is recorded and not Free shipping
					$subtotal=getSumOfPrice($product_price,$product_shipping);
					if ($subtotal===false){//invalid or not unified price format.
						// $subtotal="N/A";
						$price_sentence=$product_price;
					}else{
						$price_sentence=$product_price.' + <span style="color:blue;">'.$product_shipping.'</span> = '.$subtotal;
					}
				}

				echo "<span class='thirty_percent_span'>".$key."</span><span class='seventy_percent_span'><a style='   color:black;font-weight: bold;text-decoration: none;font-size:13px;float: right;font-family: tahoma;' href='".$product_url."'>".$price_sentence.'</a></span>';
				echo '<br>';
			}
			echo '</div>';
		echo '</td>';
		// var_dump($i);
		$i++;
	}
	echo '</tr>';
	?>
    </tbody>
  </table>
</div>

<?php
outputPager($total_page_number,$curr_page,$search_keywords);
?>


