<?php 
session_start();
date_default_timezone_set("Europe/Sofia");
$db = mysqli_connect('localhost', 'root', '', 'passornot');
$db->set_charset("utf8");
?>