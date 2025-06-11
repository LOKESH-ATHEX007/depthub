<?php

include("php/functions.php");

if(isset($_POST['submit'])){

    if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){ 
        $fileName = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];
        $folder = "images/stImages/" . $fileName; 
        if (move_uploaded_file($tempName, $folder)) {
            $imageFile = $fileName;
        } else {
            echo "Error uploading image: " . error_get_last()['message'];
            exit();
        }
    } else {
        $imageFile = ""; 
    }

 
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
    <title>Student Details Form</title>
    <style>
        :root {
            --dark-yellow: #FFC107;
            --black: #000000;
            --white: #FFFFFF;
            --gray: #f4f4f4;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--black);
            color: var(--white);
            overflow-x: hidden;
        }

        header {
            background-color: var(--dark-yellow);
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: var(--black);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .form-container {
            background-color: var(--gray);
            margin: 20px auto;
            padding: 30px;
            border-radius: 8px;
            max-width: 800px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--black);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--black);
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="file"] {
            padding: 5px;
        }

        .form-group-inline {
            display: flex;
            gap: 10px;
            flex-wrap:wrap;
        }

        .form-group-inline .form-group {
            flex: 1;
        }

        .submit-btn {
            background-color: var(--dark-yellow);
            color: var(--black);
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #e6a806;
        }
    </style>
</head>
<body>
    <header>
        Student Details Form
    </header>
    <div class="form-container">
        <h2>Enter Student Details</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Student Photo</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group-inline">
             <div class="form-group">
                <label for="name">Name of the Student</label>
                <input type="text" id="name" name="name" required>
             </div>
             <div class="form-group">
                    <label for="regno">Registration Number</label>
                    <input type="text" id="regno" name="regno" required>
                </div>
           
            </div>
         

            <div class="form-group-inline">

            <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" id="gender" name="gender" required>
                </div>
                <div class="form-group">
                <label for="bloodGroup">Blood Group</label>
                <input type="text" id="bloodGroup" name="bloodGroup" required>
            </div>
              
             
            </div>

            <div class="form-group-inline">
                
            <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" required>
                </div>
                <div class="form-group">
                    <label for="currentYear">Year</label>
                    <input type="text" id="currentYear" name="currentYear" required>
                </div>
                <div class="form-group">
                    <label for="class">class</label>
                    <input type="text" id="class" name="class" required>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <input type="text" id="semester" name="semester" required>
                </div>

            </div>

            

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="fatherName">Father's Name</label>
                    <input type="text" id="fatherName" name="fatherName" required>
                </div>
                <div class="form-group">
                    <label for="motherName">Mother's Name</label>
                    <input type="text" id="motherName" name="motherName" required>
                </div>
            </div>

         

            <div class="form-group">
                <label for="permanentAddress">Permanent Address</label>
                <textarea id="permanentAddress" name="permanentAddress" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="currentAddress">Current Address</label>
                <textarea id="currentAddress" name="currentAddress" rows="3"></textarea>
            </div>

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="community">Community</label>
                    <input type="text" id="community" name="community">
                </div>
                <div class="form-group">
                    <label for="caste">Caste</label>
                    <input type="text" id="caste" name="caste">
                </div>
                <div class="form-group">
                    <label for="religion">Religion</label>
                    <input type="text" id="religion" name="religion">
                </div>
            </div>

        

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="parentPhno">Parent's Contact Number</label>
                    <input type="tel" id="parentPhno" name="parentPhno" required>
                </div>
                <div class="form-group">
                    <label for="studentPhno">Personal Contact Number</label>
                    <input type="tel" id="studentPhno" name="studentPhno">
                </div>
                <div class="form-group">
                    <label for="studentPhno">Email</label>
                    <input type="email" id="email" name="email">
                </div>
            </div>

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="fatherOccupation">Father's Occupation</label>
                    <input type="text" id="fatherOccupation" name="fatherOccupation">
                </div>
                <div class="form-group">
                    <label for="motherOccupation">Mother's Occupation</label>
                    <input type="text" id="motherOccupation" name="motherOccupation">
                </div>
            </div>


            
            <div class="form-group-inline">
               
               <div class="form-group">
                   <label for="studentType">Day Scholar or Hosteller</label>
                   <select id="studentType" name="studentType">
                       <option value="day-scholar">Day Scholar</option>
                       <option value="hosteller">Hosteller</option>
                   </select>
               </div>

               <div class="form-group">
               <label for="scholarship">Acquires Any Scholarship?</label>
               <select id="scholarship" name="scholarship">
                   <option value="yes">Yes</option>
                   <option value="no">No</option>
               </select>
           </div>
           
           <div class="form-group">
                   <label for="fatherOccupation">Attending placements</label>
                  <select name="placement" id="placement">
                   <option value="yes">Yes</option>
                   <option value="No">No</option>
                  </select>
               </div>

           </div>
            

           

           

            <div style="text-align: center;">
                <button type="submit" class="submit-btn" name="submit">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>