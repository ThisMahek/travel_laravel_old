<!-- <?php
if (!function_exists('random_strings')) 
{
function random_strings($length_of_string) 
{ 
$str_result = 'ABCDEF1234567890';
return $str_result ;
//substr(str_shuffle($str_result), 0, $length_of_string);
}
}
// function get_unique_code()
// {
// $unique_code=random_strings(8);
// // $ci =& get_instance();
// $x=DB::table('leads')->where('unique_code',$unique_code)->first();
// if($x->isEmpty())
// {
//   return $unique_code;  
// }
// else
// {
//    get_unique_code();
// }
// }
?> -->