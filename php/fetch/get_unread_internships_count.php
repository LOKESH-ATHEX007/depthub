<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$database = "depthub";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

session_start();
$regno = $_SESSION['regno']; // Assuming the student's regno is stored in the session

// Fetch the student's current year and class
$query = "SELECT currentYear, class FROM st1 WHERE regno = $regno";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Error fetching student details: " . mysqli_error($conn)]);
    exit();
}

$student = mysqli_fetch_assoc($result);
$currentYear = $student['currentYear'];
$class = $student['class'];

// Fetch the count of unread internships
$query = "
    SELECT COUNT(*) as unread_count 
    FROM internships 
    WHERE is_read = 0 
      AND (sentTo = 'everyone' 
           OR sentTo = '$currentYear' 
           OR sentTo = '$currentYear$class') 
      AND (expiryDate IS NULL OR expiryDate >= CURDATE())";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo json_encode(["status" => "error", "message" => "Error fetching unread internships count: " . mysqli_error($conn)]);
    exit();
}

$row = mysqli_fetch_assoc($result);
$unreadCount = $row['unread_count'] ?? 0;

echo json_encode(["unread_count" => $unreadCount]);

$conn->close();
?>