<?php
$page_num = isset($_GET['example']) ? $_GET['example']:'';
$pages = array(
	array('classic_theme.php','Classic Theme'),
	array('basic.php', 'Basic set up'),
	array('drag_drop.php', 'Upload by Drag & Drop'),
	array('file_ext.php', 'Restrict file extension'),
	array('auto.php', 'Auto start'),
	array('api_calls.php', 'API calls'),
	array('events.php', 'Events and Options'),
	array('form.php', 'Form Integration and File rename'),
	array('form_validation.php', 'Advanced Form Integration'),
	array('fallback.php', 'No JS fallback')
);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Real Ajax Uploader - a jQuery multi uploader</title>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Real Ajax Uploader - a jQuery multi uploader" />
        <meta name="keywords" content="ajax file uploader, uploader ajax, iframe upload, file upload, multiupload" />
		<meta name="author" content="Alban Xhaferllari" />
		<!-- PAGE CSS -->
        <link rel="stylesheet" type="text/css" href="codebrush/css/demo.css" />
		
		<!-- SET UP AXUPLOADER  -->
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/ajaxupload-min.js"></script>
		
		
		<!-- This is just for the demo code  -->
		<link rel="stylesheet" href="codebrush/css/shCore.css" type="text/css" media="all" />
		<link rel="stylesheet" href="codebrush/css/shThemeEclipse.css" type="text/css" media="all" />
		<link rel="stylesheet" href="codebrush/css/shCoreDefault.css" type="text/css"/>
		<script src="codebrush/shCore.js" type="text/javascript"></script>
		<script src="codebrush/shBrushJScript.js"  type="text/javascript" ></script>
		<script src="codebrush/shBrushXml.js"  type="text/javascript" ></script>
		<script type="text/javascript">
			SyntaxHighlighter.all({toolbar:false});
		</script>
		
	</head>
	<body>
		<div>
		
			<div class="header">
				<a href="http://codecanyon.net/item/real-ajax-multi-uploader/805976?ref=albanx"><span>Buy this plugin on </span>Codecanyon</a>
				<span class="right_ab">

				</span>
			</div>
			
			
			<h1>Real Ajax Uploader <span>a jQuery multi uploader plugin</span></h1>
			<div class="line"></div>
			
			<div class="more">
				<ul>
					<li>Live Examples:</li>
					<?php
						foreach($pages as $num=>$p)
						{
							$selected = ($num==$page_num)?'class="selected"':'';
							echo '<li '.$selected.'><a href="?example='.$num.'">'.$p[1].'</a></li>';
						}
					?>

				</ul>
			</div>
			<?php 
			if(isset($pages[$page_num]))
			{
				include 'php/'.$pages[$page_num][0];
			}
			else
			{
				include 'php/'.$pages[0][0];
			}
			?>
		</div>
	</body>
</html>	