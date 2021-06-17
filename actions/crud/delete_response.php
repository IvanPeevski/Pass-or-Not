<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  
  $response_id = $_POST['response_id'];
  $query = "SELECT response.content, username FROM response 
  INNER JOIN test ON response.test_id = test.id 
  INNER JOIN user ON test.user_id = user.id
  WHERE response.id = $response_id";
  $result = mysqli_fetch_assoc(mysqli_query($db, $query));
  if($_SESSION['username'] == $result['username']){
    $content = json_decode($response['content']);
    foreach($content as $answer){
      if(isset($answer->file)){
        $file_id = $answer->file;
        $file_query = "SELECT href FROM `file` WHERE `file`.`id` = $file_id";
        $file = mysqli_fetch_assoc(mysqli_query($db, $file_query));
        unlink($_SERVER['DOCUMENT_ROOT'].$file['href']);
        $file_query = "DELETE FROM `file` WHERE `file`.`id` = $file_id";
        mysqli_query($db, $file_query);
      }
    }
    $query = "DELETE FROM response WHERE id=$response_id";
    mysqli_query($db, $query);
  }
?>