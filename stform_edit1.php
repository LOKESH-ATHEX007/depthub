<?php
include("php/functions.php");

// Initialize variables to hold student data
$studentData = [
    'regno' => '',
    'imageFile' => '',
    'stName' => '',
    'dob' => '',
    'gender' => '',
    'department' => '',
    'semester' => '',
    'class' => '',
    'currentYear' => '',
    'fatherName' => '',
    'motherName' => '',
    'studentType' => '',
    'scholarship' => '',
    'bloodGroup' => '',
    'permanentAddress' => '',
    'currentAddress' => '',
    'community' => '',
    'caste' => '',
    'religion' => '',
    'fatherOccupation' => '',
    'motherOccupation' => '',
    'parentPhno' => '',
    'studentPhno' => '',
    'email' => '',
    'placement' => '',

];

// Connect to the database
 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");


// Check if regno is provided in the query string
if (isset($_GET['regno'])) {
    $regno = $_GET['regno'];

    // Fetch student data
    $stmt = $conn->prepare("SELECT * FROM st1 WHERE regno = :regno");
    $stmt->bindValue(":regno", $regno);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$studentData) {
        echo "Student not found!";
        exit();
    }

    // Assign selected values
    $selectedYear = $studentData['currentYear'];
    $selectedClass = $studentData['class'];
}

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $regno = $_POST["regno"];

    // Update student details
    $updateStmt = $conn->prepare("
        UPDATE st1 SET 
            stName = :stName, 
            dob = :dob, 
            gender = :gender, 
            email = :email, 
            studentPhno = :studentPhno, 
            fatherName = :fatherName, 
            motherName = :motherName, 
            parentPhno = :parentPhno, 
            bloodGroup = :bloodGroup, 
            currentYear = :currentYear, 
            class = :class, 
            department = :department, 
            community = :community, 
            caste = :caste, 
            religion = :religion, 
            fatherOccupation = :fatherOccupation, 
            motherOccupation = :motherOccupation, 
            studentType = :studentType, 
            scholarship = :scholarship, 
            placement = :placement, 
            currentAddress = :currentAddress, 
            permanentAddress = :permanentAddress
        WHERE regno = :regno
    ");

    $updateStmt->execute([
        ':stName' => $_POST["name"],
        ':dob' => $_POST["dob"],
        ':gender' => $_POST["gender"],
        ':email' => $_POST["email"],
        ':studentPhno' => $_POST["studentPhno"],
        ':fatherName' => $_POST["fatherName"],
        ':motherName' => $_POST["motherName"],
        ':parentPhno' => $_POST["parentPhno"],
        ':bloodGroup' => $_POST["bloodGroup"],
        ':currentYear' => $_POST["currentYear"],
        ':class' => $_POST["class"],
        ':department' => $_POST["department"],
        ':community' => $_POST["community"],
        ':caste' => $_POST["caste"],
        ':religion' => $_POST["religion"],
        ':fatherOccupation' => $_POST["fatherOccupation"],
        ':motherOccupation' => $_POST["motherOccupation"],
        ':studentType' => $_POST["studentType"],
        ':scholarship' => $_POST["scholarship"],
        ':placement' => $_POST["placement"],
        ':currentAddress' => $_POST["currentAddress"],
        ':permanentAddress' => $_POST["permanentAddress"],
        ':regno' => $regno
    ]);

    // Handle image upload
    if ($_FILES["image"]["name"]) {
        $imageName = time() . "_" . $_FILES["image"]["name"];
        $imagePath = "images/stImages/" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $updateImageStmt = $conn->prepare("UPDATE st1 SET imageFile = :imageFile WHERE regno = :regno");
            $updateImageStmt->execute([
                ':imageFile' => $imageName,
                ':regno' => $regno
            ]);
        }
    }

   
//     echo "<script>alert('Student data updated successfully!');</script>";
// header("Location: stform_edit1.php?regno=" . urlencode($regno));
// exit();
echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: 'Student data updated successfully!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'stform_edit1.php?regno=" . urlencode($regno) . "';
        });
    });
</script>";




}

