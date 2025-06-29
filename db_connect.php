<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = ""; // default WAMP password is empty
$database = "caters_db";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user inputs
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to hash passwords
function hash_password($password) {
    return md5($password); // Using MD5 for simplicity, consider using password_hash() in production
}

// Function to verify passwords
function verify_password($password, $hash) {
    return md5($password) === $hash;
}

// Function to check if user is logged in
function is_user_logged_in() {
    return isset($_SESSION['user_name']);
}

// Function to check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_username']);
}

// Function to redirect
function redirect($location) {
    header("Location: $location");
    exit;
}
?>