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
									url:'upload.php',
									data:'asd=asd&qwe=123',
									thumbHeight:200,
									finish:function(files){
										console.log(files);
									}
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
				data:'asd=asd&qwe=123',
				thumbHeight:200,
				finish:function(files){
					console.log(files);
				}
			});
			</script>