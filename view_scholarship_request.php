<?php
session_start();
include 'scholarshipphp/db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['T_id'])) {
    header('Location: index.php');
    exit();
}

$T_id = $_SESSION['T_id'];

// Fetch teacher's allocated class and year using prepared statements
$teacherQuery = "SELECT class, currentYear, dept_id FROM class_teacher_allocation WHERE T_id = ?";
$stmt = mysqli_prepare($conn, $teacherQuery);

// Check if prepare succeeded
if ($stmt === false) {
    die("Database error: " . mysqli_error($conn));
}

// Bind parameters and execute
if (!mysqli_stmt_bind_param($stmt, "s", $T_id)) {
    die("Parameter binding failed: " . mysqli_stmt_error($stmt));
}

if (!mysqli_stmt_execute($stmt)) {
    die("Query execution failed: " . mysqli_stmt_error($stmt));
}

$teacherResult = mysqli_stmt_get_result($stmt);
if ($teacherResult === false) {
    die("Failed to get result: " . mysqli_stmt_error($stmt));
}

$teacher = mysqli_fetch_assoc($teacherResult);
if (!$teacher) {
    die("Teacher details not found for ID: $T_id");
}

// Verify all required fields exist
if (!isset($teacher['class']) || !isset($teacher['currentYear']) || !isset($teacher['dept_id'])) {
    die("Incomplete teacher allocation data. Missing required fields.");
}

$class = $teacher['class'];
$currentYear = $teacher['currentYear'];
$dept_id = $teacher['dept_id'];

