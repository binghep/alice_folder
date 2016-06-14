<?php


if (TEMPLATE_HINT){
    echo basename(__FILE__, '.php');
    echo '<div class="table_block" style="border:1px solid red;">';
}else{
    echo '<div class="table_block">';
}

echo '<form method="post" action="">';
outputPager($page_total,$p,$cat_id,$type_id,$non_empty_attribute);
// var_dump($cat_id);
// var_dump($filter_options_non_empty_attribute);
// var_dump($non_empty_attribute);//url_jd
outputFilterPopUp($cat_id,$filter_options_non_empty_attribute,$non_empty_attribute);
echo '<script type="text/javascript">
$(document).ready(function(){
    $("#close_pop_up").click(function(){
        document.getElementById("light").style.display="none";
        document.getElementById("fade").style.display="none";
    });
    $("#open_pop_up").click(function(){
        document.getElementById("light").style.display="block";
        document.getElementById("fade").style.display="block";
    })
});

</script>';


echo '<div>';
if ($number_of_products_to_display===0){
    echo "<div id='container'>";
    echo "No matching products";
    echo "</div>";
}else{
    foreach ($productCollection as $product) {
    	echo '<div class="product">';
        if ($_SESSION['user']['login']=='ddd'){
                // var_dump(count($productCollection));
                $thumbnail=$product->getThumbnail();
                // if (!is_null($image_external_url)){
                    // echo '<img width="125px" src="'.$image_base_url.'media/catalog/product'.$image_external_url.'">';
                if (!is_null($thumbnail) && $thumbnail!='no_selection'){
                    echo '<img width="125px" src="'.$image_base_url.'media/catalog/product'.$thumbnail.'">';
                }else{
                    echo 'No image';
                }
                echo '<a href="https://www.1661usa.com/cn/catalog/product/view/id/'.$product->getEntityId().'">View Product Page</a><br>';
        }else{
            require_once 'views/form_block.php';
        }
        echo '</div>';
    }
}
echo '</div>';



// outputPager($page_total,$p,$cat_id,$type_id,$non_empty_attribute);

echo '</form>';



echo '</div>';