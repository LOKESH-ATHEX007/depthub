<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "../functions.php"; 
include "../../session_start.php";

// Ensure the session has user details and department ID
if (!isset($_SESSION["dept_id"])) {
    echo json_encode(["status" => "error", "message" => "Department ID not found in session"]);
    exit();
}

$dept_id = $_SESSION["dept_id"];

// Use dbConnect()
$conn = dbConnect();
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Fetch the count of new complaints (status = 'Pending') for the specific department
$query = "SELECT COUNT(*) as new_complaint_count 
          FROM complaint c
          JOIN st1 s ON c.regno = s.regno
          WHERE c.status = 'Pending' AND s.department = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $dept_id);
$stmt->execute();
$result = $stmt->get_result();
$newComplaintCount = $result->fetch_assoc()["new_complaint_count"] ?? 0;

$stmt->close();
$conn->close();

echo json_encode(["new_complaint_count" => $newComplaintCount]);
?>