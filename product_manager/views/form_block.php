<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
$curr_page=$p;
$next_page_link=$product_manager_url.'index.php?cat_id='.$cat_id.'&p='.($curr_page+1).'&non_empty_attribute='.$non_empty_attribute;

if (TEMPLATE_HINT){
	echo basename(__FILE__, '.php');
	echo '<div id="container" class="ltr" style="border:1px solid red;">';
}else{
	echo '<div id="container" class="ltr">';
}
?>
<style type="text/css">
	.positioner{
	    position: relative;
    	left: 654px;
	}
	#floating_nav{
		position: fixed;
    	margin-left: 1px;

		min-width: 90px;
		padding:15px; 
		box-shadow: 4px 4px 4px rgba(136, 136, 136, 0.06);
		/*border: rgba(95, 95, 126, 0.21) 1px solid;    */
		border: rgba(95, 95, 126, 0.03) 1px solid;
		/*background: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 188, 212, 0.18)), to(rgba(138, 158, 77, 0.58))) fixed !important;*/
		background: white !important;
		border-radius:5px
	}
</style>
	<?php
	//--------------floating window-----------------------
	echo '<div class="positioner">';
	echo '<div id="floating_nav">';
	echo '<ul>';
	echo '<li><a href="#top" style="color:black;">TOP</a></li>';
	foreach ($allowed_competitors as $competitor) {
		echo '<li>';
		echo '<a style="color:black" href="#'.$competitor.'" class="no_match">[x]</a>';
		echo '<a style="color:black;display:none" href="#'.$competitor.'" class="confirm_no_match">[&#10004;]</a>';
		echo '&nbsp;';
		echo '<a href="#'.$competitor.'" class="jump_to_competitor">'.$competitor.'</a></li>';
	}
	echo '<li><a href="#i_recommend" style="color:black;
	font-weight: 400;
	font-style: italic;
	font-variant: small-caps;">I recommend</a></li>';
	echo '</ul>';

	echo '<li><a href="'.$next_page_link.'" style="color:black;">Next</a></li>';
		echo '</div>';
	echo '</div>';
	//---------------------------------------------
	?>
	<form id="form2" name="form2" class="wufoo topLabel page1" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="">
  
<!-- <header id="header" class="info">
	<h2>Price Research Form / 价格调查表</h2>
	<div></div>
	
</header>
 -->


<ul id="main_form_ul" style="margin: 0 10px;">
	<li class="main_form_li">
		<!-- <label class="desc" id="top">Product Name</label> -->
		<div>
			<span id="name"><?php echo $product->getName();?></span>
			<span id="product_id" style="display:none"><?php echo $product->getId();?></span>
		</div>

		<a href="#" class="has_underline" id="search_on_amazon">Amazon.cn</a>
		&nbsp;
		<a href="#" class="has_underline" id="search_on_jd">JD</a>
		&nbsp;
		<a href="#" class="has_underline" id="search_on_taobao">Taobao</a>
		&nbsp;
		<a href="#" class="has_underline" id="search_on_tmall">Tmall</a>
		&nbsp;
		<?php 
			// $price=(!is_null($product->getSpecialPrice())&&!empty($product->getSpecialPrice()))?$product->getSpecialPrice():$product->getPrice();
			$price=$product->getFinalPrice();
			$price=number_format($price,2);
		?>
		<a href="#" class="has_underline" id="search_on_1661usa">1661USA($<?php echo $price;?>)</a>
	</li>

	<li class="main_form_li" style="color: grey;
    background-color: rgba(202, 202, 202, 0.35);">
		<!--<label class="desc" style="color:grey;">Product Sku</label>-->
		<div>
			<span id="sku" style="color:grey;"><?php echo $product->getSku();?></span>
		</div>
	</li>
	
	<li class="main_form_li">
		<!--<label class="desc">Product Image</label>-->
		<div>
			<?php
	// var_dump($product);

			$image_external_url=$product->getData('image_external_url');
            $thumbnail=$product->getThumbnail();
            // if (!is_null($image_external_url)){
	            // echo '<img width="125px" src="'.$image_base_url.'media/catalog/product'.$image_external_url.'">';
            if (!is_null($thumbnail) && $thumbnail!='no_selection'){
	            echo '<img width="125px" src="'.$image_base_url.'media/catalog/product'.$thumbnail.'">';
            }else{
            	echo 'No image';
            }
            // var_dump($product->getData('price_wholefood'));
            ?>
		</div>
	</li>
</ul>



<?php
	// require_once 'setting_manager.php';
	//-------------------------------------------------------------
	$setting=new setting_manager("database/setting.txt");
	
	$allowed_competitors=$setting->get_allowed_competitor_array();
	if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
		echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Exiting...';
		exit;
	}

	// $result=$setting->addCompetitor("100pm");

	// var_dump($result);//true
	$attributes=array();
	foreach ($allowed_competitors as $competitor) {
		// var_dump($competitor);
		//------------put in price attributes------------------
		// $exceptions=array("amazon","jd","taobao","tmall");//1661usa
		$exceptions=array();
		if (in_array($competitor, $exceptions)){
			$price_attribute_code=$competitor;
			$attributes[$competitor]['price']=$product->getData($price_attribute_code);
			// var_dump($price_attribute_code);
		}else{
			$price_attribute_code="price_".$competitor;
			// var_dump($price_attribute_code);
			$attributes[$competitor]['price']=$product->getData($price_attribute_code);
		}
		//------------put in url attributes------------------
		$url_attribute_code="url_".$competitor;
		$attributes[$competitor]['url']=$product->getData($url_attribute_code);
	}
