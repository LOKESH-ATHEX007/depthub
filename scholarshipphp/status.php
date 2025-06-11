<?php
include 'db.php'; // Include database connection

// Function to fetch the latest status and request date
function fetchLatestStatus() {
    global $conn;
    $query = "SELECT status, requestDate FROM scholarship_requests ORDER BY requestDate DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'success' => true,
            'status' => $row['status'],
            'requestDate' => date('d-m-Y', strtotime($row['requestDate'])),
        ];
    } else {
        return [
            'success' => false,
            'message' => 'No scholarship requests found.',
        ];
    }
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $response = fetchLatestStatus();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Fetch initial status for the page load
$statusData = fetchLatestStatus();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Request Status</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #4f46e5;
            margin-bottom: 20px;
        }

        .status-message {
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 1.1em;
        }

        .success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 5px solid #10b981;
        }

        .error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 5px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Scholarship Request Status</h2>
        <div id="statusResult" class="status-message <?= $statusData['success'] ? 'success' : 'error' ?>">
            <?php if ($statusData['success']) : ?>
                <p><strong>Status:</strong> <?= $statusData['status'] ?></p>
                <p><strong>Request Date:</strong> <?= $statusData['requestDate'] ?></p>
            <?php else : ?>
                <p><?= $statusData['message'] ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to fetch and update the status
        function fetchStatus() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'status.php?ajax=1', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const statusResult = document.getElementById('statusResult');

                    if (response.success) {
                        statusResult.className = 'status-message success';
                        statusResult.innerHTML = `
                            <p><strong>Status:</strong> ${response.status}</p>
                            <p><strong>Request Date:</strong> ${response.requestDate}</p>
                        `;
                    } else {
                        statusResult.className = 'status-message error';
                        statusResult.innerHTML = `<p>${response.message}</p>`;
                    }
                }
            };
            xhr.send();
        }

        // Fetch status every 5 seconds
        setInterval(fetchStatus, 5000);

        // Fetch status immediately on page load
        fetchStatus();
    </script>
</body>
</html>