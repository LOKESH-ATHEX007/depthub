<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "depthub");
if ($conn->connect_error) {
    die("<tr><td colspan='6'>Database connection failed</td></tr>");
}

// Session verification
if(session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION["dept_admin_id"], $_SESSION["dept_id"])) {
    die("<tr><td colspan='6'>Access Denied</td></tr>");
}

$dept_id = $_SESSION["dept_id"];

// Parameter handling
$search = $conn->real_escape_string($_GET['search'] ?? '');
$designationFilter = $conn->real_escape_string($_GET['designation'] ?? '');

// Base query
$query = "SELECT T_id, tName, phone, email, designation 
          FROM teachers 
          WHERE department = '$dept_id'";

// Apply filters
if (!empty($search)) {
    $query .= " AND (tName LIKE '%$search%' OR T_id LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($designationFilter)) {
    $query .= " AND designation = '$designationFilter'";
}

// Execute query
$result = $conn->query($query);

// Output results
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($row['T_id'])."</td>
                <td>".htmlspecialchars($row['tName'])."</td>
                <td>".htmlspecialchars($row['phone'])."</td>
                <td>".htmlspecialchars($row['email'])."</td>
                <td>".htmlspecialchars($row['designation'] ?? 'N/A')."</td>
                <td class='action-btns'>
                    <a href='teacher_view_A.php?t_id=".urlencode($row['T_id'])."' class='btn btn-info btn-sm'>View</a>
                    <a href='teacher_edit_A.php?t_id=".urlencode($row['T_id'])."' class='btn btn-primary btn-sm'>Edit</a>
                    <button class='btn btn-danger btn-sm delete-teacher-btn' data-tid='".htmlspecialchars($row['T_id'])."'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No teachers found</td></tr>";
}

$conn->close();
?>