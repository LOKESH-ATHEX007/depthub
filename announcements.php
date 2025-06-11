

<?php
include('php/functions.php');

$conn = dbConnect();
if ($conn === FALSE) {
    die("Database connection failed.");
}


$class=null;
$currentYear=null;
$query1 = "SELECT currentYear, class FROM st1 WHERE regno = 2201721033026";
$query1Res=mysqli_query($conn,$query1);
$query1Final= mysqli_fetch_assoc($query1Res);
if ($query1Final) {
    $class = $query1Final['class'];
    $currentYear = $query1Final['currentYear'];
    echo "Class: $class, Current Year: $currentYear";
}




$query2 = "SELECT * FROM announcements where sentTo='everyone' or sentTo='$currentYear' or sentTo='$currentYear$class' ";
$result = $conn->query($query2);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <style>
        /* Wrapper for announcements */
        .announcements-wrapper {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Individual announcement */
        .announcement {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
            position: relative;
        }

        /* Announcement header: Title and Date */
        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .announcement-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .announcement-date {
            font-size: 12px;
            color: #777;
        }

        /* Message content */
        .announcement-message {
            margin: 10px 0;
            color: #555;
        }

        /* File and links section */
        .announcement-extras {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .announcement-file,
        .announcement-links {
            flex: 1;
        }

        .announcement-file a,
        .announcement-links a {
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }

        .announcement-file a:hover,
        .announcement-links a:hover {
            text-decoration: underline;
        }

        /* Button styling */
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color:rgb(151, 201, 255);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            margin: 5px 0;
        }

        .btn:hover {
            background-color:rgb(191, 222, 255);
        }

        .btn-green {
            background-color:rgb(197, 255, 210);
        }

        .btn-green:hover {
            background-color:rgb(189, 255, 203);
        }

        /* Sent by */
        .announcement-sender {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="announcements-wrapper">
        <h2>Announcements</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="announcement">
                    <div class="announcement-header">
                        <div class="announcement-title"><?php echo $row['title']; ?></div>
                        <div class="announcement-date"><?php echo $row['createdAt']; ?></div>
                    </div>
                    <div class="announcement-message"><?php echo $row['message']; ?></div>
                    <div class="announcement-extras">
                        <?php if (!empty($row['file'])): ?>
                            <div class="announcement-file">
                                <a href="<?php echo htmlspecialchars($row['file']); ?>" class="btn btn-green" target="_blank">View File</a>
                                <a href="<?php echo htmlspecialchars($row['file']); ?>" class="btn" download>Download File</a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($row['links'])): ?>
                            <div class="announcement-links">
                                <a href="<?php echo htmlspecialchars($row['links']); ?>" class="btn" target="_blank">Visit Link</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="announcement-sender">Sent by: <?php echo $row['sentBy']; ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
        <?php $conn->close(); ?>
    </div>
</body>
</html>
