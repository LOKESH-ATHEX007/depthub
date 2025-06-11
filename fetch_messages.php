<?php
header("Content-Type: application/json"); // Ensure JSON output
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "db.php";
include "session_start.php";

if (!isset($_SESSION["user_email"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$sender = $_SESSION["user_email"];
$receiver = isset($_GET["receiver"]) ? $_GET["receiver"] : "";

if (!$receiver) {
    echo json_encode(["status" => "error", "message" => "Receiver email is missing"]);
    exit();
}

// Fetch messages between sender and receiver
$stmt = $conn->prepare("SELECT * FROM messages 
    WHERE ((sender_email = ? AND receiver_email = ?) 
    OR (sender_email = ? AND receiver_email = ?)) 
    ORDER BY timestamp ASC");

$stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

// ✅ Mark messages as read when fetched
$updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_email = ? AND receiver_email = ? AND is_read = 0");
$updateStmt->bind_param("ss", $receiver, $sender);
$updateStmt->execute();
$updateStmt->close();

echo json_encode($messages);
?>
