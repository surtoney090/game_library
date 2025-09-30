<?php
# Database connectie gegevens
$servername = "localhost";    # De database draait lokaal
$username   = "root";         # Standaard MySQL user in XAMPP
$password   = "IJmuiden1611"; # mn pass

# Eerst verbinden zonder specifieke database
$conn = new mysqli($servername, $username, $password);

# Check connectie
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

# Nieuwe database aanmaken
#$sql = "CREATE DATABASE IF NOT EXISTS game_library";
#if ($conn->query($sql) === TRUE) {
#    echo "DB created succesfully type shi";
#} else {
#    echo "Error creating shi like db: " . $conn->error . "<br>";
#}

# Sluit de eerste connectie
$conn->close();

# Maak een nieuwe connectie met de zojuist aangemaakte database
$conn = new mysqli($servername, $username, $password, "game_library");

if ($conn->connect_error) {
    die("Connection to game_library type shi failed dramatically: " . $conn->connect_error);
}

# Maak de games tabel met alle kolommen die GameManager verwacht
$sql = "CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    developer VARCHAR(255),
    description TEXT,
    genre VARCHAR(100),
    platform VARCHAR(100),
    release_year INT,
    rating INT
)";

$conn->close(); # Sluit de connectie
