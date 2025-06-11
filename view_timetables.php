<?php

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM timetable_uploads ORDER BY uploadedAt DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>View Timetables</title>
</head>
<body>
    <div class="container my-5">
        <h2>Uploaded Timetables</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Class</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['currentYear'] ? "Year " . $row['currentYear'] : "All Years" ?></td>
                        <td><?= $row['class'] ? "Class " . $row['class'] : "All Classes" ?></td>
                        <td><img src="<?= $row['imagePath'] ?>" width="100"></td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).on("click", ".delete-btn", function() {
            var id = $(this).data("id");

            Swal.fire({
                title: "Are you sure?",
                text: "This timetable will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Delete"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("delete_timetable.php", { id: id }, function(response) {
                        location.reload();
                    });
                }
            });
        });
    </script>
</body>
</html>
