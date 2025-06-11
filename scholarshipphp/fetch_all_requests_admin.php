<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';

try {
    // Check if admin is logged in
    if (!isset($_SESSION['dept_admin_id'])) {
        throw new Exception('Admin not logged in');
    }

    $dept_admin_id = $_SESSION['dept_admin_id'];

    // Get the department ID of the logged-in admin
    // MODIFIED: Changed column name from 'id' to your actual primary key column name
    $dept_query = "SELECT dept_id FROM dept_admins WHERE dept_admin_id = ?";
    if (!($dept_stmt = mysqli_prepare($conn, $dept_query))) {
        throw new Exception('Failed to prepare department query: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($dept_stmt, "s", $dept_admin_id);
    if (!mysqli_stmt_execute($dept_stmt)) {
        throw new Exception('Failed to execute department query: ' . mysqli_stmt_error($dept_stmt));
    }
    
    $dept_result = mysqli_stmt_get_result($dept_stmt);
    
    if (mysqli_num_rows($dept_result) === 0) {
        throw new Exception('Admin department not found');
    }

    $dept_row = mysqli_fetch_assoc($dept_result);
    $dept_id = $dept_row['dept_id'];

    // Initialize arrays for results
    $underProcessRequests = [];
    $approvedRequests = [];
    $rejectedRequests = [];

    // Function to execute department-filtered queries
    function fetchRequests($conn, $status, $dept_id) {
        $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
                  FROM scholarship_requests sr 
                  JOIN st1 s ON sr.regno = s.regno 
                  WHERE sr.status = ? 
                  AND s.department = ?
                  ORDER BY sr.requestDate DESC";
        
        if (!($stmt = mysqli_prepare($conn, $query))) {
            throw new Exception('Failed to prepare query: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $status, $dept_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute query: ' . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Fetch requests for each status
    $underProcessRequests = fetchRequests($conn, 'Under Process', $dept_id);
    $approvedRequests = fetchRequests($conn, 'Approved', $dept_id);
    $rejectedRequests = fetchRequests($conn, 'Rejected', $dept_id);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'underProcess' => $underProcessRequests,
        'approved' => $approvedRequests,
        'rejected' => $rejectedRequests
    ]);
} catch (Exception $e) {
    // Handle errors and return a JSON response
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>