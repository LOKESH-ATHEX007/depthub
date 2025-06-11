<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("php/functions.php");
$conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
header('Content-Type: application/json'); // Set JSON response type

try {
    // Ensure all required fields exist in POST request
    $requiredFields = ['name', 'regno', 'department', 'dob', 'gender', 'class', 'currentYear', 'studentType', 
        'fatherName', 'motherName', 'bloodGroup', 'permanentAddress', 'currentAddress', 'community', 'caste', 
        'religion', 'scholarship', 'parentPhno', 'studentPhno', 'fatherOccupation', 'motherOccupation', 
        'placement', 'email'];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(["status" => "error", "message" => "Error: Missing required field - $field"]);
            exit;
        }
    }

    // Assign form data to variables
    $stName = $_POST['name'];
    $regno = $_POST['regno'];
    $department = $_POST['department'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $class = $_POST['class'];
    $currentYear = $_POST['currentYear'];
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
    $email = $_POST['email'];

    // ✅ Ensure regno is unique (case-sensitive check)
    $checkQuery = $conn->prepare("SELECT COUNT(*) FROM st1 WHERE BINARY regno = :regno");
    $checkQuery->bindValue(":regno", $regno, PDO::PARAM_STR);
    $checkQuery->execute();
    $regnoExists = $checkQuery->fetchColumn();

    if ($regnoExists > 0) {
        echo json_encode(["status" => "error", "message" => "Error: Registration number already exists!"]);
        exit;
    }

    // ✅ Handle image upload
    $imageFile = "";
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];
        $folder = "images/stImages/" . $fileName;
        
        if (move_uploaded_file($tempName, $folder)) {
            $imageFile = $fileName;
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading image."]);
            exit;
        }
    }

    // ✅ Insert student data
    $insertQuery = "INSERT INTO st1 
        (regno, imageFile, stName, dob, gender, department, class, currentYear, fatherName, motherName, 
        studentType, scholarship, bloodGroup, permanentAddress, currentAddress, community, caste, religion, 
        fatherOccupation, motherOccupation, parentPhno, studentPhno, email, placement) 
        VALUES 
        (:regno, :imageFile, :stName, :dob, :gender, :department, :class, :currentYear, :fatherName, :motherName, 
        :studentType, :scholarship, :bloodGroup, :permanentAddress, :currentAddress, :community, :caste, :religion, 
        :fatherOccupation, :motherOccupation, :parentPhno, :studentPhno, :email, :placement)";

    $st = $conn->prepare($insertQuery);
    $st->bindValue(":regno", $regno);
    $st->bindValue(":imageFile", $imageFile);
    $st->bindValue(":stName", $stName);
    $st->bindValue(":dob", $dob);
    $st->bindValue(":gender", $gender);
    $st->bindValue(":department", $department);
    $st->bindValue(":class", $class);
    $st->bindValue(":currentYear", $currentYear);
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
    $st->bindValue(":fatherOccupation", $fatherOccupation);
    $st->bindValue(":motherOccupation", $motherOccupation);
    $st->bindValue(":parentPhno", $parentPhno);
    $st->bindValue(":studentPhno", $studentPhno);
    $st->bindValue(":email", $email);
    $st->bindValue(":placement", $placement);
    $st->execute();

    echo json_encode(["status" => "success", "message" => "Student data inserted successfully!"]);
    exit;

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Insertion failed: " . $e->getMessage()]);
    exit;
}
?>
