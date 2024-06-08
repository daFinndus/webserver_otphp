<?php

require ('../vendor/autoload.php');
require ('functions.php');

use OTPHP\TOTP;

// Hardcoded pepper
$pepper = 'piRHOCe4';

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

// This function is from mysql.php
CreateDatabase($conn, $sql["dbname"]);

// Select the database
$conn->select_db($sql["dbname"]);

// This function is from mysql.php
CreateUsersTable($conn, $sql["dbname"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['otp'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $salt = RandomString(8);

    file_put_contents('../seven/files/password.txt', $password);

    shell_exec('javac -cp "../seven/lib/*" -d "../seven/bin" src/*.java');
    shell_exec('java -cp "../seven/bin;../seven/lib/*" Main hash "../seven/files/password.txt" MD5');

    $password = file_get_contents('../seven/files/MD5_password.txt');

    // Store username, password, and secret in session
    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password . $salt . $pepper;
    $_SESSION['secret'] = $secret;
    $_SESSION['salt'] = $salt;

    // Set label and add username to TOTP
    $totp->setIssuer('Eight');
    $totp->setLabel($username);

    $qrCodeUrl = $totp->getProvisioningUri();

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
    $salt = $_SESSION['salt'];
    $otp = $_POST['otp'];

    $totp = TOTP::create($secret);
    $time = time();

    if ($totp->verify($otp, ($time - 30)) || $totp->verify($otp) || $totp->verify($otp, ($time + 30))) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, secret, salt) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $watchword, $secret, $salt);

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
        // Stay on the OTP page and show an error message
        echo "
                <html>
                    <head>
                        <title>Scan QR Code</title>
                        <link rel='stylesheet' type='text/css' href='form.css'>
                    </head>
                    <body>
                        <br>
                        <h1>Scan the QR Code</h1>
                        <form method='POST' action=''>
                            <label for='otp'>Enter OTP:</label>
                            <input type='text' id='otp' name='otp' required>
                            <p style='color: red;'>Invalid OTP. Please try again.</p>
                            <button type='submit'>Finish registration!</button>
                        </form>
                    </body>
                </html>
                ";
        exit();
    }

    session_destroy();

    file_put_contents('../seven/files/password.txt', 'This file is already overwritten!');
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