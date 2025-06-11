<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "depthub";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the announcement ID from the query parameter
if (isset($_GET['A_id'])) {
    $A_id = intval($_GET['A_id']); // Sanitize input

    // Update the is_read field to 1
    $query = "UPDATE announcements SET is_read = 1 WHERE A_id = $A_id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update announcement status."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
?>