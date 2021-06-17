<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $test_id = $_POST['test_id'];
  $settings_query = "SELECT tags, public, grading, one_per_one, randomize_questions, randomize_answers, 
  question_limit, time_limit, individual_time, require_profile, allow_anonymous, limit_response, check_points, check_answers, limit_check, team_limit FROM test WHERE id='$test_id'";
  $settings = mysqli_fetch_assoc(mysqli_query($db, $settings_query));
  echo json_encode($settings, JSON_UNESCAPED_UNICODE);
?>