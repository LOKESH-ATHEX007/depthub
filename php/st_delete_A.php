<?php
session_start();

// Verify admin is logged in and has department permissions
if (!isset($_SESSION['dept_admin_id'], $_SESSION['dept_id'])) {
    header('HTTP/1.1 403 Forbidden');
    die(json_encode(['error' => 'Access Denied']));
}

// Database connection
$conn = new mysqli("localhost", "root", "", "depthub");
if ($conn->connect_error) {
    header('HTTP/1.1 500 Internal Server Error');
    die(json_encode(['error' => 'Database connection failed']));
}

// Get and validate input
$regno = $_POST['regno'] ?? '';
if (empty($regno)) {
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'Registration number required']));
}

// Verify student belongs to admin's department before deletion
$check_query = "SELECT regno FROM st1 WHERE regno = ? AND department = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $regno, $_SESSION['dept_id']);
$stmt->execute();

if (!$stmt->get_result()->num_rows > 0) {
    header('HTTP/1.1 404 Not Found');
    die(json_encode(['error' => 'Student not found in your department']));
}

// Proceed with deletion
$delete_query = "DELETE FROM st1 WHERE regno = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("s", $regno);

if ($stmt->execute()) {
    // Also delete related records (example)
    $conn->query("DELETE FROM scholarship_requests WHERE regno = '$regno'");
    
    echo json_encode(['success' => true]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Deletion failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>