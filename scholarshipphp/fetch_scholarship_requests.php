<?php
include 'db.php';

$status = $_GET['status'];
$sql = "SELECT * FROM scholarship_requests WHERE status = '$status'";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr data-id='{$row['id']}'><td>{$row['id']}</td><td>{$row['student_name']}</td><td>{$row['department']}</td><td>{$row['status']}</td></tr>";
}
?>
