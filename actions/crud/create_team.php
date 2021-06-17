<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $name = $_POST['name'];
  $user_id = $_SESSION['id'];
  $query = "INSERT INTO `team` (`user_id`, `name`) VALUES ($user_id, '$name')";
  mysqli_query($db, $query);
  $id = mysqli_insert_id($db);
  echo $id;
?>