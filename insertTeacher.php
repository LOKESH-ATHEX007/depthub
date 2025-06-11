<?php
include("php/functions.php");

// Enable detailed error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
try {
    $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

$response = ['status' => 'error', 'message' => ''];

try {
    // Validate required fields (removed T_id from required since we'll generate it)
    $required = ['tName', 'dob', 'gender', 'department', 'designation', 'email', 'phone'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }

    // Auto-generate Teacher ID
    $deptCode = $_POST['department'];
    $year = date("Y"); // Current year
    
    // Get next sequence number
    $seqStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(T_id, -3) AS UNSIGNED)) 
                              FROM teachers 
                              WHERE T_id LIKE ?");
    $pattern = $deptCode . $year . '%';
    $seqStmt->execute([$pattern]);
    $lastSeq = (int)$seqStmt->fetchColumn();
    $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
    
    $T_id = $deptCode . $year . $nextSeq; // e.g., "CS2023001"

    // Check for duplicate phone number
    $checkPhone = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE phone = :phone");
    $checkPhone->execute([':phone' => $_POST['phone']]);
    if ($checkPhone->fetchColumn() > 0) {
        throw new Exception("Phone number '{$_POST['phone']}' is already registered");
    }

    // Check for duplicate Email
    $checkEmail = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE email = :email");
    $checkEmail->execute([':email' => $_POST['email']]);
    if ($checkEmail->fetchColumn() > 0) {
        throw new Exception("Email '{$_POST['email']}' is already registered");
    }

    // Check for duplicate Aadhar number (if provided)
    if (!empty($_POST['aadharNumber'])) {
        $checkAadharNumber = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE aadharNumber = :aadharNumber");
        $checkAadharNumber->execute([':aadharNumber' => $_POST['aadharNumber']]);
        if ($checkAadharNumber->fetchColumn() > 0) {
            throw new Exception("Aadhar number '{$_POST['aadharNumber']}' is already registered");
        }
    }

    // Handle file upload (using generated T_id for filename)
    $imageFileName = null;
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == UPLOAD_ERR_OK) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imageFile']['type'], $allowedTypes)) {
            throw new Exception("Only JPG, PNG, and GIF images are allowed");
        }

        // Validate file size (max 2MB)
        if ($_FILES['imageFile']['size'] > 2097152) {
            throw new Exception("Image size must be less than 2MB");
        }

        $target_dir = "uploads/teachers/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        $file_extension = strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
        $imageFileName = $T_id . '.' . $file_extension; // Using generated T_id
        $target_file = $target_dir . $imageFileName;

        // Delete previous image if exists
        $existingFiles = glob($target_dir . $T_id . '.*');
        foreach ($existingFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
            throw new Exception("Failed to save teacher photo");
        }
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO teachers (
        T_id, tName, imageFile, dob, gender, bloodGroup, 
        department, designation, qualification, specialization, 
        joiningDate, experienceYears, email, phone, alternatePhone, 
        currentAddress, permanentAddress, panNumber, aadharNumber
    ) VALUES (
        :T_id, :tName, :imageFile, :dob, :gender, :bloodGroup, 
        :department, :designation, :qualification, :specialization, 
        :joiningDate, :experienceYears, :email, :phone, :alternatePhone, 
        :currentAddress, :permanentAddress, :panNumber, :aadharNumber
    )");

    // Bind parameters (using auto-generated T_id)
    $bindParams = [
        ':T_id' => $T_id, // Using the auto-generated ID
        ':tName' => $_POST['tName'],
        ':imageFile' => $imageFileName,
        ':dob' => $_POST['dob'],
        ':gender' => $_POST['gender'],
        ':bloodGroup' => $_POST['bloodGroup'] ?? null,
        ':department' => $_POST['department'],
        ':designation' => $_POST['designation'],
        ':qualification' => $_POST['qualification'] ?? null,
        ':specialization' => $_POST['specialization'] ?? null,
        ':joiningDate' => $_POST['joiningDate'] ?? null,
        ':experienceYears' => $_POST['experienceYears'] ?? 0,
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone'],
        ':alternatePhone' => $_POST['alternatePhone'] ?? null,
        ':currentAddress' => $_POST['currentAddress'] ?? null,
        ':permanentAddress' => $_POST['permanentAddress'] ?? null,
        ':panNumber' => $_POST['panNumber'] ?? null,
        ':aadharNumber' => $_POST['aadharNumber'] ?? null
    ];

    // Execute the statement
    if (!$stmt->execute($bindParams)) {
        throw new Exception("Failed to save teacher data");
    }

    $response = [
        'status' => 'success',
        'message' => 'Teacher registered successfully!',
        'generated_id' => $T_id  // Include the generated ID in response
    ];

} catch (Exception $e) {
    // Cleanup uploaded file if insertion failed
    if (isset($target_file) && file_exists($target_file)) {
        unlink($target_file);
    }
    
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>