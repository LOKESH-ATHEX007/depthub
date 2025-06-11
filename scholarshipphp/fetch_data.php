<?php
session_start();
include 'db.php';

// Validate session
if (!isset($_SESSION['T_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate request ID
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$id = $_GET['id'];

// Fetch updated request data
$query = "SELECT status FROM scholarship_requests WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$request = mysqli_fetch_assoc($result);

if (!$request) {
    echo json_encode(['success' => false, 'message' => 'Request not found']);
    exit();
}

// Return the updated status
echo json_encode(['success' => true, 'status' => $request['status']]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>