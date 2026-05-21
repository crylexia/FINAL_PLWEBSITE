<?php
session_start();

if (!isset($_SESSION["recovery_otp"])) {
    header("Location: forgot.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $entered_otp = trim($_POST["otp"]);

    // Check expiration
    if (time() > $_SESSION["recovery_expires"]) {

        $error = "OTP expired. Please request a new recovery code.";

    }

    // Check OTP
    elseif ($entered_otp == $_SESSION["recovery_otp"]) {

        // Forgot password
        if ($_SESSION["recovery_action"] === "password") {

            header("Location: reset_password.php");
            exit();

        }

        // Forgot username
        else {

            header("Location: show_username.php");
            exit();

        }

    } else {

        $error = "Invalid OTP code.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Verify Recovery Code | LakbayLokal</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:linear-gradient(135deg,#0f172a 0%, #102a43 50%, #1e3a8a 100%);
}

.verify-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.verify-card{
    width:min(420px,100%);
    background:white;
    padding:45px 40px;
    border-radius:22px;
    box-shadow:0 25px 60px rgba(0,0,0,0.35);
    text-align:center;
}

.logo{
    font-size:26px;
    font-weight:bold;
    color:#f59e0b;
    margin-bottom:10px;
}

.icon-circle{
    width:70px;
    height:70px;
    background:#fef3c7;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 18px;
    font-size:32px;
}

.verify-card h2{
    margin:0;
    color:#102a43;
    font-size:24px;
}

.subtitle{
    margin-top:12px;
    color:#64748b;
    font-size:14px;
    line-height:1.6;
}

.email-box{
    margin-top:18px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    padding:12px;
    border-radius:10px;
    font-size:14px;
    color:#334155;
}

.verify-card input{
    width:100%;
    padding:15px;
    margin-top:22px;
    border:1.5px solid #dbeafe;
    border-radius:12px;
    font-size:18px;
    text-align:center;
    letter-spacing:5px;
    outline:none;
    transition:0.2s;
}

.verify-card input:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 4px rgba(59,130,246,0.12);
}

.verify-card button{
    width:100%;
    padding:15px;
    margin-top:18px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#0f172a;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.verify-card button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(245,158,11,0.35);
}

.error-box{
    margin-top:18px;
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#dc2626;
    padding:12px;
    border-radius:10px;
    font-size:14px;
}

.back-link{
    margin-top:28px;
    font-size:13px;
    color:#64748b;
}

.back-link a{
    color:#1e3a8a;
    text-decoration:none;
    font-weight:600;
}

.back-link a:hover{
    text-decoration:underline;
}

</style>
</head>

<body>

<div class="verify-wrapper">

    <div class="verify-card">

        <div class="logo">LakbayLokal</div>

        <div class="icon-circle">
            🔐
        </div>

        <h2>Verify Recovery Code</h2>

        <p class="subtitle">
            Enter the 6-digit OTP code sent to your email address.
        </p>

        <div class="email-box">
            Code sent to:
            <br>
            <strong><?= htmlspecialchars($_SESSION["recovery_email"]) ?></strong>
        </div>

        <form method="POST">

            <input
                type="text"
                name="otp"
                maxlength="6"
                placeholder="------"
                required
            >

            <button type="submit">
                Verify OTP →
            </button>

        </form>

        <?php if($error): ?>

            <div class="error-box">
                ⚠ <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <div class="back-link">
            <a href="forgot.php">← Back to Recovery</a>
        </div>

    </div>

</div>

</body>
</html>