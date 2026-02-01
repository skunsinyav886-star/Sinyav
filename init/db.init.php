<?php
$host = '127.0.0.1';
$dbname = 'g19bcsy3c';
$user = 'root';
$pwd = '';
$port = '3306';


$db = new mysqli($host,$user,$pwd,$dbname,$port);

if ($db->connect_error) {
    echo $db->connect_error;
    die();

}


?>