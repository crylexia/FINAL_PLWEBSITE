<?php
session_start();
include "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Use prepared statement (prevents SQL injection)
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // Verify hashed password
        if (password_verify($password, $row["password"])) {

            // Save login session
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["role"] = $row["role"];

            header("Location: ../admin/dashboard.php");
            exit();

        } else {
            $error = "Invalid password";
        }

    } else {
        $error = "User not found";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login | LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.login-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(to right, #102a43, #1e3a8a);
}
.login-card {
    background: white;
    width: 380px;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    text-align: center;
}
.login-card .logo {
    font-size: 26px;
    font-weight: bold;
    color: #f59e0b;
}
.login-card h2 {
    color: #102a43;
}
.login-card input {
    width: 100%;
    padding: 14px;
    margin-top: 15px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}
.login-card button {
    width: 100%;
    padding: 14px;
    margin-top: 20px;
    background: #f59e0b;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="login-wrapper">
<div class="login-card">

    <div class="logo">LakbayLokal</div>
    <h2>Welcome Back</h2>
    <p>Login to manage and explore local products</p>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p style="margin-top:16px; font-size:14px;">
        <a href="forgot.php" 
        style="color:#1e3a8a; font-weight:600; text-decoration:none;">
            Forgot Username or Password?
        </a>
    </p>

    <p style="color:red; margin-top:40px;">
        <?= $error ?>
    </p>

    <p style="margin-top:40px; font-size:14px;">
        Don't have an account?
        <a href="register.php" style="color:#1e3a8a; font-weight:bold;">
            Register here
        </a>
    </p>
    
    <p class="back-home">
        <a href="../index.php">← Back to Home</a>
    </p>
    
</div>
</div>

</body>
</html>
