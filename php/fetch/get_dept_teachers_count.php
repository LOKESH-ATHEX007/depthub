<?php
session_start();
header('Content-Type: application/json');

// Database connection
$conn = new PDO("mysql:host=localhost;dbname=depthub", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if department admin is logged in and has department ID
if (!isset($_SESSION['dept_admin_id']) || !isset($_SESSION['dept_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Get department ID from session
    $dept_id = $_SESSION['dept_id'];
    
    // Count active teachers in the department
    $stmt = $conn->prepare("SELECT COUNT(*) as total_teachers FROM teachers WHERE department = ? AND status = 'active'");
    $stmt->execute([$dept_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'total_teachers' => $result['total_teachers'],
        'department_id' => $dept_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>