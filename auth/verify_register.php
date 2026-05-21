<?php
session_start();
include "../config/db.php";
include "../config/mailer.php"; // ← add this

// Redirect if no OTP session
if (!isset($_SESSION["reg_otp"])) {
    header("Location: register.php");
    exit();
}

// Init attempts counter
if (!isset($_SESSION["otp_attempts"])) {
    $_SESSION["otp_attempts"] = 0;
}

$error = "";

/* -----------------------------
   RESEND OTP LOGIC
------------------------------*/
if (isset($_GET["resend"])) {

    $otp     = rand(100000, 999999);
    $expires = time() + (10 * 60);

    $_SESSION["reg_otp"]      = $otp;
    $_SESSION["otp_expires"]  = $expires;
    $_SESSION["otp_attempts"] = 0;

    $data    = $_SESSION["reg_data"];
    $subject = "Your New LakbayLokal Verification Code";
    $body = "
        <div style='font-family:sans-serif; max-width:480px; margin:auto;'>
            <h2 style='color:#102a43;'>New Verification Code</h2>
            <p>Hello <strong>{$data['fullname']}</strong>,</p>
            <p>Here is your new code. It expires in <strong>10 minutes</strong>.</p>
            <div style='background:#fef3c7; padding:20px; border-radius:10px;
                        text-align:center; font-size:32px; font-weight:bold;
                        letter-spacing:8px; color:#92400e;'>
                {$otp}
            </div>
        </div>
    ";

    if (sendMail($data["email"], $subject, $body)) {
        $resent = true;
    } else {
        $error = "Could not resend code. Please try again.";
    }
}

/* -----------------------------
   VERIFY LOGIC
------------------------------*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Too many attempts — kill session
    if ($_SESSION["otp_attempts"] >= 5) {
        session_destroy();
        header("Location: register.php?error=toomany");
        exit();
    }

    // ← NEW: Check if OTP has expired
    if (time() > $_SESSION["otp_expires"]) {
        session_destroy();
        header("Location: register.php?error=expired");
        exit();
    }

    if ($_POST["code"] == $_SESSION["reg_otp"]) {

        $data = $_SESSION["reg_data"];

        // Prepared statement INSERT (fixes SQL injection)
        $sql  = "INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss",
            $data["fullname"],
            $data["username"],
            $data["email"],
            $data["password"]
        );

        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION["reg_data"]);
            unset($_SESSION["reg_otp"]);
            unset($_SESSION["otp_expires"]);
            unset($_SESSION["otp_attempts"]);

            header("Location: login.php?registered=1");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }

    } else {
        $_SESSION["otp_attempts"]++;
        $remaining = 5 - $_SESSION["otp_attempts"];
        $error = "Invalid code. {$remaining} attempt(s) left.";
    }
}

// Expiry countdown for display
$secondsLeft = isset($_SESSION["otp_expires"]) 
    ? max(0, $_SESSION["otp_expires"] - time()) 
    : 0;
$minutesLeft = ceil($secondsLeft / 60);
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .verify-card input {
            width: 100%;
            padding: 14px;
            margin-top: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 22px;
            letter-spacing: 6px;
            border: 1px solid #e5e7eb;
            box-sizing: border-box;
        }
        .verify-card button {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background: #f59e0b;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }
        .email-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #1e40af;
            margin-top: 12px;
        }
        .expiry-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }
        .actions {
            margin-top: 20px;
        }
        .actions a {
            display: inline-block;
            margin: 6px 0;
            color: #1e3a8a;
            font-size: 14px;
            text-decoration: none;
        }
        .actions a:hover { text-decoration: underline; }
        .success-note { color: green; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>

<div class="verify-wrapper">
<div class="verify-card">

    <h2>Check Your Email</h2>
    <p>We sent a 6-digit verification code to:</p>

    <div class="email-note">
        📧 <?= htmlspecialchars($_SESSION["reg_data"]["email"]) ?>
    </div>

    <p class="expiry-note">Code expires in ~<?= $minutesLeft ?> minute(s)</p>

    <?php if (!empty($resent)): ?>
        <p class="success-note">✅ A new code has been sent!</p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="code" maxlength="6" 
               placeholder="------" autocomplete="one-time-code" required>
        <button type="submit">Verify & Register</button>
    </form>

    <p style="color:red; margin-top:12px;"><?= $error ?></p>

    <div class="actions">
        <a href="register.php">← Back to Registration</a><br>
        <a href="verify_register.php?resend=1">Resend Code</a>
    </div>

</div>
</div>

</body>
</html>