<?php

// Retrieve username from cookie
$username = $_COOKIE['username'];

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
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin et ex nec lorem fringilla fermentum nec at justo. Integer at dui purus. Pellentesque et dapibus risus.</p>
            <a href="post.php?id=1">Read more</a>
        </div>
        <div class="post">
            <h2>Second Blog Post</h2>
            <p>Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem.</p>
            <a href="post.php?id=2">Read more</a>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Simple Blog</p>
    </footer>
</body>

</html>