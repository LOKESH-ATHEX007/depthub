<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ InfinityFree database credentials
$servername = "sql202.infinityfree.com";
$username   = "if0_39191720";
$password   = "JqlHPQjc3rGSk";
$database   = "if0_39191720_depthub";

// ✅ Create connection
$conn = new mysqli($servername, $username, $password, $database);

// ❌ Connection error handling
if ($conn->connect_error) {
    die("🔴 Connection failed: " . $conn->connect_error . 
        "<br>⚠️ Please check your InfinityFree database credentials.");
}

// ✅ Set UTF-8 charset
$conn->set_charset("utf8mb4");
?>
