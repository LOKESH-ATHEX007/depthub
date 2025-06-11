<?php
$conn = mysqli_connect("sql202.infinityfree.com", "if0_39191720", "JqlHPQjc3rGSk", "if0_39191720_depthub");

$id = $_POST['id'];
mysqli_query($conn, "DELETE FROM timetable_uploads WHERE id = $id");

mysqli_close($conn);
?>
