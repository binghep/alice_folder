<?php

$start_date=$_GET['from'];
$end_date=$_GET['to'];

if (empty($start_date) || !isset($start_date)){
    // $error_msg.="start date cannot be empty<br>";
    $start_date=date("m/d/Y");
    $end_date=date("m/d/Y", time() + 86400);
}
if (empty($end_date) || !isset($end_date)){
	$start_date=date("m/d/Y");
    $end_date=date("m/d/Y", time() + 86400);
}

$array_start_date=explode('/', $start_date);
$formated_start_date=$array_start_date[2].'-'.$array_start_date[0].'-'.$array_start_date[1];
$array_end_date=explode('/', $end_date);
$formated_end_date=$array_end_date[2].'-'.$array_end_date[0].'-'.$array_end_date[1];

// if (!empty($_POST['submit'])){
// }
?>
<div id="container" class="ltr">
	<h1 id="logo">
		 <div style="font-size:20px;padding: 10px 10px;">1661USA.com
		 <span style="font-size:15px;float: right;margin-top: -7px;">
		 	Hi, <?php echo $_SESSION['user']['name'];?> &nbsp;
		 	<a style="height:19px;" href="<?php echo $product_manager_url;?>trimmed_version/logout.php">Log Out</a>
		 </span>
		 </div>
	</h1>
	<div style="
    padding: 3px 10px 0px 10px;
    background: rgba(63, 81, 181, 0.06);
">
  		<a style="height:19px;" href="<?php echo $product_manager_url;?>standalone.index.php">Go Back</a>
  	</div>
	
	<form  style="border:none;" id="form3" name="form3" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" method="get" novalidate="" action="standalone.view-history.php">
  
<header id="header" class="info">
	<h2>Products I have uploaded / 我上传过的产品</h2>
</header>


 <style>
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
	background-color: #46b8da;
	border: 1px solid rgba(97, 97, 97, 0.3);
}*/
table#product_table td{
	height: 210px;;
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



<?php require_once "models/retrieved_standalone_product.php";?>
<div>
	<form>
		<label for="from">From</label>
		<input type="text" id="from" style="float: none;" name="from" value="<?php if (!empty($start_date)) echo $start_date; ?>">
		<label for="to">to</label>
		<input type="text" id="to"  style="float: none;" name="to" value="<?php if (!empty($end_date)) echo $end_date;?>">
		<input type="submit" name="submit" value="Submit">
	</form>

	<script>
	$(function() {
	$( "#from" ).datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  numberOfMonths: 1,
	  onClose: function( selectedDate ) {
	    $( "#to" ).datepicker( "option", "minDate", selectedDate );
	  }
	});
	$( "#to" ).datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  numberOfMonths: 1,
	  onClose: function( selectedDate ) {
	    $( "#from" ).datepicker( "option", "maxDate", selectedDate );
	  }
	});
	});
	</script>
	</script>

</div>
<?php

require_once("database/dbcontroller.php");
require_once("config.php");

$db_handle=new DBController();

$all_skus_to_display=array();
// $results=$db_handle->runQuery("select sku from products");
// $results=$db_handle->runQuery("select  sku from products where update_timestamp>=\"$date\" and update_timestamp<=\"$date\"+ INTERVAL 1 DAY ;");
$worker_id=$_SESSION['user']['id'];
/*
select products.sku, standalone_product_eav_attribute_values.worker_id,products.name,products.image,products.update_timestamp 
from products 
inner join standalone_product_eav_attribute_values
on products.sku=standalone_product_eav_attribute_values.sku
where standalone_product_eav_attribute_values.worker_id = 17
	and products.update_timestamp
group by standalone_product_eav_attribute_values.sku;
*/
$query="select products.sku, standalone_product_eav_attribute_values.worker_id,products.name,products.image,products.update_timestamp 
from products 
inner join standalone_product_eav_attribute_values
on products.sku=standalone_product_eav_attribute_values.sku
where standalone_product_eav_attribute_values.worker_id = '".$worker_id."'
	and products.update_timestamp >=\"$formated_start_date\" and products.update_timestamp<=\"$formated_end_date\"
group by standalone_product_eav_attribute_values.sku;";

// var_dump($query);
$results=$db_handle->runQuery($query);

if (empty($results) || $results==false || $results==-1){
    echo '<div style="margin: 20px 50px;padding: 10px;background: #F3F3F3;border-radius: 10px;border: 1px solid lightgrey;">No Records Matching this date<br></div>';
    exit;
}
// var_dump($results);

foreach ($results as $key => $value) {
    array_push($all_skus_to_display,$value['sku']);
}

// var_dump($all_skus_to_display);
$eligible_products=array();
foreach ($all_skus_to_display as $sku) {
	$product=new retrieved_standalone_product($sku);
	// var_dump($product);
	
	$eligible_products[]=$product;
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
	echo '<div style="margin: 0 20px 15px 20px;color: grey;"><font face="verdana">I have uploaded '.count($eligible_products)." products from ".$start_date." to ".$end_date.":</font></div>";
	$i=0;
	echo '<tr>';
	foreach ($eligible_products as $object) {
		if ($i!==0 && $i%4==0){
			echo "</tr><tr>";
		}
	/*	
		echo '<pre>';
		var_dump($object);
		echo '</pre>';
	*/

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
			echo '<div class="stick_to_bottom">';
			echo implode(", ", $all_competitor_names_uploaded);
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








