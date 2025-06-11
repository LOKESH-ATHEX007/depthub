<?php
session_start();
include 'db.php';

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the uploads directory exists
    if (!is_dir('uploads/')) {
        mkdir('uploads/', 0777, true);
    }

    // Retrieve form data
    $regno = $_SESSION['regno'];
    $reason = $_POST['reason'];
    $accountHolderName = $_POST['account_holder_name'];
    $accountNumber = $_POST['account_number'];
    $ifscCode = $_POST['ifsc_code'];
    $requestDate = date('Y-m-d');

    // Handle required file upload (income certificate)
    if ($_FILES['incomeCert']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Income certificate is required']);
        exit();
    }

    $uploadDir = 'uploads/';
    $incomeCert = basename($_FILES['incomeCert']['name']);
    $incomeCertPath = $uploadDir . $incomeCert;

    // Handle optional file upload (other proofs)
    $otherProofs = null;
    $otherProofsPath = null;
    if (isset($_FILES['otherProofs']) && $_FILES['otherProofs']['error'] === UPLOAD_ERR_OK) {
        $otherProofs = basename($_FILES['otherProofs']['name']);
        $otherProofsPath = $uploadDir . $otherProofs;
    }

    // Move required income certificate file
    if (!move_uploaded_file($_FILES['incomeCert']['tmp_name'], $incomeCertPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload income certificate']);
        exit();
    }

    // Move optional other proofs file if it exists
    if ($otherProofs && !move_uploaded_file($_FILES['otherProofs']['tmp_name'], $otherProofsPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload other proofs']);
        exit();
    }

    // Fetch additional student details
    $studentQuery = "SELECT fatherOccupation, motherOccupation, currentYear, class, department FROM st1 WHERE regno = ?";
    $stmt = mysqli_prepare($conn, $studentQuery);
    mysqli_stmt_bind_param($stmt, "s", $regno);
    mysqli_stmt_execute($stmt);
    $studentResult = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($studentResult);

    if ($student) {
        // Insert data into scholarship_requests
        $insertQuery = "INSERT INTO scholarship_requests (
            regno, reason, incomeCert, otherProofs, requestDate, status, currentYear, class, dept_id,
            account_holder_name, account_number, ifsc_code, father_occupation, mother_occupation
        ) VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssss",
            $regno,
            $reason,
            $incomeCert,
            $otherProofs,
            $requestDate,
            $student['currentYear'],
            $student['class'],
            $student['department'],
            $accountHolderName,
            $accountNumber,
            $ifscCode,
            $student['fatherOccupation'],
            $student['motherOccupation']
        );

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Scholarship request submitted successfully!']);
        } else {
            error_log("SQL Error: " . mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Student details not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>