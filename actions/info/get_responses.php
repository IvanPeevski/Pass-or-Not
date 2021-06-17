<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');

  $return_arr = [];

  $test_id = $_POST['test_id'];
  $test_query = "SELECT content, grading FROM test WHERE id='$test_id'";
  $test = mysqli_fetch_assoc(mysqli_query($db, $test_query));
  $query = "SELECT * FROM response WHERE test_id='$test_id'";
  $results = mysqli_query($db, $query);
  while($response = mysqli_fetch_assoc($results)){
    $obj = grade_test(json_decode($response['content']),json_decode($test['content']), $test['grading'], $response['state']);
    $obj->id = $response['id'];
    $obj->pin = $response['pin'];
    $obj->name = $response['name'];
    $obj->class = $response['class'];
    $obj->division = $response['division'];
    $obj->date = date("H:i d/m/y", strtotime($response['sent_on']));
    $obj->state = $response['state'];
    array_push($return_arr, $obj);
  }
  echo json_encode($return_arr, JSON_UNESCAPED_UNICODE)
?>