<?php
session_start();
include 'scholarshipphp/db.php';

// Check if the user is logged in
if (!isset($_SESSION['T_id'])) {
    die(json_encode(['error' => 'Unauthorized access.']));
}

// Get the ID from the query parameters
$id = $_GET['id'] ?? null;

if (!$id) {
    die(json_encode(['error' => 'Invalid request.']));
}

// Fetch the single request
$query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
          FROM scholarship_requests sr 
          JOIN st1 s ON sr.regno = s.regno 
          WHERE sr.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row) {
    // Return the row data as JSON
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    die(json_encode(['error' => 'Request not found.']));
}
?>