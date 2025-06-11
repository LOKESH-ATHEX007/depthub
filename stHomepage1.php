<?php
session_start();
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";


$conn = new mysqli($servername, $username, $password, $database);

if (!isset($_SESSION["regno"])) {
    header("Location: index.php");
    exit();
}

$regno=$_SESSION['regno'];

$query = "SELECT stName,regno,currentYear, class,imageFile FROM st1 WHERE regno = $regno";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching student details: " . mysqli_error($conn));
}
$student = mysqli_fetch_assoc($result);

$currentYear = null;
$class = null;
if ($student) {
    $currentYear = $student['currentYear'];
    $class = $student['class'];
}

// Query to get the most recent important announcement
$important_announcement_query = "
    SELECT A_id, title, createdAt, category, sentBy 
    FROM announcements 
    WHERE (sentTo = 'everyone' 
        OR sentTo = '$currentYear' 
        OR sentTo = '$currentYear$class') 
        AND (expiryDate IS NULL OR expiryDate >= CURDATE()) 
        AND important = 1
    ORDER BY createdAt DESC
    LIMIT 1";

$important_announcement = mysqli_query($conn, $important_announcement_query);
if (!$important_announcement) {
    die("Error fetching announcements: " . mysqli_error($conn));
}

$announcement_data = mysqli_fetch_assoc($important_announcement);
$title = $announcement_data['title'] ?? "No important announcements";
$sentBy = $announcement_data['sentBy'] ?? "N/A";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeptHub - Student Home</title>
    <link rel="stylesheet" href="styles/stHomePage1.css">
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
  include "includes/nav.php"
  ?>

    <div class="container">
        <div class="row row1">
            <div class="left-col">
             
                <div class="student-info">
                    
                <h1><?php echo $student['stName']; ?></h1>
                <h2><?php echo $student['regno']; ?></h2>
                </div>

<div class="communicateDiv">
    <h2>Stay Connected</h2>
    <p class="slogan">Communicate directly with your teachers !!!!</p>
    <div style="display: flex; align-items: center; margin-top:1.5rem; ">
        <img src="pic/network_783379.png" alt="">
       
            <a href="msg_board.php"> <button>Contact Now</button></a>
        
    </div>
    <span id="unread-msg-count" class="notification-badge">0</span> <!-- Notification badge -->
</div>

            </div>
            <div class="right-col">
                <div class="empty-div">
                
                        <div class="profileDiv">
                        <img src="images/stImages/<?php echo $student["imageFile"] ?> " alt="">
                        </div>

                        <div style="display:flex;flex-direction:column;align-items: center;">
                        <h4 >Student Info</h4>
                      
                       <a href="stInfo.php"> <button>view </button></a>
                    </div>
                  
                  
                </div>
                    

                <div class="notification-bar" id="notificationBar">
    <span class="close-btn" id="closeNotification">&times;</span>
    <h2 id="heading">IMPORTANT ANNOUNCEMENTS</h2>
    <h2 id="title"><?php echo htmlspecialchars($title); ?></h2>
    <?php if ($announcement_data): ?>
       
            <p id="from">From: <?php echo htmlspecialchars($sentBy); ?></p>
            <button><a href="a7.php">view now</a></button>
      
    <?php endif; ?>
</div>
                  

                <div class="smartDiv">
                    
                    <div class="text">
                        <h3>need help?</h3>
                        <h3>Day order?</h3>
                        <h3>Events?</h3>
                       <a href="dayorder_calendar.php" id="ClickHere">CLICK HERE</a>
                    </div>
                    <div class="robotDiv">
                        <img src="pic/robot.png" alt="">
                    </div>
                  

                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="row row2">
        <div class="feature-card" data-link="a7.php">
    <img src="pic/campaign_11413086.png" alt="">
    <h3>Announcements</h3>
    <p>View all the announcements posted by teachers.</p>
    <span id="new-announcement-count" class="notification-badge">0</span>
</div>
            <div class="feature-card" data-link="i7.php">
                <img src="pic/mentorship_15325540.png" alt="" >
                <h3>Internships</h3>
                <p>Explore available internships for students.</p>
                <span id="unread-internship-count" class="notification-badge">0</span>
            </div>
            <div class="feature-card" data-link="complaint.php">
                <img src="pic/feedback_1260185.png" alt="" >
                <h3>Reports & Feedback</h3>
                <p>Submit feedback or report issues to the department head.</p>
            </div>
            <div class="feature-card" data-link="scholarship_request.php">
                <img src="pic/scholarship_5609483.png" alt="" >
                <h3>Scholarship</h3>
                <p>Apply or request for scholarship</p>
            </div>
        </div>
    </div>



