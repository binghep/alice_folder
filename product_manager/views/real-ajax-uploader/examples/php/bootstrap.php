			<link rel="stylesheet" type="text/css" href="css/bootstrapTheme/style.css" />
			
			<table class="options">
				<tr>
					<th style="width:50%">Example with bootstrap</th>
					<th style="width:50%">Set up code</th>
				</tr>
				<tr>
					<td>
						<div id="uploader_div">

						</div>
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">
								
								&lt;!-- Load the css of bootstrap theme  --&gt;
								&lt;link rel="stylesheet" type="text/css" href="css/bootstrapTheme/style.css" /&gt;
								
								&lt;!-- Load Boostrap css  --&gt;
								&lt;link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" /&gt;
								
								&lt;div id="uploader_div"&gt;&lt;/div&gt;
								
								&lt;script type="text/javascript"&gt;
								$('#uploader_div').ajaxupload({
									url:'upload.php',
									bootstrap:true//TELL THE UPLOADER TO ADD boostrap classes
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
				remotePath:'example10/',
				bootstrap:true,
				maxFileSize:'1000M'
			});
			</script>