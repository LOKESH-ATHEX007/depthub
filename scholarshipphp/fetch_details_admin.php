<?php
session_start();
include 'scholarshipphp/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure the ID is an integer

    // Fetch scholarship request details
    $query = "SELECT sr.id, sr.regno, sr.reason, sr.incomeCert, sr.otherProofs, sr.status, sr.requestDate, 
                     sr.account_holder_name, sr.account_number, sr.ifsc_code, 
                     s.stName, s.fatherOccupation, s.motherOccupation, s.community 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Return only the content to be displayed inside the details row
        echo '
        <div class="details-card">
            <h2>Scholarship Request Details</h2>
            <div>
                <p><strong>Student Name:</strong> ' . htmlspecialchars($row['stName']) . '</p>
                <p><strong>Register No:</strong> ' . htmlspecialchars($row['regno']) . '</p>
                <p><strong>Father\'s Occupation:</strong> ' . htmlspecialchars($row['fatherOccupation']) . '</p>
                <p><strong>Mother\'s Occupation:</strong> ' . htmlspecialchars($row['motherOccupation']) . '</p>
                <p><strong>Community:</strong> ' . htmlspecialchars($row['community']) . '</p>
                <p><strong>Reason:</strong> ' . htmlspecialchars($row['reason']) . '</p>
                <p><strong>Income Certificate:</strong> 
                    <a href="uploads/' . htmlspecialchars($row['incomeCert']) . '" target="_blank">View</a>
                </p>
                <p><strong>Other Documents:</strong> 
                    <a href="uploads/' . htmlspecialchars($row['otherProofs']) . '" target="_blank">View</a>
                </p>
            </div>
            <div>
                <p><strong>Account Holder Name:</strong> ' . htmlspecialchars($row['account_holder_name']) . '</p>
                <p><strong>Account Number:</strong> ' . htmlspecialchars($row['account_number']) . '</p>
                <p><strong>IFSC Code:</strong> ' . htmlspecialchars($row['ifsc_code']) . '</p>
                <p><strong>Request Date:</strong> ' . htmlspecialchars($row['requestDate']) . '</p>
                <p><strong>Status:</strong> ' . htmlspecialchars($row['status']) . '</p>
            </div>
        </div>
        ';
    } else {
        echo '<p>No details found for the selected request.</p>';
    }
} else {
    echo '<p>Invalid request. Please provide a valid ID.</p>';
}
?>