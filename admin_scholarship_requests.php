<?php
session_start();
include 'scholarshipphp/db.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Scholarship Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/nav3.css"/>
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
  padding-left:110px;
        }
        .container { max-width: 1200px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 20px;margin-bottom:3rem; }
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
    </style>
</head>
<body>
<?php include "includes/nav3.php" ?>
<div class="container">
    <h2>Dept Admin Scholarship Requests</h2>

    <!-- Tabs and Filters in the same row -->
    <div class="tabs-and-filters">
        <!-- Tabs on the left -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('under-process')">Under Process</button>
            <button class="tab-button" onclick="showTab('approved')">Approved</button>
            <button class="tab-button" onclick="showTab('rejected')">Rejected</button>
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

    <!-- Under Process Tab -->
    <div id="under-process-tab">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Register No</th>
                        <th>Student Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="under-process-tab-body">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
            <div id="under-process-tab-no-records" class="no-records" style="display: none;">
                <img src="pic/no-complaints.svg" alt="No records found">
                <p>No scholarship requests under process found.</p>
            </div>
        </div>
    </div>

    <!-- Approved Tab -->
    <div id="approved-tab" style="display: none;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Register No</th>
                        <th>Student Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="approved-tab-body">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
            <div id="approved-tab-no-records" class="no-records" style="display: none;">
                <img src="pic/no-complaints.svg" alt="No records found">
                <p>No approved scholarship requests found.</p>
            </div>
        </div>
    </div>

    <!-- Rejected Tab -->
    <div id="rejected-tab" style="display: none;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Register No</th>
                        <th>Student Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rejected-tab-body">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
            <div id="rejected-tab-no-records" class="no-records" style="display: none;">
                <img src="pic/no-complaints.svg" alt="No records found">
                <p>No rejected scholarship requests found.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Function to show the selected tab
function showTab(tabName) {
    // Hide all tabs
    document.getElementById('under-process-tab').style.display = 'none';
    document.getElementById('approved-tab').style.display = 'none';
    document.getElementById('rejected-tab').style.display = 'none';

    // Show the selected tab
    document.getElementById(`${tabName}-tab`).style.display = 'block';

    // Update active tab button
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    document.querySelector(`button[onclick="showTab('${tabName}')"]`).classList.add('active');
}


// Function to fetch all or filtered requests
function fetchAllRequests(startDate = null, endDate = null) {
    let url = 'scholarshipphp/fetch_all_requests_admin.php'; // Default URL for all requests
    if (startDate && endDate) {
        url = 'scholarshipphp/fetch_filtered_requests_admin.php'; // URL for filtered requests
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
            updateTable('under-process-tab-body', data.underProcess);
            updateTable('approved-tab-body', data.approved);
            updateTable('rejected-tab-body', data.rejected);
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
    let rows = '';
    data.forEach(row => {
        rows += `
            <tr data-id="${row.id}">
                <td>${row.regno}</td>
                <td>${row.stName}</td>
                <td>${row.requestDate}</td>
                <td>${row.status}</td>
                <td>
                    <a href="scholarshipphp/update_request.php?id=${row.id}" class="see-more">See More</a>
                </td>
            </tr>
        `;
    });
    tableBody.innerHTML = rows;
}

function updateTableAndNoRecords(tabBodyId, noRecordsId, data) {
    const tableBody = document.getElementById(tabBodyId);
    const noRecordsDiv = document.getElementById(noRecordsId);
    
    if (data.length === 0) {
        tableBody.innerHTML = '';
        noRecordsDiv.style.display = 'flex';
    } else {
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
                        <a href="scholarshipphp/update_request.php?id=${row.id}" class="see-more">See More</a>
                    </td>
                </tr>
            `;
        });
        tableBody.innerHTML = rows;
    }
}

// Function to filter requests by date
function filterByDate() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    console.log("Start Date:", startDate); // Log start date
    console.log("End Date:", endDate); // Log end date

    // Validate date format (YYYY-MM-DD)
    const dateFormat = /^\d{4}-\d{2}-\d{2}$/;
    if (startDate && endDate && (!dateFormat.test(startDate) || !dateFormat.test(endDate))) {
        alert("Invalid date format. Use YYYY-MM-DD.");
        return;
    }

    if (startDate && endDate && startDate > endDate) {
        alert("Start date cannot be after end date.");
        return;
    }

    // Fetch filtered data
    fetchAllRequests(startDate, endDate);
}

// Fetch all requests when the page loads
document.addEventListener('DOMContentLoaded', () => {
    fetchAllRequests(); // Fetch all records initially
});
</script>
</body>
</html>