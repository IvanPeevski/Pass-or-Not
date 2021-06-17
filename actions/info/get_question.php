<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    $obj = new stdClass();
    $obj->info = '';
    $response_pin = $_POST['response_pin'];
    $test_pin = $_POST['test_pin'];
    $get_test_query = "SELECT * FROM test WHERE pin='$test_pin'";
    $test = mysqli_fetch_assoc(mysqli_query($db, $get_test_query));
    $test_content = json_decode($test['content']);
    
    $get_response_query = "SELECT * FROM response WHERE pin='$response_pin'";
    $response = mysqli_fetch_assoc(mysqli_query($db, $get_response_query));
    $response_content = json_decode($response['content']);

    $question_index;
    $filtered = array_filter($response_content, function($answer){
        if(!property_exists($answer, 'value') && !isset($answer->file) && !isset($answer->text)){
            return true; 
        }
        else {return false;}
    });
    $filtered = array_values($filtered);
    $question_index = $filtered[0]->id;

    $question_content = $test_content[$question_index];
    if(isset($question_content->file)){
        $index = $question_content->file;
        $get_file_query = "SELECT * FROM `file` WHERE `id`=$index";
        $result = mysqli_query($db, $get_file_query);
        $file = mysqli_fetch_assoc($result);
        $question_content->file = array(
            'src'=>$file['href'],
            'name'=>$file['name']
        );
    }
    if($question_content->type=='radio' || $question_content->type=='checkbox'){
        for($i=0; $i<count($question_content->answers); $i++){
            if(isset($question_content->answers[$i]->file)){
                $index = $question_content->answers[$i]->file;
                $get_file_query = "SELECT * FROM `file` WHERE `id`=$index";
                $result = mysqli_query($db, $get_file_query);
                $file = mysqli_fetch_assoc($result);
                $question_content->answers[$i]->file = array(
                    'src'=>$file['href'],
                    'name'=>$file['name']
                );
            }
            unset($question_content->answers[$i]->correct); 
        }
        if($test['randomize_answers']=='1'){
            shuffle($question_content->answers);
        }
    }
    $obj->response = $question_content;
    if(count($filtered)==1){
        $obj->info = 'last';
    }
    echo json_encode($obj, JSON_UNESCAPED_UNICODE);
?>