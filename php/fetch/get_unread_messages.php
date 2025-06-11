<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "../functions.php"; 
include "../../session_start.php";

// Ensure the session has user details
if (!isset($_SESSION["user_email"]) || !isset($_SESSION["user_type"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_email = $_SESSION["user_email"]; // Common for both student & teacher
$user_type = $_SESSION["user_type"]; // Identify if student or teacher

// Use dbConnect()
$conn = dbConnect();
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Fetch unread messages count for the logged-in user (student or teacher)
$query = "SELECT COUNT(*) as unread_count FROM messages 
          WHERE receiver_email = ? AND is_read = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$unreadCount = $result->fetch_assoc()["unread_count"] ?? 0;

$stmt->close();
echo json_encode(["unread_count" => $unreadCount]);
?>
