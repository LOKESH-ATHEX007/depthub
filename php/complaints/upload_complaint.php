<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depthub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $regno = $_POST["regno"] ?? '';
    $stName = $_POST["stName"] ?? '';
    $phno = $_POST["phno"] ?? '';
    $email = $_POST["email"] ?? '';
    $complaint_type = $_POST["complaint_type"] ?? '';
    $subject = $_POST["subject"] ?? '';
    $description = $_POST["description"] ?? '';
    $incident_date = $_POST["incident_date"] ?? '';

    // File upload handling
    $upload_folder = "uploads/complaints/";
    $target_dir = __DIR__ . "/../../" . $upload_folder; // Absolute path

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $evidence1 = null;
    $evidence2 = null;

    if (!empty($_FILES["evidence1"]["name"])) {
        $evidence1_name = time() . "_" . basename($_FILES["evidence1"]["name"]);
        $evidence1_path = $target_dir . $evidence1_name;
        if (move_uploaded_file($_FILES["evidence1"]["tmp_name"], $evidence1_path)) {
            $evidence1 = $upload_folder . $evidence1_name;
        }
    }

    if (!empty($_FILES["evidence2"]["name"])) {
        $evidence2_name = time() . "_" . basename($_FILES["evidence2"]["name"]);
        $evidence2_path = $target_dir . $evidence2_name;
        if (move_uploaded_file($_FILES["evidence2"]["tmp_name"], $evidence2_path)) {
            $evidence2 = $upload_folder . $evidence2_name;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO complaint (regno, stName, phno, email, complaint_type, subject, description, incident_date, evidence1, evidence2, complaint_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("ssssssssss", $regno, $stName, $phno, $email, $complaint_type, $subject, $description, $incident_date, $evidence1, $evidence2);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Complaint submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error submitting complaint."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request!"]);
}