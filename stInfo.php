<?php
session_start();
include "includes/nav.php";
require "php/functions.php";

// Determine which regno to use (teacher's view or student's own view)
if (isset($_GET['regno'])) {
    $regno = $_GET['regno'];  // Teacher clicked view button
} elseif (isset($_SESSION['regno'])) {
    $regno = $_SESSION['regno']; // Student viewing their own details
} else {
    echo "<p style='color: red; text-align: center;'>Error: No student selected.</p>";
    exit;
}

// Fetch student data
$data = getStData($regno);

// Handle case where student is not found
if (!$data) {
    echo "<p style='color: red; text-align: center;'>Student data not found.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info Page</title>
    <link rel="stylesheet" href="styles/stInfo.css">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>

<section id="stInfoSec">
    <div class="leftDiv">
        <div class="imgHolder">
            <img src="images/stImages/<?php echo htmlspecialchars($data['imageFile']); ?>" alt="Student Image">
        </div>
        <h1 id="sName"><?php echo htmlspecialchars($data['stName']); ?></h1> 
        <h2 id="sId"><?php echo htmlspecialchars($data['regno']); ?></h2>
    </div>

    <div class="rightDiv">
        <h2>Personal Information</h2>
        <div class="dataGroup">
            <div class="dataField" id="long"><span>Name</span><?php echo htmlspecialchars($data['stName']); ?></div>
            <div class="dataField"><span>Register No</span><?php echo htmlspecialchars($data['regno']); ?></div>
            <div class="dataField"><span>DOB</span><?php echo htmlspecialchars($data['dob']); ?></div>
            <div class="dataField"><span>Gender</span><?php echo !empty($data['gender']) ? htmlspecialchars($data['gender']) : "Not Entered"; ?></div>
        </div>

        <h2>Contact Information</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Father's Name</span><?php echo htmlspecialchars($data['fatherName']); ?></div>
            <div class="dataField"><span>Mother's Name</span><?php echo htmlspecialchars($data['motherName']); ?></div>
            <div class="dataField"><span>Parent Number</span><?php echo htmlspecialchars($data['parentPhno']); ?></div>
            <div class="dataField"><span>Student Number</span><?php echo htmlspecialchars($data['studentPhno']); ?></div> 
            <div class="dataField" id="long"><span>Student E-mail</span><?php echo htmlspecialchars($data['email']); ?></div> 
        </div>

        <h2>Academic Information</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Year of Study</span><?php echo htmlspecialchars($data['currentYear']); ?></div>
            <div class="dataField"><span>Class</span><?php echo htmlspecialchars($data['class']); ?></div>
            <div class="dataField" id="fullLong"><span>Department</span><?php echo htmlspecialchars($data['department']); ?></div>  
        </div>

        <h2>Personal Details</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Blood Group</span><?php echo htmlspecialchars($data['bloodGroup']); ?></div>
            <div class="dataField"><span>Community</span><?php echo htmlspecialchars($data['community']); ?></div>
            <div class="dataField"><span>Caste</span><?php echo htmlspecialchars($data['caste']); ?></div>
            <div class="dataField"><span>Religion</span><?php echo htmlspecialchars($data['religion']); ?></div>
        </div>

        <h2>Other Information</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Father's Occupation</span><?php echo htmlspecialchars($data['fatherOccupation']); ?></div>
            <div class="dataField"><span>Mother's Occupation</span><?php echo htmlspecialchars($data['motherOccupation']); ?></div>
            <div class="dataField"><span>Student Type</span><?php echo htmlspecialchars($data['studentType']); ?></div>
            <div class="dataField"><span>Getting Scholarship</span><?php echo htmlspecialchars($data['scholarship']); ?></div>
            <div class="dataField"><span>Attending Placements</span><?php echo htmlspecialchars($data['placement']); ?></div>
        </div>

        <h2>Address</h2>
        <div class="dataGroup">
            <div class="dataField" id="fullLong"><span>Permanent Address</span><?php echo htmlspecialchars($data['permanentAddress']); ?></div>
            <div class="dataField" id="fullLong"><span>Current Address</span><?php echo htmlspecialchars($data['currentAddress']); ?></div>
        </div>
    </div>
</section>

</body>
</html>
