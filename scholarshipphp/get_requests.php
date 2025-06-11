<?php
include 'db.php';

$status = isset($_GET['status']) ? $_GET['status'] : 'Recent';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

$query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
          FROM scholarship_requests sr 
          JOIN st1 s ON sr.regno = s.regno 
          WHERE 1 ";

if ($status !== 'Recent') {
    $query .= " AND sr.status = ?";
}

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND sr.requestDate BETWEEN ? AND ?";
} elseif (!empty($startDate)) {
    $query .= " AND sr.requestDate >= ?";
} elseif (!empty($endDate)) {
    $query .= " AND sr.requestDate <= ?";
}

$query .= " ORDER BY sr.requestDate DESC";

$stmt = mysqli_prepare($conn, $query);

if ($status !== 'Recent' && !empty($startDate) && !empty($endDate)) {
    mysqli_stmt_bind_param($stmt, "sss", $status, $startDate, $endDate);
} elseif ($status !== 'Recent' && !empty($startDate)) {
    mysqli_stmt_bind_param($stmt, "ss", $status, $startDate);
} elseif ($status !== 'Recent' && !empty($endDate)) {
    mysqli_stmt_bind_param($stmt, "ss", $status, $endDate);
} elseif ($status !== 'Recent') {
    mysqli_stmt_bind_param($stmt, "s", $status);
} elseif (!empty($startDate) && !empty($endDate)) {
    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
} elseif (!empty($startDate)) {
    mysqli_stmt_bind_param($stmt, "s", $startDate);
} elseif (!empty($endDate)) {
    mysqli_stmt_bind_param($stmt, "s", $endDate);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

echo json_encode($requests);
?>