// echo '<pre>';
// var_dump($attributes);
// echo '</pre>';
	// return;
	//-------------------------------------------------------------
/*	$attributes=array("url_amazon"=>$product->getData('url_amazon'),
						"url_jd"=>$product->getData('url_jd'),
						"url_taobao"=>$product->getData('url_taobao'),
						"url_tmall"=>$product->getData('url_tmall'),
						"price_amazon"=>$product->getData('amazon'),
						"price_jd"=>$product->getData('jd'),
						"price_taobao"=>$product->getData('taobao'),
						"price_tmall"=>$product->getData('tmall')
						);
*/


?>	
<ul id="main_form_ul_part_b">
		

<!-- Amazon part -->
<?php

function printCompetitorLinkAndPriceLi($all_allowed_info,$competitor,$is_admin){
	$this_competitor_info=$all_allowed_info[$competitor];
	$price=$this_competitor_info['price'];
	$url=$this_competitor_info['url'];

	if ($is_admin || empty($url)){
		echo '<li id="fo2li119" class="notranslate main_form_li">
		<label class="desc" id="title119" for="Field119" style="margin_bottom:15px">
			'.$competitor.' <span style="color:rgba(128, 128, 128, 0.45);"> Url / 产品链接 </span>
		</label>
		<div style="width: 100%;">
			<input id="'.'url_'.$competitor.'" name="'.'url_'.$competitor.'" type="url" class="field text large TextInput TextInput_large" value="'.$url.'" maxlength="255" tabindex="1"   style="cursor: auto;">
		</div>
	</li>';
	}
	// now print price:
	if ($is_admin || empty($price) || $price=="RMB" || $price=="USD"){		
		echo '<li id="fo2li102" class="notranslate  main_form_li" >
	<label class="desc" id="title109">
		'.$competitor.' <span style="color:rgba(128, 128, 128, 0.45);"> Price / 价格 </span>
	</label>
	<table>
		<tr>
			
			<td>
				<div>
					<select id="currency_'.$competitor.'" name="currency_'.$competitor.'" class="field select small TextInput TextInput_large"  tabindex="3" style="color:#747474;">
							<option value="USD" ';
							if (substr($price,0,3)=="USD") {
									echo 'selected'; 
									$price=substr($price,3);
								}
							echo '>USD</option>
							<option value="RMB" ';
							if (substr($price,0,3)=="RMB" || empty($price)) {//empty($price) is for 1661 version to display RMB
									echo 'selected'; 
									$price=substr($price,3);
								}
							echo '>RMB</option>
					</select>
				</div>
			</td>
			<td>
				<div>
					<input id="price_'.$competitor.'" name="price_'.$competitor.'" type="text" class="field text small TextInput TextInput_large" value="'.$price.'" maxlength="255" tabindex="4"  >
				</div>
			</td>
		</tr>
	</table>
</li>';
	}
}

foreach ($allowed_competitors as $competitor) {
	// printCompetitorLinkAndPriceLi($attributes,'amazon',$is_admin);
	echo '<div id="'.$competitor.'" class="competitor_block">';
	printCompetitorLinkAndPriceLi($attributes,$competitor,$is_admin);//the same attribute array contains every  competitor as key
	echo '</div>';
}

$i_recommend=$product->getData('i_recommend');
$checked_or_not=($i_recommend==="1")?"checked":"";
// var_dump($checked_or_not);
echo '<div style="margin:30px;"> 
		  <input type="checkbox" id="i_recommend" '.$checked_or_not.' style="display: inline !important;">
		  <label for="i_recommend" class="i_recommend_label">I RECOMMEND this product because it is good brand and has significant cheaper price on 1661USA.com. </label>
	  </div>';

$note=$product->getData('research_note');
echo '<div class="note">
		<textarea id="research_note" rows="5" cols="67" placeholder="Sales Volume on Other Platforms/此产品在其他网站的销量">'.$note.'</textarea>
	  </div>';

?>
<style type="text/css">
  textarea {
	  resize: none;
	  /*margin:30px;*/

	}

  .note{
  	margin:30px;
  }
</style>
	<li class="buttons ">
		<div>
			<!-- <input id="saveForm" name="saveForm" class="btTxt submit" type="submit" value="Submit" onmousedown="doSubmitEvents();"> -->
			<!-- <input id="next" name="next" class="btTxt submit" type="submit" value="next_page"> -->
			<?php 
				// $product_manager_url=$base_url.'alice/product_manager/';//overwirte the one in config.php
				?>
			<span> <a style="background: #DEDEDE;text-decoration: none;float: right;color: black;padding: 10px 5px;border: 1px solid black;" 
    					href="<?php echo $next_page_link;?>">Next Product / 下一个产品</a></span>
		</div>
	</li>
</ul>
</form>

 
</div><!--container-->

<script type="text/javascript" src="js/update_fields1.js"></script>
<script type="text/javascript" src="js/convenient_search.js"></script>
<script type="text/javascript" src="js/fill_in_no_match.js"></script>
<script type="text/javascript" src="js/highlight___competitor.js"></script>
<script type="text/javascript" src="js/refresh_session.js"></script>


