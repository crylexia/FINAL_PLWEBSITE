<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["recovery_user_id"])) {
    header("Location: forgot.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST["password"];
    $confirm  = $_POST["confirm_password"];

    if ($password !== $confirm) {

        $error = "Passwords do not match.";

    } else {

        // Get current password hash
        $stmt = mysqli_prepare(
            $conn,
            "SELECT password FROM users WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $_SESSION["recovery_user_id"]
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);

        // Reject same password
        if (password_verify($password, $user["password"])) {

            $error = "New password cannot be the same as your old password.";

        } else {

            // Hash new password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Update password
            $update = mysqli_prepare(
                $conn,
                "UPDATE users SET password=? WHERE id=?"
            );

            mysqli_stmt_bind_param(
                $update,
                "si",
                $hashed,
                $_SESSION["recovery_user_id"]
            );

            mysqli_stmt_execute($update);

            session_destroy();

            $success = "Password updated successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reset Password | LakbayLokal</title>

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
}

.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f172a 0%, #102a43 50%, #1e3a8a 100%);
    padding:20px;
}

.card{
    width:min(430px,100%);
    background:white;
    border-radius:22px;
    padding:45px 40px;
    text-align:center;
    box-shadow:0 30px 60px rgba(0,0,0,0.25);
}

.logo{
    font-size:26px;
    font-weight:bold;
    color:#f59e0b;
    margin-bottom:12px;
}

.icon{
    width:70px;
    height:70px;
    margin:auto;
    border-radius:50%;
    background:#fef3c7;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:34px;
    margin-bottom:18px;
}

h2{
    color:#102a43;
    margin-bottom:10px;
}

.subtitle{
    color:#64748b;
    font-size:14px;
    line-height:1.6;
    margin-bottom:28px;
}

input{
    width:100%;
    padding:14px 16px;
    margin-bottom:16px;
    border-radius:10px;
    border:1.5px solid #e5e7eb;
    box-sizing:border-box;
    font-size:15px;
}

input:focus{
    outline:none;
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,0.1);
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#0f172a;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(245,158,11,0.3);
}

.error{
    background:#fef2f2;
    color:#dc2626;
    border:1px solid #fecaca;
    padding:12px;
    border-radius:10px;
    margin-top:16px;
    font-size:14px;
}

.success{
    background:#ecfdf5;
    color:#059669;
    border:1px solid #a7f3d0;
    padding:12px;
    border-radius:10px;
    margin-top:16px;
    font-size:14px;
}

.login-link{
    margin-top:18px;
    font-size:14px;
}

.login-link a{
    color:#1e3a8a;
    font-weight:bold;
    text-decoration:none;
}

</style>
</head>

<body>

<div class="wrapper">

<div class="card">

    <div class="logo">LakbayLokal</div>

    <div class="icon">🔒</div>

    <h2>Create New Password</h2>

    <p class="subtitle">
        Your identity has been verified.
        Enter a new password for your account.
    </p>

    <form method="POST">

        <input type="password"
               name="password"
               placeholder="New Password"
               required>

        <input type="password"
               name="confirm_password"
               placeholder="Confirm Password"
               required>

        <button type="submit">
            Update Password →
        </button>

    </form>

    <?php if($error): ?>
        <div class="error">
            ⚠ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success">
            ✅ <?= htmlspecialchars($success) ?>
            <br><br>
            <a href="login.php">Go to Login</a>
        </div>
    <?php endif; ?>

</div>

</div>

</body>
</html>