<?php
session_start();
include "../config/db.php";
include "../config/mailer.php";

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email  = trim($_POST["email"]);
    $action = $_POST["action"]; // "username" or "password"

    // Look up user by email
    $stmt = mysqli_prepare($conn, "SELECT id, fullname, username FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if (!$user) {
        $error = "No account found with that email address.";
    } else {

        $otp     = rand(100000, 999999);
        $expires = time() + (15 * 60); // 15 minutes

        // Store in session
        $_SESSION["recovery_email"]   = $email;
        $_SESSION["recovery_user_id"] = $user["id"];
        $_SESSION["recovery_name"]    = $user["fullname"];
        $_SESSION["recovery_username"]= $user["username"];
        $_SESSION["recovery_action"]  = $action;
        $_SESSION["recovery_otp"]     = $otp;
        $_SESSION["recovery_expires"] = $expires;
        $_SESSION["recovery_attempts"]= 0;

        // Build email
        $label   = ($action === "username") ? "Username Recovery" : "Password Reset";
        $purpose = ($action === "username")
            ? "retrieve your username"
            : "reset your password";

        $subject = "LakbayLokal – {$label} Code";
        $body = "
            <div style='font-family:sans-serif; max-width:480px; margin:auto;'>
                <div style='background:#102a43; padding:24px; border-radius:12px 12px 0 0; text-align:center;'>
                    <span style='font-size:22px; font-weight:bold; color:#fbbf24;'>LakbayLokal</span>
                </div>
                <div style='background:#ffffff; padding:32px; border-radius:0 0 12px 12px;
                            border:1px solid #e5e7eb;'>
                    <h2 style='color:#102a43; margin-top:0;'>{$label}</h2>
                    <p>Hello <strong>{$user['fullname']}</strong>,</p>
                    <p>Use the code below to {$purpose}. It expires in <strong>15 minutes</strong>.</p>
                    <div style='background:#fef3c7; padding:24px; border-radius:10px;
                                text-align:center; font-size:34px; font-weight:bold;
                                letter-spacing:10px; color:#92400e; margin:24px 0;'>
                        {$otp}
                    </div>
                    <p style='color:#6b7280; font-size:13px;'>
                        If you didn't request this, you can safely ignore this email.
                        Your account remains secure.
                    </p>
                </div>
            </div>
        ";

        $mailResult = sendMail($email, $subject, $body);

        if ($mailResult === true) {

            header("Location: verify_recovery.php");
            exit();

        } else {

            $error = $mailResult;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Credentials | LakbayLokal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .recovery-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a 0%, #102a43 50%, #1e3a8a 100%);
            padding: 20px;
        }

        .recovery-card {
            background: white;
            width: min(420px, 100%);
            padding: 44px 40px;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        .recovery-logo {
            font-size: 24px;
            font-weight: bold;
            color: #f59e0b;
            margin-bottom: 6px;
        }

        .recovery-card h2 {
            color: #102a43;
            font-size: 22px;
            margin: 0 0 8px;
        }

        .recovery-card p.subtitle {
            color: #64748b;
            font-size: 14px;
            margin: 0 0 28px;
            line-height: 1.6;
        }

        /* Tab toggle */
        .tab-group {
            display: flex;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }

        .tab-group label {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            transition: 0.2s;
            user-select: none;
        }

        .tab-group input[type="radio"] {
            display: none;
            width: auto;
            margin: 0;
            padding: 0;
        }

        .tab-group input[type="radio"]:checked + label {
            background: white;
            color: #102a43;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .recovery-card input[type="email"] {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            box-sizing: border-box;
            transition: 0.2s;
            outline: none;
        }

        .recovery-card input[type="email"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .recovery-card button[type="submit"] {
            width: 100%;
            padding: 14px;
            margin-top: 16px;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: #0f172a;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .recovery-card button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245,158,11,0.35);
        }

        .error-msg {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-top: 14px;
            text-align: left;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 24px 0 20px;
        }

        .back-links {
            font-size: 13px;
            color: #64748b;
        }

        .back-links a {
            color: #1e3a8a;
            font-weight: 600;
            text-decoration: none;
        }

        .back-links a:hover { text-decoration: underline; }

        .icon-circle {
            width: 56px;
            height: 56px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 26px;
        }
    </style>
</head>
<body>

<div class="recovery-wrapper">
<div class="recovery-card">

    <div class="recovery-logo">LakbayLokal</div>
    <div class="icon-circle">🔑</div>
    <h2>Account Recovery</h2>
    <p class="subtitle">Enter the email linked to your account and choose what you'd like to recover.</p>

    <form method="POST">

        <!-- Tab: username vs password -->
        <div class="tab-group">
            <input type="radio" name="action" id="tab_user" value="username"
                   <?= (!isset($_POST['action']) || $_POST['action']==='username') ? 'checked' : '' ?>>
            <label for="tab_user">Forgot Username</label>

            <input type="radio" name="action" id="tab_pass" value="password"
                   <?= (isset($_POST['action']) && $_POST['action']==='password') ? 'checked' : '' ?>>
            <label for="tab_pass">Forgot Password</label>
        </div>

        <input type="email" name="email" placeholder="Your registered email address"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

        <button type="submit">Send Recovery Code →</button>

        <?php if ($error): ?>
            <div class="error-msg">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>

    <hr class="divider">

    <div class="back-links">
        Remember your details? <a href="login.php">Login</a>
        &nbsp;·&nbsp;
        <a href="../index.php">← Home</a>
    </div>

</div>
</div>

</body>
</html>