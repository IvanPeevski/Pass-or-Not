<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $testId = $_POST["testId"];
  $testQuery = "SELECT * FROM test WHERE test.id = $testId";
  $result = mysqli_query($db, $testQuery);
  $assoc_arr = mysqli_fetch_assoc($result);
  $test = [];
  $content = json_decode($assoc_arr['content'], true);
  //Get Files
  foreach($content as $q_key => $question){
    if(array_key_exists('file', $question)){
      $content[$q_key]['file'] = GetFile($question['file'], $db);
      if($content[$q_key]['file']==null){
        unset($content[$q_key]['file']);
      }
    }
    if($question['type']=='radio' || $question['type']=='checkbox'){
      foreach($question['answers'] as $a_key => $answer){
        if(array_key_exists('file', $answer)){
          $content[$q_key]['answers'][$a_key]['file'] = GetFile($answer['file'], $db);
          if($content[$q_key]['answers'][$a_key]['file']==null){
            unset($content[$q_key]['answers'][$a_key]['file']);
          }
        }
      }
    }
  }

  function GetFile($fileId, $db){
    $fileQuery = "SELECT * FROM `file` WHERE `file`.`id` = $fileId";
    $result = mysqli_query($db, $fileQuery);
    $file = mysqli_fetch_assoc($result);
    if($file){
      $path = $file['href'];
      if(file_exists($path)){
        $type = mime_content_type($_SERVER['DOCUMENT_ROOT'].$file['href']);
        $data = file_get_contents($_SERVER['DOCUMENT_ROOT'].$path);
        $base64 = 'data:'.$type.';base64,'.base64_encode($data);
        
        preg_match('/\w+(?=\/)/m', $type, $matches);
        $type = $matches[0];
        $file_obj = array('name'=>$file['name'], 'src'=>$base64, 'type'=>$type);
        return $file_obj;
      }
      else{
        return null;
      }
    }
    else{
      return null;
    }
  }

  $test['settings']['tags'] = $assoc_arr['tags'];
  $test['settings']['unlocked'] = $assoc_arr['unlocked'];
  $test['settings']['public'] = $assoc_arr['public'];
  $test['settings']['grading'] = $assoc_arr['grading'];
  $test['settings']['one_per_one'] = $assoc_arr['one_per_one'];
  $test['settings']['randomize_questions'] = $assoc_arr['randomize_questions'];
  $test['settings']['randomize_answers'] = $assoc_arr['randomize_answers'];
  $test['settings']['time_limit'] = $assoc_arr['time_limit'];
  $test['settings']['require_profile'] = $assoc_arr['require_profile'];
  $test['settings']['allow_anonymous'] = $assoc_arr['allow_anonymous'];
  $test['settings']['limit_response'] = $assoc_arr['limit_response'];
  $test['settings']['check_points'] = $assoc_arr['check_points'];
  $test['settings']['check_answers'] = $assoc_arr['check_answers'];
  $test['settings']['limit_check'] = $assoc_arr['limit_check'];
  $test['settings']['team_limit'] = $assoc_arr['team_limit'];


  $test['name']=$assoc_arr['test_name'];
  $test['questions']=$content;

  echo json_encode($test, JSON_UNESCAPED_UNICODE)
?>