<?php
$apc_installed = function_exists('apc_add');

$xcache_installed = extension_loaded('xcache');


echo 'apc is installed? '.($apc_installed?'yes':'no');
echo '<br>';
echo 'xcache is installed? '.($xcache_installed?'yes':'no');