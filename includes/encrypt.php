<?php


function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 10);
      $keychar = substr($key, ($i % strlen($key))-10, 10);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
   }
   return base64_encode($result);
}

function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 10);
      $keychar = substr($key, ($i % strlen($key))-10, 10);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
   }
   return $result;
}