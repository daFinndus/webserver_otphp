<?php

require ('../vendor/autoload.php');

function HelloWorld(): bool
{
    print ("Hello World");
    return true;
}

function RandomString(int $length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function for creating a database if not existent and selecting it
function CreateDatabase($conn, $dbname): bool
{
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error creating database: " . $conn->error;
        return false;
    }
}

// Function for creating a table if not existent
function CreateUsersTable($conn, $dbname): bool
{
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        secret VARCHAR(255) NOT NULL,
        salt VARCHAR(255) NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error creating table: " . $conn->error;
        return false;
    }
}

?>