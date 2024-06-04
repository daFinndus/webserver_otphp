<?php

require('../vendor/autoload.php');
require('mysql.php');

use OTPHP\TOTP;

$sql = array(
    "servername" => "localhost",
    "username" => "root",
    "password" => "swordfish",
    "dbname" => "eight"
);

// Create connection
$conn = new mysqli($sql["servername"], $sql["username"], $sql["password"], $sql["dbname"]);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $otp = $_POST['otp'];

    try {
        $stmt = $conn->prepare("SELECT password, secret FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($watchword, $secret);
        $stmt->fetch();
        $stmt->close();
    } catch (Exception $e) {
        echo "Error while logging in: " . $e->getMessage();
    }

    if (password_verify($password, $watchword)) {
        $totp = TOTP::create($secret);

        if ($totp->verify($otp)) {
            echo "Login successful!";

            // Redirect to home page and store username in cookie
            setcookie("username", $username, time() + 3600, "/");
            header("Location: /");
        } else {
            echo "<script>alert('Invalid OTP.')</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password.')</script>";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="form.css">
</head>

<body>
    <h1>Login</h1>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="otp">OTP:</label>
        <input type="text" id="otp" name="otp" required><br>

        <button type="submit">Log in</button>
    </form>
</body>

</html>