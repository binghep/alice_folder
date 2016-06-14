<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
?>
<div class="dashboard">
<span id="session_user_id" style="display:none"><?php echo $_SESSION['user']['id'];?></span>

<div class="container">
    <div class="index_tap total">
        <ul class="outer_ul">
            <li class="index_tap_item total_fans extra">
                <a href="http://www.ipzmall.com/alice/product_manager/index.php?cat_id=662">
                    <span class="tap_inner">
                        <i class="icon_index_tap"></i>
                        <span class="column_title">iPZmall</span>
                        <!-- <strong class="title">总用户数</strong> -->
                    </span>
                </a>
                <ul class="inner_ul">
                    <li>
                        <a href="http://www.ipzmall.com/alice/product_manager/reporthub/admin/report.php">PDF Report for Sam</a>
                        <a href="http://www.ipzmall.com/alice/product_manager/reporthub/worker/report.php?worker_id=<?php echo $_SESSION['user']['id'];?>">PDF Report for Me</a>
                    </li>
                    <li>
                    	<a href="http://www.ipzmall.com/alice/product_manager/reporthub/admin/report_recommended_products_by_cat.php">View Recommended Products By Category</a>
                    </li>
                    <li>
                    	<a href="http://www.ipzmall.com/alice/product_manager/my_notes.php">My Notes</a>
                    </li>
                    <li>
                        <a href="http://www.ipzmall.com/alice/product_manager/create_magento_attribute.php">Add Competitor</a>
                    </li>
                </ul>
            </li>
            <li class="index_tap_item total_fans extra">
                <a href="http://www.1661hk.com/alice/product_manager/index.php?cat_id=415">
                    <span class="tap_inner">
                        <i class="icon_index_tap"></i>
                        <span class="column_title">1661USA</span>
                        <!-- <strong class="title">总用户数</strong> -->
                    </span>
                </a>
                <ul class="inner_ul">
                    <li>
                        <a href="http://www.1661hk.com/alice/product_manager/reporthub/admin/report.php">PDF Report for Sam</a>
                        <a href="http://www.1661hk.com/alice/product_manager/reporthub/worker/report.php?worker_id=<?php echo $_SESSION['user']['id'];?>">PDF Report for Me</a>

                    </li>
                    <li>
                    	<a href="http://www.1661hk.com/alice/product_manager/reporthub/admin/report_recommended_products_by_cat.php">View Recommended Products By Category</a>
                    </li>
                    <li>
                    	<a href="http://www.1661hk.com/alice/product_manager/my_notes.php">My Notes</a>
                    </li>
                    <li>
                        <a href="create_magento_attribute.php">Add Competitor</a>
                    </li>
                </ul>
            </li>
            <li class="index_tap_item total_fans extra">
                <a href="http://www.1661hk.com/alice/product_manager/standalone.index.php">
                    <span class="tap_inner">
                        <i class="icon_index_tap"></i>
                        <span class="column_title">External Products</span>
                        <!-- <strong class="title">总用户数</strong> -->
                    </span>
                </a>
                <ul class="inner_ul">
                    <li>
                        <a href="http://www.1661hk.com/alice/product_manager/reporthub/admin/external_product_report.php">PDF Report for Sam</a>
                        <a href="http://www.1661hk.com/alice/product_manager/reporthub/worker/external_product_report.php?worker_id=<?php echo $_SESSION['user']['id'];?>">PDF Report for Me</a>

                        <?php if ($_SESSION['user']['login']==="samip"){
                        	//This one is commented out because it is for Sam only. Can't reveal data.
                           	echo  '<a href="http://www.1661hk.com/alice/product_manager/standalone.search_product.admin_version.php">HTML Report (Search Brand etc)</a>';
                        }
                        ?>
                        <a href="http://www.1661hk.com/alice/product_manager/standalone.view-history.php">HTML Report (No Price Shown) for Me</a>
                    </li>
                    <li>
                        <a href="http://www.1661hk.com/alice/product_manager/standalone.search_product.employee_version.php">Edit Previous Product</a>
                    </li>
                    <li>
                        <a href="http://www.1661hk.com/alice/product_manager/views/standalone/inner.manage_competitors.block.php">
                            Add Competitor
                        </a>
                    </li>
                </ul>
            </li>
        </ul> 
    </div>
</div>


<!-- </form>; -->



</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".save-button").css("display","none");

        // $(".evernote-logo").css("display","none");
        // $(".evernote-logo").after("<div>Dashboard</div>");
        //------------change top left icon to Dashboard ICON:-----------------
        $("a.evernote-logo").css("background","url(img/dashboard-xxl.png) 13px -3px no-repeat");
        $("a.evernote-logo").css("background-size","55px");
        //------hide "Dashboard" and "Switch Category" in User Dropdown.------
        $("a.go-to-notes").css("display","none");
        $("div.switch-account-menuitem").css("display","none");
    });
</script>