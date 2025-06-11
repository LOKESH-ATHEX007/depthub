<?php
session_start();
include 'db.php';

// Redirect to login if the user is not logged in


if (!isset($_GET['id'])) {
    die("Invalid Request!");
}

$id = $_GET['id'];

// Fetch scholarship request details
$query = "SELECT sr.*, s.stName, s.studentPhno, s.currentYear, s.class, s.community 
          FROM scholarship_requests sr 
          JOIN st1 s ON sr.regno = s.regno 
          WHERE sr.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$request = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$request) {
    die("Request not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../styles/contact2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa;
            background:url("../pic/scholarship_elements_bg.png"); 
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
  /* padding-left:120px; */
         
        }
        .container { max-width: 900px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); }
        h3 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        .complaint-wrapper { display: flex; gap: 20px; margin-top: 20px; }
        .left-panel, .right-panel { flex: 1; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .right-panel { display: flex; flex-direction: column; justify-content: space-between; }
        .table th { width: 40%; background-color: #f8f9fa; }
        .subject-header { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .description { background-color: #f0f0f0; padding: 10px; border-radius: 4px; margin-top: 10px; }
        .status-section { margin-top: 20px; }
        .alert { margin-top: 10px; padding: 10px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    
<div class="container">
    <h3>Update Request Details</h3>
    <div class="complaint-wrapper">
        <div class='left-panel'>
            <table class='table table-bordered'>
                <tr><th>Registration No:</th><td><?= htmlspecialchars($request['regno']) ?></td></tr>
                <tr><th>Student Name:</th><td><?= htmlspecialchars($request['stName']) ?></td></tr>
                <tr><th>Phone number:</th><td><?= htmlspecialchars($request['studentPhno']) ?></td></tr>
                <tr><th>Community:</th><td><?= htmlspecialchars($request['community']) ?></td></tr>
                <tr><th>Status:</th><td><?= htmlspecialchars($request['status']) ?></td></tr>
                <tr><th>Request Date:</th><td><?= htmlspecialchars($request['requestDate']) ?></td></tr>
                <tr><th>Account Holder Name:</th><td><?= htmlspecialchars($request['account_holder_name']) ?></td></tr>
                <tr><th>Account Number:</th><td><?= htmlspecialchars($request['account_number']) ?></td></tr>
                <tr><th>IFSC Code:</th><td><?= htmlspecialchars($request['ifsc_code']) ?></td></tr>
                <tr><th>Income Certificate:</th>
                    <td>
                        <?php if (!empty($request['incomeCert'])): ?>
                            <a href="uploads/<?= htmlspecialchars($request['incomeCert']) ?>" download>Download</a>
                        <?php else: ?>
                            No file uploaded
                        <?php endif; ?>
                    </td>
                </tr>
                <tr><th>Other Proofs:</th>
                    <td>
                        <?php if (!empty($request['otherProofs'])): ?>
                            <a href="uploads/<?= htmlspecialchars($request['otherProofs']) ?>" download>Download</a>
                        <?php else: ?>
                            No file uploaded
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <form id="deleteRequestForm" style="margin-top: 20px;">
                <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                <button type="submit" class="btn btn-danger">Delete Request</button>
            </form>
        </div>

        <div class='right-panel'>
            <table class='table table-bordered'>
                <tr><th>Father's Occupation:</th><td><?= htmlspecialchars($request['father_occupation']) ?></td></tr>
                <tr><th>Mother's Occupation:</th><td><?= htmlspecialchars($request['mother_occupation']) ?></td></tr>
            </table>

            <p><strong>Subject:</strong></p>
            <textarea readonly style="width: 100%; padding: 10px; margin-top: 10px; border-radius: 4px; border: 1px solid #ccc; height: 200px; resize: none; background-color: #f0f0f0;"><?= htmlspecialchars($request['reason']) ?></textarea>

            <form id="updateStatusForm">
                <label for="status" style="margin-top: 20px;"><strong>Update Status:</strong></label>
                <select name="status" id="status" style="width: 100%; padding: 10px; margin-top: 10px; border-radius: 4px; border: 1px solid #ccc;" required>
                    <option value="Pending" <?= $request['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Under Process" <?= $request['status'] === 'Under Process' ? 'selected' : '' ?>>Under Process</option>
                </select>
                <button type="submit" class="btn btn-success" style="width: 100%; padding: 10px; margin-top: 10px; border-radius: 4px; border: none; background-color: #28a745; color: white;">Update Status</button>
            </form>

            <div id="statusMessage" style="margin-top: 10px;"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Handle form submission using AJAX
        $('#updateStatusForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            const formData = $(this).serialize(); // Serialize form data
            const id = <?= $request['id'] ?>; // Get the request ID from PHP

            $.ajax({
                url: 'update_status.php', // Backend script to handle the update
                type: 'POST',
                data: formData + '&id=' + id, // Include the request ID
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Display success message
                        $('#statusMessage').html('<div class="alert alert-success">' + result.message + '</div>');
                        // Update the status in the table
                        $('td:contains("<?= htmlspecialchars($request['status']) ?>")').text(result.newStatus);
                    } else {
                        // Display error message
                        $('#statusMessage').html('<div class="alert alert-danger">' + result.message + '</div>');
                    }
                },
                error: function () {
                    // Display error message if the AJAX request fails
                    $('#statusMessage').html('<div class="alert alert-danger">Failed to update status. Please try again.</div>');
                }
            });
        });

        // Handle delete request form submission
        $('#deleteRequestForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX request
                    const formData = $(this).serialize(); // Serialize form data

                    $.ajax({
                        url: 'delete_request.php', // Backend script to handle the deletion
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            const result = JSON.parse(response);
                            if (result.success) {
                                // Display success message and redirect
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: result.message,
                                }).then(() => {
                                    history.back(); // Redirect to the previous page
                                });
                            } else {
                                // Display error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message,
                                });
                            }
                        },
                        error: function () {
                            // Display error message if the AJAX request fails
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete the request. Please try again.',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>