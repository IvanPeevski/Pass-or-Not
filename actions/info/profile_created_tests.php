<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $username = $_SESSION['username'];

  $test_query = "SELECT test.pin, test.test_name, test.tags, test.unlocked, test.content, test.id, test.last_modified FROM test INNER JOIN user ON test.user_id = user.id WHERE user.username='$username'";
  $tests = mysqli_fetch_all(mysqli_query($db, $test_query), MYSQLI_ASSOC);

  foreach($tests as $key=>$test){
    $test_content = json_decode($test['content'],true);
    $points = 0;
    if(gettype($test_content)!='array'){
      $test_content = [];
    }
    foreach ($test_content as $question) {
        $points += (int) $question['points'];
    }
    $tests[$key]['points'] = $points;
    $tests[$key]['questions'] = count($test_content);
    $tests[$key]['last_modified'] = date("H:i d/m/y", strtotime($test['last_modified']));
    unset($tests[$key]['content']);
  }

  echo json_encode($tests);
?>