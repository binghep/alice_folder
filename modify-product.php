<?php
return;
/*
usage:
go to www.1661hk.com/alice/modify.php
where sku is the conf that you want to delete
then you will see all its children products, then itself (sku)
then the corresponding content will show up in magmi folder's csv. Pls use that csv in magmi to delete all the conf with their simples. 
-----------------------------------------------------------
Note the header line of log.csv should be written by hand: 
For example: 
store,sku,weight
admin,2912901300270408L,3
*/

require_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
/*
$sku=$_GET['sku'];

if (empty($sku)) {
	echo 'param not valid. exiting...';
	exit;
}
*/

$conf_product_skus=array(572383060,572383061,577974213,578645986,587251593,580934374,580934448,577974214);


foreach ($conf_product_skus as $sku){
    $product = Mage::getModel('catalog/product');
    $id=$product->getIdBySku($sku);//will return "false" if not exist

    if ($id===false){
         echo 'no such sku exist. skipping...';
         break;
    }
    //-------------------------------------------------------------------
    $p=$product->load($id);
    // var_dump($product->getCategoryIds());
    echo '<br>';
    $catIds_string="";
    foreach($p->getCategoryIds() as $cat_id){
        $catIds_string.="".$cat_id.",";
    }
    // $catIds_string=substr($catIds_string, 0,-1);
    
    $catIds_string.="634";
    echo $catIds_string;
    write_log('admin,'.$sku.',"'.$catIds_string.'"');
		//---------------write one line for each child--------------------
		// $childProducts = Mage::getModel('catalog/product_type_configurable')
		//                     ->getUsedProducts(null,$p);

		// foreach($childProducts as $child) {
		//     print_r($child->getSku());  // You can use any of the magic get functions on this object to get the value
		//     print_r("<br>");
		//     write_log('admin,'.$child->getSku().',"'.$catIds_string.'"');
		// }

}






function write_log($object){
	error_log($object."\n",3,'magmi_csv/modify-cat-bayern.csv');
	return true;
}

function getChildSkus($sku){

}