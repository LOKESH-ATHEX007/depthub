<?php
include 'db.php'; // Include the database connection file

$response = ['success' => false, 'message' => ''];

try {
    // Fetch data for each status
    $statuses = ['Pending', 'Under Process', 'Approved', 'Rejected'];
    $data = [];

    foreach ($statuses as $status) {
        $query = "SELECT sr.id, sr.regno, sr.requestDate, sr.status, s.stName 
                  FROM scholarship_requests sr 
                  JOIN st1 s ON sr.regno = s.regno 
                  WHERE sr.status = ? 
                  ORDER BY sr.requestDate DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status]);
        $data[strtolower(str_replace(' ', '', $status))] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $response['success'] = true;
    $response['pending'] = $data['pending'];
    $response['underProcess'] = $data['underprocess'];
    $response['approved'] = $data['approved'];
    $response['rejected'] = $data['rejected'];
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>