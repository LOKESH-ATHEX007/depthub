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

try {
    // Initialize arrays for results
    $pendingRequests = [];
    $underProcessRequests = [];

    // Fetch Pending requests with department filter
    $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.class = ? AND sr.currentYear = ? AND s.department = ? AND sr.status = 'Pending' 
              ORDER BY sr.requestDate DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $class, $currentYear, $dept_id);
    mysqli_stmt_execute($stmt);
    $pendingRequests = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    // Fetch Under Process requests with department filter
    $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.class = ? AND sr.currentYear = ? AND s.department = ? AND sr.status = 'Under Process' 
              ORDER BY sr.requestDate DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $class, $currentYear, $dept_id);
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