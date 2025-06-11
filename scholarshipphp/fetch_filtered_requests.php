<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['T_id'])) {
    die(json_encode(['error' => 'Unauthorized access.']));
}

$T_id = $_SESSION['T_id'];

// Fetch the teacher's allocated class and year from the database
$teacherQuery = "SELECT class, currentYear FROM class_teacher_allocation WHERE T_id = ?";
$stmt = mysqli_prepare($conn, $teacherQuery);
if (!$stmt) {
    die(json_encode(['error' => 'Failed to prepare teacher query: ' . mysqli_error($conn)]));
}
mysqli_stmt_bind_param($stmt, "s", $T_id);
if (!mysqli_stmt_execute($stmt)) {
    die(json_encode(['error' => 'Failed to execute teacher query: ' . mysqli_stmt_error($stmt)]));
}
$teacherResult = mysqli_stmt_get_result($stmt);
$teacherDetails = mysqli_fetch_assoc($teacherResult);

if (!$teacherDetails) {
    die(json_encode(['error' => 'Teacher details not found in the database.']));
}

$class = $teacherDetails['class'];
$currentYear = $teacherDetails['currentYear'];

// Get the start and end dates from the query parameters
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    die(json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD.']));
}

// Initialize arrays for results
$pendingRequests = [];
$underProcessRequests = [];

// Fetch pending requests within the date range
$pendingQuery = "SELECT sr.id, sr.regno, sr.requestDate, s.stName, sr.status 
                 FROM scholarship_requests sr 
                 JOIN st1 s ON sr.regno = s.regno 
                 WHERE sr.class = ? AND sr.currentYear = ? AND sr.status = 'Pending' 
                 AND sr.requestDate BETWEEN ? AND ? 
                 ORDER BY sr.requestDate DESC";
$stmt = mysqli_prepare($conn, $pendingQuery);
if (!$stmt) {
    die(json_encode(['error' => 'Failed to prepare pending query: ' . mysqli_error($conn)]));
}
mysqli_stmt_bind_param($stmt, "ssss", $class, $currentYear, $startDate, $endDate);
if (!mysqli_stmt_execute($stmt)) {
    die(json_encode(['error' => 'Failed to execute pending query: ' . mysqli_stmt_error($stmt)]));
}
$pendingResult = mysqli_stmt_get_result($stmt);
if ($pendingResult) {
    $pendingRequests = mysqli_fetch_all($pendingResult, MYSQLI_ASSOC);
} else {
    die(json_encode(['error' => 'Failed to fetch pending requests: ' . mysqli_error($conn)]));
}

// Fetch under process requests within the date range
$underProcessQuery = "SELECT sr.id, sr.regno, sr.requestDate, s.stName, sr.status 
                      FROM scholarship_requests sr 
                      JOIN st1 s ON sr.regno = s.regno 
                      WHERE sr.class = ? AND sr.currentYear = ? AND sr.status = 'Under Process' 
                      AND sr.requestDate BETWEEN ? AND ? 
                      ORDER BY sr.requestDate DESC";
$stmt = mysqli_prepare($conn, $underProcessQuery);
if (!$stmt) {
    die(json_encode(['error' => 'Failed to prepare under process query: ' . mysqli_error($conn)]));
}
mysqli_stmt_bind_param($stmt, "ssss", $class, $currentYear, $startDate, $endDate);
if (!mysqli_stmt_execute($stmt)) {
    die(json_encode(['error' => 'Failed to execute under process query: ' . mysqli_stmt_error($stmt)]));
}
$underProcessResult = mysqli_stmt_get_result($stmt);
if ($underProcessResult) {
    $underProcessRequests = mysqli_fetch_all($underProcessResult, MYSQLI_ASSOC);
} else {
    die(json_encode(['error' => 'Failed to fetch under process requests: ' . mysqli_error($conn)]));
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'pending' => $pendingRequests,
    'underProcess' => $underProcessRequests
]);
?>