// Rest of your code...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/contact2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9;
  background:url("pic/scholarship_elements_bg.png"); 
  height: 100vh;
  width: 100vw;
  min-height: 100vh;
  min-width: 100vw;
  max-height: 100vh;
  max-width: 100vw;

  background-size: cover; 
  background-position: center center;
  background-repeat: no-repeat;
  background-attachment: fixed; 
  padding-left:120px;
  
        }
        .container { max-width: 1200px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 3rem; }
        .tabs-and-filters { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .tabs { display: flex; gap: 10px; }
        .tab-button { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; background-color: #e0e0e0; color: #333; }
        .tab-button.active { background-color: #4f46e5; color: white; }
        .date-filter { display: flex; gap: 10px; align-items: center; }
        .date-filter input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .date-filter button { padding: 8px 12px; border: none; border-radius: 4px; background-color: #4f46e5; color: white; cursor: pointer; }
        .date-filter button:hover { background-color: #3b82f6; }
        .table-container { max-height: 60vh; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #4f46e5; color: white; cursor: pointer; position: sticky; top: 0; }
        .see-more { background-color: #4f46e5; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; }
        .see-more:hover { background-color: #3b82f6; }
        .status-btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .status-btn.underprogress { background-color: #3b82f6; color: white; }
        .status-btn.pending { background-color: #f44336; color: white; }
        .no-records { text-align: center; padding: 20px; color: #666;}
        .no-records img {margin-bottom: 10px;}
        .no-records p {font-size: 16px;margin: 0;}
    </style>
</head>
<body>
<?php include "includes/nav2.php" ?>
<div class="container">
    <h2>Scholarship Requests</h2>

    <!-- Tabs and Filters in the same row -->
    <div class="tabs-and-filters">
        <!-- Tabs on the left -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('pending')">Pending</button>
            <button class="tab-button" onclick="showTab('under-process')">Under Process</button>
        </div>

        <!-- Date Filter on the right -->
        <div class="date-filter">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date">
            <button onclick="filterByDate()">Filter</button>
        </div>
    </div>

    <!-- Pending Tab -->
    <div id="pending-tab">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th onclick="sortTable('pending-tab', 0)">Register No</th>
                        <th onclick="sortTable('pending-tab', 1)">Student Name</th>
                        <th onclick="sortTable('pending-tab', 2)">Request Date</th>
                        <th onclick="sortTable('pending-tab', 3)">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pending-tab-body">
                    <?php
// Fetch pending requests with department filter
$query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
          FROM scholarship_requests sr 
          JOIN st1 s ON sr.regno = s.regno 
          WHERE sr.class = ? 
            AND sr.currentYear = ? 
            AND s.department = ?
            AND sr.status = 'Pending' 
          ORDER BY sr.requestDate DESC";

$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

// Bind parameters (class, year, department)
$bind_result = mysqli_stmt_bind_param($stmt, "sss", $class, $currentYear, $dept_id);
if ($bind_result === false) {
    die("Error binding parameters: " . mysqli_stmt_error($stmt));
}

// Execute
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}

// Get results
$result = mysqli_stmt_get_result($stmt);
$pendingRequests = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($pendingRequests as $row) { ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= htmlspecialchars($row['regno']) ?></td>
                            <td><?= htmlspecialchars($row['stName']) ?></td>
                            <td><?= htmlspecialchars($row['requestDate']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="scholarshipphp/update_request_teacher.php?id=<?= $row['id'] ?>" class="see-more">See More</a>
                                <button onclick="updateStatus(<?= $row['id'] ?>, 'Under Process')" class="status-btn underprogress">Mark as Under Process</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div id="pending-tab-no-records" class="no-records" style="display: none;">
            <img src="no-complaints.svg" alt="No records found" style="width: 100px; height: 100px;">
            <p>No pending scholarship requests found.</p>
        </div>
        </div>
    </div>

    <!-- Under Process Tab -->
    <div id="under-process-tab" style="display: none;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th onclick="sortTable('under-process-tab', 0)">Register No</th>
                        <th onclick="sortTable('under-process-tab', 1)">Student Name</th>
                        <th onclick="sortTable('under-process-tab', 2)">Request Date</th>
                        <th onclick="sortTable('under-process-tab', 3)">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="under-process-tab-body">
                    <?php
// Fetch under process requests with department filter
$query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
          FROM scholarship_requests sr 
          JOIN st1 s ON sr.regno = s.regno 
          WHERE sr.class = ? 
            AND sr.currentYear = ? 
            AND s.department = ? 
            AND sr.status = 'Under Process' 
          ORDER BY sr.requestDate DESC";

$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

// Bind parameters (class, year, department)
if (!mysqli_stmt_bind_param($stmt, "sss", $class, $currentYear, $dept_id)) {
    die("Error binding parameters: " . mysqli_stmt_error($stmt));
}

if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if ($result === false) {
    die("Error getting result set: " . mysqli_stmt_error($stmt));
}

$underProcessRequests = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($underProcessRequests as $row) { ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= htmlspecialchars($row['regno']) ?></td>
                            <td><?= htmlspecialchars($row['stName']) ?></td>
                            <td><?= htmlspecialchars($row['requestDate']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="scholarshipphp/update_request_teacher.php?id=<?= $row['id'] ?>" class="see-more">See More</a>
                                <button onclick="updateStatus(<?= $row['id'] ?>, 'Pending')" class="status-btn pending">Mark as Pending</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div id="under-process-tab-no-records" class="no-records" style="display: none;">
            <img src="no-complaints.svg" alt="No records found" style="width: 100px; height: 100px;">
            <p>No scholarship requests under process found.</p>
        </div>
        </div>
    </div>
</div>

<script>
// Function to fetch all or filtered requests
function fetchAllRequests(startDate = null, endDate = null) {
    let url = 'scholarshipphp/fetch_all_requests_teacher.php'; // Default URL for all requests
    if (startDate && endDate) {
        url = 'scholarshipphp/fetch_filtered_requests_teacher.php'; // URL for filtered requests
        url += `?startDate=${startDate}&endDate=${endDate}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            updateTable('pending-tab-body', data.pending);
            updateTable('under-process-tab-body', data.underProcess);
        })
        .catch(error => {
            console.error('Error fetching requests:', error);
            // Log the full error response
            fetch(url)
                .then(response => response.text())
                .then(text => console.error("Server Response:", text))
                .catch(err => console.error("Failed to fetch error details:", err));
        });
}


// Function to update the table with filtered data
function updateTable(tabBodyId, data) {
    const tableBody = document.getElementById(tabBodyId);
    const noRecordsDiv = document.getElementById(tabBodyId.replace('-body', '-no-records'));

    if (data.length === 0) {
        // Show fallback UI if no records are found
        tableBody.innerHTML = '';
        noRecordsDiv.style.display = 'block';
    } else {
        // Hide fallback UI and populate the table
        noRecordsDiv.style.display = 'none';
        let rows = '';
        data.forEach(row => {
            rows += `
                <tr data-id="${row.id}">
                    <td>${row.regno}</td>
                    <td>${row.stName}</td>
                    <td>${row.requestDate}</td>
                    <td>${row.status}</td>
                    <td>
                        <a href="scholarshipphp/update_request_teacher.php?id=${row.id}" class="see-more">See More</a>
                        <button onclick="updateStatus(${row.id}, '${row.status === 'Pending' ? 'Under Process' : 'Pending'}')" class="status-btn ${row.status === 'Pending' ? 'underprogress' : 'pending'}">
                            ${row.status === 'Pending' ? 'Mark as Under Process' : 'Mark as Pending'}
                        </button>
                    </td>
                </tr>
            `;
        });
        tableBody.innerHTML = rows;
    }
}
// Function to update the status of a request
function updateStatus(id, newStatus) {
    if (confirm(`Change status to ${newStatus}?`)) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_scholarship_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Remove the row from the current table
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }

                    // Fetch the updated data and refresh the tables
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;
                    fetchAllRequests(startDate, endDate);
                } else {
                    alert('Failed to update status: ' + response.message);
                }
            }
        };
        xhr.send(`id=${id}&status=${newStatus}`);
    }
}

// Function to show the selected tab
function showTab(tabName) {
    // Hide all tabs
    document.getElementById('pending-tab').style.display = 'none';
    document.getElementById('under-process-tab').style.display = 'none';

    // Show the selected tab
    document.getElementById(`${tabName}-tab`).style.display = 'block';

    // Update active tab button
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    document.querySelector(`button[onclick="showTab('${tabName}')"]`).classList.add('active');
}

// Function to filter requests by date
function filterByDate() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    console.log("Start Date:", startDate); // Log start date
    console.log("End Date:", endDate); // Log end date

    // Validate date format (YYYY-MM-DD)
    const dateFormat = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateFormat.test(startDate) || !dateFormat.test(endDate)) {
        alert("Invalid date format. Use YYYY-MM-DD.");
        return;
    }

    if (!startDate || !endDate) {
        alert("Please select both a start date and an end date.");
        return;
    }

    // Fetch filtered data
    fetchAllRequests(startDate, endDate);
}

// Function to sort the table
function sortTable(tabId, columnIndex) {
    const table = document.getElementById(tabId).getElementsByTagName('table')[0];
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));

    rows.sort((a, b) => {
        const aValue = a.getElementsByTagName('td')[columnIndex].textContent;
        const bValue = b.getElementsByTagName('td')[columnIndex].textContent;
        return aValue.localeCompare(bValue, undefined, { numeric: true, sensitivity: 'base' });
    });

    // Reverse the order if the column is already sorted
    if (table.getAttribute('data-sort-column') === columnIndex.toString()) {
        rows.reverse();
        table.setAttribute('data-sort-order', table.getAttribute('data-sort-order') === 'asc' ? 'desc' : 'asc');
    } else {
        table.setAttribute('data-sort-column', columnIndex.toString());
        table.setAttribute('data-sort-order', 'asc');
    }

    // Clear the table and append sorted rows
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    rows.forEach(row => tbody.appendChild(row));
}
</script>
</body>
</html>