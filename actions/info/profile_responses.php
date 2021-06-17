<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');
  $username = $_SESSION['username'];;

  $responses_query = "SELECT response.pin, response.content, test.test_name, test.pin AS 'test_pin', test.content AS 'test_content', test.grading, response.sent_on FROM response INNER JOIN test ON response.test_id = test.id INNER JOIN user ON response.user_id = user.id WHERE user.username='$username'";
  $responses = mysqli_fetch_all(mysqli_query($db, $responses_query), MYSQLI_ASSOC);
  foreach($responses as $key=>$response){
    $response_content = json_decode($response['content']);
    $test_content = json_decode($response['test_content']);
    $grade_obj = grade_test($response_content, $test_content, $response['grading']);
    unset($responses[$key]['content']);
    unset($responses[$key]['test_content']);
    unset($responses[$key]['grading']);
    $responses[$key]['grade'] = $grade_obj->grade;
    $responses[$key]['grade_text'] = $grade_obj->grade_text;
    $responses[$key]['points'] = $grade_obj->points;
    $responses[$key]['maxpoints'] = $grade_obj->maxpoints;
    $responses[$key]['undecided'] = $grade_obj->undecided;
    $responses[$key]['sent_on'] = date("H:i d/m/y", strtotime($response['sent_on']));
  }
  echo json_encode($responses);
?>