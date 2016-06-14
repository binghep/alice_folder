<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();


if (!isset($_SESSION['user'])){
	header('Location: trimmed_version/login.php');
}
?>

<head>
	<title>My Notes</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/app_icon.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://code.jquery.com/jquery-1.12.1.min.js" type="text/javascript"></script>
	
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

	<link href="views/Issue Tracking Form_files/index.0264.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/logo_bar2.css">
	<link rel="stylesheet" type="text/css" href="css/main_form4.css">
	<link rel="stylesheet" type="text/css" href="css/my_notes.css">
</head>
<body>

<?php

// echo '<span id="session_user_id" style="display:none">'.$_SESSION['user']['id'].'</span>';


require 'config.php';
require_once 'views/index_view_header_block.php';
require_once '../../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


$product = Mage::getModel('catalog/product');


$curr_page=1;
if (!empty($_GET['curr_page'])) {
	$curr_page=$_GET['curr_page'];
}


$productCollection = Mage::getResourceModel('reports/product_collection')
					    // ->addAttributeToSelect('*')
					    ->addAttributeToSelect('sku')
					    ->addAttributeToSelect('name')
					     ->addAttributeToSelect('small_image')
					    ->addAttributeToSelect('price')
					    ->addAttributeToSelect('special_price')
					    ->addAttributeToSelect('research_note')

					    ;


//-------------------------------------------------------------
	require_once 'setting_manager.php';
	$setting=new setting_manager("database/setting.txt");

	$allowed_competitors=$setting->get_allowed_competitor_array();
	if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
		echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Exiting';
		exit;
	}
	// var_dump($allowed_competitors);
	
	//-------------------------------------------------------------
	foreach ($allowed_competitors as $competitor) {
		$productCollection->addAttributeToSelect('url_'.$competitor)
						  ->addAttributeToSelect('price_'.$competitor);
		
	}
	//-------------------------------------------------------------

$productCollection->addAttributeToFilter('status',1); //only enabled product
$productCollection->addAttributeToFilter('research_note',array('notnull'=>true));


$product_per_page=50;//overwrite default one in config.php used in form_block_container.php
$number_of_products_to_display=$productCollection->getSize();
$page_total=ceil($number_of_products_to_display/$product_per_page);


$productCollection->setPageSize($product_per_page)
				  ->setCurPage($curr_page);




?>
<div class="gridHeading">
	<div>
		<h4 class="headingLeft">
		  	My Product Notes
		</h4>
		<!-- div class="headingRight buttongroup">
			  <span class="featuredText">About Featured</span>
			  <span id="featuredDealsInfo" data-tooltip-position="bottom-right" class="icon icon-info2 tooltip tooltipstered" data-tooltip-text="Offers in this section include some of our best recent deals and may be sponsored by the merchant. <strong><a href='/corp/featured-deals.html'>Learn More</a></span><strong>"></span>
		</div> -->
		<?php outputPager($page_total,$curr_page);?>
	</div>
</div>

<div id="gridContainer">
<?php
foreach ($productCollection as $p) {
	// var_dump($p->getSmallImage());
?>
<div class="fpGridBox grid  frontpage firedeal">
    <div class="fpItem ">
        <div class="itemImageAndName">
            <a class="itemImageLink" href="<?php echo $magento_base_url.'catalog/product/view/id/'.$p->getId();?>" title="<?php echo $p->getName();?>" target="_new">
                <!-- <div class="imageContainer">
                     <img src="<?php echo $image_base_url.'media/catalog/product'.$p->getSmallImage();?>" title="<?php echo $p->getName();?>">
                </div> -->
            	<div class="imageContainer" style="background: url(<?php echo $image_base_url.'media/catalog/product'.$p->getSmallImage();?>)"></div>
            </a>
          <!-- <a class="itemStore" href="/coupons/amazon/" >Amazon&nbsp;</a> -->
            <a  class="itemTitle" href="<?php echo $product_manager_url.'search.php?sku='.$p->getSku();?>"><?php echo $p->getName();?></a>
        </div>
        <a class="itemInfoLine track-fpDealLink" >
            <!--<div class="avatarBox">
                <img src="<?php echo $p->getSmallImage();?>" alt="" class="itemUserAvatar tooltip tooltipstered" >
            </div>
            -->
            <div class="priceLine" title=<?php echo $p->getName();?>>
	            <div class="itemPrice  ">
	            	$<?php echo $p->getFinalPrice();?>
	            </div>
	        </div>
	            <!-- <span class="fire icon icon-fire"></span> -->
	            <!-- <div class="priceInfo">+ Free Shipping</div> -->
     	 </a>
     	 <div class="competitors">
     	 	<table style="width: 100%;">
	      	<?php
	      	   	$skip_values=array("RMBNo Match","RMB","USD","USDNo Match",null);
      	    	foreach ($allowed_competitors as $competitor) {
      	    		$competitor_price=$p->getData("price_".$competitor);
      	    		// if (in_array($competitor_price, $skip_values)){conitnue;}
      	    		if (array_search($competitor_price, $skip_values)!==false){
      	    			// echo 'here';
      	    			continue;
      	    		}
      	    		$competitor_price=str_replace("RMB", "", $competitor_price);
      	    		$competitor_price=str_replace("USD", "$", $competitor_price);
      	    		echo "<tr><td class='align_left'>",$competitor,"</td><td class='align_right'>",$competitor_price,"</td></tr>";
      	    	}
	      	?>
	      	</table>
	     </div>
	  	<div class="research_note">
	  	<?php
	  		$full_note=$p->getData('research_note');
	  		// var_dump($note);
	  	    $full_note_html=str_replace("\n", "<br>", $full_note);
	  	    $note=$full_note_html;
	  	    if (strlen($full_note_html)>35){
		  	    // echo "<div style='display:none' class='full_note_div'>",$note,"</div>";
	  	    	// $note=substr($note, 0,35)."...";
	  	    	$note=mb_substr($full_note_html,0,30,"utf-8")."...";
	  	    	echo '<a class="info_icon" href="#" data-toggle="tooltip" data-placement="right" title="" data-original-title="'.$full_note.'"><img height="20px" src="http://www.1661hk.com/alice/product_manager/icons/info_icon.png"></a>';
	  	    }
		  	echo "<div class='summary_note'>",$note,"</div>";
	  	?>
	  	</div>
    </div>
  </div>
<?php
}

