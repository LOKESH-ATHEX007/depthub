<?php
include 'db.php'; // Include database connection

// Get the tab and date range parameters from the request
$tab = $_GET['tab'] ?? 'recent'; // Default to 'recent' if tab is not provided
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;

// Fetch requests based on the selected tab and date range
function fetchRequests($status, $startDate = null, $endDate = null) {
    global $conn;
    $query = "SELECT sr.id, sr.regno, sr.reason, sr.incomeCert, sr.otherProofs, sr.status, sr.requestDate, s.stName 
              FROM scholarship_requests sr 
              JOIN st1 s ON sr.regno = s.regno 
              WHERE sr.status = ?";
    
    // Add date range filter if provided
    if ($startDate && $endDate) {
        $query .= " AND sr.requestDate BETWEEN ? AND ?";
    }
    $query .= " ORDER BY sr.requestDate DESC";

    $stmt = mysqli_prepare($conn, $query);
    if ($startDate && $endDate) {
        mysqli_stmt_bind_param($stmt, "sss", $status, $startDate, $endDate);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $status);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Determine the status based on the tab
$status = '';
switch ($tab) {
    case 'recent':
        $status = 'Under Process';
        break;
    case 'approved':
        $status = 'Approved';
        break;
    case 'rejected':
        $status = 'Rejected';
        break;
    default:
        die("Invalid tab.");
}

// Fetch requests for the selected tab
$requests = fetchRequests($status, $startDate, $endDate);

// Generate the HTML content for the tab
echo "<h3>" . ucfirst($tab) . " Requests</h3>";
echo "<div class='date-filter'>
        <input type='date' id='{$tab}-start-date' placeholder='Start Date'>
        <input type='date' id='{$tab}-end-date' placeholder='End Date'>
        <button onclick='sortRequests(\"{$tab}\")'>Sort</button>
      </div>";
echo "<table>
        <tr>
            <th>Register No</th>
            <th>Student Name</th>
            <th>Income Certificate</th>
            <th>Other Proofs</th>
            <th>Request Date</th>
            <th>Actions</th>
        </tr>";
while ($row = mysqli_fetch_assoc($requests)) {
    echo "<tr>
            <td>" . htmlspecialchars($row['regno']) . "</td>
            <td>" . htmlspecialchars($row['stName']) . "</td>
            <td><a href='uploads/" . htmlspecialchars($row['incomeCert']) . "' target='_blank'>View</a></td>
            <td><a href='uploads/" . htmlspecialchars($row['otherProofs']) . "' target='_blank'>View</a></td>
            <td>" . htmlspecialchars($row['requestDate']) . "</td>
            <td>
                <a href='scholarshipphp/update_request.php?id=" . $row['id'] . "' class='btn btn-see-more'>See More</a>
            </td>
          </tr>";
}
echo "</table>";