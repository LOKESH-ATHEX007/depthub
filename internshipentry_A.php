<?php
include('php/functions.php');
session_start(); // Make sure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sentBy = $_POST['sentBy'];
    $sentTo = $_POST['sentTo'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $category = $_POST['category'];
    $file = null;
    $link = isset($_POST['link']) ? $_POST['link'] : null;
    $expiryDate = !empty($_POST['expiryDate']) ? $_POST['expiryDate'] : NULL;

    // Get department ID based on who is logged in
    $dept_id = null;
    $conn = dbConnect();
    
    if (isset($_SESSION['T_id'])) {
        // Teacher is logged in - get department from teachers table
        $teacher_id = $_SESSION['T_id'];
        $query = "SELECT department FROM teachers WHERE T_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $dept_id = $row['department'];
        }
        $stmt->close();
    } elseif (isset($_SESSION['dept_admin_id'])) {
        // Department admin is logged in - fetch their department ID from database
        $dept_admin_id = $_SESSION['dept_admin_id'];
        
        // Query to get the department ID for this admin
        $query = "SELECT dept_id FROM dept_admins WHERE dept_admin_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dept_admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dept_id = $row['dept_id'];
        } else {
            // Handle case where department admin record isn't found
            $dept_id = null; // or some error handling
        }
        
        $stmt->close();
    }
    
    if (!$dept_id) {
        echo "Error: Could not determine department.";
        exit();
    }

    // File upload handling (your existing code)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $target_dir = "internshipFiles/";
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file = $target_file;
        } else {
            echo "Error uploading file.";
            exit();
        }
    }

    // Prepare SQL query with dept_id
    $query = "INSERT INTO internships (sentBy, sentTo, title, message, filePath, link, expiryDate, category, dept_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("sssssssss", $sentBy, $sentTo, $title, $message, $file, $link, $expiryDate, $category, $dept_id);
        if ($stmt->execute()) {
            echo "<script>alert('Internship posted successfully.'); window.location.href = 'trHomepage.php';</script>";
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
    <title>Post Internshipt</title>
    <link rel="stylesheet" href="styles/checkBox.css">
    <link rel="stylesheet" href="styles/announce.css">

    <link rel="stylesheet" href="styles/contact2.css"/>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
</head>
<body>
    <?php include "includes/nav3.php" ?>
    
    <div class="outerWrapper">
    <form method="POST"  enctype="multipart/form-data" class="formContainer">

    <div class="container">
        <h2>Post a New Internship</h2>
        
            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">

            <div style="flex: 1;">
                    <label for="sentTO">Send To</label>
                    <select id="sentTO" name="sentTo" required>
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
                    <input type="text" id="sentBy" name="sentBy" required >
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
                    <option value="" disabled selected>Select an option</option>
        <option value="web-development">Web Development</option>
        <option value="data-science">Data Science</option>
        <option value="marketing">Marketing</option>
        <option value="graphic-design">Graphic Design</option>
        <option value="finance">Finance</option>
        <option value="human-resources">Human Resources</option>
        <option value="software-engineering">Software Engineering</option>
        <option value="content-writing">Content Writing</option>
        <option value="cyber-security">Cyber Security</option>
        <option value="project-management">Project Management</option>
        <option value="others">Others</option>
                       
                    </select>
                </div>
               
            </div>

          
            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <label for="file">Attach File</label>
                    <input type="file" id="file" name="file">
                </div>
                <div style="flex: 1;">
                    <label for="link">Additional URL</label>
                    <input type="text" id="link" name="link">
                </div>
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <label for="expiryDate">Expiry Date</label>
                    <input type="date" id="expiryDate" name="expiryDate" required>
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
