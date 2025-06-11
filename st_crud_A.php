<?php
session_start();
if (!isset($_SESSION["dept_admin_id"]) || !isset($_SESSION["dept_id"])) {
    header("Location: dept_admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Student Management</title>
    <link href="bootstrap/bootstrap.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .small-dropdown { width: 150px !important; }
        .action-btns { white-space: nowrap; }
    </style>
</head>
<body>
    <?php
    $conn = mysqli_connect("sql202.infinityfree.com", "if0_39191720", "JqlHPQjc3rGSk", "if0_39191720_depthub");
    if (!$conn) {
        die("<div class='alert alert-danger'>Database connection failed</div>");
    }
    
    $dept_id = $_SESSION["dept_id"];
    ?>

    <div class="container my-5">
        <h2 class="mb-4">Student Management - <?php echo htmlspecialchars($dept_id); ?> Department</h2>
        
        <div class="mb-4 d-flex flex-wrap align-items-center gap-3">
            <a href="stForm2.php" class="btn btn-primary">Add New Student</a>
            
            <div class="flex-grow-1">
                <input type="text" id="search_bar" placeholder="Search by name or regno..." 
                       class="form-control" style="max-width: 400px;">
            </div>

            <select id="sortYear" class="form-select small-dropdown">
                <option value="">All Years</option>
                <?php
                $years = mysqli_query($conn, 
                    "SELECT DISTINCT currentYear FROM st1 
                     WHERE department = '$dept_id' 
                     ORDER BY currentYear");
                while ($year = mysqli_fetch_assoc($years)) {
                    $selected = isset($_GET['year']) && $_GET['year'] == $year['currentYear'] ? 'selected' : '';
                    echo "<option value='{$year['currentYear']}' $selected>Year {$year['currentYear']}</option>";
                }
                ?>
            </select>

            <select id="sortClass" class="form-select small-dropdown">
                <option value="">All Classes</option>
                <?php
                $classes = mysqli_query($conn, 
                    "SELECT DISTINCT class FROM st1 
                     WHERE department = '$dept_id' 
                     ORDER BY class");
                while ($class = mysqli_fetch_assoc($classes)) {
                    $selected = isset($_GET['class']) && $_GET['class'] == $class['class'] ? 'selected' : '';
                    echo "<option value='{$class['class']}' $selected>{$class['class']}</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Year</th>
                        <th>Class</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <?php include "php/stList_A.php"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Load students with current filters
        function loadStudents() {
            const search = $("#search_bar").val();
            const year = $("#sortYear").val();
            const classFilter = $("#sortClass").val();
            
            $.ajax({
                url: "php/st_search_A.php",
                type: "GET",
                data: { 
                    search: search,
                    year: year, 
                    class: classFilter
                },
                beforeSend: function() {
                    $("#studentTable").html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                },
                success: function(response) {
                    $("#studentTable").html(response);
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load students', 'error');
                }
            });
        }

        // Initial load
        loadStudents();

        // Event handlers
        $("#search_bar").on('keyup', function() {
            loadStudents();
        });

        $("#sortYear, #sortClass").on('change', function() {
            loadStudents();
        });

        // Delete student handler
        $(document).on('click', '.delete-btn', function() {
            const regno = $(this).data('regno');
            Swal.fire({
                title: 'Confirm Delete',
                text: "Are you sure you want to delete this student?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "php/st_delete_A.php",
                        type: "POST",
                        data: { regno: regno },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Student has been deleted.', 'success');
                            loadStudents();
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete student', 'error');
                        }
                    });
                }
            });
        });
    });
    </script>
</body>
</html>