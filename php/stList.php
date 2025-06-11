<?php
// Database Connection
$conn = mysqli_connect("localhost", "root", "", "depthub");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$year = isset($_GET['year']) ? mysqli_real_escape_string($conn, $_GET['year']) : '';
$classFilter = isset($_GET['class']) ? mysqli_real_escape_string($conn, $_GET['class']) : '';

// Base query
$query = "SELECT * FROM st1 WHERE 1";

// Apply search filter if provided
if (!empty($search)) {
    $query .= " AND (stName LIKE '%$search%' OR regno LIKE '%$search%' OR studentPhno LIKE '%$search%')";
}

// Apply year filter if selected (excluding "All" case)
if (!empty($year)) {
    $query .= " AND currentYear = '$year'";
}

// Apply class filter if selected (excluding "All" case)
if (!empty($classFilter)) {
    $query .= " AND class = '$classFilter'";
}

// Execute query
$result = mysqli_query($conn, $query);

// Check if any results exist
if ($result && mysqli_num_rows($result) > 0) {
    while ($fetch = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$fetch['regno']}</td>
                <td>{$fetch['stName']}</td>
                <td>{$fetch['currentYear']}</td>
                <td>{$fetch['class']}</td>
                <td>{$fetch['studentPhno']}</td>
                <td>
                    <a href='stInfo.php?regno={$fetch['regno']}' class='btn btn-info btn-sm'>View</a>
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
