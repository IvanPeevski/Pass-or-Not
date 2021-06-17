<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/get_rnd_numbers.php');
  function copy_file($file_id, $testId, $db){
    $file_query = "SELECT * FROM `file` WHERE id=$file_id";
    $file = mysqli_fetch_assoc(mysqli_query($db, $file_query));
    $file_name=$file['name'];
    $my_file = uniqid().'-'.time().'-'.$file_name;
    $href = '/files/'.$my_file;
    copy($_SERVER['DOCUMENT_ROOT'].$file['href'], $_SERVER['DOCUMENT_ROOT'].$href);
    $query = "INSERT INTO file (test_id, name, href) VALUES ('$testId','$file_name','$href')";
    mysqli_query($db, $query);
    return mysqli_insert_id($db);
  }
  
  $test_id = $_POST['test_id'];

  $auth_query = "SELECT user.id, username, public FROM `test` INNER JOIN user ON test.user_id=user.id WHERE `test`.`id` = $test_id";
  $creator_info = mysqli_fetch_assoc(mysqli_query($db, $auth_query));
  $check = ($creator_info['username'] == $_SESSION['username']) || $creator_info['public']=='1';
  if($check){
    $query = "SELECT * FROM `test` WHERE `test`.`id` = $test_id";
    $test= mysqli_fetch_assoc(mysqli_query($db, $query));

    $pin='';
    do{
      $pin = getRndNumbers(8);
      $pin_query = "SELECT * FROM test WHERE pin = '$pin' LIMIT 1";
      $result = mysqli_fetch_assoc(mysqli_query($db, $pin_query));
    } while($result);
    $name = $test['test_name'];
    $userId = $_SESSION['id'];
    $content = $test['content'];
    $unlocked = $test['unlocked'];
    $public = $test['public'];
    $grading = $test['grading'];
    $one_per_one = $test['one_per_one'];
    $randomize_questions = $test['randomize_questions'];
    $randomize_answers = $test['randomize_answers'];
    $time_limit = $test['time_limit'];
    $require_profile = $test['require_profile'];
    $allow_anonymous = $test['allow_anonymous'];
    $limit_response = $test['limit_response'];
    $check_points = $test['check_points'];
    $check_answers = $test['check_answers'];
    $tags = $test['tags'];
    $date = date('Y-m-d H:i:s');

    $query = "INSERT INTO test (pin, test_name, user_id, tags, unlocked, public, grading, one_per_one, randomize_questions, randomize_answers, 
    time_limit, require_profile, allow_anonymous, limit_response, check_points, check_answers, last_modified)
    VALUES ('$pin','$name','$userId', '$tags', '$unlocked', '$public', '$grading', '$one_per_one', 
    '$randomize_questions', '$randomize_answers', '$time_limit', '$require_profile', '$allow_anonymous', '$limit_response', '$check_points', 
    '$check_answers','$date')";
    mysqli_query($db, $query);

    $testId = mysqli_insert_id($db);
    $content = str_replace('\\', '\\\\', $content);
    $content = json_decode($content, true);
    foreach($content as $q_key => $question){
      if(array_key_exists('file', $question)){
        $content[$q_key]['file'] = copy_file($question['file'], $testId, $db);
      }
      foreach($question['answers'] as $a_key => $answer){
        if(array_key_exists('file', $answer)){
          $content[$q_key][$a_key]['file'] = copy_file($answer['file'], $testId, $db);
        }
      }
    }
    $content = json_encode($content, JSON_UNESCAPED_UNICODE);

    $query = "UPDATE `test` SET content='$content' WHERE id=$testId";
    mysqli_query($db, $query);

  }  
?>