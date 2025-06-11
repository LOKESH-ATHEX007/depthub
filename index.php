<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/signIn.css"/>
    <link
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />
    <title>Sign In Page</title>
    <style>
        .container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background-color: #fff;
            overflow: hidden;
        }

        #error-msg-container {
            color: white;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            margin-inline: auto;
            width: 10rem;
            background-color: rgb(255, 85, 85);
            padding: 10px 0;
            font-size: 14px;
            z-index: 1000;
            border-radius: 0 0 5px 5px;
            opacity: 0; /* Hidden by default */
            visibility: hidden; /* Prevent interaction */
            transition: opacity 0.3s ease, visibility 0.3s ease; /* Smooth transition */
        }
    </style>
</head>
<body>
   <div class="container">
       <div id="error-msg-container"></div>

       <div class="form-container">
           <div class="signin-signup">
               <!-- Student Login Form -->
               <form action="" method="POST" class="sign-in-form">
                   <h2 class="title">Student Login</h2>
                   <div class="input-field">
                       <i class="fa-solid fa-user"></i>
                       <input type="text" name="regno" placeholder="Register No" required>
                   </div>
                   <div class="input-field">
                       <i class="fa-solid fa-envelope"></i>
                       <input type="email" name="email" placeholder="Email" required>
                   </div>
                   <input type="submit" name="submit_student" value="Login" class="btn solid">
               </form>
               
               <!-- Teacher Login Form -->
               <form action="" method="POST" class="sign-up-form">
                   <h2 class="title">Teacher Login</h2>
                   <div class="input-field">
                       <i class="fa-solid fa-user"></i>
                       <input type="text" name="T_id" placeholder="Teacher ID" required>
                   </div>
                   <div class="input-field">
                       <i class="fa-solid fa-envelope"></i>
                       <input type="email" name="email" placeholder="Email" required>
                   </div>
                   <input type="submit" name="submit_teacher" value="Login" class="btn solid">
               </form>
           </div>
       </div>
       <div class="panels-container">
           <div class="panel left-panel">
               <div class="content">
                   <h3>Teacher?</h3>
                   <p>Sign In now, Take care of your students!</p>
                   <button class="btn transparent" id="sign-up-btn">Sign In</button>
               </div>
               <img src="pic/rb_2147907627.png" class="image" alt="">
           </div>
           <div class="panel right-panel">
               <div class="content">
                   <h3>Student?</h3>
                   <p>Welcome back! Sign In to stay connected with your class.</p>
                   <button class="btn transparent" id="sign-in-btn">Sign In</button>
               </div>
               <img src="pic/7367529_3647050.svg" class="image" alt="">
           </div>
       </div>
   </div>

   <?php

include "session_start.php";
   
include("php/functions.php"); // Include your functions

// Handle Student Login
if (isset($_POST['submit_student'])) {
    $regno = $_POST['regno'];
    $email = $_POST['email'];

    // Connect to the database
   $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");


    // Fetch the student data from the st1 table using regno and email
    $stmt = $conn->prepare("SELECT * FROM st1 WHERE regno = :regno AND email = :email");
    $stmt->bindValue(":regno", $regno);
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        // Set session variables for main application
        $_SESSION['regno'] = $regno;
        $_SESSION['student_dept_id'] = $student['department']; // Store department ID
        // Set session variables for chat feature
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = "student";
        // Redirect to student homepage
        header("Location: stHomepage1.php");
        exit();
    } else {
        echo '<script>
            const errorMsgContainer = document.getElementById("error-msg-container");
            errorMsgContainer.textContent = "Invalid credentials!";
            errorMsgContainer.style.visibility = "visible";
            errorMsgContainer.style.opacity = "1";
            setTimeout(() => {
                errorMsgContainer.style.opacity = "0";
                errorMsgContainer.style.visibility = "hidden";
            }, 2000);
        </script>';
    }
}

// Handle Teacher Login
if (isset($_POST['submit_teacher'])) {
    $T_id = $_POST['T_id'];
    $email = $_POST['email'];

    // Connect to the database
    $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");

    // Fetch the teacher data from the teachers table using T_id and email
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE T_id = :T_id AND email = :email");
    $stmt->bindValue(":T_id", $T_id);
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher) {
        // Set session variables for main application
        $_SESSION['T_id'] = $T_id;
        $_SESSION['teacher_dept_id'] = $teacher['department']; // Store department ID
        // Set session variables for chat feature
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = "teacher";
        // Redirect to teacher homepage
        header("Location: trHomePage.php");
        exit();
    } else {
        echo '<script>
            const errorMsgContainer = document.getElementById("error-msg-container");
            errorMsgContainer.textContent = "Invalid credentials!";
            errorMsgContainer.style.visibility = "visible";
            errorMsgContainer.style.opacity = "1";
            setTimeout(() => {
                errorMsgContainer.style.opacity = "0";
                errorMsgContainer.style.visibility = "hidden";
            }, 2000);
        </script>';
    }
}
?>
   <script src="scripts/login.js"></script>
</body>
</html>