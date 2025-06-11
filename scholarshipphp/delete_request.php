<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['T_id'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include 'db.php';

// Check if the request ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die("Invalid request. Request ID is missing.");
}

// Sanitize the input
$id = intval($_POST['id']);

// Prepare the delete query
$query = "DELETE FROM scholarship_requests WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    // Bind the ID parameter
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Check if any row was affected
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Deletion successful
            echo json_encode(['success' => true, 'message' => 'Request deleted successfully.']);
        } else {
            // No rows affected (ID might not exist)
            echo json_encode(['success' => false, 'message' => 'No request found with the provided ID.']);
        }
    } else {
        // Query execution failed
        echo json_encode(['success' => false, 'message' => 'Failed to delete the request. Please try again.']);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Statement preparation failed
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}

// Close the database connection
mysqli_close($conn);
?>