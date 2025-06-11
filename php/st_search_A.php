<?php
session_start();
if (!isset($_SESSION['dept_admin_id']) || !isset($_SESSION['dept_id'])) {
    die("<tr><td colspan='6'>Access Denied</td></tr>");
}

$conn = mysqli_connect("localhost", "root", "", "depthub");
if (!$conn) {
    die("<tr><td colspan='6'>Database error</td></tr>");
}

$dept_id = $_SESSION['dept_id'];
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$year = mysqli_real_escape_string($conn, $_GET['year'] ?? '');
$class = mysqli_real_escape_string($conn, $_GET['class'] ?? '');

$query = "SELECT * FROM st1 WHERE department = '$dept_id'";

if (!empty($search)) {
    $query .= " AND (stName LIKE '%$search%' OR regno LIKE '%$search%')";
}
if (!empty($year)) {
    $query .= " AND currentYear = '$year'";
}
if (!empty($class)) {
    $query .= " AND class = '$class'";
}

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['regno']}</td>
                <td>{$row['stName']}</td>
                <td>{$row['currentYear']}</td>
                <td>{$row['class']}</td>
                <td>{$row['studentPhno']}</td>
                <td>
                    <a href='stInfo_t.php?regno={$row['regno']}' class='btn btn-info btn-sm'>View</a>
                    <a href='stform_edit1.php?regno={$row['regno']}' class='btn btn-primary btn-sm'>Edit</a>
                    <button class='btn btn-danger btn-sm delete-btn' data-regno='{$row['regno']}'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No students found</td></tr>";
}

mysqli_close($conn);
?>