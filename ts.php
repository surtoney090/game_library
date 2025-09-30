<?php
require 'create_db.php';
require 'game_class.php';
require 'gamemanager_class.php';

# adds a connection type shi
$db = new PDO('create_db.php');

# creates a game manager type shi
$manager = new GameManager($db);

# add a new game type shi
$newGame = new Game("Title", "Developer", "Description", "Genre", "Platform", 2017, 9.5);
$manager->addGame($newGame);

# gets every game around the campfire type shi
$games = $manager->getAllGames();
foreach ($games as $game) {
    echo $game->getTitle() . " - Rating: " . $game->getRating() . "<br>";
}
