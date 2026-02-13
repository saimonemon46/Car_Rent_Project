<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          
define('DB_NAME', 'car');

// Connection using PDO
try{
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e){
    die("Connection failed: " . $e->getMessage());
}   

?>