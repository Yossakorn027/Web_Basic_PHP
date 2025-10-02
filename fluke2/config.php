<?php
$host = "localhost";
$username = "root";
$database = "db6645_027";
$password = "";

$dns = "mysql:host=$host;dbname=$database";

try {
    //$conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn = new PDO($dns, $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "PDO: Conected successfully";



} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();

}
?>