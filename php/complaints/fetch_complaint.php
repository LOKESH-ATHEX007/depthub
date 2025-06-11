<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depthub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Ensure 'id' parameter is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die(json_encode(["error" => "Invalid complaint ID"]));
}

$id = intval($_GET['id']);

// Fetch the complaint details
$sql = "SELECT * FROM complaint WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $complaint = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($complaint);
} else {
    echo json_encode(["error" => "Complaint not found"]);
}

$stmt->close();
$conn->close();
?>
