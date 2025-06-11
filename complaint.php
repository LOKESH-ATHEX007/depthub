<?php

$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";



$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['regno'])) {
    die("Error: Registration number not found in session.");
}

$regno = $_SESSION['regno'];


$student_query = "SELECT stName, regno FROM st1 WHERE regno = '$regno'";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Complaint Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles/nav.css"/>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
    <link rel="stylesheet" href="styles/complaint.css" />
</head>
<body>
<?php
     include("includes/nav.php")
    ?>

    <div id="vector-img">
        <h2>Raise a complaint now </h2>
        <img src="pic/raise-complaint.svg" alt="compaints icon img">
    </div>

<div class="container" id="container">
    <!-- <h3 class="text-center text-primary">📩 Complaint Here </h3> -->
    
    <form id="complaintForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="stName" class="form-control" value="<?php echo htmlspecialchars($student['stName'] ?? ''); ?>" readonly>
            </div>
            <div class="col-md-6">
                <input type="text" name="regno" class="form-control" value="<?php echo htmlspecialchars($student['regno'] ?? ''); ?>" readonly>
            </div>
        </div>
        
        <div class="row">
        <div class="col-md-6"><input type="text" name="phno" class="form-control" placeholder="📞 Phone Number" required> </div>
        <div class="col-md-6">  <input type="email" name="email" class="form-control" placeholder="✉️ Email Address" required></div></div>
        
       
        

        <select class="form-select" name="complaint_type" required>
            <option value="" disabled selected>⚠️ Select Complaint Type</option>
            <option value="Academic Issue">📖 Academic Issue</option>
            <option value="Hostel Problem">🏠 Hostel Problem</option>
            <option value="Infrastructure Issue">🏗️ Infrastructure Issue</option>
            <option value="Harassment Complaint">🚨 Harassment Complaint</option>
            <option value="Faculty Misconduct">👨‍🏫 Faculty Misconduct</option>
            <option value="Library Issue">📚 Library Issue</option>
            <option value="Sports & Extracurricular Issues">⚽ Sports Issues</option>
            <option value="Medical Emergency">🏥 Medical Emergency</option>
            <option value="Administration Issue">🏢 Administration Issue</option>
            <option value="Other">❓ Other</option>
        </select>

        <input type="text" name="subject" class="form-control" placeholder="📝 Subject" required>
        <textarea name="description" class="form-control" placeholder="🗒️ Complaint Description" rows="3" required></textarea>

        <div class="row">
            <div class="col-md-12">
                <label>Incident Date:</label>
                <input type="date" name="incident_date" class="form-control">
            </div>
        </div>

        <label>Upload Evidence:</label>
        <div class="col-md-6"> <input type="file" name="evidence1" class="form-control"></div>
        <div class="col-md-6"> <input type="file" name="evidence2" class="form-control"> </div>
        
        <button type="submit" class="btn btn-primary btn-block mt-3">📨 Submit Complaint</button>
    </form>

    <a href="view_status.php" class="btn btn-secondary btn-block mt-2">📂 View your Complaints Status</a>
</div>

<script>
    $(document).ready(function() {
        $('#complaintForm').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './php/complaints/upload_complaint.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Complaint submitted successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error submitting complaint.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
