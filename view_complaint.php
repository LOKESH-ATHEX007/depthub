<?php
session_start();
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";



$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Invalid Request!");
}
$id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complaint Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/view_complaints.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./styles/nav3.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
<body>

<?php include("includes/nav3.php") ?>
<div class="container">
    <h3 class="text-center mb-4">Complaint Details</h3>
    <div class="complaint-wrapper" id="complaint-details">
        <!-- Data will be loaded here using AJAX -->
    </div>
</div>

<script>
    function fetchComplaintDetails() {
        $.ajax({
            url: 'php/complaints/fetch_complaint.php',
            method: 'GET',
            data: { id: <?php echo $id; ?> },
            dataType: 'json',
            success: function(row) {
                let incidentDate = (row.incident_date === '0000-00-00') ? 'Not Entered' : new Date(row.incident_date).toDateString();
                let complaintDate = new Date(row.complaint_date).toDateString();
                let evidence1 = row.evidence1 ? `<a href='${row.evidence1}' class='btn btn-sm btn-primary' download>Download</a>` : 'No Evidence Uploaded';
                let evidence2 = row.evidence2 ? `<a href='${row.evidence2}' class='btn btn-sm btn-primary' download>Download</a>` : 'No Evidence Uploaded';

                $('#complaint-details').html(`
                    <div class='left-panel'>
                        <table class='table table-bordered'>
                            <tr><th>Registration No:</th><td>${row.regno}</td></tr>
                            <tr><th>Student Name:</th><td>${row.stName}</td></tr>
                            <tr><th>Phone Number:</th><td>${row.phno}</td></tr>
                            <tr><th>Email:</th><td>${row.email}</td></tr>
                            <tr><th>Complaint Type:</th><td>${row.complaint_type}</td></tr>
                            <tr><th>Incident Date:</th><td>${incidentDate}</td></tr>
                            <tr><th>Complaint Date:</th><td>${complaintDate}</td></tr>
                            <tr><th>Status:</th><td>${row.status}</td></tr>
                            <tr><th>Evidence 1:</th><td>${evidence1}</td></tr>
                            <tr><th>Evidence 2:</th><td>${evidence2}</td></tr>
                        </table>
                        <button onclick='deleteComplaint(${row.id})' class='btn btn-danger mt-3'>Delete Complaint</button>
                    </div>
                    <div class='right-panel'>
                        <div class='subject-header'>Subject: ${row.subject}</div>
                        <p style="transform:translateY(10px);">Discription: </p>
                        <div class='description'>${row.description.replace(/\n/g, '<br>')}</div>
                        <div class='status-section'>
                            <label for='status' class='form-label mt-3'>Update Status:</label>
                            <select id='status' class='form-select mb-3'>
                                <option value='Pending' ${row.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value='In Progress' ${row.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                <option value='Resolved' ${row.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                            </select>
                            <button onclick='updateStatus(${row.id})' class='btn btn-success'>Update Status</button>
                        </div>
                    </div>
                `);
            },
            error: function(error) {
                console.error("Error fetching complaint details:", error);
            }
        });
    }

    function updateStatus(id) {
    let status = $('#status').val();
    $.ajax({
        url: 'php/complaints/update_status.php',
        method: 'POST',
        data: { id: id, status: status },
        dataType: 'json', // Expecting JSON response
        success: function(response) {
            Swal.fire({
                icon: response.status === "success" ? 'success' : 'error',
                title: response.message,
            }).then(() => fetchComplaintDetails());
        },
        error: function(error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status' });
        }
    });
}

function deleteComplaint(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This complaint will be deleted permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php/complaints/delete_complaint.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json', // Expecting JSON response
                success: function(response) {
                    Swal.fire({
                        icon: response.status === "success" ? 'success' : 'error',
                        title: response.message,
                    }).then(() => {
                        if (response.status === "success") {
                            window.location.href = 'complaints_admin.php';
                        }
                    });
                },
                error: function(error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete complaint' });
                }
            });
        }
    });
}


    $(document).ready(fetchComplaintDetails);
</script>
</body>
</html>
