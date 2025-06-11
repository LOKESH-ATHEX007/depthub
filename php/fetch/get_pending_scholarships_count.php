<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "../functions.php"; 
include "../../session_start.php";

$response = ["pending_scholarship_count" => 0];

try {
    $conn = dbConnect();
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Verify teacher is logged in
    if (!isset($_SESSION['T_id'])) {
        throw new Exception("Unauthorized access - Teacher ID not found in session");
    }

    $teacher_id = $_SESSION['T_id'];

    // Get teacher's single allocation (since one teacher handles one class)
    $query = "SELECT class, currentYear, dept_id FROM class_teacher_allocation WHERE T_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $teacher_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $allocation = $result->fetch_assoc();
        $class = $allocation['class'];
        $currentYear = $allocation['currentYear'];
        $dept_id = $allocation['dept_id'];
        
        // Count pending requests for this specific class/year/dept
        $query = "SELECT COUNT(*) as count 
                FROM scholarship_requests 
                WHERE status = 'Pending' 
                AND class = ? 
                AND currentYear = ? 
                AND dept_id = ?";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $class, $currentYear, $dept_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'] ?? 0;
        $response["pending_scholarship_count"] = $count;
    }

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