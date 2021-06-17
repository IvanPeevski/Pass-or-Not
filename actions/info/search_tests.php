<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
$search_string = $_POST['search_string'];
$search_query = "SELECT test.test_name, test.pin, test.id, CONCAT(user.first_name,' ',user.surname) AS fullName FROM test INNER JOIN user ON test.user_id = user.id WHERE (test.test_name LIKE CONCAT('%','$search_string','%') OR test.tags LIKE CONCAT('%','$search_string','%')) AND test.public=1";
$tests = mysqli_fetch_all(mysqli_query($db, $search_query), MYSQLI_ASSOC);
echo json_encode($tests)
?>