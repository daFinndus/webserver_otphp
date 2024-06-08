<?php

require ('functions.php');

// Retrieve username from cookie
try {
    $username = $_COOKIE['username'];
} catch (Exception $e) {
    echo "Error while retrieving username: " . $e->getMessage();
}

$sql = array(
    "servername" => "localhost",
    "username" => "root",
    "password" => "swordfish",
    "dbname" => "eight"
);

// Check if the username is in our database and redirect to register if it is not
$conn = new mysqli($sql["servername"], $sql["username"], $sql["password"], $sql["dbname"]);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// This function is from mysql.php
CreateDatabase($conn, $sql["dbname"]);

// Select the database
$conn->select_db($sql["dbname"]);

// This function is from mysql.php
CreateUsersTable($conn, $sql["dbname"]);

try {
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user);
    $stmt->fetch();
    $stmt->close();
} catch (Exception $e) {
    echo "Error while checking username: " . $e->getMessage();
}

if ($user != $username) {
    header("Location: /register.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Simple Blog</title>
    <link rel="stylesheet" type="text/css" href="blog.css">
</head>

<body>
    <header>
        <h1>Welcome to the Simple Blog</h1>
    </header>
    <div class="container">
        <h2>Hello, <?php echo htmlspecialchars($username); ?>!</h2>
        <div class="post">
            <h2>First Blog Post</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin et ex nec lorem fringilla fermentum nec at
                justo. Integer at dui purus. Pellentesque et dapibus risus.</p>
            <a href="post.php?id=1">Read more</a>
        </div>
        <div class="post">
            <h2>Second Blog Post</h2>
            <p>Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Curabitur arcu erat, accumsan id
                imperdiet et, porttitor at sem.</p>
            <a href="post.php?id=2">Read more</a>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Simple Blog</p>
    </footer>
</body>

</html>