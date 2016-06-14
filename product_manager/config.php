<?php

$ww1_image_base_url="http://ww1.1661hk.com/";
$live_image_base_url="http://www.1661hk.com/";

$image_base_url=$live_image_base_url;
//-------------------------------------------
$ww1_base_url="http://ww1.1661hk.com/";
$live_base_url="http://www.1661hk.com/";

$base_url=$live_base_url;
//-------------------------------------------
$ww1_server_magento_root="/usr/share/nginx/www/1661hk/";
$live_server_magento_root="/usr/share/nginx/www/1661hk.com/";

$magento_root=$live_server_magento_root;
//-------------------------------------------
$product_manager_url=$base_url."alice/product_manager/";
// $product_manager_url=$base_url."alice/product_manager_test/";
$post_manager_url=$base_url."alice/product_manager/";
// $post_manager_url=$base_url."alice/product_manager_test/";

$debug=false;//in login.php, if debug is true, automatically set session. so go to users.php directly.
$product_per_page=1;
$product_per_page_for_test_account=300;//for ddd (login) and ddd(pw) account. display more products per page
$is_admin=false;




define("TEMPLATE_HINT",false);
$magento_base_url="https://www.1661usa.com/en/";