<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  $test_id = $_POST['test_id'];
  $auth_query = "SELECT test.id, username FROM `test` INNER JOIN user ON test.user_id=user.id WHERE `test`.`id` = $test_id";
  $check = mysqli_fetch_assoc(mysqli_query($db, $auth_query))['username']==$_SESSION['username'];

  if($check){
    $query = "DELETE FROM `test` WHERE `test`.`id` = $test_id";
    $results = mysqli_query($db, $query);
    
    $query = "SELECT * FROM `response` WHERE `response`.`test_id` = $id";
    $results = mysqli_query($db, $query);
    while($response = mysqli_fetch_assoc($results)){
      $content = json_decode($response['content']);
      foreach($content as $answer){
        if(property_exists($answer, 'file')){
          $file_id = $answer->file;
          $file_query = "SELECT href FROM `file` WHERE `file`.`id` = $file_id";
          $file = mysqli_fetch_assoc(mysqli_query($db, $file_query));
          unlink($_SERVER['DOCUMENT_ROOT'].$file['href']);
          $file_query = "DELETE FROM `file` WHERE `file`.`id` = $file_id";
          mysqli_query($db, $file_query);
        }
      }
    }

    $query = "DELETE FROM `response` WHERE `response`.`test_id` = $id";
    $results = mysqli_query($db, $query);
  
    $query = "SELECT * FROM `file` WHERE `file`.`test_id` = $id";
    $results = mysqli_query($db, $query);
    while( $row = mysqli_fetch_assoc($results)){
      unlink($_SERVER['DOCUMENT_ROOT'].$row['href']);
    } 
    $query = "DELETE FROM `file` WHERE `file`.`test_id` = $id";
    $results = mysqli_query($db, $query);
  }
?>