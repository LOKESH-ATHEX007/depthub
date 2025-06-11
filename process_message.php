<?php
include "db.php";
session_start();

header("Content-Type: application/json");
$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user_email"])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    $sender = $_SESSION["user_email"];
    $receiver = $_POST["receiver"];
    $message = isset($_POST["message"]) ? $conn->real_escape_string($_POST["message"]) : "";
    
    // No file upload on InfinityFree
    $file_path = "";  // Always empty

    $query = "INSERT INTO messages (sender_email, receiver_email, message, file_path, timestamp) 
              VALUES ('$sender', '$receiver', '$message', '$file_path', NOW())";

    if ($conn->query($query)) {
        $response = ["status" => "success", "message" => "Message sent successfully"];
    } else {
        $response = ["status" => "error", "message" => "Database error: " . $conn->error];
    }
}

echo json_encode($response);
exit;
?>
