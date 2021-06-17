<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
$pin = $_POST['test_pin'];
$query = "SELECT * FROM test WHERE pin='$pin'";
$results = mysqli_query($db, $query);
$test = mysqli_fetch_assoc($results);
$test_id = $test['id'];
$content = json_decode($test['content']);
$answers=[];
$date = date('Y-m-d H:i:s');
$name_field = $_POST["name_field"];
$class = $_POST["class"];
$division = $_POST["division"];
$guest = $_POST["guest"];
if($guest=='true'){
    $name_field = 'Лиспва име';
    $class = '';
    $division= '';
}
if($test['unlocked'] == '0'){
    $name_field = $name_field.' - Изпратен след заключване';
}

$regex = '/(?<=question-)\d+(?=-answers)/m';
$sent_answers = array_filter(
    $_POST,
    function ($key) use ($regex){
        return preg_match($regex, $key);
    },
    ARRAY_FILTER_USE_KEY
);
foreach ($sent_answers as $key => $value){
    preg_match_all($regex, $key, $matches, PREG_SET_ORDER, 0);
    $index = $matches[0][0];
    if($content[$index]->type=='file'){
        if(basename($_FILES[$key]["name"]!='')){
            $target_dir = "/files/";
            $name=basename($_FILES[$key]["name"]);
            $name = explode('.',$name);
            if($name[count($name)-1]=='php'){
                $name[count($name)-1]='txt';
            }
            $name = join('.',$name);
            $target_file = $target_dir .uniqid().'-'.time().'-'.$name;
            move_uploaded_file($_FILES[$key]["tmp_name"], $_SERVER['DOCUMENT_ROOT'].$target_file);
            $query = "INSERT INTO file (name, href) VALUES ('$name','$target_file')";
            mysqli_query($db, $query);
            $last_fileId = mysqli_insert_id($db);
            array_push($answers, ['id' => $index[0], 'file' => $last_fileId,'graded'=>false, 'points'=>0]);
        } 
        else{
            array_push($answers, ['id' => $index[0], 'file' => null,'graded'=>false, 'points'=>0]);
        }
    }
    else if($content[$index]->type=='text'){
        array_push($answers, ['id' => $index[0], 'text' => htmlspecialchars($_POST[$key]), 'graded'=>false, 'points'=>0]);
    }else{
        array_push($answers, ['id' => $index[0], 'value' => $_POST[$key]]);
    }
}

$answers = json_encode($answers, JSON_UNESCAPED_UNICODE );
function getRndNumbers($int){
    $num="";
    for ($i = 0; $i<$int; $i++) 
    {
        $num .= mt_rand(0,9);
    }
    return $num;
}
$index='';
do{
    $index = getRndNumbers(8);
    $index_query = "SELECT * FROM response WHERE pin = '$index' LIMIT 1";
    $results = mysqli_query($db, $index_query);
    $result = mysqli_fetch_assoc($results);
} while($result);

if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $query = "SELECT id FROM user WHERE  username='$username'";
    $results = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($results);
    $userId = $user['id'];
    $query = "INSERT INTO response (pin, user_id, name, class, division, test_id, content, sent_on) VALUES ('$index','$userId','$name_field','$class','$division','$test_id', '$answers', '$date')";
}
else{
    $query = "INSERT INTO response ( pin, name, class, division, test_id, content, sent_on) VALUES ('$index','$name_field','$class','$division','$test_id', '$answers', '$date')";
}
mysqli_query($db, $query);
header('Location: /viewscore.php?viewscore='.$index);
?>