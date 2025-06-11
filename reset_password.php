<?php
include("php/functions.php");

// Database connection
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $adminId = $_POST['admin_id'];
    
    // Generate new random password (plain text)
    $newPassword = bin2hex(random_bytes(8));
    
    // Update in database - using password column (not password_hash)
    $stmt = $conn->prepare("UPDATE dept_admins SET password = ? WHERE dept_admin_id = ?");
    $stmt->execute([$newPassword, $adminId]);
    
    echo json_encode([
        'success' => true,
        'new_password' => $newPassword
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}