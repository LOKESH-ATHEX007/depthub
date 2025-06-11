<?php
include 'db.php';

if (isset($_POST['regno'])) {
    $regno = $_POST['regno'];
    $query = "SELECT status FROM scholarship_requests WHERE regno = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $regno);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    echo htmlspecialchars($row['status']);
}
?>
