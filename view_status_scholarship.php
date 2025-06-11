<?php
session_start();
include 'scholarshipphp/db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['regno'])) {
    header('Location: index.php');
    exit();
}

$regno = $_SESSION['regno'];

// Fetch the most recent scholarship request for the user
$query = "SELECT requestDate, reason, status, incomeCert, otherProofs FROM scholarship_requests WHERE regno = ? ORDER BY requestDate DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $regno);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$request = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Scholarship Request Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
        }
        body {
            background:url("pic/education-bg.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            min-height:100vh;
    height:100vh;
    padding-top:3rem;
    display:flex;
    align-items:center;
    justify-content: center;
    /* flex-wrap: wrap; */
   
}
        .container {
            max-width: 800px;
            background: url("pic/scholarship.svg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .status-card {
            background: transparent;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent white */
            backdrop-filter: blur(10px);
        }
        .status-card h4 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .status-card p {
            margin-bottom: 10px;
            color: #555;
        }
        .status-card .status {
            font-weight: bold;
            color: #3498db;
        }
        .btn-back {
            margin-top: 20px;
        }
        #vector-img{
height:70vh;
width:auto;
}
#vector-img h2{
    
    margin-left: 3rem;
    transform: translateY(3rem);
    color: rgb(110, 148, 149);
    font-family: Verdana, Geneva, Tahoma, sans-serif;
}
#vector-img img{
    height:100%;
}


.status-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .scrollable-reason {
            max-height: 150px; /* Fixed height */
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 10px;
            /* background: rgba(255, 255, 255, 0.88); */
            border-radius: 5px;
            margin: 10px 0;
            margin-bottom:1rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .file-links a {
            display: inline-block;
            margin-right: 15px;
            padding: 5px 10px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 5px;
            color: #007bff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .file-links a:hover {
            background: rgba(0, 123, 255, 0.2);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            /* border:1px solid black; */
            font-weight: bold;
        }
        
        .status-pending {
            background-color:rgba(255, 180, 150, 0.45);
            color:rgb(255, 102, 0);
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <link href="styles/nav.css" rel="stylesheet">
</head>
<body>
<?php include("includes/nav.php") ?>
    <div class="container">
        <h3 class="text-center text-primary">📋 Scholarship Request Status</h3>
        <?php if ($request): ?>
            <div class="status-card">
                <h4>Request Details</h4>
                <p><strong>Request Date:</strong> <?php echo date('d M Y', strtotime($request['requestDate'])); ?></p>
                
                <p><strong>Reason:</strong></p>
                <div class="scrollable-reason">
                    <?php echo nl2br(htmlspecialchars($request['reason'])); ?>
                </div>
                
                <p><strong>Status:</strong> 
                    <span class="status-badge status-<?php echo strtolower(htmlspecialchars($request['status'])); ?>">
                        <?php echo htmlspecialchars($request['status']); ?>
                    </span>
                </p>
                
                <div class="file-links">
                    <p><strong>Documents:</strong></p>
                    <a href="scholarshipphp/uploads/<?php echo htmlspecialchars($request['incomeCert']); ?>" target="_blank">
                        <i class="fas fa-file-alt"></i> Income Certificate
                    </a>
                    <?php if (!empty($request['otherProofs'])): ?>
                        <a href="scholarshipphp/uploads/<?php echo htmlspecialchars($request['otherProofs']); ?>" target="_blank">
                            <i class="fas fa-file-pdf"></i> Other Proofs
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                No scholarship request found. You can submit a new request <a href="scholarship_request.php">here</a>.
            </div>
        <?php endif; ?>

        <a href="scholarship_request.php" class="btn btn-secondary btn-block btn-back">
            <i class="fas fa-arrow-left"></i> Back to Scholarship Request Form
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>