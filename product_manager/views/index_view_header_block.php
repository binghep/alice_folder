
<?php

date_default_timezone_set("Asia/Shanghai");
// $today_date=date("m/d/Y h:i:s");
$today_date=date("m/d/Y");
// $tomorrow_date=date("m/d/Y",strtotime("+1 days"));
$todays_pdf_report_link="http://www.1661hk.com/alice/library/fpdf/fpdf181/tutorial/view_pdf_report_for_website_products_api.myself.php?start_date={$today_date}&end_date={$today_date}&secret_code=sadioiow8923inksk65xzdweXdj&worker_id=".$_SESSION['user']['id'];

if (TEMPLATE_HINT){
    echo basename(__FILE__, '.php');
    echo '<div class="logo-bar public-layout" style="border:1px solid red;">';
}else{
    echo '<div class="logo-bar public-layout">';
}
?>
      <a href="#"  class="evernote-logo "></a>
        <!-- <a class="Btn Btn_emph view-button view-button-desktop show-single-note-app-install-box" href="#" style="display:none">
          View in Evernote</a> -->
        <a class="Btn Btn_emph save-button normal-save-button save-button-desktop" href="<?php echo $todays_pdf_report_link;?>" style="display:block;height:34px;width: 160px;padding:0px !important;margin: 6px 8px 3px 8px;">
          Today's Report</a>
      <div class="switch-account-div active" style="display: block;">
        <div class="switch-account-icon switch-account-icon-free"></div>
        <div class="switch-account-name"><?php echo $_SESSION['user']['name'];?></div>
        <div class="switch-account-arrow"></div>
        <div class="switch-account-dropdown" style="display: none;">
          <div class="switch-dropdown-arrow"></div>
          <a href="dashboard.php" class="switch-account-menuitem go-to-notes" >
              Dashboard</a>
          <div class="switch-account-menuitem switch" onclick="window.open('menu.php','_self')">
            Switch Category </div>
          <div class="switch-account-logout" onclick="window.open('trimmed_version/logout.php','_self')">
            Sign Out</div>
        </div>
      </div>
    </div>

<?php

// echo '<link rel="stylesheet" type="text/css" href="Style.css">';


// echo '<div class="header" style="height:80px;">';
// if ($is_admin){
// 	echo '<div><a href="report.php" target="_blank">View Product Update Report</a></div>';
// }
// echo '<div><a href="create_magento_attribute.php" target="_blank">Create New Competitor (e.g. ebay, wholefoods) / 添加新的竞争对手 (比如taobao, amazon) </a></div>';
// echo '<div><a href="standalone.index.php">Product not in our website yet? Click here. / 产品不在我们网站中？点这里。</div>';

// echo '</div>';

?>

<script type="text/javascript">
$(document).ready(function() {
	$('.switch-account-div.active').click(function(){
		if ($('.switch-account-dropdown').css("display")=="block"){
			$('.switch-account-dropdown').css("display","none");
		}else{
			$('.switch-account-dropdown').css("display","block");
		}
	});

	$("div :not('.switch-account-div'):not('.switch-account-name'):not('.switch-account-arrow'):not('.switch-account-icon')").click(function(){
		$('.switch-account-dropdown').css("display","none");
	});
	// $(".logo-bar").click(function(){
	// 	$('.switch-account-dropdown').css("display","none");
	// });
});

</script>