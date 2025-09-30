<?php
require_once 'Game_class.php';
require_once 'GameManager_class.php';

# connecting to database
$pdo = new PDO('mysql:host=localhost;dbname=gamestore;charset=utf8', 'root', '');
$gameManager = new GameManager($pdo);

$errors = [];
$success = "";
$game = null;

# get game ID from url
$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit($id)) {
    die("invalid ID.");
}

# get game info
$game = $gameManager->getGameById((int)$id);
if (!$game) {
    die("Game not found!");
}

# processing the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update'])) {
        # update
        $title       = trim($_POST['title'] ?? '');
        $developer   = trim($_POST['developer'] ?? '');
        $releaseDate = trim($_POST['release_date'] ?? '');
        $description = trim($_POST['description'] ?? '');

        # image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $uploadDir . $fileName;

            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    $errors[] = "Picture upload failed!";
                }
            } else {
                $errors[] = "only JPG, PNG and GIF are allowed";
            }
        } else {
            // If no image is uploaded, set imagePath to null or empty string
            $imagePath = null;  // Or '' if you prefer an empty string
        }

        if (empty($errors)) {
            $updatedGame = new Game($title, $developer, $description, $releaseDate, $imagePath, (int)$rating, (int)$id);
            if ($gameManager->updateGame($updatedGame)) {
                $success = "Game successfully edited!";
                $game = $updatedGame; #show the new data
            } else {
                $errors[] = "Error during editing of the game";
            }
        }
    }

    if (isset($_POST['delete'])) {
        if ($gameManager->deleteGame((int)$id)) {
            header("Location: index.php?msg=Game removed");
            exit;
        } else {
            $errors[] = "Deleting failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($game->getTitle()) ?> - Details</title>
</head>

<body>
    <h1><?= htmlspecialchars($game->getTitle()) ?></h1>

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

    <div>
        <!-- Display image only if it exists -->
        <?php if ($imagePath): ?>
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($game->getTitle()) ?>" width="250"><br>
        <?php else: ?>
            <p>No image available.</p>
        <?php endif; ?>
        <strong>Developer:</strong> <?= htmlspecialchars($game->getDeveloper()) ?><br>
        <strong>Release:</strong> <?= htmlspecialchars($game->getReleaseYear()) ?><br>
        <p><?= nl2br(htmlspecialchars($game->getDescription())) ?></p>
    </div>

    <h2>Update Game</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($game->getTitle()) ?>" required><br><br>

        <label for="developer">Developer:</label><br>
        <input type="text" name="developer" value="<?= htmlspecialchars($game->getDeveloper()) ?>" required><br><br>

        <label for="release_date">Release:</label><br>
        <input type="date" name="release_date" value="<?= htmlspecialchars($game->getReleaseYear()) ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($game->getDescription()) ?></textarea><br><br>

        <label for="image">Image (optional):</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit" name="update">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this game?');">Delete</button>
    </form>
</body>

</html>