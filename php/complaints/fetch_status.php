<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depthub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$regno = $_GET['regno'] ?? '';

$sql = "SELECT * FROM complaint WHERE regno = ? ORDER BY complaint_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $regno);
$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($complaints);
?>
