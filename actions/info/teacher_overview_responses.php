<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');
  $username = $_SESSION['username'];
  
  $active_responses_query = "SELECT response.pin FROM response INNER JOIN test ON response.test_id = test.id INNER JOIN user ON test.user_id = user.id WHERE user.username='$username' AND response.state = 'active'";
  $active_responses_count = mysqli_num_rows(mysqli_query($db, $active_responses_query));
  $sent_responses_query = "SELECT response.pin, response.content, response.name AS fullName, CONCAT(response.class,response.division) AS 'class',test.test_name, test.content AS test_content, test.grading FROM response INNER JOIN test ON response.test_id = test.id INNER JOIN user ON test.user_id = user.id WHERE user.username = '$username'AND (response.state<>'active' OR response.state IS NULL) ORDER BY response.sent_on DESC";
  $sent_responses = mysqli_fetch_all(mysqli_query($db, $sent_responses_query), MYSQLI_ASSOC);

  $total = count($sent_responses)+$active_responses_count;

  foreach($sent_responses as $key=>$row){
    $response_content = json_decode($row['content']);
    $test_content = json_decode($row['test_content']);
    if(grade_test($response_content, $test_content, $row['grading'])->undecided>0){
      unset($sent_responses[$key]['content']);
      unset($sent_responses[$key]['test_content']);
    }
    else{
      unset($sent_responses[$key]);
    }
  }
  $sent_responses = array_merge(array(),$sent_responses);
  $object = new stdClass();
  $object->total = $total;
  $object->active = $active_responses_count;
  $object->responses = $sent_responses;

  echo json_encode($object);
?>