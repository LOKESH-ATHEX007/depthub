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

// Parameter handling
$search = $conn->real_escape_string($_GET['search'] ?? '');
$yearFilter = is_numeric($_GET['year'] ?? '') ? (int)$_GET['year'] : null;
$classFilter = $conn->real_escape_string($_GET['class'] ?? '');

// Query building
$query = "SELECT * FROM st1 WHERE department = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION["dept_id"]);

// Dynamic filters
if (!empty($search)) {
    $query .= " AND (stName LIKE CONCAT('%',?,'%') OR regno LIKE CONCAT('%',?,'%'))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $_SESSION["dept_id"], $search, $search);
}

if (!empty($yearFilter)) {
    $query .= " AND currentYear = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $_SESSION["dept_id"], $yearFilter);
}

if (!empty($classFilter)) {
    $query .= " AND class = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $_SESSION["dept_id"], $classFilter);
}

// Execute and output
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($row['regno'])."</td>
                <td>".htmlspecialchars($row['stName'])."</td>
                <td>".htmlspecialchars($row['currentYear'])."</td>
                <td>".htmlspecialchars($row['class'])."</td>
                <td>".htmlspecialchars($row['studentPhno'])."</td>
                <td>
                    <a href='stInfo_A.php?regno=".urlencode($row['regno'])."' class='btn btn-info btn-sm'>View</a>
                    <a href='stform_edit_A.php?regno=".urlencode($row['regno'])."' class='btn btn-primary btn-sm'>Edit</a>
                    <button class='btn btn-danger btn-sm delete-btn' data-regno='".htmlspecialchars($row['regno'])."'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No students found</td></tr>";
}

$stmt->close();
$conn->close();
?>