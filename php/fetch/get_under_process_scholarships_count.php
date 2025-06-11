<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "../functions.php"; 
include "../../session_start.php";

$response = ["under_process_scholarship_count" => 0];

try {
    $conn = dbConnect();
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Verify department admin is logged in
    if (!isset($_SESSION['dept_admin_id']) && !isset($_SESSION['dept_id'])) {
        throw new Exception("Unauthorized access - Department admin not logged in");
    }

    // Get department ID from session (prefer dept_id if available)
    $dept_id = $_SESSION['dept_id'] ?? null;

    // If dept_id isn't in session, we might need to fetch it (commented out as you mentioned we can use dept_id directly)
    /*
    if (!$dept_id && isset($_SESSION['dept_admin_id'])) {
        $query = "SELECT dept_id FROM department_admins WHERE admin_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['dept_admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $dept_id = $result->fetch_assoc()['dept_id'];
        }
        $stmt->close();
    }
    */

    if (!$dept_id) {
        throw new Exception("Department ID not found");
    }

    // Count Under Process requests for this department
    $query = "SELECT COUNT(*) as count 
              FROM scholarship_requests 
              WHERE status = 'Under Process' AND dept_id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $dept_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'] ?? 0;
    $response["under_process_scholarship_count"] = $count;

} catch (Exception $e) {
    $response["error"] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
exit();
?>