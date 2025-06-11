<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Student Management</title>
</head>
<body>
    <div class="container my-5">
        <h2>List of Students</h2>
        <div class="mb-3 d-flex align-items-center">
            <a href="stForm2.php" class="btn btn-primary me-3" role="button">New Student</a>
            <input type="text" name="search" placeholder="Search Student..." id="search_bar" class="form-control w-50">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    
                    <th>Current Year</th>
                    <th>Class</th>
                    <th>Phone No</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <?php include "php/stList_T.php"; ?>
            </tbody>
        </table>
    </div>

    <script src="js/search.js"></script>
</body>
</html>
