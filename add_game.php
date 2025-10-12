<?php
# adding the classes.
require_once 'Game.php';
require_once 'GameManager.php';

# connection to the database.
$pdo = new PDO('mysql:host=localhost;dbname=gamestore;charset=utf8', 'root', '');

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