// Fetch available years and classes
$stmt = $conn->prepare("SELECT DISTINCT currentYear, class FROM class_teacher_allocation ORDER BY currentYear, class");
$stmt->execute();
$classTeacherAllocations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$years = array_unique(array_column($classTeacherAllocations, 'currentYear'));
$classes = array_unique(array_column($classTeacherAllocations, 'class'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Data</title>
    <link rel="stylesheet" href="styles/stForm.css">
    
    <link rel="stylesheet" href="styles/contact2.css"/>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>


<body>
    <?php require "includes/nav2.php"; ?>
    <form method="POST" enctype="multipart/form-data">
        <section id="formSec">
            <div class="leftDiv">
                <h1>Edit Student Data</h1>
                <div id="imageContainer" class="image-container">
                    <p id="placeholderText">Select or drag a passport-size image (150x200px).</p>
                    
                    <?php
                       $imagePath = 'images/stImages/' . htmlspecialchars(trim($studentData['imageFile']));
                      ?>
                     <img id="previewImage" src="<?php echo $imagePath; ?>" alt="Preview Image">

                </div>
                <div class="custom-file-input">
                    <label for="imageInput" id="customButton">Choose File</label>
                    <span id="fileName">No file chosen</span>
                    <input type="file" id="imageInput" name="image" accept="image/*">
                </div>
            </div>

            <!-- Start of rightDiv -->
            <div class="rightDiv">

                <div class="formGroup">
                    <div class="formField">
                        <label for="name">Student Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $studentData['stName']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="regno">Register Number</label>
                        <input type="text" id="regno" name="regno" value="<?php echo $studentData['regno']; ?>" required readonly>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?php echo $studentData['dob']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="Male" <?php echo ($studentData['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($studentData['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($studentData['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $studentData['email']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="studentPhno">Phone Number</label>
                        <input type="text" id="studentPhno" name="studentPhno" value="<?php echo $studentData['studentPhno']; ?>" required>
                    </div>
                </div>


                <div class="formGroup">
                    <div class="formField">
                        <label for="fatherName">Father's Name</label>
                        <input type="text" id="fatherName" name="fatherName" value="<?php echo $studentData['fatherName']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="motherName">Mother's Name</label>
                        <input type="text" id="motherName" name="motherName" value="<?php echo $studentData['motherName']; ?>" required>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="parentPhno">Parent's Phone Number</label>
                        <input type="text" id="parentPhno" name="parentPhno" value="<?php echo $studentData['parentPhno']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="bloodGroup">Blood Group</label>
                        <input type="text" id="bloodGroup" name="bloodGroup" value="<?php echo $studentData['bloodGroup']; ?>" required>
                    </div>
                </div>


                <div class="formGroup">
                    <div class="formField">
                        <label for="currentYear">Current Year</label>
                        <select id="currentYear" name="currentYear" required>
                            <option value="">Select Year</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?php echo $year; ?>" 
                                    <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>>
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
                                <option value="<?php echo $class; ?>" 
                                    <?php echo ($class == $selectedClass) ? 'selected' : ''; ?>>
                                    <?php echo $class; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="formField">
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" value="<?php echo $studentData['department']; ?>" required>
                    </div>
                </div>

              

         

                <div class="formGroup">
                    <div class="formField">
                        <label for="community">Community</label>
                        <input type="text" id="community" name="community" value="<?php echo $studentData['community']; ?>">
                    </div>
                    <div class="formField">
                        <label for="caste">Caste</label>
                        <input type="text" id="caste" name="caste" value="<?php echo $studentData['caste']; ?>">
                    </div>
                    <div class="formField">
                        <label for="religion">Religion</label>
                        <input type="text" id="religion" name="religion" value="<?php echo $studentData['religion']; ?>">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="fatherOccupation">Father's Occupation</label>
                        <input type="text" id="fatherOccupation" name="fatherOccupation" value="<?php echo $studentData['fatherOccupation']; ?>">
                    </div>
                    <div class="formField">
                        <label for="motherOccupation">Mother's Occupation</label>
                        <input type="text" id="motherOccupation" name="motherOccupation" value="<?php echo $studentData['motherOccupation']; ?>">
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="studentType">Student Type</label>
                        <select id="studentType" name="studentType">
                            <option value="">Select</option>
                            <option value="Regular" <?php echo ($studentData['studentType'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                            <option value="Part-Time" <?php echo ($studentData['studentType'] == 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                        </select>
                    </div>
                    
                    <div class="formField">
                        <label for="scholarship">Getting Scholarship</label>
                        <select id="scholarship" name="scholarship">
                            <option value="">Select</option>
                            <option value="Yes" <?php echo ($studentData['scholarship'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo ($studentData['scholarship'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    
                    <div class="formField">
                        <label for="placement">Attending Placements</label>
                        <select id="placement" name="placement">
                            <option value="">Select</option>
                            <option value="Yes" <?php echo ($studentData['placement'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo ($studentData['placement'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="formGroup">
                    <div class="formField">
                        <label for="currentAddress">Current Address</label>
                        <input type="text" id="currentAddress" name="currentAddress" value="<?php echo $studentData['currentAddress']; ?>" required>
                    </div>
                    <div class="formField">
                        <label for="permanentAddress">Permanent Address</label>
                        <input type="text" id="permanentAddress" name="permanentAddress" value="<?php echo $studentData['permanentAddress']; ?>" required>
                    </div>
                </div>

                <button type="submit" class="submitBtn" name="submit">Update</button>
            </div> <!-- End of rightDiv -->
        </section>
    </form>


    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");
        const placeholderText = document.getElementById("placeholderText");
        const fileName = document.getElementById("fileName");

        // Set existing image if available
        let existingImage = "<?php echo !empty($studentData['imageFile']) ? 'images/stImages/' . $studentData['imageFile'] : ''; ?>";
        if (existingImage.trim() !== "") {
            previewImage.src = existingImage;
            previewImage.style.display = "block";
            placeholderText.style.display = "none";
            fileName.textContent = "Previously selected: " + existingImage.split('/').pop();
        }

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
    });






</script>



</body>
</html>

