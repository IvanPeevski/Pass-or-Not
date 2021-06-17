<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
$error = '';

$username = mysqli_real_escape_string($db, $_POST['name']);
$password = md5(mysqli_real_escape_string($db, $_POST['password']));
$query = "SELECT * FROM user WHERE  username='$username' AND password='$password'";
$results = mysqli_query($db, $query);
if(mysqli_num_rows($results)){
    $account = mysqli_fetch_assoc($results);
    $_SESSION['id'] = $account['id'];
    $_SESSION['username'] = $account['username'];
    $_SESSION['fullName']= $account['first_name'].' '.$account['surname'];
    $_SESSION['class']= $account['class'];
    $_SESSION['division']= $account['division'];
}
else{
    $error = "Грешно потребителско име/парола";
    echo $error;
}
?>