<?php
session_start();
include "../config/db.php";

/* -----------------------------
   REDIRECT IF NO OTP SESSION
------------------------------*/
if (!isset($_SESSION["reg_otp"])) {
    header("Location: register.php");
    exit();
}

/* -----------------------------
   INIT ATTEMPTS COUNTER
------------------------------*/
if (!isset($_SESSION["otp_attempts"])) {
    $_SESSION["otp_attempts"] = 0;
}

/* -----------------------------
   RESEND OTP LOGIC
------------------------------*/
if (isset($_GET["resend"])) {

    $otp = rand(100000, 999999);

    $_SESSION["reg_otp"] = $otp;
    $_SESSION["show_otp"] = $otp;
    $_SESSION["otp_attempts"] = 0;

    header("Location: verify_register.php");
    exit();
}

$error = "";

/* -----------------------------
   SHOW OTP ONCE (DEV ONLY)
------------------------------*/
$otp_display = $_SESSION["show_otp"] ?? null;
unset($_SESSION["show_otp"]);

/* -----------------------------
   VERIFY LOGIC WITH RETRIES
------------------------------*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_SESSION["otp_attempts"] >= 5) {

        $error = "Too many attempts. Please register again.";

        session_destroy();
        header("Location: register.php");
        exit();

    } else {

        if ($_POST["code"] == $_SESSION["reg_otp"]) {

            $data = $_SESSION["reg_data"];

            $sql = "INSERT INTO users (fullname, username, email, password)
                    VALUES ('{$data["fullname"]}','{$data["username"]}','{$data["email"]}','{$data["password"]}')";

            if (mysqli_query($conn, $sql)) {

                unset($_SESSION["reg_data"]);
                unset($_SESSION["reg_otp"]);
                unset($_SESSION["otp_attempts"]);

                header("Location: login.php");
                exit();

            } else {
                $error = "Registration failed!";
            }

        } else {

            $_SESSION["otp_attempts"]++;

            $remaining = 5 - $_SESSION["otp_attempts"];

            $error = "Invalid code. Try again ({$remaining} attempts left)";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify Registration</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.verify-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(to right, #102a43, #1e3a8a);
}
.verify-card {
    background: white;
    width: 380px;
    padding: 40px;
    border-radius: 16px;
    text-align: center;
}
.verify-card input {
    width: 100%;
    padding: 14px;
    margin-top: 15px;
    border-radius: 8px;
    text-align: center;
    font-size: 18px;
}
.verify-card button {
    width: 100%;
    padding: 14px;
    margin-top: 20px;
    background: #f59e0b;
    border: none;
    border-radius: 8px;
    font-weight: bold;
}
.otp-box {
    background: #fef3c7;
    padding: 10px;
    margin-top: 10px;
    border-radius: 8px;
}
.actions {
    margin-top: 20px;
}
.actions a {
    display: inline-block;
    margin: 8px 0;
    color: #1e3a8a;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="verify-wrapper">
<div class="verify-card">

<h2>Verify Registration</h2>
<p>Enter your 6-digit code</p>

<?php if ($otp_display): ?>
<div class="otp-box">
Code: <?= $otp_display ?>
</div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="code" maxlength="6" required>
    <button type="submit">Verify & Register</button>
</form>

<p style="color:red;"><?= $error ?></p>

<!-- BACK + RESEND OPTIONS -->
<div class="actions">
    <a href="register.php">← Back to Registration</a><br>
    <a href="verify_register.php?resend=1">Resend Code</a>
</div>

</div>
</div>

</body>
</html>