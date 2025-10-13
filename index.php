<?php
require 'create_db.php'; // Verantwoordelijk voor het aanmaken van de databaseverbinding of -structuur
require 'game_class.php'; // Bevat de Game-klasse die individuele gameobjecten beschrijft
require 'gamemanager_class.php'; // Bevat de GameManager-klasse voor CRUD-operaties

# connect to db with tha PDO
$servername = "localhost";
$username   = "root";
$password   = "IJmuiden1611";
$database   = "game_library";

try {
    $dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$manager = new GameManager($db);
$message = "";
$editGame = null;

# Als formulier is ingediend voeg toe of update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    if ($action === 'add') {
        $newGame = new Game(
            $_POST['title'],
            $_POST['developer'],
            $_POST['description'],
            $_POST['genre'],
            $_POST['platform'],
            $_POST['release_year'],
            $_POST['rating'],
            null,
            $imagePath
        );

        if ($manager->addGame($newGame)) {
            $message = "<div class='success'>Game succesvol toegevoegd!</div>";
        } else {
            $message = "<div class='error'>Fout bij toevoegen van game.</div>";
        }
    }

    if ($action === 'delete') {
        if ($manager->deleteGame($_POST['id'])) {
            $message = "<div class='success'>Game succesvol verwijderd!</div>";
        } else {
            $message = "<div class='error'>Fout bij verwijderen van game.</div>";
        }
    }

    if ($action === 'update') {
        $currentGame = $manager->getGameById($_POST['id']);
        $imagePath = $currentGame->getImagePath();

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath;
            }
        }

        $gameToUpdate = new Game(
            $_POST['title'],
            $_POST['developer'],
            $_POST['description'],
            $_POST['genre'],
            $_POST['platform'],
            $_POST['release_year'],
            $_POST['rating'],
            $_POST['id'],
            $imagePath
        );

        if ($manager->updateGame($gameToUpdate)) {
            $message = "<div class='success'>Game succesvol bijgewerkt!</div>";
        } else {
            $message = "<div class='error'>Fout bij updaten van game.</div>";
        }
    }
}

# Als er een edit-request komt game ophalen
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editGame = $manager->getGameById($_GET['edit']);
}

$games = $manager->getAllGames();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <title>Game Library</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #1e3c72, #2a5298);
            margin: 0;
            padding: 0;
            color: #fff;
        }

        header {
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        main {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        form textarea {
            grid-column: span 2;
            min-height: 80px;
        }

        form button {
            grid-column: span 2;
            background: #ff9800;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            padding: 10px;
            border: none;
            border-radius: 8px;
        }

        form button:hover {
            background: #e68900;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            border-radius: 12px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #2a5298;
            color: #fff;
        }

        tr:hover td {
            background: rgba(42, 82, 152, 0.1);
        }

        .success,
        .error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .success {
            background: rgba(76, 175, 80, 0.9);
        }

        .error {
            background: rgba(244, 67, 54, 0.9);
        }

        .delete-btn,
        .edit-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            color: #fff;
        }

        .delete-btn {
            background: #f44336;
        }

        .delete-btn:hover {
            background: #d32f2f;
        }

        .edit-btn {
            background: #2196F3;
        }

        .edit-btn:hover {
            background: #1976D2;
        }
    </style>
</head>

<body>
    <header>
        <h1>Game Library</h1>
    </header>
    <main>

        <h2><?= $editGame ? "Game bewerken" : "Nieuwe game toevoegen" ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $editGame ? "update" : "add" ?>">
            <?php if ($editGame): ?>
                <input type="hidden" name="id" value="<?= $editGame->getId(); ?>">
            <?php endif; ?>

            <input type="text" name="title" placeholder="Titel" value="<?= $editGame ? htmlspecialchars($editGame->getTitle()) : "" ?>" required>
            <input type="text" name="developer" placeholder="Developer" value="<?= $editGame ? htmlspecialchars($editGame->getDeveloper()) : "" ?>">
            <textarea name="description" placeholder="Beschrijving"><?= $editGame ? htmlspecialchars($editGame->getDescription()) : "" ?></textarea>
            <input type="text" name="genre" placeholder="Genre" value="<?= $editGame ? htmlspecialchars($editGame->getGenre()) : "" ?>">
            <input type="text" name="platform" placeholder="Platform" value="<?= $editGame ? htmlspecialchars($editGame->getPlatform()) : "" ?>">
            <input type="number" name="release_year" placeholder="Release jaar" value="<?= $editGame ? htmlspecialchars($editGame->getReleaseYear()) : "" ?>">
            <input type="number" name="rating" min="1" max="10" placeholder="Rating (1-10)" value="<?= $editGame ? htmlspecialchars($editGame->getRating()) : "" ?>">
            <input type="file" name="image" accept="image/*">
            <button type="submit"><?= $editGame ? "Save" : "Add" ?></button>
        </form>

        <h2>All Games</h2>
        <?php if (count($games) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Developer</th>
                    <th>Description</th>
                    <th>Genre</th>
                    <th>Platform</th>
                    <th>Release Year</th>
                    <th>Rating</th>
                    <th>Actions</th>
                    <th>Remove/Edit</th>
                </tr>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= $game->getId(); ?></td>
                        <td><?= htmlspecialchars($game->getTitle()); ?></td>
                        <td><?= htmlspecialchars($game->getDeveloper()); ?></td>
                        <td><?= htmlspecialchars($game->getDescription()); ?></td>
                        <td><?= htmlspecialchars($game->getGenre()); ?></td>
                        <td><?= htmlspecialchars($game->getPlatform()); ?></td>
                        <td><?= htmlspecialchars($game->getReleaseYear()); ?></td>
                        <td><?= htmlspecialchars($game->getRating()); ?></td>
                        <td><a href="game_details.php?id=<?= $game->getId(); ?>" style="color:#2a5298;">View</a></td>
                        <td>

                            <a href="?edit=<?= $game->getId(); ?>" class="edit-btn">Edit</a>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $game->getId(); ?>">
                                <button type="submit" class="delete-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Games Yet To Be Added!</p>
        <?php endif; ?>
    </main>
</body>

</html>