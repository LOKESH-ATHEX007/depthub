<?php
session_start();
include("php/functions.php");

// Database connection with error handling
try {
    $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed. Please try again later.");
}

// Verify department session
if (!isset($_SESSION['dept_id'])) {
    die(json_encode(["error" => "Department not identified"]));
}
$session_dept_id = $_SESSION['dept_id'];

// Initialize message variables
$message = $status = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clear any previous messages
    $message = $status = null;
    
    $currentYear = $_POST['currentYear'];
    $class = $_POST['class'];
    $T_id = $_POST['T_id'];

    try {
        // 1. Verify teacher exists and get their department
        $teacherStmt = $conn->prepare("SELECT department FROM teachers WHERE T_id = ?");
        $teacherStmt->execute([$T_id]);
        $teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            throw new Exception("Teacher not found!");
        }

        $teacherDept = $teacher['department'];

        // 2. Hybrid validation - session vs teacher's department
        if ($session_dept_id != $teacherDept) {
            throw new Exception("You can only assign teachers from your department (Department ID: $session_dept_id)");
        }

        // 3. Check for existing class/year in same department
        $classCheck = $conn->prepare("SELECT COUNT(*) FROM class_teacher_allocation 
                                    WHERE currentYear = ? AND class = ? AND dept_id = ?");
        $classCheck->execute([$currentYear, $class, $session_dept_id]);
        
        if ($classCheck->fetchColumn() > 0) {
            throw new Exception("Class $class (Year $currentYear) already has a teacher in your department!");
        }

        // 4. Check if teacher is already assigned (any department)
        $teacherCheck = $conn->prepare("SELECT cta.*, d.dept_name 
                                      FROM class_teacher_allocation cta
                                      JOIN departments d ON cta.dept_id = d.dept_id
                                      WHERE cta.T_id = ?");
        $teacherCheck->execute([$T_id]);
        
        if ($existing = $teacherCheck->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("This teacher is already assigned to: ".
                              "Class {$existing['class']} (Year {$existing['currentYear']}) ".
                              "in {$existing['dept_name']} department");
        }

        // 5. Insert new allocation
        $insertStmt = $conn->prepare("INSERT INTO class_teacher_allocation 
                                     (currentYear, class, T_id, dept_id) 
                                     VALUES (?, ?, ?, ?)");
        
        if ($insertStmt->execute([$currentYear, $class, $T_id, $session_dept_id])) {
            $message = "Successfully assigned teacher to Class $class (Year $currentYear)";
            $status = "success";
        } else {
            throw new Exception("Failed to assign teacher");
        }

    } catch (Exception $e) {
        $message = $e->getMessage();
        $status = "error";
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    // Clear any previous messages
    $message = $status = null;
    
    $currentYear = $_GET['year'];
    $class = $_GET['class'];

    try {
        // 1. Verify allocation exists and belongs to department
        $verifyStmt = $conn->prepare("SELECT cta.*, t.tName 
                                    FROM class_teacher_allocation cta
                                    JOIN teachers t ON cta.T_id = t.T_id
                                    WHERE currentYear = ? AND class = ? AND cta.dept_id = ?");
        $verifyStmt->execute([$currentYear, $class, $session_dept_id]);
        $allocation = $verifyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$allocation) {
            throw new Exception("Allocation not found in your department!");
        }

        // 2. Delete allocation
        $deleteStmt = $conn->prepare("DELETE FROM class_teacher_allocation 
                                    WHERE currentYear = ? AND class = ? AND dept_id = ?");
        
        if ($deleteStmt->execute([$currentYear, $class, $session_dept_id])) {
            $_SESSION['flash_message'] = "Unassigned {$allocation['tName']} from Class $class (Year $currentYear)";
            $_SESSION['flash_status'] = "success";
            header("Location: ".strtok($_SERVER['REQUEST_URI'], '?'));
            exit();
        } else {
            throw new Exception("Failed to unassign teacher");
        }

    } catch (Exception $e) {
        $_SESSION['flash_message'] = $e->getMessage();
        $_SESSION['flash_status'] = "error";
        header("Location: ".strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
}

// Check for flash messages from redirect
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $status = $_SESSION['flash_status'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_status']);
}

// Fetch data for display
try {
    // Get teachers only from current department
    $teachers = $conn->prepare("SELECT T_id, tName FROM teachers WHERE department = ?");
    $teachers->execute([$session_dept_id]);
    $teachers = $teachers->fetchAll(PDO::FETCH_ASSOC);

    // Get allocations for current department with teacher names
    $allocations = $conn->prepare("SELECT cta.currentYear, cta.class, t.tName, d.dept_name
                                 FROM class_teacher_allocation cta
                                 JOIN teachers t ON cta.T_id = t.T_id
                                 JOIN departments d ON cta.dept_id = d.dept_id
                                 WHERE cta.dept_id = ?
                                 ORDER BY cta.currentYear, cta.class");
    $allocations->execute([$session_dept_id]);
    $allocations = $allocations->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error loading data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Teacher Allocation</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles/nav3.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
<style>
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    /* display: flex;
    gap: 20px; */
    background-color:rgb(255, 255, 255);
    height:100vh;
    max-height:100vh;
    
}

.allocation-container {
    display: flex;
    align-items: flex-start;
    width: calc(100% - 120px);
    margin-left: 120px;
    gap:20px;
    height:100%;
}

/* Form Styling */
.allocation-form {
    width: 320px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
    height:100%;
    max-height:100%;
    background-color:aliceblue;
}

.allocation-form h3 {
    text-align: center;
    margin-bottom: 15px;
    color: #333;
}

.allocation-form label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.allocation-form select,
.allocation-form button {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.allocation-form button {
    background: #007bff;
    color: white;
    cursor: pointer;
    font-weight: bold;
    margin-top: 15px;
}

.allocation-form button:hover {
    background: #0056b3;
}

/* Table Styling */
.allocation-table {
    flex-grow: 1;
    max-width: 75%;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

.allocation-table h2 {
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
</style>

</head>
<body>
<?php
    require "includes/nav3.php"
    ?>
<div class="allocation-container">
    <form method="POST" class="allocation-form">
        <h3>Assign Teacher</h3>
        <label for="currentYear">Current Year:</label>
        <select name="currentYear" required>
            <option value="">Select Year</option>
            <?php for ($i = 1; $i <= 8; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select>

        <label for="class">Class:</label>
        <select name="class" required>
            <option value="">Select Class</option>
            <?php foreach (range('A', 'Z') as $letter) echo "<option value='$letter'>$letter</option>"; ?>
        </select>

        <label for="T_id">Assign Teacher:</label>
        <select name="T_id" required>
            <option value="">Select Teacher</option>
            <?php foreach ($teachers as $teacher) echo "<option value='{$teacher['T_id']}'>{$teacher['tName']}</option>"; ?>
        </select>

        <button type="submit">Allocate</button>
    </form>

    <div class="allocation-table">
        <h2>Existing Allocations</h2>
        <table>
            <tr>
                <th>Year</th>
                <th>Class</th>
                <th>Assigned Teacher</th>
                <th>Action</th>
            </tr>
            <?php if (empty($allocations)): ?>
                <tr>
                    <td colspan="4">No allocations found for your department</td>
                </tr>
            <?php else: ?>
                <?php foreach ($allocations as $allocation): ?>
                    <tr>
                        <td><?= htmlspecialchars($allocation['currentYear']); ?></td>
                        <td><?= htmlspecialchars($allocation['class']); ?></td>
                        <td><?= htmlspecialchars($allocation['tName']); ?></td>
                        <td><button class="delete-btn" onclick="confirmDelete('<?= $allocation['currentYear']; ?>', '<?= $allocation['class']; ?>')">Unassign</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
    function confirmDelete(year, className) {
        Swal.fire({
            title: "Are you sure?",
            text: "Unassign this teacher?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, unassign!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?delete=1&year=" + year + "&class=" + className;
            }
        });
    }

    <?php if ($message): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ 
            text: "<?= addslashes($message); ?>", 
            icon: "<?= $status; ?>", 
            timer: 2000, 
            showConfirmButton: false 
        });
    });
    <?php endif; ?>
    </script>

</body>
</html>