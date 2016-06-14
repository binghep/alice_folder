<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
exit;//only accessible when I am using it. in case someone upload bad scripts
?>

<html>
 
<head>   
 
<!-- 1 -->
<link href="css/dropzone.css" type="text/css" rel="stylesheet" >
 
<!-- 2 -->
<!-- <script ></script> -->
<!-- <script type="text/javascript" src="dropzone.min.js"></script> -->
<script type="text/javascript" src="js/dropzone.js"></script>
 
</head>
 
<body>
 
<!-- 3 -->
<form action="upload.php" class="dropzone">
	<span>Product Name</span>
	<div>
		<input type="text" value="yoyo">
	</div>
	<span>Product Sku</span>
	<div>
		<input type="text" value="yoyo">
	</div>
</form>
   
</body>
 
<!-- - See more at: http://www.startutorial.com/articles/view/how-to-build-a-file-upload-form-using-dropzonejs-and-php#sthash.uqyJDql4.dpuf -->
</html>