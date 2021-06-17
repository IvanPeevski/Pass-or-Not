<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  
  $response_pin = $_POST['response_pin'];
  $current_date = date('Y-m-d H:i:s');
  $query = "UPDATE response SET response.state='halted', response.sent_on='$current_date' WHERE pin=$response_pin";
  mysqli_query($db, $query);
?>