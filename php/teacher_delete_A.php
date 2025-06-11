<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['dept_admin_id'], $_SESSION['dept_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Access Denied']));
}

$conn = new mysqli("localhost", "root", "", "depthub");
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

$t_id = $_POST['t_id'] ?? '';
if (empty($t_id)) {
    http_response_code(400);
    die(json_encode(['error' => 'Teacher ID required']));
}

// Verify teacher belongs to admin's department
$check_query = "SELECT T_id FROM teachers WHERE T_id = ? AND department = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $t_id, $_SESSION['dept_id']);
$stmt->execute();

if (!$stmt->get_result()->num_rows > 0) {
    http_response_code(404);
    die(json_encode(['error' => 'Teacher not found in your department']));
}

// Delete teacher
$delete_query = "DELETE FROM teachers WHERE T_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("s", $t_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Teacher deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Deletion failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>