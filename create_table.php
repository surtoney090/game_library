<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "game_library";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

# Creates the table
# games is the name of our table
# title, genre, platform, release_year are other columns.
# VARCHAR = Variable Character, stores strings, unlike CHAR that has a fixed space VARCHAR defines a maximum length. title VARCHAR(100) means the column can store 100 characters.
# NOT NULL = just like NULL = absence of value in python, NOT NULL means it can't be NULL, can't have an absence of value.
# DECIMAL (3,1) 3 stands for the amount of ints, 1 stands for the floats.
$sql = "CREATE TABLE games (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    platform VARCHAR(50),
    release_year YEAR
    rating DECIMAL(3,1) 
    image_path VARCHAR (255)
)";

# Sends "make table" instruction to MySQL.
#if ($conn->query($sql) === TRUE) {
#echo "Table 'games' created successfully";
#} else {
#echo "Error creating table: " . $conn->error;
#}

$conn->close();
