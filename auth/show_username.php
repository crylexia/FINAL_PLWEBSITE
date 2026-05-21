<?php
session_start();

if (!isset($_SESSION["recovery_username"])) {
    header("Location: forgot.php");
    exit();
}

$username = $_SESSION["recovery_username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Recovered Username | LakbayLokal</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>

body{
    margin:0;
    font-family:Arial, sans-serif;
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
    background:white;
    width:min(420px,100%);
    padding:45px 40px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 25px 60px rgba(0,0,0,0.25);
}

.logo{
    font-size:26px;
    font-weight:bold;
    color:#f59e0b;
    margin-bottom:10px;
}

.icon{
    width:70px;
    height:70px;
    margin:auto;
    border-radius:50%;
    background:#dbeafe;
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

.username-box{
    background:#f8fafc;
    border:2px dashed #cbd5e1;
    border-radius:14px;
    padding:22px;
    margin-bottom:24px;
}

.username-label{
    font-size:13px;
    color:#64748b;
    margin-bottom:8px;
}

.username{
    font-size:32px;
    font-weight:800;
    color:#1e3a8a;
    letter-spacing:1px;
}

.login-btn{
    display:block;
    width:100%;
    padding:14px;
    border-radius:12px;
    text-decoration:none;
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#0f172a;
    font-weight:bold;
    transition:0.3s;
    box-sizing:border-box;
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(245,158,11,0.3);
}

.home-link{
    margin-top:18px;
    font-size:13px;
}

.home-link a{
    color:#64748b;
    text-decoration:none;
}

.home-link a:hover{
    text-decoration:underline;
}

</style>
</head>

<body>

<div class="wrapper">

<div class="card">

    <div class="logo">LakbayLokal</div>

    <div class="icon">👤</div>

    <h2>Username Recovered</h2>

    <p class="subtitle">
        We successfully verified your identity.
        Your username is shown below.
    </p>

    <div class="username-box">

        <div class="username-label">
            YOUR USERNAME
        </div>

        <div class="username">
            <?= htmlspecialchars($username) ?>
        </div>

    </div>

    <a href="login.php" class="login-btn">
        Continue to Login →
    </a>

    <div class="home-link">
        <a href="../index.php">← Back to Home</a>
    </div>

</div>

</div>

</body>
</html>