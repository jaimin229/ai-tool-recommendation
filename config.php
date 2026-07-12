<?php
$host = "localhost";
$username = "root"; 
$password = "";     
$database = "ai_tool_portal";

// This line actually connects to phpMyAdmin
$conn = new mysqli($host, $username, $password, $database);

// This checks if it worked, and throws an error if it failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>