<?php
include('php/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = dbConnect();
    $department_id = null;

    // Check if user is a teacher (has T_id in session)
    if (isset($_SESSION['T_id'])) {
        $teacher_id = $_SESSION['T_id'];
        $dept_query = "SELECT department FROM teachers WHERE T_id = ?";
        $dept_stmt = $conn->prepare($dept_query);
        $dept_stmt->bind_param("s", $teacher_id); // Using "s" for string since T_id is varchar
        $dept_stmt->execute();
        $dept_result = $dept_stmt->get_result();
        
        if ($dept_result->num_rows > 0) {
            $department_id = $dept_result->fetch_assoc()['department'];
        }
        $dept_stmt->close();
    }
    // Check if user is a department admin (has dept_admin_id in session)
    elseif (isset($_SESSION['dept_admin_id'])) {
        $admin_id = $_SESSION['dept_admin_id'];
        $dept_query = "SELECT dept_id FROM dept_admins WHERE dept_admin_id = ?";
        $dept_stmt = $conn->prepare($dept_query);
        $dept_stmt->bind_param("s", $admin_id); // Using "s" for string since dept_admin_id is varchar
        $dept_stmt->execute();
        $dept_result = $dept_stmt->get_result();
        
        if ($dept_result->num_rows > 0) {
            $department_id = $dept_result->fetch_assoc()['dept_id'];
        }
        $dept_stmt->close();
    }

    // If department couldn't be determined
    if ($department_id === null) {
        echo "Error: Could not determine department for this user.";
        $conn->close();
        exit();
    }

    $sentBy = $_POST['sentBy'];
    $sentTo = $_POST['sentTo'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $file = null;
    $links = isset($_POST['links']) ? $_POST['links'] : null;
    $important = isset($_POST['important']) ? 1 : 0;
    $expiryDate = !empty($_POST['expiryDate']) ? $_POST['expiryDate'] : NULL;
    $category = $_POST['category'];

    // File upload handling
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $target_dir = "announcementsFiles/";
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file = $target_file;
        } else {
            echo "Error uploading file.";
            $conn->close();
            exit();
        }
    }

    // Prepare SQL query with department
    $query = "INSERT INTO announcements (sentBy, sentTo, title, message, filePath, link, important, expiryDate, category, dept_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssssssssss", $sentBy, $sentTo, $title, $message, $file, $links, $important, $expiryDate, $category, $department_id);
        if ($stmt->execute()) {
            echo "<script>alert('Announcement posted successfully.'); window.location.href = 'trHomepage.php';</script>";
            $stmt->close();
            $conn->close();
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing the query: " . $conn->error;
    }

    $conn->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="styles/checkBox.css">
    <link rel="stylesheet" href="styles/announce.css">
    <link rel="stylesheet" href="styles/contact2.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
</head>
<body>

   <?php 
   include "includes/nav2.php"
   ?>
      
        <div class="outerWrapper">
        <form method="POST" enctype="multipart/form-data" class="formContainer">

          <div class="container">
            <h2>Post a New Announcement</h2>
    
            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">

            <div style="flex: 1;">
                    <label for="sentTo">Send To</label>
                    <select id="sentTo" name="sentTo" required>
                        <option value="everyone">Everyone</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="1A">1st A sec</option>
                        <option value="1B">1st B sec</option>
                        <option value="1C">1st C sec</option>
                        <option value="2A">2nd A sec</option>
                        <option value="2B">2nd B sec</option>
                        <option value="2C">2nd C sec</option>
                        <option value="3A">3rd A sec</option>
                        <option value="3B">3rd B sec</option>
                        <option value="3C">3rd C sec</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
                <div style="flex: 1;">
                    <label for="sentBy">From</label>
                    <input type="text" id="sentBy" name="sentBy" required>
                </div>
              
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div style="flex: 1;">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="general">General</option>
                        <option value="exams">Exams</option>
                        <option value="assignments">Assignments</option>
                        <option value="events">Events</option>                       
                        <option value="scholarships">Scholarships</option>
                        <option value="placements">Placements</option>                       
                        <option value="internships">Interships</option>
                        
                       
                    </select>
                </div>
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <label for="file">Attach File</label>
                    <input type="file" id="file" name="file">
                </div>
                <div style="flex: 1;">
                    <label for="links">Additional URL</label>
                    <input type="text" id="links" name="links">
                </div>
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <label for="expiryDate">Expiry Date</label>
                    <input type="date" id="expiryDate" name="expiryDate" required>
                </div>
                <div style="flex: 1;">
                    <label for="important">Mark as Important</label>
                   
                 <div class="toggler">
                      <input id="toggler-1" name="important" type="checkbox" value="1">
                        <label for="toggler-1">
                        <svg class="toggler-on" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                       <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5"></polyline>
                       </svg>
                      <svg class="toggler-off" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                     <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                     <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
                     </svg>
                    </label>
                    </div>
                </div>
            </div>

       

            <button type="submit" class="submit-button">Post Announcement</button>
       
          </div>

          <div class="container2">
            <h4>Enter Your Message here</h4>
             
            <textarea name="message" id="" required></textarea>

          </div>
      
          </form>
        </div>

     

</body>
</html>
