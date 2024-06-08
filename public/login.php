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
        $stmt = $conn->prepare("SELECT password, secret, salt FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($watchword, $secret, $salt);
        $stmt->fetch();
        $stmt->close();
    } catch (Exception $e) {
        echo "Error while logging in: " . $e->getMessage();
    }

    file_put_contents('../seven/files/password.txt', $password);

    shell_exec('javac -cp "../seven/lib/*" -d "../seven/bin" src/*.java');
    shell_exec('java -cp "../seven/bin;../seven/lib/*" Main hash "../seven/files/password.txt" MD5');

    $password = file_get_contents('../seven/files/MD5_password.txt');

    $password = $password . $salt . $pepper;

    echo "Comparing " . $password . " with " . $watchword . "<br>";

    if ($password == $watchword) {
        $totp = TOTP::create($secret);
        $time = time();

        // Also accept the previous and next OTPs
        if ($totp->verify($otp, ($time - 30)) || $totp->verify($otp) || $totp->verify($otp, ($time + 30))) {
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

    file_put_contents('../seven/files/password.txt', 'This file is already overwritten!');
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
    <br>
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