			<link rel="stylesheet" type="text/css" href="css/classicTheme/style.css" />
			<table class="options">
				<tr>
					<th style="width:30%">Example</th>
					<th style="width:70%">Set up code</th>
				</tr>
				<tr>
					<td>						
						<div id="uploader_div"></div>
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">							
								&lt;div id="uploader_div"&gt;&lt;/div&gt;

								&lt;script type="text/javascript"&gt;
								$('#uploader_div').ajaxupload({
									url:'upload.php',
									finish:function(files, filesObj){
										
										alert('All files has been uploaded:' + filesObj);
									},
									success:function(file){
										console.log('File ' + file + ' uploaded correctly');
									},
									beforeUpload: function(filename, fileobj){
										if(filename.length>20){
											return false; //file will not be uploaded
										}
										else
										{
											return true; //file will be uploaded
										}
									},
									error:function(txt, obj){
										alert('An error occour '+ txt);
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
			remotePath:'example6/',
			finish:function(files){
				alert('All files has been uploaded:' + files);
			},
			success:function(file){
				console.log('File ' + file + ' uploaded correctly');
			},
			beforeUpload: function(filename, fileobj){
				if(filename.length>20){
					return false; //file will not be uploaded
				}
				else
				{
					return true; //file will be uploaded
				}
			},
			error:function(txt, obj){
				alert('An error occour '+ txt);
			}
		});
		</script>