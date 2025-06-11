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

// Fetch the teacher's assigned class and year
$T_id = $_SESSION["T_id"];
$stmt = $conn->prepare("SELECT class, currentYear FROM class_teacher_allocation WHERE T_id = ?");
$stmt->bind_param("s", $T_id);
$stmt->execute();
$classResult = $stmt->get_result();
$classData = $classResult->fetch_assoc();

$class = $classData['class'];
$currentYear = $classData['currentYear'];

// Fetch unread messages count from students in the teacher's assigned class
$query = "SELECT COUNT(*) as unread_count 
          FROM messages m
          JOIN st1 s ON m.sender_email = s.email
          WHERE m.receiver_email = ? 
            AND m.is_read = 0
            AND s.class = ?
            AND s.currentYear = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $user_email, $class, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
$unreadCount = $result->fetch_assoc()["unread_count"] ?? 0;

$stmt->close();
echo json_encode(["unread_count" => $unreadCount]);
?>