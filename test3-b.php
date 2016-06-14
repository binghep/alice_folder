<?php
return;
/*
This file is used for reading product category-wise json
*/
//$string = file_get_contents("mens_rain_jacket.json");
$string = file_get_contents("mens_rain_jacket_pretty.json");
$back=(array)json_decode($string);

echo "<pre>";
//var_dump($back);
var_dump($back[2]); //->this is an object! 
var_dump($back[0]->categoryIds); //->this is an array 
//var_dump($back[0]->sku);

echo "</pre>";


// var_dump($back[0]['categoryIds']);