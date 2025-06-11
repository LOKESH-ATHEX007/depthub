<?php
include "conn.php";
session_start();
$searchTerm = $_POST['searchTerm'];
$T_id = $_SESSION['T_id']; // Assuming teacher ID is stored in session

// Get the department of the logged-in teacher
$deptQuery = mysqli_query($conn, "SELECT department FROM teachers WHERE T_id = '{$T_id}'");

if (!$deptQuery) {
    die("Error fetching department: " . mysqli_error($conn));
}

$deptData = mysqli_fetch_assoc($deptQuery);

if (!$deptData) {
    die("No department found for this teacher.");
}

$department = $deptData['department'];

// Get the classes and years allocated to this teacher
$allocationQuery = mysqli_query($conn, "SELECT class, currentYear FROM class_teacher_allocation WHERE T_id = '{$T_id}'");

if (!$allocationQuery) {
    die("Error fetching teacher allocations: " . mysqli_error($conn));
}

// Prepare arrays for classes and years
$allocatedClasses = array();
$allocatedYears = array();

while ($alloc = mysqli_fetch_assoc($allocationQuery)) {
    if (!empty($alloc['class'])) {
        $allocatedClasses[] = mysqli_real_escape_string($conn, $alloc['class']);
    }
    if (!empty($alloc['currentYear'])) {
        $allocatedYears[] = mysqli_real_escape_string($conn, $alloc['currentYear']);
    }
}

// If no allocations found, show no students
if (empty($allocatedClasses) || empty($allocatedYears)) {
    die('<tr><td colspan="6">No classes or years allocated to this teacher</td></tr>');
}

// Prepare the class and year conditions for SQL
$classConditions = "'" . implode("','", $allocatedClasses) . "'";
$yearConditions = "'" . implode("','", $allocatedYears) . "'";

// Fetch students from the same department AND matching the allocated classes/years
$sql = mysqli_query($conn, "SELECT * FROM `st1` 
    WHERE department = '" . mysqli_real_escape_string($conn, $department) . "'
    AND class IN ($classConditions)
    AND currentYear IN ($yearConditions)
    AND (
        `regno` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%' OR
        `stName` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%' OR
        `class` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%' OR
        `currentYear` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%' OR
        `studentPhno` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
    )");

if (!$sql) {
    die("Error fetching students: " . mysqli_error($conn));
}

$output = "";
if (mysqli_num_rows($sql) > 0) {
    while ($fetch = mysqli_fetch_assoc($sql)) {
        $output .= '<tr>
            <td>' . htmlspecialchars($fetch['regno']) . '</td>
            <td>' . htmlspecialchars($fetch['stName']) . '</td>
            <td>' . htmlspecialchars($fetch['class']) . '</td>
            <td>' . htmlspecialchars($fetch['currentYear']) . '</td>
            <td>' . htmlspecialchars($fetch['studentPhno']) . '</td>
            <td> 
                <a href="stInfo_t.php?regno=' . urlencode($fetch['regno']) . '" class="btn btn-info btn-sm">View</a>                
                <a href="stform_edit1.php?regno=' . urlencode($fetch['regno']) . '" class="btn btn-primary btn-sm">Edit</a>
                <button class="btn btn-danger btn-sm delete-btn" data-regno="' . htmlspecialchars($fetch['regno']) . '">Delete</button>
            </td>
        </tr>';
    }
} else {
    $output .= '<tr><td colspan="6">No Students Found Matching Your Search Term in Your Allocated Classes/Years</td></tr>';
}

echo $output;
?>