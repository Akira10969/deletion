<?php
$host = 'localhost';        // or your DB host
$db   = 'deletion';    // change this to your actual DB name
$user = 'root';    // change this to your DB username
$pass = '';    // change this to your DB password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
