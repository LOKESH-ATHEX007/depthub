<?php
include("php/functions.php");

// Database connection
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");

// Initialize message variables
$message = $status = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $deptId = $_POST['dept_id'];
    $password = bin2hex(random_bytes(8)); // Generate random password (plain text)
    $startDate = date('Y-m-d');

    try {
        // Check if email exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM dept_admins WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already exists!");
        }

        // Generate admin ID (format: DEPT_CS_001)
        $lastAdmin = $conn->prepare("SELECT dept_admin_id FROM dept_admins 
                                   WHERE dept_id = ? 
                                   ORDER BY dept_admin_id DESC LIMIT 1");
        $lastAdmin->execute([$deptId]);
        $lastId = $lastAdmin->fetch(PDO::FETCH_ASSOC);

        if ($lastId) {
            $lastNum = (int)substr($lastId['dept_admin_id'], -3);
            $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNum = '001';
        }
        $adminId = "DEPT_" . $deptId . "_" . $newNum;

        // Get department name for response
        $deptStmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
        $deptStmt->execute([$deptId]);
        $deptName = $deptStmt->fetchColumn();

        // Insert new admin with plain text password
        $stmt = $conn->prepare("INSERT INTO dept_admins 
                              (dept_admin_id, full_name, email, password, dept_id, start_date)
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$adminId, $fullName, $email, $password, $deptId, $startDate]);

        // Return JSON for AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Department admin created successfully!',
                'admin_id' => $adminId,
                'password' => $password,
                'full_name' => $fullName,
                'email' => $email,
                'dept_name' => $deptName,
                'start_date' => $startDate
            ]);
            exit;
        }

        $message = "Department admin created successfully! Password: " . $password;
        $status = "success";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $status = "error";
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $adminId = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM dept_admins WHERE dept_admin_id = ?");
        $stmt->execute([$adminId]);

        $message = "Admin deleted successfully!";
        $status = "success";
    } catch (Exception $e) {
        $message = "Error deleting admin: " . $e->getMessage();
        $status = "error";
    }
}

// Fetch departments for dropdown
$departments = $conn->query("SELECT dept_id, dept_name FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing admins
$admins = $conn->query("
    SELECT da.*, d.dept_name 
    FROM dept_admins da
    JOIN departments d ON da.dept_id = d.dept_id
    ORDER BY da.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Department Admin</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles/contact2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        display: flex;
        gap: 20px;
        background-color: #f4f4f4;
        height: 100dvh;
    }

    .admin-container {
        display: flex;
        align-items: flex-start;
        width: calc(100% - 120px);
        margin-left: 120px;
        gap: 20px;
    }

    .admin-form {
        width: 320px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 10px;
        height: 100%;
        background-color: aliceblue;
    }

    .admin-form h3 {
        text-align: center;
        margin-bottom: 15px;
        color: #333;
    }

    .admin-form label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    .admin-form input,
    .admin-form select,
    .admin-form button {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .admin-form button {
        background: #007bff;
        color: white;
        cursor: pointer;
        font-weight: bold;
        margin-top: 15px;
    }

    .admin-form button:hover {
        background: #0056b3;
    }

    .admin-table {
        flex-grow: 1;
        max-width: 75%;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    .admin-table h2 {
        margin-bottom: 10px;
        text-align: center;
    }

    .table-container {
        max-height: 400px;
        overflow-y: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #007bff;
        color: white;
        text-align: center;
    }

    tr:hover {
        background: #f9f9f9;
    }

    .delete-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
    }

    .delete-btn:hover {
        background: #c82333;
    }

    .reset-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
        margin-right: 5px;
    }

    .reset-btn:hover {
        background: #218838;
    }

    td:nth-child(6) {
        min-width: 180px;
    }
    </style>
</head>
<body>
<?php require "includes/nav2.php" ?>

<div class="admin-container">
    <form method="POST" class="admin-form" id="adminForm">
        <h3>Create Department Admin</h3>
        
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        
        <label for="dept_id">Department:</label>
        <select name="dept_id" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept['dept_id']) ?>">
                    <?= htmlspecialchars($dept['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Create Admin</button>
    </form>

    <div class="admin-table">
        <h2>Existing Department Admins</h2>
        <div class="table-container">
            <table id="adminTable">
                <thead>
                    <tr>
                        <th>Admin ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['dept_admin_id']) ?></td>
                            <td><?= htmlspecialchars($admin['full_name']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= htmlspecialchars($admin['dept_name']) ?></td>
                            <td><?= htmlspecialchars($admin['password']) ?></td>
                            <td>
                                <button class="reset-btn" 
                                        onclick="resetPassword('<?= $admin['dept_admin_id'] ?>')">
                                    Reset Password
                                </button>
                                <button class="delete-btn" 
                                        onclick="confirmDelete('<?= $admin['dept_admin_id'] ?>')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// AJAX form submission
$("#adminForm").submit(function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Creating Admin...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        type: "POST",
        url: "",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                html: `<p>${response.message}</p><p>Admin ID: <strong>${response.admin_id}</strong></p>
                       <p>Password: <strong>${response.password}</strong></p>`,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Add new admin to the table
                addAdminToTable(response);
                // Clear the form
                document.getElementById("adminForm").reset();
            });
        },
        error: function(xhr) {
            let error = JSON.parse(xhr.responseText);
            Swal.fire('Error!', error.message, 'error');
        }
    });
});

// Add new admin to the table
function addAdminToTable(admin) {
    const tbody = document.querySelector('#adminTable tbody');
    const newRow = document.createElement('tr');
    
    newRow.innerHTML = `
        <td>${admin.admin_id}</td>
        <td>${admin.full_name}</td>
        <td>${admin.email}</td>
        <td>${admin.dept_name}</td>
        <td>${admin.password}</td>
        <td>
            <button class="reset-btn" onclick="resetPassword('${admin.admin_id}')">
                Reset Password
            </button>
            <button class="delete-btn" onclick="confirmDelete('${admin.admin_id}')">
                Delete
            </button>
        </td>
    `;
    
    // Insert at the top of the table
    tbody.insertBefore(newRow, tbody.firstChild);
    
    // Add hover effect
    newRow.addEventListener('mouseenter', () => {
        newRow.style.backgroundColor = '#f9f9f9';
    });
    newRow.addEventListener('mouseleave', () => {
        newRow.style.backgroundColor = '';
    });
}

function confirmDelete(adminId) {
    Swal.fire({
        title: "Confirm Deletion",
        text: "Remove this department admin?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, delete!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?delete=1&id=" + adminId;
        }
    });
}

function resetPassword(adminId) {
    Swal.fire({
        title: 'Reset Password?',
        text: 'This will generate a new password',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'reset_password.php',
                type: 'POST',
                data: { admin_id: adminId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            `Password reset! New password: <strong>${response.new_password}</strong>`,
                            'success'
                        ).then(() => {
                            updatePasswordInTable(adminId, response.new_password);
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to reset password', 'error');
                }
            });
        }
    });
}

function updatePasswordInTable(adminId, newPassword) {
    const rows = document.querySelectorAll('#adminTable tbody tr');
    rows.forEach(row => {
        if (row.cells[0].textContent === adminId) {
            row.cells[4].textContent = newPassword;
        }
    });
}

<?php if ($message): ?>
Swal.fire({ 
    text: "<?= addslashes($message) ?>", 
    icon: "<?= $status ?>", 
    timer: 2000, 
    showConfirmButton: false 
});
<?php endif; ?>
</script>

</body>
</html>