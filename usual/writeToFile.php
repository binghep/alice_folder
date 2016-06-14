<?php
return;
//$fp = fopen('lidn.txt', 'w');
$fp = fopen('lidn.txt', 'a');//append
fwrite($fp, "Cats chase mice\n");
fclose($fp);
//exit;