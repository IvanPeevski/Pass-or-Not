<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $username = $_SESSION['username'];
  $created_tests_query = "SELECT * FROM test INNER JOIN user ON test.user_id = user.id WHERE user.username = '$username'";
  $created_tests_count = mysqli_num_rows(mysqli_query($db, $created_tests_query));

  $tests_query = "SELECT test.id, test.test_name, MAX(response.sent_on) AS 'sent_on' FROM test INNER JOIN user ON test.user_id = user.id INNER JOIN response ON response.test_id = test.id WHERE user.username = '$username' GROUP BY test.id ORDER BY response.sent_on DESC LIMIT 3";
  $last_active_tests = mysqli_fetch_all(mysqli_query($db, $tests_query), MYSQLI_ASSOC);

  $object = new stdClass();
  $object->created_tests_count = $created_tests_count;
  $object->active_tests = $last_active_tests;

  echo json_encode($object);

?>