<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "db_attendance";

try {
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}