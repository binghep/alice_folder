<link rel="stylesheet" type="text/css" href="css/baseTheme/style.css" />

			<table class="options">
				<tr>
					<th style="width:50%">Example</th>
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
								&lt;div id="uploader_div"&gt;&lt;/div&gt;
								
								&lt;script type="text/javascript"&gt;
								$('#uploader_div').ajaxupload({
									url:'upload.php'
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
				remotePath:'example1/',
				thumbHeight:	100,				//max thumbnial height if set generate thumbnial of images on server side
				thumbWidth:		150,				//max thumbnial width if set generate thumbnial of images on server side
				thumbPostfix:	'_thumb',			//set the post fix of generated thumbs, default filename_thumb.ext,
				thumbPath:		'upload_thumbs/',	//set the path where thumbs should be saved, if empty path setted as remotePath
				thumbFormat:	'png',				//default same as image, set thumb output format, jpg, png, gif
			});
			</script>