<?php
# Database connectie gegevens
$servername = "localhost";
$username   = "root";
$password   = "IJmuiden1611";

# Verbinden met MySQL
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

# Database maken als die nog niet bestaat
# $conn->query("CREATE DATABASE IF NOT EXISTS game_library");

$conn->close();

# Verbinden met de specifieke database
$conn = new mysqli($servername, $username, $password, "game_library");

if ($conn->connect_error) {
    die("Connection to game_library failed: " . $conn->connect_error);
}

# Correcte SQL met komma's
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

# Voer de query uit
if ($conn->query($sql) === TRUE) {
    echo "Table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
