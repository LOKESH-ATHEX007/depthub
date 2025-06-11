<?php
session_start();
require_once "../conn.php";

if (!isset($_SESSION['dept_id']) && !isset($_SESSION['teacher_dept_id'])) {
    die("Unauthorized access.");
}

$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : $_SESSION['teacher_dept_id'];

if (isset($_GET['fetch_classes'])) {
    if (isset($_SESSION['T_id'])) {
        $T_id = $_SESSION['T_id'];
        $query = "SELECT DISTINCT currentYear, class FROM class_teacher_allocation 
                  WHERE dept_id = ? AND T_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $dept_id, $T_id);
    } else {
        $query = "SELECT DISTINCT currentYear, class FROM class_teacher_allocation 
                  WHERE dept_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dept_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentYear = $conn->real_escape_string($_POST['currentYear']);
    $class = $conn->real_escape_string($_POST['class']);

    if (!isset($_FILES['timetableImage']) || $_FILES['timetableImage']['error'] !== UPLOAD_ERR_OK) {
        echo "File upload error.";
        exit;
    }

    $fileName = $_FILES['timetableImage']['name'];
    $fileTmp = $_FILES['timetableImage']['tmp_name'];
    $fileSize = $_FILES['timetableImage']['size'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $uploadDir = "../../uploads/timetables/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($fileExt, $allowedTypes)) {
        echo "Invalid file type. Allowed: JPG, PNG, PDF.";
        exit;
    }

    if ($fileSize > 5 * 1024 * 1024) {
        echo "File too large. Max size: 5MB.";
        exit;
    }

    if (isset($_SESSION['T_id'])) {
        $T_id = $_SESSION['T_id'];
        $checkQuery = "SELECT * FROM class_teacher_allocation 
                      WHERE currentYear=? AND class=? AND dept_id=? AND T_id=?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ssii", $currentYear, $class, $dept_id, $T_id);
    } else {
        $checkQuery = "SELECT * FROM class_teacher_allocation 
                      WHERE currentYear=? AND class=? AND dept_id=?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ssi", $currentYear, $class, $dept_id);
    }
    
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo "Invalid class selection or you don't have permission.";
        exit;
    }
    $stmt->close();

    $checkQuery = "SELECT * FROM timetable_uploads 
                  WHERE currentYear=? AND class=? AND dept_id=?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ssi", $currentYear, $class, $dept_id);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "A timetable for this class already exists in your department.";
        exit;
    }
    $stmt->close();

    $uniqueFileName = $uploadDir . time() . "_" . bin2hex(random_bytes(5)) . "." . $fileExt;
    
    if (move_uploaded_file($fileTmp, $uniqueFileName)) {
        $insertQuery = "INSERT INTO timetable_uploads (currentYear, class, imagePath, dept_id) 
                       VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssi", $currentYear, $class, $uniqueFileName, $dept_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Database error: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "File upload failed.";
    }
    exit;
}

$conn->close();
?>