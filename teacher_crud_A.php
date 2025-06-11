<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION["dept_admin_id"], $_SESSION["dept_id"])) {
    header("Location: login.php");
    exit();
}

// Create database connection that will stay open
$conn = new mysqli("sql202.infinityfree.com", "if0_39191720", "JqlHPQjc3rGSk", "if0_39191720_depthub");
if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Database connection failed: " . htmlspecialchars($conn->connect_error) . "</div>");
}

// Store department ID
$dept_id = $conn->real_escape_string($_SESSION["dept_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management - <?= htmlspecialchars($dept_id) ?></title>
    <link href="./bootstrap/bootstrap.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .small-dropdown { width: 180px !important; }
        .action-btns { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">Teacher Management - <?= htmlspecialchars($dept_id) ?> Department</h2>
        
        <div class="mb-4 d-flex flex-wrap align-items-center gap-3">
            <a href="teacher_form_A.php" class="btn btn-primary">Add New Teacher</a>
            
            <div class="flex-grow-1">
                <input type="text" id="search_bar" placeholder="Search by name or ID..." 
                       class="form-control" style="max-width: 400px;">
            </div>

            <select id="sortDesignation" class="form-select small-dropdown">
                <option value="">All Designations</option>
                <?php
                $designations = $conn->query(
                    "SELECT DISTINCT designation FROM teachers 
                     WHERE department = '$dept_id' 
                     AND designation IS NOT NULL
                     ORDER BY designation");
                
                if ($designations) {
                    while ($row = $designations->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['designation']) . "'>" 
                            . htmlspecialchars($row['designation']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="teacherTable">
                    <?php 
                    // Get initial teacher list
                    $teachers = $conn->query(
                        "SELECT T_id, tName, phone, email, designation 
                         FROM teachers 
                         WHERE department = '$dept_id'
                         ORDER BY tName");
                    
                    if ($teachers && $teachers->num_rows > 0) {
                        while ($row = $teachers->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['T_id']) . "</td>
                                <td>" . htmlspecialchars($row['tName']) . "</td>
                                <td>" . htmlspecialchars($row['phone']) . "</td>
                                <td>" . htmlspecialchars($row['email']) . "</td>
                                <td>" . htmlspecialchars($row['designation'] ?? 'N/A') . "</td>
                                <td class='action-btns'>
                                    <a href='teacher_view_A.php?t_id=" . urlencode($row['T_id']) . "' class='btn btn-info btn-sm'>View</a>
                                    <a href='teacher_edit_A.php?t_id=" . urlencode($row['T_id']) . "' class='btn btn-primary btn-sm'>Edit</a>
                                    <button class='btn btn-danger btn-sm delete-teacher-btn' data-tid='" . htmlspecialchars($row['T_id']) . "'>Delete</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No teachers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
$(document).ready(function() {
    let searchTimer;
    const debounceTime = 300;

    // Delete handler (unchanged)
    $(document).on('click', '.delete-teacher-btn', function() {
        // ... existing delete code ...
    });

    function loadTeachers() {
        const search = $("#search_bar").val().trim();
        const designation = $("#sortDesignation").val();
        
        $("#teacherTable").html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
        
        $.ajax({
            url: "php/teacher_search_A.php",
            type: "GET",
            data: { 
                search: search,
                designation: designation 
            },
            success: function(response) {
                $("#teacherTable").html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $("#teacherTable").html(
                    '<tr><td colspan="6" class="text-center text-danger">Error loading teachers</td></tr>'
                );
            }
        });
    }

    // Event handlers
    $("#search_bar").on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadTeachers, debounceTime);
    });

    $("#sortDesignation").on('change', loadTeachers);

    // Initial load
    loadTeachers();
});
</script>
</body>
</html>
<?php 
// Close connection only if it's still open
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>