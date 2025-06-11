<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/t_dashboard.css"/>
     <link rel="stylesheet" href="styles/contact2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
<body>
    <?php
     session_start(); // This must be the FIRST line in your PHP file
     if (!isset($_SESSION['T_id'])) {
         header('Location: index.php');
         exit();
     }
    $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");


     // Fetch teacher's image
$teacherImage = "images/default_teacher.jpg"; // Default image path
try {
    $stmt = $conn->prepare("SELECT imageFile FROM teachers WHERE T_id = ?");
    $stmt->execute([$_SESSION['T_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && !empty($result['imageFile'])) {
        $teacherImage = "uploads/teachers/" . $result['imageFile'];
    }
} catch (PDOException $e) {
    error_log("Error fetching teacher image: " . $e->getMessage());
}

     include("includes/nav2.php");

    ?>

    
<div class="main--content">
    <div class="header--wrapper">
        <div class="header--title">
            <span>Teacher</span>
            <h2>Dashboard</h2>
        </div>
   
    
     <div class="user--info">
    <a href="teacher_view_A.php?t_id=<?php echo $_SESSION['T_id']; ?>">
        <img src="<?php echo htmlspecialchars($teacherImage); ?>" alt="Teacher Profile Image">
    </a>
</div>
    </div>

    <div class="card--container">
        <h3 class="main--title">Department data</h3>
        <div class="card--wrapper">
            <div class=" payment--card light-blue"  data-link="tmsg_board1.php">
                <div class="card--header">
                    <div class="amount">
                        <span class="title"></span>
                        
                        <span class="amount--value">Connect with Students</span>
                    </div>
                    <i class="fa-solid fa-comments icon dark-blue"></i>
                </div>
                <span class="card-detail"id="unread-msg-count">Loading...</span>
                </div> 


                <div class=" payment--card light-red"  data-link="msg_board_all.php">
                <div class="card--header">
                    <div class="amount">
                        <span class="title"></span>
                        
                        <span class="amount--value">Connect with Department Students</span>
                    </div>
                    <i class="fa-solid fa-users icon dark-red"></i>
                </div>
                <div class="card-detail" id="unread-msg-class-count" >Loading...</div>
                </div> 



 


          

                
                        <div class="payment--card light-blue" data-link="st_crud_T.php">
                            <div class="card--header">
                                <div class="amount">
                                    <span class="title"></span>
                                
                                    <span class="amount--value">Manage your students Data</span>
                                </div>
                                <i class="fa-solid fa-user-graduate icon dark-yellow" style="color:rgb(255, 255, 255);"></i>
                            </div>
                            <div id="total-students-count"></div>
                            </div> 
    </div> 
    
    <div class="card--wrapper">

            
    <div class="payment--card light-green" data-link="view_scholarship_request.php">
                        <div class="card--header">
                            <div class="amount">
                                <span class="title"></span>
                                
                                <span class="amount--value">Scholarship Requests</span>
                            </div>
                            <i class="fa-solid fa-graduation-cap icon dark-green2"></i>
                        </div>
                        <span class="card-detail" id="pending-scholarship-count">4 Requests</span>
                        </div> 
           

     



 

                <div class="payment--card light-purple" data-link="announce.php">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title"></span>
                            
                            <span class="amount--value">Post Announcements</span>
                        </div>
                        <i class="fa-solid fa-chalkboard-user icon dark-purple"></i>
                    </div>
                    <span class="card-detail"></span>
                    </div> 


                    

                    <div class="payment--card light-green" data-link="internshipentry.php">
                        <div class="card--header">
                            <div class="amount">
                                <span class="title"></span>
                                
                                <span class="amount--value">Post Internship</span>
                            </div>
                            <i class="fa-solid fa-graduation-cap icon dark-green"></i>
                        </div>
                        <span class="card-detail"></span>
                        </div> 

                     
    </div>

    </div>

    <div class="tabular--wrapper">
   
   <div class="card--wrapper">
         


  
           <div class="feature-card payment--card" data-link="upload_timetable.php">
               <img src="pic/female.png" alt="" >
               <h3>My profile</h3>
               <p>See all your details</p>
               
           </div>

           <div class="feature-card payment--card" data-link="stForm2.php">
               <img src="pic/student.png" alt="" >
               <h3>Enroll Students</h3>
               <p>Add details of new Student</p>
           </div>
          





         

                   
   </div>
</div>

  
</div>
    


<script>

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".payment--card").forEach(card => {
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

// Function to fetch general unread messages
function fetchUnreadMessages() {
    fetch("php/fetch/get_unread_messages.php") // Replace with your actual endpoint
        .then(response => response.json())
        .then(data => {
            let unreadMsgDiv = document.getElementById("unread-msg-count");
            if (unreadMsgDiv) {
                if (data.unread_count !== undefined) {
                    let count = data.unread_count;
                    unreadMsgDiv.innerText = count > 0 ? `${count} new messages` : "no new messages";
                } else {
                    unreadMsgDiv.innerText = "no new messages";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching unread messages:", error);
            let unreadMsgDiv = document.getElementById("unread-msg-count");
            if (unreadMsgDiv) {
                unreadMsgDiv.innerText = "no new messages";
            }
        });
}




// Function to fetch unread messages from assigned class students (for teachers)
function fetchUnreadMessagesClass() {
    fetch("php/fetch/get_unread_messages_class.php") // Replace with your actual endpoint
        .then(response => response.json())
        .then(data => {
            let unreadMsgDiv = document.getElementById("unread-msg-class-count");
            if (unreadMsgDiv) {
                if (data.unread_count !== undefined) {
                    let count = data.unread_count;
                    unreadMsgDiv.innerText = count > 0 ? `${count} new messages` : "no new messages";
                } else {
                    unreadMsgDiv.innerText = "no new messages";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching unread messages from class:", error);
            let unreadMsgDiv = document.getElementById("unread-msg-class-count");
            if (unreadMsgDiv) {
                unreadMsgDiv.innerText = "no new messages";
            }
        });
}


function fetchTotalStudents() {
    fetch("php/fetch/get_total_students.php") // Replace with your actual endpoint
        .then(response => response.json())
        .then(data => {
            let totalStudentsDiv = document.getElementById("total-students-count");
            if (totalStudentsDiv) {
                if (data.total_students !== undefined) {
                    totalStudentsDiv.innerText = `Total Students: ${data.total_students}`;
                } else if (data.error) {
                    totalStudentsDiv.innerText = data.error;
                } else {
                    totalStudentsDiv.innerText = "Data not available";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching total students:", error);
            let totalStudentsDiv = document.getElementById("total-students-count");
            if (totalStudentsDiv) {
                totalStudentsDiv.innerText = "Error loading student count";
            }
        });
}

// Call this function when needed, for example when the page loads
window.addEventListener('load', fetchTotalStudents);

function fetchPendingScholarshipCount() {
    fetch("php/fetch/get_pending_scholarships_count.php")
        .then(response => response.json())
        .then(data => {
            let pendingScholarshipDiv = document.getElementById("pending-scholarship-count");
            if (pendingScholarshipDiv) {
                if (data.pending_scholarship_count !== undefined) {
                    let count = data.pending_scholarship_count;
                    if(count == 1) {
                        pendingScholarshipDiv.innerText = `${count} new request`;
                    } else {
                        pendingScholarshipDiv.innerText = count > 0 ? `${count} new requests` : "no new requests";
                    }
                } else {
                    pendingScholarshipDiv.innerText = "0 Requests";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching pending scholarship requests:", error);
            let pendingScholarshipDiv = document.getElementById("pending-scholarship-count");
            if (pendingScholarshipDiv) {
                pendingScholarshipDiv.innerText = "0 Requests";
            }
        });
}


function fetchNewComplaintCount() {
    fetch("php/fetch/get_new_complaints_count.php") // Path to your PHP script
        .then(response => response.json())
        .then(data => {
            let newComplaintDiv = document.getElementById("new-complaint-count");
            if (newComplaintDiv) {
                if (data.new_complaint_count !== undefined) {
                    let count = data.new_complaint_count;
                    if(count==1){
                        newComplaintDiv.innerText = count > 0 ? `${count} pending complaint` : "no new complaints";
                    }
                    else{
                    newComplaintDiv.innerText = count > 0 ? `${count} pending complaints` : "no new complaints";
                    }
                } else {
                    newComplaintDiv.innerText = "no new complaints";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching new complaints:", error);
            let newComplaintDiv = document.getElementById("new-complaint-count");
            if (newComplaintDiv) {
                newComplaintDiv.innerText = "no new complaints";
            }
        });
}



// Call both functions when the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    fetchUnreadMessages();
    fetchUnreadMessagesClass();
    fetchTotalStudents();
    fetchNewComplaintCount();
    fetchPendingScholarshipCount()
});

// Auto-refresh both functions every 5 seconds
setInterval(() => {
    fetchUnreadMessages();
    fetchUnreadMessagesClass();
    fetchTotalStudents();
    fetchNewComplaintCount();
    fetchPendingScholarshipCount()
}, 1000);


</script>
</body>
</html>