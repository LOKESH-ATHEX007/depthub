<?php
session_start();
include("conn.php");

// Check if teacher is logged in
if (!isset($_SESSION["T_id"])) {
    die("<tr><td colspan='6'>Access Denied</td></tr>");
}

$T_id = $_SESSION["T_id"];

// Get teacher's department from teachers table
$teacherQuery = mysqli_query($conn, 
    "SELECT department FROM teachers WHERE T_id = '$T_id'");
$teacherData = mysqli_fetch_assoc($teacherQuery);

if (!$teacherData) {
    die("<tr><td colspan='6'>Teacher not found</td></tr>");
}

$dept_id = $teacherData['department'];

// Get the class and year assigned to the teacher
$classQuery = mysqli_query($conn, 
    "SELECT class, currentYear FROM class_teacher_allocation 
     WHERE T_id = '$T_id'");
$classData = mysqli_fetch_assoc($classQuery);

if (!$classData) {
    die("<tr><td colspan='6'>No Class Assigned</td></tr>");
}

$class = $classData['class'];
$currentYear = $classData['currentYear'];

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$yearFilter = isset($_GET['year']) ? mysqli_real_escape_string($conn, $_GET['year']) : $currentYear;
$classFilter = isset($_GET['class']) ? mysqli_real_escape_string($conn, $_GET['class']) : $class;

// Base query - filters by teacher's department automatically
$query = "SELECT * FROM st1 
          WHERE class = '$classFilter' 
          AND currentYear = '$yearFilter'
          AND department = '$dept_id'"; // Added department filter

// Apply search filter if provided
if (!empty($search)) {
    $query .= " AND (stName LIKE '%$search%' OR regno LIKE '%$search%' OR studentPhno LIKE '%$search%')";
}

// Execute query
$result = mysqli_query($conn, $query);

// Output results (same as before)
if ($result && mysqli_num_rows($result) > 0) {
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$fetch['regno']}</td>
                <td>{$fetch['stName']}</td>
                <td>{$fetch['currentYear']}</td>
                <td>{$fetch['class']}</td>
                <td>{$fetch['studentPhno']}</td>
                <td>
                    <a href='stInfo_t.php?regno={$fetch['regno']}' class='btn btn-info btn-sm'>View</a>
                    <a href='stform_edit1.php?regno={$fetch['regno']}' class='btn btn-primary btn-sm'>Edit</a>
                    <button class='btn btn-danger btn-sm delete-btn' data-regno='{$fetch['regno']}'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No students found</td></tr>";
}

mysqli_close($conn);
?>