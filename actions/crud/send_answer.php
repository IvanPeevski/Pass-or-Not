<?php 
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');
  $response_pin = $_POST['response_pin'];
  $question_id = $_POST['question_id'];
  $timestamp = date('Y-m-d H:i:s');
  $query = "SELECT * FROM response WHERE pin='$response_pin'";
  $response = mysqli_fetch_assoc(mysqli_query($db, $query));
  $content = json_decode($response['content']);

  $return_obj = new stdClass();
  $return_obj->msg = '';

  if($response['state']=='sent' || $response['state']=='halted'){
    $return_obj->msg = 'redirect';
  }
  else{
    $query = "SELECT test.content, grading FROM response INNER JOIN test ON response.test_id = test.id WHERE response.pin='$response_pin'";
    $result = mysqli_fetch_assoc(mysqli_query($db, $query));
    $test_content = json_decode($result['content']);
    $question = $test_content[intval($question_id)];
    if($question->type=='file'){
      $file = $_FILES['answer'];
      if($file['name']!=''){
        $target_dir = "/files/";
        $name=basename($file['name']);
        $name = explode('.',$name);
        if($name[count($name)-1]=='php'){
            $name[count($name)-1]='txt';
        }
        $name = join('.',$name);
        $target_file = $target_dir .uniqid().'-'.time().'-'.$name;
        move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$target_file);
        $query = "INSERT INTO file (name, href) VALUES ('$name','$target_file')";
        mysqli_query($db, $query);
        $last_fileId = mysqli_insert_id($db);

        for($i=0; $i<count($content); $i++){
          if((int)$content[$i]->id == (int)$question_id){
            $content[$i]->graded = false;
            $content[$i]->points = "0";
            $content[$i]->file = $last_fileId;
            $content[$i]->timestamp = $timestamp;
          }
        }
      }
    }
    else{
      if (isset($_POST['answer'])){
        $answer = $_POST['answer'];
      }
      else{
        $answer = null;
      }
      if($question->type=="text"){
        $obj->graded = false;
        $obj->points = "0";
        $obj->text = $answer;
        for($i=0; $i<count($content); $i++){
          if((int)$content[$i]->id == (int)$question_id){
            $content[$i]->graded = false;
            $content[$i]->points = "0";
            $content[$i]->text = $answer;
            $content[$i]->timestamp = $timestamp;
          }
        }
      }
      else{
        for($i=0; $i<count($content); $i++){
          if((int)$content[$i]->id == (int)$question_id){
            $content[$i]->value = $answer;
            $content[$i]->timestamp = $timestamp;
          }
        }
      }
    }

    //Limiter here
    $filtered = array_filter($content, function($answer){
      if(!property_exists($answer, 'value') && !isset($answer->file) && !isset($answer->text)){
          return true;
      }
      else {return false;}
    });
    if(count($filtered)==0){
      $current_date = date('Y-m-d H:i:s');
      $return_obj->msg = 'finish';
      $return_obj->response = grade_test($content,$test_content, $result['grading']);;
      $return_obj->response->name = $response['name'];
      $return_obj->response->class = $response['class'];
      $return_obj->response->division = $response['division'];
      $return_obj->response->date = date("H:i d/m/y", strtotime($current_date));
      $return_obj->content = $content;
      $content = json_encode($content, JSON_UNESCAPED_UNICODE);
      $query = "UPDATE response SET response.state='sent', sent_on='$current_date', content='$content' WHERE pin='$response_pin'";
      mysqli_query($db, $query);
    }
    else{
      $content = json_encode($content, JSON_UNESCAPED_UNICODE);
      $return_obj->content = $content;
      $query = "UPDATE response SET content='$content' WHERE pin='$response_pin'";
      mysqli_query($db, $query);
    }
  }

  echo json_encode($return_obj, JSON_UNESCAPED_UNICODE);
?>