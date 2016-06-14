<?php 
$run2 = "<script type=\"text/javascript\">
	$('#uploader_div').ajaxupload({
		url:'upload.php',
		editFilename:true,
		form:'#THEFORM',
		remotePath:'example8/',
		beforeUploadAll: function(files)
		{
			//validate form. this validation will take 
			//place only if any file has been selected for upload
			var name = $('#form_name').val();
			var email= $('#form_email').val();
			if(name=='' || email.indexOf('@')<0)
			{
				alert('Please fill name and email before continue');
				return false;
			}
			
			return true;
		},
		onInit:function()
		{
			//hide remove files and upload button
			this.removeFiles.hide();
			this.uploadFiles.hide();
		}
	});

	//if we want to make mandatory file upload
	$('#THEFORM').submit(function(){
		var AU_class = $('#uploader_div').data('AU'); //get the uploader class
		//we can use also $('#uploader_div').ajaxupload('getFiles');
		var selected_file = AU_class.files; //access selected files
		
		 //if there not are file ready to upload, then do not submit the form
		if(selected_file.length==0){
			alert('Please select any file before send');
			return false;
		}
		return true;
	});
</script>";

$run1='<form action="test.php" method="post" id="THEFORM">
	<fieldset>
		<legend>A Normal Form</legend>
		
		<label>Name *</label><input type="text"  name="name" id="form_name" /><br>
		<label>Email *</label><input type="text"  name="email" id="form_email" /><br>
		<label>Description</label><textarea name="txt" id="form_txt"></textarea><br>
		<input type="submit" value="Submit" />
		
		* This fields are mandatory
	</fieldset>
</form>
<div id="uploader_div"></div>';

?>

<div class="row-fluid show-grid">

	<div class="row-fluid">
		<div class="span8">
			<h2 id="result">Result:</h2>
			<div  style="border:1px solid black;padding:5px;">
				<?php echo $run1; ?>
				<?php echo $run2; ?>
			</div>
		</div>


		<div class="span8">
			<h2 id="html_code">Html code:</h2>
			<pre class="prettyprint linenums">
<?php echo htmlspecialchars($run1);?>
			</pre>
		</div>

		<div class="span8">
			<h2 id="js_code">Javascript:</h2>
			<pre class="prettyprint linenums">
<?php echo htmlspecialchars($run2);?>
			</pre>
		</div>

	</div>
</div>