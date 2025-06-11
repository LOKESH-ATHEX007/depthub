<?php
header("Content-Type: application/json");
include "db.php";
session_start();

if (!isset($_SESSION["user_email"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_email = $_SESSION["user_email"];
$user_type = $_SESSION["user_type"];

$unreadCounts = [];

if ($user_type === "student") {
    // Student: Get unread messages from teachers
    $query = "SELECT sender_email, COUNT(*) as unread_count 
              FROM messages 
              WHERE receiver_email = ? AND is_read = 0 
              GROUP BY sender_email";
} else if ($user_type === "teacher") {
    // Teacher: Get unread messages from students
    $query = "SELECT sender_email, COUNT(*) as unread_count 
              FROM messages 
              WHERE receiver_email = ? AND is_read = 0 
              GROUP BY sender_email";
} else {
    echo json_encode(["status" => "error", "message" => "Invalid user type"]);
    exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $unreadCounts[$row["sender_email"]] = $row["unread_count"];
}

$stmt->close();
echo json_encode($unreadCounts);
exit;
?>
