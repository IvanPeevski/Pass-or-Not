<?php
  include($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  include($_SERVER['DOCUMENT_ROOT'].'/actions/functions/get_rnd_numbers.php');
  //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
  function uploadFile($file, $testId, $db){
    $file_name=$file['name'];
    $file_name = explode('.',$file_name);
    if($file_name[count($file_name)-1]=='php'){
      $file_name[count($file_name)-1]='txt';
    }
    $file_name = join('.',$file_name);

    $my_file = uniqid().'-'.time().'-'.$file_name;
    $href = '/files/'.$my_file;
    $handle = fopen($_SERVER['DOCUMENT_ROOT'].$href, 'a');
    $data = $file['src'];
    $data = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $data));
    fwrite($handle, $data);
    $query = "INSERT INTO file (test_id, name, href) VALUES ('$testId','$file_name','$href')";
    mysqli_query($db, $query);
    return mysqli_insert_id($db);
  }
  $username = $_SESSION['username'];
  $testId = $_POST['testId'];
  $name = $_POST['name'];
  $content = $_POST['test_content'];
  $unlocked = $_POST['unlocked'];
  $public = $_POST['public'];
  $grading = $_POST['grading'];
  $one_per_one = $_POST['one_per_one'];
  $randomize_questions = $_POST['randomize_questions'];
  $randomize_answers = $_POST['randomize_answers'];
  $question_limit = $_POST['question_limit'];
  $time_limit = $_POST['time_limit'];
  $individual_time = $_POST['individual_time'];
  $require_profile = $_POST['require_profile'];
  $allow_anonymous = $_POST['allow_anonymous'];
  $limit_response = $_POST['limit_response'];
  $check_points = $_POST['check_points'];
  $check_answers = $_POST['check_answers'];
  $limit_check = $_POST['limit_check'];
  $team_limit = $_POST['team_limit'];
  $tags = $_POST['tags'];

  $userId = $_SESSION['id'];
  $date = date('Y-m-d H:i:s');

  //Delete File
  $query = "SELECT * FROM `file` WHERE `file`.`test_id` = $testId";
  $results = mysqli_query($db, $query);
  while( $row = mysqli_fetch_assoc($results)){
    unlink($_SERVER['DOCUMENT_ROOT'].$row['href']);
  } 
  $query = "DELETE FROM `file` WHERE `file`.`test_id` = $testId";
  $results = mysqli_query($db, $query);

  //Escape chars and put ids
  for($i=0; $i<count($content); $i++){
    if(isset($content[$i]['info'])){
      $content[$i]['info'] = preg_replace('/"/m', '&quot;', $content[$i]['info']);
      $content[$i]['info'] = preg_replace("/'/m", '&apos;', $content[$i]['info']);
      $content[$i]['info'] = preg_replace("/'/m", '&apos;', $content[$i]['info']);
      $content[$i]['info'] = preg_replace("/\\\\/m", '\\\\', $content[$i]['info']);
      $content[$i]['info'] = addslashes($content[$i]['info']);
    }
    $content[$i]['title'] = preg_replace('/"/m', '&quot;', $content[$i]['title']);
    $content[$i]['title'] = preg_replace("/'/m", '&apos;', $content[$i]['title']);
    $content[$i]['title'] = preg_replace("/'/m", '&apos;', $content[$i]['title']);
    $content[$i]['title'] = preg_replace("/\\\\/m", '\\\\', $content[$i]['title']);
    $content[$i]['title'] = preg_replace("/\s+/m", ' ', $content[$i]['title']);
    $content[$i]['title'] = addslashes($content[$i]['title']);
    $content[$i]['id'] = $i;
    if($content[$i]['type']=='radio'|| $content[$i]['type']=='checkbox'){
      for($a=0; $a<count($content[$i]['answers']); $a++){
        $content[$i]['answers'][$a]['text'] = preg_replace('/"/m', '&quot;', $content[$i]['answers'][$a]['text']);
        $content[$i]['answers'][$a]['text'] = preg_replace("/'/m", '&apos;', $content[$i]['answers'][$a]['text']);
        $content[$i]['answers'][$a]['text'] = preg_replace("/\s+/m", ' ', $content[$i]['answers'][$a]['text']);
        $content[$i]['answers'][$a]['text'] = addslashes($content[$i]['answers'][$a]['text']);
        $content[$i]['answers'][$a]['id'] = $a;
      }
    }
  }

  if($testId){
    foreach($content as $q_key => $question){
      if(array_key_exists('file', $question)){
        $content[$q_key]['file'] = uploadFile($question['file'], $testId, $db);
      }
      if($question['type']=='radio' || $question['type']=='checkbox'){
        foreach($question['answers'] as $a_key => $answer){
          if(array_key_exists('file', $answer)){
            $content[$q_key]['answers'][$a_key]['file'] = uploadFile($answer['file'], $testId, $db);
          }
        }
      }
    }
    $content = json_encode($content, JSON_UNESCAPED_UNICODE);
    $query = "UPDATE `test` SET 
    test_name='$name', content='$content', tags='$tags', unlocked='$unlocked', public='$public', grading='$grading', one_per_one='$one_per_one', 
    randomize_questions='$randomize_questions', randomize_answers='$randomize_answers', question_limit='$question_limit',
    time_limit='$time_limit', individual_time='$individual_time', require_profile='$require_profile', allow_anonymous='$allow_anonymous', limit_response='$limit_response', 
    check_points='$check_points', check_answers='$check_answers',limit_check='$limit_check',team_limit='$team_limit', last_modified='$date' WHERE `id` = $testId";
    mysqli_query($db, $query);

  }else{
    $pin='';
    do{
        $pin = getRndNumbers(8);
        $pin_query = "SELECT * FROM test WHERE pin = '$pin' LIMIT 1";
        $results = mysqli_query($db, $pin_query);
        $result = mysqli_fetch_assoc($results);
    } while($result);
    $query = "INSERT INTO test (pin, test_name, user_id, tags, unlocked, public, grading, one_per_one, randomize_questions, randomize_answers, 
    question_limit, time_limit, individual_time, require_profile, allow_anonymous, limit_response, check_points, check_answers, limit_check,team_limit, last_modified)
    VALUES ('$pin','$name','$userId', '$tags', '$unlocked', '$public', '$grading', '$one_per_one', 
    '$randomize_questions', '$randomize_answers', '$question_limit','$time_limit', '$individual_time', '$require_profile', '$allow_anonymous', '$limit_response', '$check_points', 
    '$check_answers', '$limit_check','$team_limit', '$date')";
    mysqli_query($db, $query);

    $testId = mysqli_insert_id($db);
    echo $testId;
    foreach($content as $q_key => $question){
      if(array_key_exists('file', $question)){
        $content[$q_key]['file'] = uploadFile($question['file'], $testId, $db);
      }
      foreach($question['answers'] as $a_key => $answer){
        if(array_key_exists('file', $answer)){
          $content[$q_key]['answers'][$a_key]['file'] = uploadFile($answer['file'], $testId, $db);
        }
      }
    }
    $content = json_encode($content, JSON_UNESCAPED_UNICODE);
    $query = "UPDATE `test` SET content='$content' WHERE id='$testId'";
    mysqli_query($db, $query);
  }
  echo('index:'.$testId);
?>