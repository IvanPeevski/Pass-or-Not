<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $team_id = $_POST['id'];
  $query = "SELECT team.name AS team_name, CONCAT(first_name,' ', surname) AS creator_name FROM team INNER JOIN user ON team.user_id = user.id WHERE team.id = $team_id";
  $team = mysqli_fetch_assoc(mysqli_query($db, $query));
  $members_query = "SELECT username FROM team_member INNER JOIN user ON team_member.user_id = user.id WHERE team_member.team_id = $team_id";
  $members = mysqli_fetch_all(mysqli_query($db, $members_query));
  $team['members'] = $members;
  echo json_encode($team)
?>