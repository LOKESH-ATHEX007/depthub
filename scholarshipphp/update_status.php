<?php
session_start();
include 'db.php';



// Get the request ID and new status from the POST data
$id = $_POST['id'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (!$id || !$newStatus) {
    die(json_encode(['success' => false, 'message' => 'Invalid input.']));
}

// Update the status in the database
$query = "UPDATE scholarship_requests SET status = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $newStatus, $id);

if (mysqli_stmt_execute($stmt)) {
    // Return a JSON response indicating success
    echo json_encode(['success' => true, 'message' => 'Status updated successfully!', 'newStatus' => $newStatus]);
} else {
    // Return a JSON response indicating failure
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}
?>