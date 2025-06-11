<?php
session_start();
require_once "../conn.php";

if (!isset($_SESSION['dept_id']) && !isset($_SESSION['teacher_dept_id'])) {
    die("Unauthorized access.");
}

$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : $_SESSION['teacher_dept_id'];

if (isset($_SESSION['T_id'])) {
    $T_id = $_SESSION['T_id'];
    $query = "SELECT t.* FROM timetable_uploads t
              JOIN class_teacher_allocation c ON t.currentYear = c.currentYear 
              AND t.class = c.class AND t.dept_id = c.dept_id
              WHERE t.dept_id = ? AND c.T_id = ?
              ORDER BY t.currentYear, t.class";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $dept_id, $T_id);
} else {
    $query = "SELECT * FROM timetable_uploads 
              WHERE dept_id = ?
              ORDER BY currentYear, class";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $dept_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>Year {$row['currentYear']}</td>
                <td>Class {$row['class']}</td>
                <td>
                    <button class='action-btn view-btn' data-img='{$row['imagePath']}'>
                        <i class='fas fa-eye'></i> View
                    </button>
                    <button class='action-btn delete-btn' data-id='{$row['id']}'>
                        <i class='fas fa-trash'></i> Delete
                    </button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center'>No timetables uploaded yet</td></tr>";
}

$stmt->close();
$conn->close();
?>