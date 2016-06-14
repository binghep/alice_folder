<?php
/**
* product class for magento product and product class for external product need to follow this contract/interface. 
*/
interface product_interface{
	//$name,$image,$sku,$not_null_attributes,$worker_id,($i_recommend),$error,$db_handle
	// public function __construct($sku,$db_handle,$eav_attributes);//url_amazon price_amazon
	/*
	returns array("price_1661"=>"$52.30","url_1661"=>"http://www.kkk",
				  "price_amazon"=>"$50.00", "url_amazon"=>"http://xxxx")
	*/
	//---------required variables in class-------------
	public function get_sku();
	public function get_image();
	public function get_name();
	public function get_error();
	// public function get_not_null_attributes();
	public function get_all_competitor_data();
	public function get_i_recommend();//returns null if no such feature
	//--------might need some database join--------------
	public function get_worker_id();//worker_id of this product
	public function get_worker_name();
	//--------helper function----------------------------
	// public function format_price($price);//keep 2 decimal digits
}