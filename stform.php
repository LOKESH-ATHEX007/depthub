<?php

include("php/functions.php");

if(isset($_POST['submit'])){
    // Handle image upload
    if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){ 
        $fileName = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];
        $folder = "images/stImages/" . $fileName; // Add a trailing slash to the folder path
        if (move_uploaded_file($tempName, $folder)) {
            $imageFile = $fileName; // Store the uploaded file name
        } else {
            echo "Error uploading image: " . error_get_last()['message'];
            exit();
        }
    } else {
        $imageFile = ""; // Set a default value (or handle the case where no image is uploaded)
    }

    // Get form data
    $stName = $_POST['name'];
    $regno = $_POST['regno'];
    $department = $_POST['department'];
    $dob = $_POST['dob'];
    $gender=$_POST['gender'];
    $class = $_POST['class'];
    $currentYear = $_POST['currentYear']; 
    $semester = $_POST['semester']; 
    $studentType = $_POST['studentType']; 
    $fatherName = $_POST['fatherName'];
    $motherName = $_POST['motherName'];
    $bloodGroup = $_POST['bloodGroup'];
    $permanentAddress = $_POST['permanentAddress'];
    $currentAddress = $_POST['currentAddress'];
    $community = $_POST['community'];
    $caste = $_POST['caste'];
    $religion = $_POST['religion'];
    $scholarship = $_POST['scholarship'];
    $parentPhno = $_POST['parentPhno'];
    $studentPhno = $_POST['studentPhno'];
    $fatherOccupation = $_POST['fatherOccupation'];
    $motherOccupation = $_POST['motherOccupation'];
    $placement = $_POST['placement'];
    $email=$_POST['email'];


  $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");


    $str = "INSERT INTO st1 (regno, imageFile, stName, dob,gender, department, semester, class, currentYear, fatherName, motherName,studentType,scholarship, bloodGroup, permanentAddress, currentAddress, community, caste, religion,  fatherOccupation, motherOccupation, parentPhno, studentPhno,email, placement) 
            VALUES (:regno, :imageFile, :stName, :dob,:gender, :department, :semester, :class, :currentYear,:fatherName, :motherName,:studentType,:scholarship, :bloodGroup, :permanentAddress, :currentAddress, :community, :caste, :religion,   :fatherOccupation, :motherOccupation, :parentPhno, :studentPhno,:email,:placement)";

    try {
        $st = $conn->prepare($str);
        $st->bindValue(":regno", $regno); 
        $st->bindValue(":imageFile", $imageFile); 
        $st->bindValue(":stName", $stName); 
        $st->bindValue(":dob", $dob); 
        $st->bindValue(":gender", $gender); 
        $st->bindValue(":department", $department); 
        $st->bindValue(":class", $class); 
        $st->bindValue(":currentYear", $currentYear); 
        $st->bindValue(":semester", $semester); 
        $st->bindValue(":studentType", $studentType); 
        $st->bindValue(":scholarship", $scholarship); 
        $st->bindValue(":fatherName", $fatherName); 
        $st->bindValue(":motherName", $motherName); 
        $st->bindValue(":bloodGroup", $bloodGroup); 
        $st->bindValue(":permanentAddress", $permanentAddress); 
        $st->bindValue(":currentAddress", $currentAddress); 
        $st->bindValue(":community", $community); 
        $st->bindValue(":caste", $caste); 
        $st->bindValue(":religion", $religion); 
        $st->bindValue(":parentPhno", $parentPhno); 
        $st->bindValue(":studentPhno", $studentPhno);
        $st->bindValue(":email",$email);
        $st->bindValue(":fatherOccupation", $fatherOccupation); 
        $st->bindValue(":motherOccupation", $motherOccupation); 
        $st->bindValue(":placement", $placement);  
        $st->execute();

        echo "Student data inserted successfully!";

    } catch (PDOException $e) {
        echo " unsuccessful.. attempt " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Entry Form</title>
    <link rel="stylesheet" href="styles/stForm.css">

    <link rel="stylesheet" href="styles/contact2.css"/>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
   
</head>

<body>
    <?php
    require "includes/nav2.php"
    ?>
    <form method="POST" enctype="multipart/form-data">
    <section id="formSec">
        
        <div class="leftDiv">
        
        <h1>Student Data Form</h1>
            <div id="imageContainer" class="image-container">
                <p id="placeholderText">Select or drag a passport-size image (150x200px).</p>
                <img id="previewImage" src="" alt="Selected Passport Image">
            </div>
            <!-- <input type="file" id="imageInput" accept="image/*" > -->
            <div class="custom-file-input">
  <label for="imageInput" id="customButton">Choose File</label>
  <span id="fileName">No file chosen</span>
  <input type="file" id="imageInput" name="image" accept="image/*">
</div>

        </div>

        <div class="rightDiv">
           
           
                <div class="formGroup">
                    <div class="formField">
                        <label for="name">Student Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="formField">
                        <label for="regno">Register Number</label>
                        <input type="text" id="regno" name="regno" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="formField">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="formField">
                        <label for="studentPhno">Phone Number</label>
                        <input type="text" id="studentPhno" name="studentPhno" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="fatherName">Father's Name</label>
                        <input type="text" id="fatherName" name="fatherName" required>
                    </div>
                    <div class="formField">
                        <label for="motherName">Mother's Name</label>
                        <input type="text" id="motherName" name="motherName" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="parentPhno">Parent's Phone Number</label>
                        <input type="text" id="parentPhno" name="parentPhno" required>
                    </div>
                    <div class="formField">
                        <label for="bloodGroup">Blood Group</label>
                        <input type="text" id="bloodGroup" name="bloodGroup" required>
                    </div>
                </div>

                <div class="formGroup">
                <div class="formField">
                        <label for="class">Current Year</label>
                        <input type="text" id="currentYear" name="currentYear" required>
                    </div>
                    <div class="formField">
                        <label for="class">Class</label>
                        <input type="text" id="class" name="class" required>
                    </div>
                    <div class="formField">
                        <label for="semester">Semester</label>
                        <input type="text" id="semester" name="semester" required>
                    </div>
                    <div class="formField">
                        <label for="class">Department</label>
                        <input type="text" id="department" name="department" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="currentAddress">Current Address</label>
                        <input type="text" id="currentAddress" name="currentAddress" required>
                    </div>
                    <div class="formField">
                        <label for="permanentAddress">Permanent Address</label>
                        <input type="text" id="permanentAddress" name="permanentAddress" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="community">Community</label>
                        <input type="text" id="community" name="community">
                    </div>
                    <div class="formField">
                        <label for="caste">Caste</label>
                        <input type="text" id="caste" name="caste">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="religion">Religion</label>
                        <input type="text" id="religion" name="religion">
                    </div>
                    <div class="formField">
                        <label for="fatherOccupation">Father's Occupation</label>
                        <input type="text" id="fatherOccupation" name="fatherOccupation">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="motherOccupation">Mother's Occupation</label>
                        <input type="text" id="motherOccupation" name="motherOccupation">
                    </div>
                    <div class="formField">
                        <label for="studentType">Student Type</label>
                        <select id="studentType" name="studentType">
                            <option value="">Select</option>
                            <option value="Regular">Regular</option>
                            <option value="Part-Time">Part-Time</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="scholarship">Getting Scholarship</label>
                        <select id="scholarship" name="scholarship">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="formField">
                        <label for="placement">Attending Placements</label>
                        <select id="placement" name="placement">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="submitBtn" name="submit">Submit</button>

            </form>
        </div>
    </section>


    <script>
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");
        const placeholderText = document.getElementById("placeholderText");
        const fileName = document.getElementById('fileName');


  


    
        imageInput.addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result; // Update the preview image source
            previewImage.style.display = "block"; // Show the image
            placeholderText.style.display = "none"; // Hide the placeholder
        };
        reader.readAsDataURL(file);

        // Correctly set the file name text
        fileName.textContent = file.name; 
    } else {
        // If no file is selected, reset the text
        fileName.textContent = 'No file chosen';
    }
});

    </script>
</body>

</html>
