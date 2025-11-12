<?php
require_once 'classes/User.php'; 

$database = new Database();
$db = $database->getConnection();

$film = new Film($db);

$message = "";
$error = "";

// Genres lijst
$genres = ['Action', 'Adventure', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Romance', 'Sci-Fi', 'Thriller'];

if($_POST) {
    $film->titel = $_POST['titel'];
    $film->genre = $_POST['genre'];
    $film->jaar = $_POST['jaar'];
    $film->beschrijving = $_POST['beschrijving'];

    if($film->create()) {
        $message = "✅ Film succesvol toegevoegd!";
        $_POST = array();
    } else {
        $error = "❌ Er ging iets mis bij het toevoegen van de film.";
    }
}

// Alle films ophalen voor overzicht
$films_stmt = $film->readAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Film Toevoegen - Film Database</title>
 
</head>
<body>
    <nav class="navbar">
        <h1>🎬 Film Database</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="film_toevoegen.php" class="active">Films</a>
            <a href="acteur_toevoegen.php">Acteurs</a>
            <a href="koppel_film_acteur.php">Koppelen</a>
        </div>
    </nav>

    <div class="container">
        <!-- FORM SECTION -->
        <div class="form-section">
            <h2>🎥 Film Toevoegen</h2>
            
            <?php if($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="titel">📽️ Film Titel *</label>
                    <input type="text" id="titel" name="titel" placeholder="Bijv. Inception" required>
                </div>

                <div class="form-group">
                    <label for="genre">🎭 Genre *</label>
                    <select id="genre" name="genre" required>
                        <option value="">-- Selecteer een genre --</option>
                        <?php foreach($genres as $genre_option): ?>
                            <option value="<?php echo $genre_option; ?>"><?php echo $genre_option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jaar">📅 Jaar *</label>
                    <input type="number" id="jaar" name="jaar" min="1900" max="2030" placeholder="Bijv. 2010" required>
                </div>

                <div class="form-group">
                    <label for="beschrijving">📝 Beschrijving</label>
                    <textarea id="beschrijving" name="beschrijving" placeholder="Korte beschrijving van de film..."></textarea>
                </div>

                <button type="submit">➕ Film Toevoegen</button>
            </form>
        </div>

        <!-- LIST SECTION -->
        <div class="list-section">
            <h2>📚 Alle Films (<?php echo $films_stmt->rowCount(); ?>)</h2>

            <?php if($films_stmt->rowCount() > 0): ?>
                <table class="films-table">
                    <thead>
                        <tr>
                            <th>Titel</th>
                            <th>Genre</th>
                            <th>Jaar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $films_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['titel']); ?></strong></td>
                                <td><span class="genre-badge"><?php echo htmlspecialchars($row['genre']); ?></span></td>
                                <td><?php echo $row['jaar']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="geen-data">
                    <p>😔 Nog geen films toegevoegd</p>
                    <p>Voeg je eerste film toe met het formulier!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>