<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $user_id = $_SESSION['id'];
  $query = "SELECT `id`,`name` FROM `team` WHERE team.user_id = $user_id";
  $results = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);
  echo json_encode($results);
?>