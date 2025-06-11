<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ InfinityFree database credentials
$servername = "sql202.infinityfree.com";           // Hostname from InfinityFree
$username   = "if0_39191720";                      // InfinityFree MySQL username
$password   = "JqlHPQjc3rGSk";                     // InfinityFree MySQL password
$database   = "if0_39191720_depthub";              // Your database name

// ✅ Create connection
$conn = new mysqli($servername, $username, $password, $database);

// ❌ Connection failed
if ($conn->connect_error) {
    die("🔴 Connection failed: " . $conn->connect_error . 
        "<br>Please check your InfinityFree DB credentials or access from Control Panel.");
}

// ✅ Set proper charset (for emojis, special characters, etc.)
$conn->set_charset("utf8mb4");

// ✅ Optional: Uncomment this line for success debug (development only)
// echo "✅ Connected to InfinityFree MySQL successfully!";
?>
