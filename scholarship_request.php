<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'scholarshipphp/db.php';

// Check login
if (!isset($_SESSION['regno'])) {
    header('Location: login.php');
    exit();
}

// DB connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$regno = $_SESSION['regno'];
$errorMessage = "";

// Fetch student details
$query = "SELECT stName, regno, fatherOccupation, motherOccupation, community, currentYear, class FROM st1 WHERE regno = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $regno);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    $errorMessage = "Student details not found.";
} else {
    // Check for previous request within 3 months
    $checkQuery = "SELECT requestDate FROM scholarship_requests WHERE regno = ? AND requestDate >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) ORDER BY requestDate DESC LIMIT 1";
    $stmt2 = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt2, "s", $regno);
    mysqli_stmt_execute($stmt2);
    $checkResult = mysqli_stmt_get_result($stmt2);

    if (mysqli_num_rows($checkResult) > 0) {
        $lastRequest = mysqli_fetch_assoc($checkResult);
        $lastRequestDate = $lastRequest['requestDate'];
        $nextRequestDate = date('Y-m-d', strtotime($lastRequestDate . ' + 90 days'));
        $daysLeft = ceil((strtotime($nextRequestDate) - time()) / (60 * 60 * 24));

        $errorMessage = "You can only submit one scholarship request every 3 months.<br><br>" .
                        "Last request date: <strong>" . date('d M Y', strtotime($lastRequestDate)) . "</strong><br>" .
                        "Days left to make a new request: <strong>" . $daysLeft . " days</strong>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scholarship Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>

    <style>
        body {
            background: url("pic/education-bg.jpg") no-repeat center center/cover;
            min-height: 100vh;
            padding-top: 5rem;
        }
        .container {
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control, .form-select {
            margin-bottom: 10px;
        }
        .btn-block {
            width: 100%;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
        }
        .required-star {
            color: red;
            position: relative;
            top: 1rem;
        }
    </style>
</head>
<body>

<?php
if (file_exists("includes/nav.php")) {
    include("includes/nav.php");
} else {
    echo "<div class='text-danger text-center'>Navigation file not found.</div>";
}
?>

<div class="container">
    <h3 class="text-center text-primary">🎓 Scholarship Request Form</h3>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <div class="text-center mt-2">Check your old status below:</div>
        <a href="view_status_scholarship.php" class="btn btn-secondary btn-block mt-2">📂 View Request Status</a>
    <?php else: ?>
        <form id="scholarshipForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <label>Student Name <span class="text-danger">*</span></label>
                    <input type="text" name="stName" class="form-control" value="<?php echo htmlspecialchars($student['stName'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label>Registration Number <span class="text-danger">*</span></label>
                    <input type="text" name="regno" class="form-control" value="<?php echo htmlspecialchars($student['regno'] ?? ''); ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Father's Occupation <span class="text-danger">*</span></label>
                    <input type="text" name="fatherOccupation" class="form-control" value="<?php echo htmlspecialchars($student['fatherOccupation'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label>Mother's Occupation <span class="text-danger">*</span></label>
                    <input type="text" name="motherOccupation" class="form-control" value="<?php echo htmlspecialchars($student['motherOccupation'] ?? ''); ?>" readonly>
                </div>
            </div>

            <input type="hidden" name="community" value="<?php echo htmlspecialchars($student['community'] ?? ''); ?>">
            <input type="hidden" name="currentYear" value="<?php echo htmlspecialchars($student['currentYear'] ?? ''); ?>">

            <div class="row">
                <div class="col-md-6">
                    <span class="required-star">*</span>
                    <input type="text" name="account_holder_name" class="form-control" placeholder="👤 Account Holder Name" required>
                </div>
                <div class="col-md-6">
                    <span class="required-star">*</span>
                    <input type="text" name="account_number" class="form-control" placeholder="🔢 Account Number" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <span class="required-star">*</span>
                    <input type="text" name="ifsc_code" class="form-control" placeholder="🏦 IFSC Code" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <span class="required-star">*</span>
                    <textarea name="reason" class="form-control" placeholder="📝 Reason for Scholarship" rows="3" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Income certificate <span class="text-danger">*</span></label>
                    <input type="file" name="incomeCert" class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
                </div>
                <div class="col-md-6">
                    <label>Upload Other Proofs</label>
                    <input type="file" name="otherProofs" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">📨 Submit Request</button>
        </form>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('#scholarshipForm').on('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You cannot make another request for the next 3 months!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(this);

                    $.ajax({
                        url: './scholarshipphp/upload_scholarship.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire('Success!', 'Scholarship request submitted!', 'success')
                                .then(() => location.reload());
                        },
                        error: function() {
                            Swal.fire('Error!', 'Submission failed. Try again.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Cancelled', 'Submission aborted.', 'info');
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
