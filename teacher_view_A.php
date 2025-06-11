<?php
session_start();

require "php/functions.php";

// Determine which teacher ID to use
if (isset($_GET['t_id'])) {
    $t_id = $_GET['t_id'];  // Admin clicked view button
} elseif (isset($_SESSION['T_id'])) {
    $t_id = $_SESSION['T_id']; // Teacher viewing their own details
} else {
    echo "<p style='color: red; text-align: center;'>Error: No teacher selected.</p>";
    exit;
}

// Fetch teacher data with department name
$data = getTeacherData($t_id);

// Handle case where teacher is not found
if (!$data) {
    echo "<p style='color: red; text-align: center;'>Teacher data not found.</p>";
    exit;
}

// Format joining date if it exists
$joiningDate = !empty($data['joiningDate']) ? date('d-m-Y', strtotime($data['joiningDate'])) : 'Not Available';
$dob = !empty($data['dob']) ? date('d-m-Y', strtotime($data['dob'])) : 'Not Available';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Info Page</title>
    <link rel="stylesheet" href="styles/stInfo_t.css">
    <link rel="stylesheet" href="styles/contact2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
<body>
<?php require "includes/nav2.php"; ?>

<section id="stInfoSec">
    <div class="leftDiv">
        <div class="imgHolder">
            <?php if (!empty($data['imageFile'])): ?>
                <img src="uploads/teachers/<?php echo htmlspecialchars($data['imageFile']); ?>" alt="Teacher Image">
            <?php else: ?>
                <div class="no-image">No Image Available</div>
            <?php endif; ?>
        </div>
        <h1 id="sName"><?php echo htmlspecialchars($data['tName']); ?></h1> 
        <h2 id="sId"><?php echo htmlspecialchars($data['T_id']); ?></h2>
        <div class="status-badge <?php echo strtolower(htmlspecialchars($data['status'])); ?>">
            <?php echo htmlspecialchars($data['status']); ?>
        </div>
    </div>

    <div class="rightDiv">
        <h2>Personal Information</h2>
        <div class="dataGroup">
            <div class="dataField" id="long"><span>Full Name</span><?php echo htmlspecialchars($data['tName']); ?></div>
            <div class="dataField"><span>Teacher ID</span><?php echo htmlspecialchars($data['T_id']); ?></div>
            <div class="dataField"><span>Date of Birth</span><?php echo $dob; ?></div>
            <div class="dataField"><span>Gender</span><?php echo !empty($data['gender']) ? htmlspecialchars($data['gender']) : "Not Provided"; ?></div>
            <div class="dataField"><span>Blood Group</span><?php echo !empty($data['bloodGroup']) ? htmlspecialchars($data['bloodGroup']) : "Not Provided"; ?></div>
        </div>

        <h2>Contact Information</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Email</span><?php echo htmlspecialchars($data['email']); ?></div>
            <div class="dataField"><span>Phone Number</span><?php echo htmlspecialchars($data['phone']); ?></div>
            <div class="dataField"><span>Alternate Phone</span><?php echo !empty($data['alternatePhone']) ? htmlspecialchars($data['alternatePhone']) : "Not Provided"; ?></div>
        </div>

        <h2>Professional Information</h2>
        <div class="dataGroup">
            <div class="dataField"><span>Department</span><?php echo !empty($data['dept_name']) ? htmlspecialchars($data['dept_name']) : "Not Assigned"; ?></div>
            <div class="dataField"><span>Designation</span><?php echo !empty($data['designation']) ? htmlspecialchars($data['designation']) : "Not Provided"; ?></div>
            <div class="dataField"><span>Highest Qualification</span><?php echo !empty($data['qualification']) ? htmlspecialchars($data['qualification']) : "Not Provided"; ?></div>
            <div class="dataField"><span>Specialization</span><?php echo !empty($data['specialization']) ? htmlspecialchars($data['specialization']) : "Not Provided"; ?></div>
            <div class="dataField"><span>Joining Date</span><?php echo $joiningDate; ?></div>
            <div class="dataField"><span>Experience (Years)</span><?php echo !empty($data['experienceYears']) ? htmlspecialchars($data['experienceYears']) : "0"; ?></div>
            <div class="dataField"><span>Status</span><?php echo !empty($data['status']) ? htmlspecialchars($data['status']) : "Active"; ?></div>
        </div>

        <h2>Official Documents</h2>
        <div class="dataGroup">
            <div class="dataField"><span>PAN Number</span><?php echo !empty($data['panNumber']) ? htmlspecialchars($data['panNumber']) : "Not Provided"; ?></div>
            <div class="dataField"><span>Aadhar Number</span><?php echo !empty($data['aadharNumber']) ? htmlspecialchars($data['aadharNumber']) : "Not Provided"; ?></div>
        </div>

        <h2>Address Information</h2>
        <div class="dataGroup">
            <div class="dataField" id="fullLong"><span>Current Address</span><?php echo !empty($data['currentAddress']) ? nl2br(htmlspecialchars($data['currentAddress'])) : "Not Provided"; ?></div>
            <div class="dataField" id="fullLong"><span>Permanent Address</span><?php echo !empty($data['permanentAddress']) ? nl2br(htmlspecialchars($data['permanentAddress'])) : "Not Provided"; ?></div>
        </div>
    </div>
</section>

<style>
    .imgHolder .no-image {
        width: 150px;
        height: 200px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        border: 1px dashed #ccc;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 14px;
        margin-top: 10px;
        font-weight: bold;
    }
    
    .status-badge.active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.resigned {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-badge.retired {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

</body>
</html>