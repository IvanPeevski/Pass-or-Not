<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    $test_id = $_POST['test_id'];
    $unlocked = $_POST['state'];
    $query = "UPDATE `test` SET unlocked='$unlocked' WHERE `id` = $test_id";;
    $results = mysqli_query($db, $query);
?>