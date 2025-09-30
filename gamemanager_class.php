<?php
class GameManager
{
    private $db;

    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    # takes every row out the database and makes them into game objects
    public function getAllGames()
    {
        try {
            $sql = "SELECT * FROM games";
            $stmt = $this->db->query($sql);
            $games = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $games[] = new Game(
                    $row['title'],
                    $row['developer'],
                    $row['description'],
                    $row['genre'],
                    $row['platform'],
                    (int)$row['release_year'],
                    (int)$row['rating'],
                    (int)$row['id']
                );
            }

            return $games;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error in getAllGames: " . $e->getMessage() . "</p>";
            return [];
        }
    }

    # gets a single game by id
    public function getGameById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Game(
                    $row['title'],
                    $row['developer'],
                    $row['description'],
                    $row['genre'],
                    $row['platform'],
                    (int)$row['release_year'],
                    (int)$row['rating'],
                    (int)$row['id']
                );
            }
            return null;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error in getGameById: " . $e->getMessage() . "</p>";
            return null;
        }
    }

    # adds a new game object to the db
    public function addGame(Game $game)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO games (title, developer, description, genre, platform, release_year, rating) 
                VALUES (:title, :developer, :description, :genre, :platform, :release_year, :rating)
            ");
            $stmt->bindValue(':title', $game->getTitle());
            $stmt->bindValue(':developer', $game->getDeveloper());
            $stmt->bindValue(':description', $game->getDescription());
            $stmt->bindValue(':genre', $game->getGenre());
            $stmt->bindValue(':platform', $game->getPlatform());
            $stmt->bindValue(':release_year', (int)$game->getReleaseYear(), PDO::PARAM_INT);
            $stmt->bindValue(':rating', (int)$game->getRating(), PDO::PARAM_INT);

            $success = $stmt->execute();

            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                echo "<p style='color:red;'>Insert failed: " . $errorInfo[2] . "</p>";
            }

            return $success;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error in addGame: " . $e->getMessage() . "</p>";
            return false;
        }
    }

    # changes an existing game
    public function updateGame(Game $game)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE games 
                SET title=:title, developer=:developer, description=:description, genre=:genre, platform=:platform, release_year=:release_year, rating=:rating 
                WHERE id=:id
            ");
            $stmt->bindValue(':title', $game->getTitle());
            $stmt->bindValue(':developer', $game->getDeveloper());
            $stmt->bindValue(':description', $game->getDescription());
            $stmt->bindValue(':genre', $game->getGenre());
            $stmt->bindValue(':platform', $game->getPlatform());
            $stmt->bindValue(':release_year', (int)$game->getReleaseYear(), PDO::PARAM_INT);
            $stmt->bindValue(':rating', (int)$game->getRating(), PDO::PARAM_INT);
            $stmt->bindValue(':id', (int)$game->getId(), PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error in updateGame: " . $e->getMessage() . "</p>";
            return false;
        }
    }

    # deletes a game via its ID
    public function deleteGame($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM games WHERE id=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error in deleteGame: " . $e->getMessage() . "</p>";
            return false;
        }
    }
}