<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".feature-card").forEach(card => {
        card.style.cursor = "pointer"; // Ensure cursor changes
        card.addEventListener("click", function() {
            const link = this.getAttribute("data-link"); // Get target URL

            if (link && link.trim() !== "") { // Skip empty links
                console.log("Navigating to:", link);
                window.location.href = link;
            } else {
                console.log("No valid link for:", this);
            }
        });
    });
});

function fetchUnreadMessages() {
    fetch("php/fetch/get_unread_messages.php")
        .then(response => response.json())
        .then(data => {
            let unreadMsgBadge = document.getElementById("unread-msg-count");
            if (unreadMsgBadge) {
                if (data.unread_count !== undefined) {
                    let count = data.unread_count;
                    if (count > 0) {
                        unreadMsgBadge.innerText = count; // Update the badge count
                        unreadMsgBadge.style.display = "flex"; // Show the badge
                    } else {
                        unreadMsgBadge.style.display = "none"; // Hide the badge if count is 0
                    }
                } else {
                    unreadMsgBadge.style.display = "none"; // Hide the badge if no data
                }
            }
        })
        .catch(error => {
            console.error("Error fetching unread messages:", error);
            let unreadMsgBadge = document.getElementById("unread-msg-count");
            if (unreadMsgBadge) {
                unreadMsgBadge.style.display = "none"; // Hide the badge on error
            }
        });
}



function fetchNewAnnouncementCount() {
    fetch("php/fetch/get_new_announcements_count.php") // Path to your PHP script
        .then(response => response.json())
        .then(data => {
            let newAnnouncementBadge = document.getElementById("new-announcement-count");
            if (newAnnouncementBadge) {
                if (data.new_announcement_count !== undefined) {
                    let count = data.new_announcement_count;
                    if (count > 0) {
                        newAnnouncementBadge.innerText = count; // Update the badge count
                        newAnnouncementBadge.style.display = "flex"; // Show the badge
                    } else {
                        newAnnouncementBadge.style.display = "none"; // Hide the badge if count is 0
                    }
                } else {
                    newAnnouncementBadge.style.display = "none"; // Hide the badge if no data
                }
            }
        })
        .catch(error => {
            console.error("Error fetching new announcements:", error);
            let newAnnouncementBadge = document.getElementById("new-announcement-count");
            if (newAnnouncementBadge) {
                newAnnouncementBadge.style.display = "none"; // Hide the badge on error
            }
        });
}

function fetchUnreadInternshipCount() {
    fetch("php/fetch/get_unread_internships_count.php") // Path to your PHP script
        .then(response => response.json())
        .then(data => {
            let unreadInternshipBadge = document.getElementById("unread-internship-count");
            if (unreadInternshipBadge) {
                if (data.unread_count !== undefined) {
                    let count = data.unread_count;
                    if (count > 0) {
                        unreadInternshipBadge.innerText = count; // Update the badge count
                        unreadInternshipBadge.style.display = "flex"; // Show the badge
                    } else {
                        unreadInternshipBadge.style.display = "none"; // Hide the badge if count is 0
                    }
                } else {
                    unreadInternshipBadge.style.display = "none"; // Hide the badge if no data
                }
            }
        })
        .catch(error => {
            console.error("Error fetching unread internships count:", error);
            let unreadInternshipBadge = document.getElementById("unread-internship-count");
            if (unreadInternshipBadge) {
                unreadInternshipBadge.style.display = "none"; // Hide the badge on error
            }
        });
}






document.addEventListener("DOMContentLoaded", () => {
    fetchUnreadMessages();
    fetchNewAnnouncementCount()
    fetchUnreadInternshipCount() 
});

// Auto-refresh both functions every 5 seconds
setInterval(() => {
    fetchUnreadMessages();
    fetchNewAnnouncementCount()
    fetchUnreadInternshipCount() 
}, 5000);




document.addEventListener("DOMContentLoaded", function() {
    const notificationBar = document.getElementById("notificationBar");
    const closeNotification = document.getElementById("closeNotification");

    // Check if the notification has already been shown in this session
    const notificationShown = sessionStorage.getItem("notificationShown");

    // Show the notification bar only if there is an important announcement AND it hasn't been shown yet in this session
    <?php if ($announcement_data): ?>
        if (!notificationShown) {
            notificationBar.style.display = "block";
        }
    <?php endif; ?>

    // Close the notification bar when the close button is clicked
    closeNotification.addEventListener("click", function() {
        notificationBar.style.display = "none";
        sessionStorage.setItem("notificationShown", "true"); // Mark the notification as shown for this session
    });

    // Optionally, auto-hide the notification after a few seconds
    setTimeout(function() {
        notificationBar.style.display = "none";
        sessionStorage.setItem("notificationShown", "true"); // Mark the notification as shown for this session
    }, 5000); // Hide after 5 seconds
});

</script>
</body>
</html>
