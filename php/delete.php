<?php
include "conn.php"; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if regno is set
    if (!isset($_POST["regno"])) {
        echo json_encode(["status" => "error", "message" => "regno is missing"]);
        error_log("Error: regno not received"); // Debugging
        exit;
    }

    // Sanitize the input
    $regno = mysqli_real_escape_string($conn, $_POST["regno"]);
    error_log("Received delete request for regno: " . $regno); // Debugging

    // Check if the regno exists before deleting
    $check = mysqli_query($conn, "SELECT * FROM `st1` WHERE `regno` = '$regno'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
        error_log("Error: Student with regno $regno not found");
        exit;
    }

    // Execute delete query
    $delete = mysqli_query($conn, "DELETE FROM `st1` WHERE `regno` = '$regno'");

    // Check if the delete was successful
    if ($delete) {
        echo json_encode(["status" => "success"]);
        error_log("Success: Student with regno $regno deleted");
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete student: " . mysqli_error($conn)]);
        error_log("SQL Error: " . mysqli_error($conn)); // Log SQL error
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    error_log("Error: Invalid request method");
}
?>