<?php

$posts = [
    1 => [
        'title' => 'First Blog Post',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin et ex nec lorem fringilla fermentum nec at justo. Integer at dui purus. Pellentesque et dapibus risus. Aliquam erat volutpat. Ut fringilla turpis sit amet arcu finibus, eget posuere justo interdum.'
    ],
    2 => [
        'title' => 'Second Blog Post',
        'content' => 'Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Sed porttitor lectus nibh. Nulla porttitor accumsan tincidunt. Vivamus suscipit tortor eget felis porttitor volutpat.'
    ]
];

$postId = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$post = isset($posts[$postId]) ? $posts[$postId] : $posts[1];

?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($post['title']); ?> - Simple Blog</title>
    <link rel="stylesheet" type="text/css" href="blog.css">
</head>

<body>
    <header>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    </header>
    <div class="container">
        <div class="post">
            <p><?php echo htmlspecialchars($post['content']); ?></p>
        </div>
        <a href="index.php">Back to Home</a>
    </div>
    <footer>
        <p>&copy; 2024 Simple Blog</p>
    </footer>
</body>

</html>