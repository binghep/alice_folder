			<link rel="stylesheet" type="text/css" href="css/classicTheme/style.css" />
			<table class="options">
				<tr>
					<th style="width:30%">Example</th>
					<th style="width:70%">Set up code</th>
				</tr>
				<tr>
					<td>
						<input type="button" onclick="$('#uploader_div').ajaxupload('enable')" value="Enable" />
						<input type="button" onclick="$('#uploader_div').ajaxupload('disable')" value="Disable" />
						<input type="button" onclick="$('#uploader_div').ajaxupload('start')" value="Start upload" />
						<input type="button" onclick="$('#uploader_div').ajaxupload('clear')" value="Clear queue" />
						
						<div id="uploader_div"></div>
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">
								&lt;input type="button" onclick="$('#uploader_div').ajaxupload('enable')" value="Enable" /&gt;
								&lt;input type="button" onclick="$('#uploader_div').ajaxupload('disable')" value="Disable" /&gt;
								&lt;input type="button" onclick="$('#uploader_div').ajaxupload('start')" value="Start upload" /&gt;
								&lt;input type="button" onclick="$('#uploader_div').ajaxupload('clear')" value="Clear queue" /&gt;
								
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
			remotePath:'example5/'
		});
		</script>