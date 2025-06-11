<?php
session_start();
include 'db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['T_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$T_id = $_SESSION['T_id'];

// Fetch teacher's allocated class, year, and department using prepared statements
$teacherQuery = "SELECT class, currentYear, dept_id FROM class_teacher_allocation WHERE T_id = ?";
$stmt = mysqli_prepare($conn, $teacherQuery);
mysqli_stmt_bind_param($stmt, "s", $T_id);
mysqli_stmt_execute($stmt);
$teacherResult = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($teacherResult);

if (!$teacher) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Teacher details not found.']);
    exit();
}

// Verify all required fields exist
if (!isset($teacher['class']) || !isset($teacher['currentYear']) || !isset($teacher['dept_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Incomplete teacher allocation data. Missing required fields.']);
    exit();
}

$class = $teacher['class'];
$currentYear = $teacher['currentYear'];
$dept_id = $teacher['dept_id'];

// Get the start and end dates from the query parameters
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD.']);
    exit();
}

if ($startDate > $endDate) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Start date cannot be after end date.']);
    exit();
}

try {
    // Initialize arrays for results
    $pendingRequests = [];
    $underProcessRequests = [];

    // Fetch Pending requests with department filter
    $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.class = ? AND sr.currentYear = ? AND s.department = ? AND sr.status = 'Pending' 
              AND sr.requestDate BETWEEN ? AND ? 
              ORDER BY sr.requestDate DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $class, $currentYear, $dept_id, $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $pendingRequests = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    // Fetch Under Process requests with department filter
    $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.class = ? AND sr.currentYear = ? AND s.department = ? AND sr.status = 'Under Process' 
              AND sr.requestDate BETWEEN ? AND ? 
              ORDER BY sr.requestDate DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $class, $currentYear, $dept_id, $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $underProcessRequests = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'pending' => $pendingRequests,
        'underProcess' => $underProcessRequests
    ]);
} catch (Exception $e) {
    // Handle errors and return a JSON response
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>