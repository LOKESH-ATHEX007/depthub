<?php

$conn = mysqli_connect("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM timetable_uploads ORDER BY uploadedAt DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>" . ($row['currentYear'] ? "Year " . $row['currentYear'] : "All Years") . "</td>
            <td>" . ($row['class'] ? "Class " . $row['class'] : "All Classes") . "</td>
            <td>
                <button class='btn btn-info btn-sm view-btn' data-img='{$row['imagePath']}'>View Image</button>
                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>
            </td>
          </tr>";
}

mysqli_close($conn);
?>
