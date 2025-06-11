<?php
session_start();
include 'scholarshipphp/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔑 Student Login
    $studentQuery = "SELECT regno FROM st1 WHERE email = ? AND regno = ?";
    $stmt = mysqli_prepare($conn, $studentQuery);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $studentResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($studentResult) === 1) {
        $student = mysqli_fetch_assoc($studentResult);
        $_SESSION['regno'] = $student['regno']; // Store regno in session
        header('Location: scholarship_request.php');
        exit();
    }

    // 👨‍🏫 Teacher Login
    $teacherQuery = "SELECT T_id FROM teachers WHERE email = ? AND T_id = ?";
    $stmt = mysqli_prepare($conn, $teacherQuery);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $teacherResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($teacherResult) === 1) {
        $teacher = mysqli_fetch_assoc($teacherResult);
        $_SESSION['T_id'] = $teacher['T_id']; // Store T_id in session
        header('Location: view_scholarship_request.php');
        exit();
    }

    // 🧑‍💼 Admin Login
    $adminQuery = "SELECT admin_id FROM admin WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $adminQuery);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $adminResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($adminResult) === 1) {
        $admin = mysqli_fetch_assoc($adminResult);
        $_SESSION['admin_id'] = $admin['admin_id']; // Store admin_id in session
        header('Location: admin_scholarship_requests.php');
        exit();
    }

    $errorMessage = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            padding: 50px;
        }
        .login-container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>