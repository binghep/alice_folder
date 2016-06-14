			<link rel="stylesheet" type="text/css" href="css/classicTheme/style.css" />
			<table class="options">
				<tr>
					<th style="width:50%">Example</th>
					<th style="width:50%">Set up code</th>
				</tr>
				<tr>
					<td>
						<div id="uploader_div"></div>
						
						<form action="test.php" method="post" id="THEFORM">
						<fieldset>
							<legend>A Normal Form</legend>
							<input type="text" value="Form field" name="test_input" />
							<input type="text" value="Form field 2" name="test_input2" />
							<input type="submit" value="Submit" />
						</fieldset>
						</form>
						
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">								
								&lt;div id="uploader_div"&gt;&lt;/div&gt;

								&lt;script type="text/javascript"&gt;
								$('#uploader_div').ajaxupload({
									url:'upload.php',
									editFilename:true,
									form:'#THEFORM'
								});
								&lt;/script&gt;
							</pre>
						</div>
						
					</td>
				</tr>
			</table>
					<script type="text/javascript">
		$('#uploader_div').ajaxupload({
			url:'upload.php',
			editFilename:true,
			form:'#THEFORM',
			remotePath:'example7/'
		});
		</script>