<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depthub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Check if dept_id exists in session
if (!isset($_SESSION['dept_id'])) {
    die(json_encode(["error" => "Department not identified"]));
}

$dept_id = $_SESSION['dept_id'];

// If fetching complaint types for the department
if (isset($_GET['fetchTypes'])) {
    $typeQuery = "SELECT DISTINCT c.complaint_type 
                  FROM complaint c
                  JOIN st1 s ON c.regno = s.regno
                  WHERE s.department = ?";
    $stmt = $conn->prepare($typeQuery);
    $stmt->bind_param("s", $dept_id);
    $stmt->execute();
    $typeResult = $stmt->get_result();

    $complaintTypes = [];
    while ($typeRow = $typeResult->fetch_assoc()) {
        $complaintTypes[] = $typeRow["complaint_type"];
    }

    header('Content-Type: application/json');
    echo json_encode($complaintTypes);
    exit();
}

// Fetch complaints grouped by status for the department
$complaints = [
    "pending" => [],
    "in_progress" => [],
    "resolved" => []
];

$sql = "SELECT c.id, c.stName, c.regno, c.complaint_type, c.complaint_date, c.status 
        FROM complaint c
        JOIN st1 s ON c.regno = s.regno
        WHERE s.department = ?
        ORDER BY c.id DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dept_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = str_replace(" ", "_", strtolower(trim($row["status"])));
        if (isset($complaints[$status])) {
            $complaints[$status][] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($complaints);
?>