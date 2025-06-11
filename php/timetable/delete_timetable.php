<?php
session_start();
require_once "../conn.php";

if (!isset($_SESSION['dept_id']) && !isset($_SESSION['teacher_dept_id'])) {
    die("Unauthorized access.");
}

if (!isset($_POST['id'])) {
    die("Invalid request.");
}

$id = intval($_POST['id']);
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : $_SESSION['teacher_dept_id'];

$checkQuery = "SELECT imagePath FROM timetable_uploads WHERE id = ? AND dept_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $id, $dept_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Unauthorized or record not found.");
}

$row = $result->fetch_assoc();
$imagePath = $row['imagePath'];

$deleteQuery = "DELETE FROM timetable_uploads WHERE id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>