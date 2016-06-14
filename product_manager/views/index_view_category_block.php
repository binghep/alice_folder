<?php
if (TEMPLATE_HINT){
	echo basename(__FILE__, '.php');
	echo '<div class="category_block" style="border:1px solid red;">';
}else{
	echo '<div class="category_block">';
}


$category_id=$category->getId();
if($category_id) {
    $parent_cat = Mage::getModel('catalog/category')
        ->load($category_id)
        ->getParentCategory();

    $parent_cat_name=is_null($parent_cat)?'':$parent_cat->getName().'/';
    $parent_cat_id=$parent_cat->getId();
    if ($parent_cat->getLevel()==1 ||$parent_cat->getLevel()==0 ){$parent_cat_name='';}
    //-----------------------------------
    $grandpa_cat=Mage::getModel('catalog/category')
        ->load($parent_cat_id)
        ->getParentCategory();

    $grandpa_cat_name=is_null($grandpa_cat)?'':$grandpa_cat->getName().'/';
    // var_dump($grandpa_cat->debug());
    if ($grandpa_cat->getLevel()==1 ||$grandpa_cat->getLevel()==0){$grandpa_cat_name='';}
    //-----------------------------------
    echo "<div style='text-align:center;'>";
    echo "<span style='font-weight:bold;'>".$grandpa_cat_name.$parent_cat_name.$category->getName()."  </span>";

    echo "<span>(".$number_of_products_to_display." products)</span>";
    echo "</div>";


  
    // echo "<span style='float:left'>Total: ".$number_of_products_to_display." visible products in this category.</span>";
    // echo "<br>";
}else{
    echo "There is no category with this id: ".$cat;
	echo " / 没有对应此分类ID的分类: ".$cat;
	exit;
}


echo '</div>';