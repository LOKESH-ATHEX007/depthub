<?php
session_start();
include("php/functions.php");

// Database connection
try {
    $conn = new PDO("mysql:host=sql202.infinityfree.com;dbname=if0_39191720_depthub", "if0_39191720", "JqlHPQjc3rGSk");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed. Please try again later.");
}

$login_error = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = trim($_POST['admin_id']);
    $password = $_POST['password'];

    try {
        // Get admin credentials including department ID
        $stmt = $conn->prepare("SELECT * FROM dept_admins WHERE dept_admin_id = ? LIMIT 1");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['password']) {
            // Store admin data in session
            $_SESSION['dept_admin_id'] = $admin['dept_admin_id'];
            $_SESSION['dept_id'] = $admin['dept_id'];  // Department ID from dept_admins table
            $_SESSION['full_name'] = $admin['full_name'];
            $_SESSION['user_role'] = 'dept_admin';
            
            header("Location: dept_admin_dashboard.php");
            exit();
        } else {
            $login_error = "Invalid admin ID or password";
        }
    } catch(PDOException $e) {
        $login_error = "System error. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Department Admin Login</title>
    <link rel="stylesheet" type="text/css" href="styles/dept_admin_login.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    .error-message {
        color: #ff3860;
        background: #ffecec;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
    }
    </style>
</head>
<body>
    <img class="wave" src="pic/wave.png">
    <div class="container">
        <div class="img">
            <img src="pic/bg.svg">
        </div>
        <div class="login-content">
            <form method="POST" action="">
                <img src="pic/avatar.svg">
                <h2 class="title">Department Admin Login</h2>
                <?php if ($login_error): ?>
                    <div class="error-message"><?= htmlspecialchars($login_error) ?></div>
                <?php endif; ?>
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="div">
                        <h5>Admin ID</h5>
                        <input type="text" name="admin_id" class="input" required>
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i"> 
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Password</h5>
                        <input type="password" name="password" class="input" required>
                    </div>
                </div>
                <a href="forgot_password.php">Forgot Password?</a>
                <input type="submit" class="btn" value="Login">
            </form>
        </div>
    </div>
    <script>
        const inputs = document.querySelectorAll(".input");
        inputs.forEach(input => {
            input.addEventListener("focus", function() {
                this.parentNode.parentNode.classList.add("focus");
            });
            input.addEventListener("blur", function() {
                if(this.value == "") {
                    this.parentNode.parentNode.classList.remove("focus");
                }
            });
        });
    </script>
</body>
</html>