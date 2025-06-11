<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <link rel="stylesheet" href="styles/nav3.css"/>
     <link rel="stylesheet" href="styles/dept_admin_dashboard.css"/>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
<body>
<?php
session_start(); // This must be the FIRST line in your PHP file
if (!isset($_SESSION['dept_admin_id'])) {
    header('Location: dept_admin_login.php');
    exit();
}

// Database connection
$conn = new PDO("mysql:host=localhost;dbname=depthub", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch department name
$dept_name = "Department"; // Default value
if (isset($_SESSION['dept_id'])) {
    try {
        $stmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
        $stmt->execute([$_SESSION['dept_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['dept_name'])) {
            $dept_name = $result['dept_name'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching department name: " . $e->getMessage());
    }
}

include("includes/nav3.php");
?>

    
<div class="main--content">
    <div class="header--wrapper">
        <div class="header--title">
            <span>Department Admin</span>
            <h2>Dashboard</h2>
        </div>
   
    
    <div class="user--info header--title">
    <h2><?php echo htmlspecialchars($dept_name); ?></h2>
    </div>
    </div>

    <div class="card--container">
        <h3 class="main--title">Department data</h3>
        <div class="card--wrapper">


        <div class=" payment--card light-red"  data-link="complaints_admin.php">
                <div class="card--header">
                    <div class="amount">
                        <span class="title"></span>
                        
                        <span class="amount--value">Review Complaints</span>
                    </div>
                    <div class="icon1">
                    <img src="pic/feedback.png" alt="">
                    </div>
                </div>
                <span class="card-detail" id="new-complaint-count"></span>
                </div> 


        

            <div class=" payment--card light-blue"  data-link="teacher_crud_A.php">
                <div class="card--header">
                    <div class="amount">
                        <span class="title"></span>
                        
                        <span class="amount--value">Manage Teachers</span>
                    </div>
                     
                    <div class="icon1">
                    <img src="pic/female.png" alt="">
                    </div>
                    
                </div>
                <span class="card-detail"id="dept-teachers-count"></span>
                </div> 


        



 


          

                        <div class="payment--card light-blue1" data-link="st_crud_A.php">
                            <div class="card--header">
                                <div class="amount">
                                    <span class="title"></span>
                                
                                    <span class="amount--value">Manage Students</span>
                                </div>
                                <i class="fa-solid fa-user-graduate icon dark-yellow" style="color:rgb(255, 255, 255);"></i>
                            </div>
                           
                            <span class="card-detail" id="dept-students-count"></span>
                            </div> 
    </div> 
    
    <div class="card--wrapper">

            
    <div class="payment--card light-green" data-link="admin_scholarship_requests.php">
                        <div class="card--header">
                            <div class="amount">
                                <span class="title"></span>
                                
                                <span class="amount--value">Scholarship Requests</span>
                            </div>
                            <i class="fa-solid fa-graduation-cap icon dark-green2"></i>
                        </div>
                        <span class="card-detail" id="under-process-scholarship-count"></span>
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


                    

                    <div class="payment--card light-orange" data-link="internshipentry.php">
                        <div class="card--header">
                            <div class="amount">
                                <span class="title"></span>
                                
                                <span class="amount--value">Post Internship</span>
                            </div>
                            <div class="icon1">
                    <img src="pic/internship.png" alt="">
                    </div>
                        </div>
                        <span class="card-detail"></span>
                        </div> 

                     
    </div>

    </div>
    <div class="tabular--wrapper">
   
    <div class="card--wrapper">
          


    <div class="feature-card payment--card" data-link="classTeacher.php">
    <img src="pic/delegation.png" alt="">
    <h3>Allocate Classes</h3>
    <p>Assign Teachers to classes of your Department</p>
    </div>
            <div class="feature-card payment--card" data-link="upload_timetable.php">
                <img src="pic/calendar.png" alt="" >
                <h3>Upload Time Tables</h3>
                <p>Add Time Table for classes</p>
                
            </div>

            <div class="feature-card payment--card" data-link="teacher_form_A.php">
                <img src="pic/female.png" alt="" >
                <h3>Enroll Teachers</h3>
                <p>Add details of new Teachers</p>
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


function fetchDeptStudentsCount() {
    fetch("php/fetch/get_dept_students_count.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const deptStudentsDiv = document.getElementById("dept-students-count");
            if (deptStudentsDiv) {
                if (data.total_students !== undefined) {
                    deptStudentsDiv.innerText = `Department Students: ${data.total_students}`;
                } else if (data.error) {
                    deptStudentsDiv.innerText = `Error: ${data.error}`;
                    console.error("Backend error:", data.error);
                } else {
                    deptStudentsDiv.innerText = "Student data not available";
                }
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            const deptStudentsDiv = document.getElementById("dept-students-count");
            if (deptStudentsDiv) {
                deptStudentsDiv.innerText = "Error loading department student count";
            }
        });
}

// Call the function when the page loads
window.addEventListener('load', fetchDeptStudentsCount);

function fetchUnderProcessScholarshipCount() {
    fetch("php/fetch/get_under_process_scholarships_count.php")
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const underProcessDiv = document.getElementById("under-process-scholarship-count");
            if (underProcessDiv) {
                if (data.under_process_scholarship_count !== undefined) {
                    const count = data.under_process_scholarship_count;
                    if (count === 1) {
                        underProcessDiv.innerText = `${count} request in process`;
                    } else {
                        underProcessDiv.innerText = count > 0 ? 
                            `${count} requests in process` : 
                            "no requests in process";
                    }
                    
                    // Optional: Add visual indicator for counts > 0
                    if (count > 0) {
                        underProcessDiv.classList.add('has-requests');
                    } else {
                        underProcessDiv.classList.remove('has-requests');
                    }
                } else if (data.error) {
                    console.error("Server error:", data.error);
                    underProcessDiv.innerText = "error loading requests";
                } else {
                    underProcessDiv.innerText = "0 requests in process";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching under process scholarship requests:", error);
            const underProcessDiv = document.getElementById("under-process-scholarship-count");
            if (underProcessDiv) {
                underProcessDiv.innerText = "error loading requests";
            }
        });
}

function fetchDeptTeachersCount() {
    fetch("php/fetch/get_dept_teachers_count.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const deptTeachersDiv = document.getElementById("dept-teachers-count");
            if (deptTeachersDiv) {
                if (data.total_teachers !== undefined) {
                    deptTeachersDiv.innerText = `Department Teachers: ${data.total_teachers}`;
                } else if (data.error) {
                    deptTeachersDiv.innerText = `Error: ${data.error}`;
                    console.error("Backend error:", data.error);
                } else {
                    deptTeachersDiv.innerText = "Teacher data not available";
                }
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            const deptTeachersDiv = document.getElementById("dept-teachers-count");
            if (deptTeachersDiv) {
                deptTeachersDiv.innerText = "Error loading teacher count";
            }
        });
}

// Call the function when the page loads
window.addEventListener('load', fetchDeptTeachersCount);

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
    fetchNewComplaintCount();
    fetchUnderProcessScholarshipCount();
});

// Auto-refresh both functions every 5 seconds
setInterval(() => {
    fetchUnreadMessages();
    fetchUnreadMessagesClass();
    fetchNewComplaintCount();
    fetchUnderProcessScholarshipCount();
}, 5000);


</script>
</body>
</html>