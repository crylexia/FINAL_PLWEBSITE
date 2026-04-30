<?php
session_start();
include "../config/db.php";

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // check username
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    if(mysqli_num_rows($check) > 0){
        $message = "Username already exists!";
    } else {

        $_SESSION["reg_data"] = [
            "fullname" => $fullname,
            "username" => $username,
            "email" => $email,
            "password" => $password
        ];

        $otp = rand(100000, 999999);
        $_SESSION["reg_otp"] = $otp;
        $_SESSION["show_otp"] = $otp;

        header("Location: verify_register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register - LakbayLokal</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="form-container">
<h2>Create Your LakbayLokal Account</h2>

<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Register</button>

    <p style="color:red;"><?php echo $message; ?></p>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>
</div>

</body>
</html>
