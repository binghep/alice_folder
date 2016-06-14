			<link rel="stylesheet" type="text/css" href="css/classicTheme/style.css" />
			<table class="options">
				<tr>
					<th style="width:50%">Example</th>
					<th style="width:50%">Set up code</th>
				</tr>
				<tr>
					<td>
						<div id="uploader_div">

						</div>
						<br>
						
						<div id="drop_here" style="border:1px dashed black;border-radius:5px; width:400px;height:150px;">
							Drag & Drop Here
						</div>
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">
								&lt;div id="uploader_div"&gt;&lt;/div&gt;
								
								&lt;div id="drop_here" style="border:1px dashed black;border-radius:5px; 
								width:400px;height:150px;"&gt;&lt;/div&gt;
								
								&lt;script type="text/javascript"&gt;
								$('#uploader_div').ajaxupload({
									url:'upload.php',
									dropArea: '#drop_here'
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
			dropArea: '#drop_here',
			remotePath:'example3/'
		});
		</script>