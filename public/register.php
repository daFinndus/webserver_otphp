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
$conn = new mysqli($sql["servername"], $sql["username"], $sql["password"]);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$totp = TOTP::create();
$secret = $totp->getSecret();
$totp->setLabel('Eight');

$qrCodeUrl = $totp->getProvisioningUri();

// This function is from mysql.php
CreateDatabase($conn, $sql["dbname"]);

// Select the database
$conn->select_db($sql["dbname"]);

// This function is from mysql.php
CreateUsersTable($conn, $sql["dbname"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['otp'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Store username, password, and secret in session
    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);
    $_SESSION['secret'] = $secret;

    // Display QR code for scanning
    echo "
    <html>
        <head>
            <title>Scan QR Code</title>
            <link rel='stylesheet' type='text/css' href='form.css'>
        </head>
        <body>
            <br>
            <h1>Scan the QR Code</h1>
            <img src='https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($qrCodeUrl) . "&size=200x200' alt='QR Code'>
            <form method='POST' action=''>
                <label for='otp'>Enter OTP:</label>
                <input type='text' id='otp' name='otp' required><br>
                <button type='submit'>Finish registration!</button>
            </form>
        </body>
    </html>
    ";
    exit();
}

// Verify OTP and complete registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    session_start();
    $username = $_SESSION['username'];
    $watchword = $_SESSION['password'];
    $secret = $_SESSION['secret'];
    $otp = $_POST['otp'];

    $totp = TOTP::create($secret);

    if ($totp->verify($otp)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, secret) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $watchword, $secret);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Insert statement failed!');</script>";
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error while signing up: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }

    session_destroy();
}

$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Eight</title>
    <link rel="stylesheet" type="text/css" href="form.css">
</head>

<body>
    <br>
    <h1>Registration</h1>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Sign up</button>
    </form>
</body>

</html>