?>
</div>




<style type="text/css">
	.gridHeading{
		/*height: 45;
	    padding: 10px;*/
	    height: 49px;
    	padding: 14px;
	    vertical-align: middle;
	}
	.headingLeft{
		display:inline !important;
	}
</style>



<script type="text/javascript">
/*$(document).ready(function() {
		// var element=this;
		// console.log('hree');
		$("div.research_note").mouseenter(function(){
			// $(this).css('background-color', 'red');//remove any color
			var display_status=$(this).find('.full_note_div').css("display");
			if (display_status==="none"){
				// alert('here');
				$(this).find('.full_note_div').css("display","block");
				$(this).find('.summary_note').css("display","none");
			}
		});
		$("div.research_note").mouseleave(function(){
			// $(this).css('background-color', 'red');//remove any color
			var display_status=$(this).find('.full_note_div').css("display");
			if (display_status==="block"){
				$(this).find('.full_note_div').css("display","none");
				$(this).find('.summary_note').css("display","block");
			}
		});
	});
*/
</script>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>



<?php
function outputPager($page_total,$curr_page){
	require "config.php";
	// echo '<div id="pager">';
	// echo '<div class="container">';

		// echo '<span id="search_sku_span"><input id="search_sku" type="text"><button type="button" id="search_sku_button">Search SKU</button></span>';
	   
	    // echo '<span id="search_sku_span">
				 //    <input id="search_sku" type="text" class="TextInput">
				 //    <button type="button" class="btn btn-default" id="search_sku_button">
			  //         <span class="glyphicon glyphicon-search"></span> Search SKU
			  //       </button>
     //    	   </span>';
       	//-----------------open pop up----------------------
	    // echo '<ul class="pagination">';
	    // echo '<li><a href="javascript:void(0)" id="open_pop_up"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span></a></li>';
	    // echo '</ul>';
       	//-----------------open pop up----------------------
		// echo '<span id="page_x_out_of_x_span">Page '.$curr_page.' out of '.$page_total.'</span>';
		echo '<ul class="pagination">';
		echo '<li> <a href="'.$product_manager_url.'my_notes.php?curr_page=1"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a></li>';
	// glyphicon glyphicon-search
		if ($curr_page>1){
			echo '<li> <a href="'.$product_manager_url.'my_notes.php?'.($curr_page-1).'"><span class="glyphicon glyphicon-step-backward"></span></a></li>';
		}
		
		for($i=$curr_page-2;$i<=$curr_page+2;$i++){
			if ($i<1 || $i>$page_total){
				continue;
			}
			if ($i==$curr_page){
				echo "<li> <a style='background: lightgoldenrodyellow;' href='#' >$i</a></li>";
			}else{
				echo '<li> <a href="'.$product_manager_url.'my_notes.php?curr_page='.$i.'">'.$i.'</a></li>';
			}
		}
		if ($curr_page<$page_total){
			echo '<li> <a href="'.$product_manager_url.'my_notes.php?&curr_page='.($curr_page+1).'"><span class="glyphicon glyphicon-step-forward"></span></a></li>';
		}
		echo '<li> <a href="'.$product_manager_url.'my_notes.php?curr_page='.$page_total.'"><span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span></a></li>';


		echo '</ul>';
	// echo '</div>';
	// echo '</div>';
}
?>