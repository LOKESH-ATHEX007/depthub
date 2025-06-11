<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depthub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if any resolved complaints exist before deleting
$checkSql = "SELECT COUNT(*) AS count FROM complaint WHERE status = 'resolved'";
$checkResult = $conn->query($checkSql);
$row = $checkResult->fetch_assoc();

if ($row["count"] == 0) {
    echo json_encode(["status" => "info", "message" => "No resolved complaints to delete."]);
    exit();
}

// If complaints exist, proceed with deletion
$sql = "DELETE FROM complaint WHERE status = 'resolved'";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "All resolved complaints deleted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting complaints: " . $conn->error]);
}

$conn->close();
?>
