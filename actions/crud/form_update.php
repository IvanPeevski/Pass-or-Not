<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    $pin = $_POST['pin'];
    $points = $_POST['points'];
    $questionId = $_POST['id'];
    $comment = $_POST['comment'];
    $query = "SELECT * FROM response WHERE pin='$pin'";
    $results = mysqli_query($db, $query);
    $response = mysqli_fetch_assoc($results);
    $content = json_decode($response['content']);
    for($i=0; $i<count($content);$i++){
        if($content[$i]->id==$questionId){
            $content[$i]->graded = true;
            $content[$i]->points = $points; 
            if($comment!=''){
                $content[$i]->comment = $comment; 
            }
            else{
                unset($content[$i]->comment);
            }
        }
    }
    $content = json_encode($content, JSON_UNESCAPED_UNICODE );
    $query = "UPDATE response SET content = '$content' WHERE pin='$pin'";
    $results = mysqli_query($db, $query);
?>