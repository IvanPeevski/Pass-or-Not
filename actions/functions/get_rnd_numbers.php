<?php
  function getRndNumbers($int){
    $num="";
    for ($i = 0; $i<$int; $i++) 
    {
      $num .= mt_rand(0,9);
    }
    return $num;
  }
?>