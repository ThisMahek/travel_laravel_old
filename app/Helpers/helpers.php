<?php
if (!function_exists('random_strings')) 
{
function random_strings($length_of_string) 
{ 
$str_result = 'ABCDEF1234567890';
return $str_result ;
//substr(str_shuffle($str_result), 0, $length_of_string);
}
}