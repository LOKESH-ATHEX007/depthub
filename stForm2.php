<?php
include("php/functions.php");
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");

// Fetch unique classes and currentYears from class_teacher_allocation table
$stmtYears = $conn->prepare("SELECT DISTINCT currentYear FROM class_teacher_allocation ORDER BY currentYear ASC");
$stmtYears->execute();
$years = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

$stmtClasses = $conn->prepare("SELECT DISTINCT class FROM class_teacher_allocation ORDER BY class ASC");
$stmtClasses->execute();
$classes = $stmtClasses->fetchAll(PDO::FETCH_COLUMN);

// Fetch departments
$stmtDepts = $conn->prepare("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
$stmtDepts->execute();
$departments = $stmtDepts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Entry Form</title>
    <link rel="stylesheet" href="styles/stForm.css">

    <link rel="stylesheet" href="styles/contact2.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
</head>
   
</head>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    text-align: center;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.close {
    float: right;
    font-size: 20px;
    cursor: pointer;
}
</style>


<body>
    <?php
    require "includes/nav2.php"
    ?>
    <form id="studentForm" method="POST" enctype="multipart/form-data">

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

        <?php if (!empty($errorMessage)): ?>
                <p class="error-message"><?= $errorMessage; ?></p>
            <?php endif; ?>
            <?php if (!empty($successMessage)): ?>
                <p class="success-message"><?= $successMessage; ?></p>
            <?php endif; ?>
           
           
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
    <label for="currentYear">Current Year</label>
    <select id="currentYear" name="currentYear" required>
        <option value="">Select Year</option>
        <?php foreach ($years as $year) : ?>
            <option value="<?php echo $year; ?>">
                <?php echo $year; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="formField">
    <label for="class">Class</label>
    <select id="class" name="class" required>
        <option value="">Select Class</option>
        <?php foreach ($classes as $class) : ?>
            <option value="<?php echo $class; ?>">
                <?php echo $class; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

               
<div class="formField">
    <label for="department" class="required">Department</label>
    <select id="department" name="department" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= htmlspecialchars($dept['dept_id']) ?>">
                <?= htmlspecialchars($dept['dept_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
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
                    <div class="formField">
                        <label for="religion">Religion</label>
                        <input type="text" id="religion" name="religion">
                    </div>
                </div>

                <div class="formGroup">
                 
                    <div class="formField">
                        <label for="fatherOccupation">Father's Occupation</label>
                        <input type="text" id="fatherOccupation" name="fatherOccupation">
                    </div>
                        <div class="formField">
                        <label for="motherOccupation">Mother's Occupation</label>
                        <input type="text" id="motherOccupation" name="motherOccupation">
                    </div>
                </div>

              

                <div class="formGroup">
                       
                <div class="formField">
                        <label for="studentType">Student Type</label>
                        <select id="studentType" name="studentType">
                            <option value="">Select</option>
                            <option value="Regular">Regular</option>
                            <option value="Part-Time">Part-Time</option>
                        </select>
                    </div>
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
                  
                <div id="responseModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
        <button id="modalOk">OK</button>
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
                previewImage.src = e.target.result;
                previewImage.style.display = "block"; // Show preview
                placeholderText.style.display = "none"; // Hide placeholder
            };
            reader.readAsDataURL(file);
            fileName.textContent = file.name; // Show file name
        } else {
            fileName.textContent = 'No file chosen'; // Reset text
        }
    });

    $(document).ready(function(){
        $("#studentForm").on("submit", function(event){
            event.preventDefault(); // Prevent page reload
            
            var formData = new FormData(this); // Collect form data including files

            $.ajax({
                url: "insertStudent.php",
                type: "POST",
                data: formData,
                processData: false, // Important for file upload
                contentType: false, // Important for file upload
                dataType: "json", // Expect JSON response
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: response.message,
                            confirmButtonColor: "#3085d6"
                        }).then(() => {
                            $("#studentForm")[0].reset(); // Reset form
                            $("#previewImage").attr("src", "").hide(); // Reset image preview
                            $("#fileName").text("No file chosen"); // Reset file name display
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                            confirmButtonColor: "#d33"
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        confirmButtonColor: "#d33"
                    });
                }
            });
        });
    });
</script>

</body>

</html>
