<?php
session_start();
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";


// Connect to database
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$regno =$_SESSION['regno']; // Temporary value, replace with $_SESSION['regno'] when ready
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Status</title>
    <link rel="stylesheet" href="styles/nav.css">
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url("pic/notes-bg.jpg");
            min-height: 100vh;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            min-height:100vh;
        
        }
        .container {
            max-width: 90%;
            margin: auto;
            margin-top:5rem;
        }
        .complaint-wrapper {
            display: flex;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .left-panel, .right-panel {
            flex: 1;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .status-badge {
            padding: 8px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            display: inline-block;
            text-align: center;
        }
        .pending { background: #f39c12; }
        .in-progress { background: #3498db; }
        .resolved { background: #2ecc71; }
        .description {
            padding: 10px;
            background: white;
            border-radius: 5px;
            height: 15rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            position: relative;
        }
        .delete-btn {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            margin-top: 15px;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
<?php
     include("includes/nav.php")
    ?>

<div class="container">
    <h3 class="text-center text-primary mb-4"><i class="fas fa-tasks"></i> Complaint Status</h3>
    <div id="complaints-container">
        <!-- Complaints will be loaded here dynamically -->
    </div>
</div>

<script>
    function loadComplaints() {
        $.ajax({
            url: 'php/complaints/fetch_status.php',
            method: 'GET',
            data: { regno: <?php echo $regno; ?> },
            dataType: 'json',
            success: function(data) {
                let container = $("#complaints-container");
                container.html("");

                if (data.length > 0) {
                    data.forEach(complaint => {
                        let evidence1 = complaint.evidence1 ? `<a href='${complaint.evidence1}' class='btn btn-sm btn-primary' download>View</a>` : 'No Evidence Uploaded';
                        let evidence2 = complaint.evidence2 ? `<a href='${complaint.evidence2}' class='btn btn-sm btn-primary' download>View</a>` : 'No Evidence Uploaded';
                        let incidentDate = (complaint.incident_date === '0000-00-00') ? 'Not Entered' : new Date(complaint.incident_date).toDateString();

                        container.append(`
                            <div class="complaint-wrapper">
                                <div class="left-panel">
                                    <p><strong>Complaint ID:</strong> ${complaint.id}</p>
                                    <p><strong>Phone:</strong> ${complaint.phno}</p>
                                    <p><strong>Email:</strong> ${complaint.email}</p>
                                    <p><strong>Incident Date:</strong> ${incidentDate}</p>
                                    <p><strong>Evidence 1:</strong> ${evidence1}</p>
                                    <p><strong>Evidence 2:</strong> ${evidence2}</p>
                                    <p><strong>Status:</strong> <span class="status-badge ${complaint.status.toLowerCase().replace(' ', '-')}">${complaint.status}</span></p>
                                </div>
                                <div class="right-panel">
                                    <p><strong>Subject:</strong> ${complaint.subject}</p>
                                    <div class="description">${complaint.description.replace(/\n/g, '<br>')}</div>
                                    <button class="delete-btn" onclick="confirmDelete(${complaint.id})">
                                        <i class="fas fa-trash-alt"></i> Delete Complaint
                                    </button>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    container.html(`
                        <div class="no-complaints" style="text-align: center; margin-top: 20px;">
                            <img src="./pic/no-complaints.svg" alt="No Complaints" style="width: 400px; opacity: 1;">
                            <p style="color: red; font-size: 16px;"><i class="fas fa-exclamation-circle"></i> No complaints found.</p>
                        </div>
                    `);
                }
            },
            error: function(error) {
                console.error("Error fetching complaints:", error);
            }
        });
    }

    function confirmDelete(id) {
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
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: response.status === "success" ? 'success' : 'error',
                            title: response.message,
                        }).then(() => loadComplaints());
                    },
                    error: function(error) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete complaint' });
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        loadComplaints();
        setInterval(loadComplaints, 2000); // Refresh every 5 seconds
    });
</script>

</body>
</html>
