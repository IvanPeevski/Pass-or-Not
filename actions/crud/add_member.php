<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $username = $_POST['member'];
  $team_id = $_POST['team_id'];
  $user_query = "SELECT id FROM user WHERE username = '$username'";
  $result = mysqli_fetch_assoc(mysqli_query($db, $user_query));
  if($result){
    $user_id = $result['id'];
    $query = "INSERT INTO `team_member` (`user_id`, `team_id`) VALUES ($user_id, $team_id)";
    mysqli_query($db, $query);
  }
  else{
    trigger_error("User doesn't exist");
  }
?>