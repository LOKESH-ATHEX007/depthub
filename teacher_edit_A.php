<?php
include("php/functions.php");
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get teacher ID from URL
$t_id = $_GET['t_id'] ?? '';
if (empty($t_id)) {
    header("Location: teacher_crud_A.php");
    exit();
}

// Fetch teacher data
$teacherStmt = $conn->prepare("SELECT * FROM teachers WHERE T_id = ?");
$teacherStmt->execute([$t_id]);
$teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    header("Location: teacher_crud_A.php");
    exit();
}

// Fetch departments
$deptStmt = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updateData = [
            'tName' => $_POST['tName'],
            'dob' => $_POST['dob'],
            'gender' => $_POST['gender'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'department' => $_POST['department'],
            'designation' => $_POST['designation'],
            'qualification' => $_POST['qualification'],
            'specialization' => $_POST['specialization'],
            'joiningDate' => $_POST['joiningDate'],
            'experienceYears' => $_POST['experienceYears'],
            'alternatePhone' => $_POST['alternatePhone'] ?? null,
            'bloodGroup' => $_POST['bloodGroup'] ?? null,
            'panNumber' => $_POST['panNumber'] ?? null,
            'aadharNumber' => $_POST['aadharNumber'] ?? null,
            'currentAddress' => $_POST['currentAddress'],
            'permanentAddress' => $_POST['permanentAddress'],
            't_id' => $t_id
        ];

        // Handle image upload if new image was provided
        if (!empty($_FILES['imageFile']['name'])) {
            $imageFile = $_FILES['imageFile'];
            $uploadDir = "uploads/teachers/";
            $fileName = uniqid() . '_' . basename($imageFile['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
                $updateData['imageFile'] = $fileName;
                // Delete old image if exists
                if (!empty($teacher['imageFile'])) {
                    @unlink($uploadDir . $teacher['imageFile']);
                }
            }
        }

        // Build update query
        $updateFields = [];
        foreach ($updateData as $field => $value) {
            if ($field !== 't_id') {
                $updateFields[] = "$field = :$field";
            }
        }

        $updateQuery = "UPDATE teachers SET " . implode(', ', $updateFields) . " WHERE T_id = :t_id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute($updateData);

        $successMessage = "Teacher information updated successfully!";
        // Refresh teacher data
        $teacherStmt->execute([$t_id]);
        $teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Error updating teacher: " . $e->getMessage();
    }
}

$imagePath = '';
if (!empty($teacher['imageFile'])) {
    $imagePath = "uploads/teachers/" . htmlspecialchars($teacher['imageFile']);
    // Verify the file actually exists
    if (!file_exists($imagePath)) {
        $imagePath = ''; // If file doesn't exist, don't try to display it
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher - <?= htmlspecialchars($teacher['tName']) ?></title>
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
.image-container {
    width: 150px;
    height: 200px;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 15px;
}

.image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}
#previewImage {
border: 1px solid #ddd;
display: block; width: 100%; height: 100%; object-fit: cover;
}
</style>

