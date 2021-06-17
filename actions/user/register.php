<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
$errors = array();

$username = mysqli_real_escape_string($db, $_POST['name']);
$email = mysqli_real_escape_string($db, $_POST['email']);
$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
$firstName = mysqli_real_escape_string($db, $_POST['firstName']);
$surname = mysqli_real_escape_string($db, $_POST['surname']);
$role = mysqli_real_escape_string($db, $_POST['role']);
$class = mysqli_real_escape_string($db, $_POST['class']);
$division = mysqli_real_escape_string($db, $_POST['division']);


$user_query = "SELECT * FROM user WHERE username = '$username' OR email = '$email' LIMIT 1";
$results = mysqli_query($db, $user_query);
$user = mysqli_fetch_assoc($results);
if($user){
    if($user['username'] == $username){
        array_push($errors,"Потребителското име е заето");
    }
    if($user['email'] == $email){
        array_push($errors,"Имейлът е вече изплозван");
    }
}
if(empty($username)){ array_push($errors,"Потребителксото име е задължително");}
if(empty($email)){ array_push($errors,"Имейлът е задължителен");}
if(empty($firstName)){ array_push($errors,"Името е задължително");}
if(empty($surname)){ array_push($errors,"Фамилията е задължителна");}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    array_push($errors,"Невалиден имейл адрес");
}
if(empty($password_1)){ array_push($errors,"Паролата е задължителна");}
if($password_1 != $password_2){
    array_push($errors,"Паролите не съвпадат");
}
if(empty($role)){array_push($errors, "Изберете типа акаунт");}
if($role=='student' && empty($class)){array_push($errors, "Въведете вашия клас");}
if(count($errors)==0){
    $password = md5($password_1);
    $query = "INSERT INTO user (username, email, password, first_name, surname, role) VALUES ('$username', '$email', '$password', '$firstName', '$surname', '$role')";
    if($role=='student'){$query = "INSERT INTO user (username, email, password, first_name, surname, role, class, division) VALUES ('$username', '$email', '$password', '$firstName', '$surname', '$role', '$class', '$division')";}
    mysqli_query($db, $query);
    $_SESSOIN['id'] = mysqli_insert_id($db);
    $_SESSION['username']= $username;
    $_SESSION['fullName']= $firstName.' '.$surname;
    $_SESSION['class']= $class;
    $_SESSION['division']= $division;
}
else{
    foreach($errors as $error) {
        echo '<div>'.$error.'</div>';
    }
}
?>