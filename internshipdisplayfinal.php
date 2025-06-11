<?php

$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";

$conn = new mysqli($servername, $username, $password, $database);

$regno ="SELECT regno FROM st1";

if (!$regno) {
    die("Registration number is missing.");
}

$query = "SELECT currentYear, class FROM st1 WHERE regno = 2201721033026";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching student details: " . mysqli_error($conn));
}
$student = mysqli_fetch_assoc($result);

$currentYear=null;
$class=null;
if($student){
    $currentYear = $student['currentYear'];
    $class = $student['class'];
}



$internship_query = "
    SELECT * 
    FROM internships 
    WHERE sentTo = 'everyone' 
    OR sentTo = '$currentYear' 
    OR sentTo = '$currentYear$class' ORDER BY createdAt DESC";

$internships = mysqli_query($conn, $internship_query);
if (!$internships) {
    die("Error fetching internships: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internships</title>
    <link rel="stylesheet" href="styles/internshipDis.css">
    <link rel="stylesheet" href="styles/nav.css">
    



    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>




</head>
<body>
<?php
    require "includes/nav.php"
    ?>
    <div class="wrapper">
        <h2>Internships</h2>
        <?php while ($row = mysqli_fetch_assoc($internships)) { ?>
            <div class="internship">
                <div class="data-group">
                  <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                  <p> <?php echo htmlspecialchars($row['createdAt']); ?></p>
                </div>
           
                <p id="sentBy"> From: <?php echo htmlspecialchars($row['sentBy']); ?></p>
                <div class="messageWrapper">
                <pre><?php echo nl2br(htmlspecialchars($row['message'])); ?></pre>
   
                </div>

                
             
                <div>
                <?php if (!empty($row['link'])) { ?>
                    <a class="link button" href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank">Visit Link</a>
                <?php } ?>

                <?php if (!empty($row['filePath'])) { ?>
                    <a class="file-download button" href="<?php echo htmlspecialchars($row['filePath']); ?>" >View File</a>
                <?php } ?>

                <?php if (!empty($row['filePath'])) { ?>
                    <a class="file-download button" href="<?php echo htmlspecialchars($row['filePath']); ?>" download>Download File</a>
                <?php } ?>
                </div>

               
               
        




           
                
            </div>
        <?php } ?>
    </div>
</body>
</html>
