<?php
session_start();
include 'scholarshipphp/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

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
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Scholarship Request Details</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .details-card {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .details-card h2 {
                    margin-top: 0;
                    color: #333;
                }
                .details-card p {
                    margin: 10px 0;
                    color: #555;
                }
                .details-card a {
                    color: #007bff;
                    text-decoration: none;
                }
                .details-card a:hover {
                    text-decoration: underline;
                }
                .status-buttons {
                    display: flex;
                    gap: 10px;
                    margin-top: 20px;
                }
                .status-btn {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    transition: background-color 0.3s ease;
                }
                .status-btn.pending {
                    background-color: #ffc107;
                    color: #000;
                }
                .status-btn.underprogress {
                    background-color: #17a2b8;
                    color: #fff;
                }
                .status-btn.rejected {
                    background-color: #dc3545;
                    color: #fff;
                }
                .status-btn:hover {
                    opacity: 0.9;
                }
            </style>
        </head>
        <body>
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
                <div class="status-buttons">
                    <button class="status-btn pending" onclick="updateStatus(' . $row['id'] . ', \'Pending\')">Pending</button>
                    <button class="status-btn underprogress" onclick="updateStatus(' . $row['id'] . ', \'Under Progress\')">Under Progress</button>
                    <button class="status-btn rejected" onclick="updateStatus(' . $row['id'] . ', \'Rejected\')">Rejected</button>
                </div>
            </div>
            <script>
                function updateStatus(id, status) {
                    if (confirm("Are you sure you want to update the status to " + status + "?")) {
                        window.location.href = "update_status.php?id=" + id + "&status=" + status;
                    }
                }
            </script>
        </body>
        </html>
        ';
    } else {
        echo '<p>No details found.</p>';
    }
}
?>