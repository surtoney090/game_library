<?php
require 'gamemanager_class.php';

$servername = "localhost";
$username   = "root";
$password   = "IJmuiden1611";
$dbname     = "game_library";

try {
    # Connect without specifying db first (in case db doesn’t exist yet)
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    # Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    # Now connect to the specific database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    # Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        developer VARCHAR(255),
        description TEXT,
        genre VARCHAR(100),
        platform VARCHAR(100),
        release_year INT,
        rating INT,
        image_path VARCHAR(255)
    )";

    $pdo->exec($sql);

    $gameManager = new GameManager($pdo);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
