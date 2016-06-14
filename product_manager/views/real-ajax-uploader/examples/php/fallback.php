<?php
$code = "
<form id=\"uploader_fall\" action=\"upload.php\" method=\"post\" encType=\"multipart/form-data\">
	<input type=\"hidden\" value=\"example9/\" name=\"ax-file-path\" />
	<input type=\"hidden\" value=\"100M\" name=\"ax-maxFileSize\" />
	<input type=\"hidden\" value=\"jpg|gif|png\" name=\"ax-allow-ext\" />
	<input type=\"file\" name=\"Filedata\" />
	<input type=\"submit\" value=\"Upload\" />
</form>

<script type=\"text/javascript\">
	$('#uploader_fall').ajaxupload({
		url:'upload.php',
		uploadPath:'example9/'
	});
</script>
";
?>
			<link rel="stylesheet" type="text/css" href="css/classicTheme/style.css" />
			<table class="options">
				<tr>
					<th>Try disabling javascript</th>
					<th>Set up code</th>
				</tr>
				<tr>
					<td>
						<?php
						echo $code;
						?>
					</td>
					<td>
				
						<div id="setup_code">
							<pre class="brush: xml">								
							<?php
								echo htmlspecialchars($code);
							?>
							</pre>
						</div>
						
					</td>
				</tr>
			</table>
