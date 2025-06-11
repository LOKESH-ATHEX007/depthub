<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo 'Unauthorized access.';
    exit();
}

// Delete all rejected requests
$query = "DELETE FROM scholarship_requests WHERE status = 'Rejected'";
if (mysqli_query($conn, $query)) {
    echo 'All rejected requests have been deleted.';
} else {
    echo 'Error deleting rejected requests: ' . mysqli_error($conn);
}
?>