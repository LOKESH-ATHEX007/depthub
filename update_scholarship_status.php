<?php
include 'scholarshipphp/db.php'; // Include your database connection file

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid request.']));
}

$id = $_POST['id'];
$status = $_POST['status'];

// Debugging: Log the received data
error_log("Received ID: $id, Status: $status");

// Update the status in the database
$query = "UPDATE scholarship_requests SET status = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    error_log("Failed to prepare statement: " . mysqli_error($conn));
    die(json_encode(['success' => false, 'message' => 'Database error.']));
}

mysqli_stmt_bind_param($stmt, "si", $status, $id);
$executed = mysqli_stmt_execute($stmt);

if ($executed && mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['success' => true, 'id' => $id, 'newStatus' => $status]);
} else {
    error_log("Failed to execute statement: " . mysqli_stmt_error($stmt));
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}

mysqli_stmt_close($stmt);
?>