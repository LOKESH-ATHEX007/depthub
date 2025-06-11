<?php
// get_total_students.php

session_start();
header('Content-Type: application/json');

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=depthub", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if teacher is logged in
if (!isset($_SESSION['T_id'])) {
    echo json_encode(['error' => 'Teacher not logged in']);
    exit;
}

$teacherId = $_SESSION['T_id'];

try {
    // Step 1: Get the class, currentYear, and dept_id from class_teacher_allocation table
    $stmt = $conn->prepare("SELECT class, currentYear, dept_id FROM class_teacher_allocation WHERE T_id = ?");
    $stmt->execute([$teacherId]);
    $allocationData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$allocationData) {
        echo json_encode(['error' => 'Teacher is not assigned as a class teacher']);
        exit;
    }
    
    $class = $allocationData['class'];
    $currentYear = $allocationData['currentYear'];
    $dept_id = $allocationData['dept_id'];
    
    // Step 2: Count students in st1 table that match the criteria
    $stmt = $conn->prepare("SELECT COUNT(*) as total_students FROM st1 
                           WHERE class = ? AND currentYear = ? AND department = ?");
    $stmt->execute([$class, $currentYear, $dept_id]);
    $countData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['total_students' => $countData['total_students']]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>