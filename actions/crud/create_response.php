<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    function getRndNumbers($int){
        $num="";
        for ($i = 0; $i<$int; $i++) 
        {
          $num .= mt_rand(0,9);
        }
        return $num;
    }
    
    $testpin = $_POST['pin'];
    $get_test_query = "SELECT * FROM test WHERE pin='$testpin'";
    $test =mysqli_fetch_assoc(mysqli_query($db, $get_test_query));
    $id = $test['id'];
    $test_content = json_decode($test['content']);
    $counter = count($test_content);
    if($test['question_limit']!='0'){
        $counter = min(count($test_content), (int)$test['question_limit']);
    }
    $response_content = [];
    if($test['randomize_questions']=='1'){
        shuffle($test_content);
    }
    for($i=0; $i<$counter; $i++){
        $obj = new stdClass();
        $obj->id =$test_content[$i]->id;
        array_push($response_content, $obj);
    }
    $response_content = json_encode($response_content, JSON_UNESCAPED_UNICODE);
    $response_pin = '';
    do{
        $response_pin = getRndNumbers(8);
        $response_pin_query = "SELECT * FROM response WHERE pin = '$response_pin' LIMIT 1";
        $results = mysqli_query($db, $response_pin_query);
        $result = mysqli_fetch_assoc($results);
    } while($result);
    $name = $_POST['name'];
    $class = $_POST['class'];
    $division = $_POST['division'];
    $date = date('Y-m-d H:i:s');
    if(isset($_SESSION['username'])){
        $user = $_SESSION['id'];
        $query = "INSERT INTO response (pin, user_id, name, class, division, test_id, content, started, state) VALUES ('$response_pin','$user', '$name','$class','$division','$id', '$response_content', '$date', 'active')";
    }
    else{
        $query = "INSERT INTO response (pin, name, class, division, test_id, content, started, state) VALUES ('$response_pin','$name','$class','$division','$id', '$response_content', '$date', 'active')";
    }
    $insert = mysqli_query($db, $query);
    echo $response_pin;
?>