<body>
    <?php require "includes/nav2.php" ?>
    
    <form id="teacherForm" method="POST" enctype="multipart/form-data">
        <section id="formSec">
            <div class="leftDiv">
                <h1>Edit Teacher Data</h1>
                <div id="imageContainer" class="image-container">
                    <?php if (!empty($teacher['imageFile'])): ?>
                        <img id="previewImage" src="uploads/teachers/<?= htmlspecialchars($teacher['imageFile']) ?>" alt="Teacher Photo">
                    <?php else: ?>
                        <p id="placeholderText">Select or drag a passport-size image (150x200px).</p>
                        <img id="previewImage" src="" alt="Selected Passport Image" style="display: none;">
                    <?php endif; ?>
                </div>
                <div class="custom-file-input">
                    <label for="imageInput" id="customButton">Change Image</label>
                    <span id="fileName"><?= !empty($teacher['imageFile']) ? htmlspecialchars($teacher['imageFile']) : 'No file chosen' ?></span>
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
                        <label for="T_id">Teacher ID</label>
                        <input type="text" id="T_id" name="T_id" value="<?= htmlspecialchars($teacher['T_id']) ?>" readonly>
                    </div>
                    <div class="formField">
                        <label for="tName">Teacher Name</label>
                        <input type="text" id="tName" name="tName" value="<?= htmlspecialchars($teacher['tName']) ?>" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($teacher['dob']) ?>" required>
                    </div>
                    <div class="formField">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select</option>
                            <option value="Male" <?= $teacher['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $teacher['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $teacher['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>
                    </div>
                    <div class="formField">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($teacher['phone']) ?>" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="department" class="required">Department</label>
                        <select id="department" name="department" required>
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['dept_id']) ?>" 
                                    <?= $dept['dept_id'] === $teacher['department'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['dept_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="formField">
                        <label for="designation">Designation</label>
                        <select id="designation" name="designation" required>
                            <option value="">Select</option>
                            <option value="Professor" <?= $teacher['designation'] === 'Professor' ? 'selected' : '' ?>>Professor</option>
                            <option value="Associate Professor" <?= $teacher['designation'] === 'Associate Professor' ? 'selected' : '' ?>>Associate Professor</option>
                            <option value="Assistant Professor" <?= $teacher['designation'] === 'Assistant Professor' ? 'selected' : '' ?>>Assistant Professor</option>
                            <option value="Lecturer" <?= $teacher['designation'] === 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
                            <option value="Visiting Faculty" <?= $teacher['designation'] === 'Visiting Faculty' ? 'selected' : '' ?>>Visiting Faculty</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="qualification">Highest Qualification</label>
                        <input type="text" id="qualification" name="qualification" value="<?= htmlspecialchars($teacher['qualification']) ?>" required>
                    </div>
                    <div class="formField">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($teacher['specialization']) ?>" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="joiningDate">Joining Date</label>
                        <input type="date" id="joiningDate" name="joiningDate" value="<?= htmlspecialchars($teacher['joiningDate']) ?>" required>
                    </div>
                    <div class="formField">
                        <label for="experienceYears">Years of Experience</label>
                        <input type="number" id="experienceYears" name="experienceYears" value="<?= htmlspecialchars($teacher['experienceYears']) ?>" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="alternatePhone">Alternate Phone</label>
                        <input type="text" id="alternatePhone" name="alternatePhone" value="<?= htmlspecialchars($teacher['alternatePhone'] ?? '') ?>">
                    </div>
                    <div class="formField">
                        <label for="bloodGroup">Blood Group</label>
                        <input type="text" id="bloodGroup" name="bloodGroup" value="<?= htmlspecialchars($teacher['bloodGroup'] ?? '') ?>">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="panNumber">PAN Number</label>
                        <input type="text" id="panNumber" name="panNumber" value="<?= htmlspecialchars($teacher['panNumber'] ?? '') ?>">
                    </div>
                    <div class="formField">
                        <label for="aadharNumber">Aadhar Number</label>
                        <input type="text" id="aadharNumber" name="aadharNumber" value="<?= htmlspecialchars($teacher['aadharNumber'] ?? '') ?>">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="currentAddress">Current Address</label>
                        <input type="text" id="currentAddress" name="currentAddress" value="<?= htmlspecialchars($teacher['currentAddress']) ?>" required>
                    </div>
                    <div class="formField">
                        <label for="permanentAddress">Permanent Address</label>
                        <input type="text" id="permanentAddress" name="permanentAddress" value="<?= htmlspecialchars($teacher['permanentAddress']) ?>" required>
                    </div>
                </div>

                <div id="responseModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <p id="modalMessage"></p>
                        <button id="modalOk">OK</button>
                    </div>
                </div>

                <button type="submit" class="submitBtn" name="submit">Update Teacher</button>
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
            if (placeholderText) placeholderText.style.display = "none";
        };
        reader.readAsDataURL(file);
        fileName.textContent = file.name;
    } else {
        // Show the original image if no new file is selected
        <?php if (!empty($imagePath)): ?>
            previewImage.src = "<?= $imagePath ?>";
            previewImage.style.display = "block";
            if (placeholderText) placeholderText.style.display = "none";
            fileName.textContent = "<?= htmlspecialchars($teacher['imageFile']) ?>";
        <?php else: ?>
            previewImage.style.display = "none";
            if (placeholderText) placeholderText.style.display = "block";
            fileName.textContent = 'No file chosen';
        <?php endif; ?>
    }
});

    $(document).ready(function(){
        $("#teacherForm").on("submit", function(event){
            event.preventDefault();
            
            var formData = new FormData(this);
            
            $.ajax({
                url: "updateTeacher.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Teacher Updated Successfully!",
                            html: `<div style="text-align: left;">
                                <p><strong>Teacher ID:</strong> ${response.teacher_id}</p>
                                <p><strong>Name:</strong> ${response.teacher_name}</p>
                                <p>${response.message}</p>
                            </div>`,
                            confirmButtonText: 'OK',
                            confirmButtonColor: "#3085d6",
                            width: '500px'
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
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        confirmButtonColor: "#d33"
                    });
                    console.error("AJAX Error:", status, error);
                }
            });
        });
    });
    </script>
</body>
</html>