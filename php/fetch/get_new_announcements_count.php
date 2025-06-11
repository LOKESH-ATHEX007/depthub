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

// Use dbConnect()
$conn = dbConnect();
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$user_email = $_SESSION["user_email"];
$user_type = $_SESSION["user_type"];

// Fetch the user's group (if they are a student)
if ($user_type === "student") {
    $stmt = $conn->prepare("SELECT currentYear, class FROM st1 WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if ($userData) {
        $currentYear = $userData["currentYear"];
        $class = $userData["class"];
        $userGroup = $currentYear . $class; // e.g., "2023A"
    } else {
        echo json_encode(["status" => "error", "message" => "User data not found"]);
        exit();
    }
} else {
    // For teachers/admins, they receive announcements targeted to "everyone" or their specific group
    $userGroup = "everyone"; // Default group for non-students
}

// Fetch the count of unread announcements for the user
$query = "SELECT COUNT(*) as new_announcement_count 
          FROM announcements 
          WHERE is_read = 0 
            AND (sentTo = 'everyone' 
                 OR sentTo = ? 
                 OR sentTo = ?) 
            AND (expiryDate IS NULL OR expiryDate >= CURDATE())";
$stmt = $conn->prepare($query);

if ($user_type === "student") {
    $stmt->bind_param("ss", $currentYear, $userGroup); // Bind $currentYear and $userGroup
} else {
    $stmt->bind_param("ss", $userGroup, $userGroup); // Bind $userGroup twice for non-students
}

$stmt->execute();
$result = $stmt->get_result();
$newAnnouncementCount = $result->fetch_assoc()["new_announcement_count"] ?? 0;

$stmt->close();
echo json_encode(["new_announcement_count" => $newAnnouncementCount]);
?>