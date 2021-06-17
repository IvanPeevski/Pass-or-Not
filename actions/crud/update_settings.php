<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    $test_id = $_POST['test_id'];
    $public = $_POST['public'];
    $grading = $_POST['grading'];
    $one_per_one = $_POST['one_per_one'];
    $randomize_questions = $_POST['randomize_questions'];
    $randomize_answers = $_POST['randomize_answers'];
    $question_limit = $_POST['question_limit'];
    $time_limit = $_POST['time_limit'];
    $individual_time = $_POST['individual_time'];
    $require_profile = $_POST['require_profile'];
    $allow_anonymous = $_POST['allow_anonymous'];
    $limit_response = $_POST['limit_response'];
    $check_points = $_POST['check_points'];
    $check_answers = $_POST['check_answers'];
    $limit_check = $_POST['limit_check'];
    $team_limit = $_POST['team_limit'];
    $tags = $_POST['tags'];
    $query = "UPDATE `test` SET tags='$tags', public='$public', grading='$grading', one_per_one='$one_per_one', 
    randomize_questions='$randomize_questions', randomize_answers='$randomize_answers', 
    question_limit='$question_limit',
    time_limit='$time_limit', individual_time='$individual_time', require_profile='$require_profile', allow_anonymous='$allow_anonymous', limit_response='$limit_response', 
    check_points='$check_points', check_answers='$check_answers', limit_check='$limit_check', team_limit='$team_limit' WHERE `id` = $test_id";;
    $results = mysqli_query($db, $query);
?>