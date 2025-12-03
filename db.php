<?php
// Database connection details
$host = "localhost";
$user = "root";       
$pass = "";            
$db   = "quickbite_db";

// Establish connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>