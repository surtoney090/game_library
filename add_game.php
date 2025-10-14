<?php
# adding the classes.
require_once 'Game.php';
require_once 'GameManager.php';

# connection to the database.
$pdo = new PDO('mysql:host=localhost;dbname=game_library;charset=utf8', 'root', '');

$gameManager = new GameManager($pdo);

# variables for feedback.
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    #get and validate the input.
    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $platform = trim($_POST['platform'] ?? '');
    $imagePath = trim($_POST['image_path'] ?? '');
    $rating = trim($_POST['rating'] ?? '');
    $release_year = trim($_POST['release_year'] ?? '');

    if (empty($title)) {
        $errors[] = "Title is a must!";
    }
    if (empty($genre)) {
        $errors[] = "Genre is a must!";
    }
    if (empty($description)) {
        $errors[] = "Description is a must!";
    }
    if (empty($platform)) {
        $errors[] = "Platform is a must!";
    }
    if (empty($imagePath)) {
        $errors[] = "Image is a must";
    }
    if (!ctype_digit($rating) || (int)$rating < 1970 || (int)$rating > ("Y") + 1) {
        $errors[] = "Please enter a valid rating";
    }
    if (!ctype_digit($release_year) || (int)$release_year < 1970 || (int)$release_year > date("Y") + 1) {
        $errors[] = "Please enter a valid year";
    }

    # if error message = empty, $game = new game (get these variables basically adding a game) if (I can add a game from game, which game manager will do) success, print ""
    if (empty($errors)) {
        $game = new Game($title, $description, $genre, $platform, $release_year, $rating, $id = null, $imagePath);
        if ($gameManager->addGame($game)) {
            $success = "Game added succesfully!";
        } else {
            $errors[] = "Something went wrong when trying to add a game";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <title>Add New Game</title>
</head>

<body>
    <h1>Add New Game</h1>

    <?php if (!empty($success)): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="add_game.php">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title" required><br><br>

        <label for="genre">Genre:</label><br>
        <input type="text" name="genre" id="genre" required><br><br>

        <label for="release_year">ReleaseYear:</label><br>
        <input type="number" name="release_year" id="release_year" required><br><br>

        <button type="submit">Add</button>
    </form>
</body>

</html>