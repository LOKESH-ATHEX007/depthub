<?php
include("php/functions.php");
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch departments using PDO
$deptStmt = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Entry Form</title>
    <link rel="stylesheet" href="styles/stForm.css">
    <link rel="stylesheet" href="styles/contact2.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
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
    <?php require "includes/nav2.php" ?>
    
    <form id="teacherForm" method="POST" enctype="multipart/form-data">
        <section id="formSec">
            <div class="leftDiv">
                <h1>Teacher Data Form</h1>
                <div id="imageContainer" class="image-container">
                    <p id="placeholderText">Select or drag a passport-size image (150x200px).</p>
                    <img id="previewImage" src="" alt="Selected Passport Image">
                </div>
                <div class="custom-file-input">
                    <label for="imageInput" id="customButton">Choose File</label>
                    <span id="fileName">No file chosen</span>
                    <input type="file" id="imageInput" name="imageFile" accept="image/*">
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
                        <label for="tName">Teacher Name</label>
                        <input type="text" id="tName" name="tName" required>
                    </div>
                    <!-- <div class="formField">
                        <label for="T_id">Teacher ID</label>
                        <input type="text" id="T_id" name="T_id" required>
                    </div> -->
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
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="formGroup">
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

                    <div class="formField">
                        <label for="designation">Designation</label>
                        <select id="designation" name="designation" required>
                            <option value="">Select</option>
                            <option value="Professor">Professor</option>
                            <option value="Associate Professor">Associate Professor</option>
                            <option value="Assistant Professor">Assistant Professor</option>
                            <option value="Lecturer">Lecturer</option>
                            <option value="Visiting Faculty">Visiting Faculty</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="qualification">Highest Qualification</label>
                        <input type="text" id="qualification" name="qualification" required>
                    </div>
                    <div class="formField">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="joiningDate">Joining Date</label>
                        <input type="date" id="joiningDate" name="joiningDate" required>
                    </div>
                    <div class="formField">
                        <label for="experienceYears">Years of Experience</label>
                        <input type="number" id="experienceYears" name="experienceYears" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="alternatePhone">Alternate Phone</label>
                        <input type="text" id="alternatePhone" name="alternatePhone">
                    </div>
                    <div class="formField">
                        <label for="bloodGroup">Blood Group</label>
                        <input type="text" id="bloodGroup" name="bloodGroup">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="panNumber">PAN Number</label>
                        <input type="text" id="panNumber" name="panNumber">
                    </div>
                    <div class="formField">
                        <label for="aadharNumber">Aadhar Number</label>
                        <input type="text" id="aadharNumber" name="aadharNumber">
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
            </div>
        </section>
    </form>

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
                previewImage.style.display = "block";
                placeholderText.style.display = "none";
            };
            reader.readAsDataURL(file);
            fileName.textContent = file.name;
        } else {
            fileName.textContent = 'No file chosen';
        }
    });

    $(document).ready(function(){
        $("#teacherForm").on("submit", function(event){
            event.preventDefault();
            
            var formData = new FormData(this);
            // Store form values before reset
            const teacherName = $('#tName').val();
            const department = $('#department option:selected').text();

            $.ajax({
                url: "insertTeacher.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Teacher Registered Successfully!",
                            html: `<div style="text-align: left;">
                                 <p><strong>Teacher ID:</strong> ${response.generated_id}</p>
                                 <p><strong>Name:</strong> ${teacherName}</p>
                                 <p><strong>Department:</strong> ${department}</p>
                                 <p>${response.message}</p>
                               </div>`,
                            confirmButtonText: 'OK',
                            confirmButtonColor: "#3085d6",
                            width: '500px'
                        }).then(() => {
                            $("#teacherForm")[0].reset();
                            $("#previewImage").attr("src", "").hide();
                            $("#fileName").text("No file chosen");
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