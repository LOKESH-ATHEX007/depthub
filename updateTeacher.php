<?php
session_start();
include("php/functions.php");

 $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

try {
    $t_id = $_POST['T_id'] ?? '';
    if (empty($t_id)) {
        throw new Exception("Teacher ID is required");
    }

    // Verify teacher exists
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE T_id = ?");
    $stmt->execute([$t_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        throw new Exception("Teacher not found");
    }

    // Prepare update data
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

    // Handle file upload if provided
    if (!empty($_FILES['imageFile']['name'])) {
        $uploadDir = "uploads/teachers/";
        $fileName = uniqid() . '_' . basename($_FILES['imageFile']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetPath)) {
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

    echo json_encode([
        'status' => 'success',
        'teacher_id' => $t_id,
        'teacher_name' => $_POST['tName'],
        'message' => 'Teacher information has been updated successfully.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>