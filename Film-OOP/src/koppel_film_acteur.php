<?php
require_once 'classes/User.php';  // ALLEEN DIT!

$database = new Database();
$db = $database->getConnection();

$film = new Film($db);
$acteur = new Acteur($db);
$filmActeur = new FilmActeur($db);

$message = "";
$error = "";

// Koppeling toevoegen
if($_POST) {
    $filmActeur->film_id = $_POST['film_id'];
    $filmActeur->acteur_id = $_POST['acteur_id'];

    if($filmActeur->bestaatKoppeling()) {
        $error = "⚠️ Deze koppeling bestaat al!";
    } else {
        if($filmActeur->koppel()) {
            $message = "✅ Acteur succesvol gekoppeld aan film!";
        } else {
            $error = "❌ Er ging iets mis bij het koppelen.";
        }
    }
}

// Alle films en acteurs ophalen voor dropdowns
$films_stmt = $film->readAll();
$acteurs_stmt = $acteur->readAll();

// Alle koppelingen ophalen voor overzicht
$koppelingen_stmt = $filmActeur->getAlleKoppelingen();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Film & Acteur Koppelen - Film Database</title>

</head>
<body>
    <nav class="navbar">
        <h1>🎬 Film Database</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="film_toevoegen.php">Films</a>
            <a href="acteur_toevoegen.php">Acteurs</a>
            <a href="koppel_film_acteur.php" class="active">Koppelen</a>
        </div>
    </nav>

    <div class="container">
        <!-- FORM SECTION -->
        <div class="form-section">
            <h2>🔗 Koppelen</h2>

            <div class="info-box">
                <p><strong>ℹ️ Instructie:</strong><br>
                Selecteer een film en een acteur om ze aan elkaar te koppelen. Dit zorgt ervoor dat de acteur wordt gekoppeld aan de geselecteerde film.</p>
            </div>
            
            <?php if($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="film_id">🎬 Selecteer Film *</label>
                    <select id="film_id" name="film_id" required>
                        <option value="">-- Kies een film --</option>
                        <?php while($film_row = $films_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $film_row['id']; ?>">
                                <?php echo htmlspecialchars($film_row['titel']) . " (" . $film_row['jaar'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="acteur_id">⭐ Selecteer Acteur *</label>
                    <select id="acteur_id" name="acteur_id" required>
                        <option value="">-- Kies een acteur --</option>
                        <?php while($acteur_row = $acteurs_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $acteur_row['id']; ?>">
                                <?php echo htmlspecialchars($acteur_row['naam']) . " - " . htmlspecialchars($acteur_row['nationaliteit']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit">🔗 Koppel Acteur aan Film</button>
            </form>
        </div>

        <!-- LIST SECTION -->
        <div class="list-section">
            <h2>📋 Alle Koppelingen (<?php echo $koppelingen_stmt->rowCount(); ?>)</h2>

            <?php if($koppelingen_stmt->rowCount() > 0): ?>
                <table class="koppelingen-table">
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Acteur</th>
                            <th>Nationaliteit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $koppelingen_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <div class="film-info">🎬 <?php echo htmlspecialchars($row['titel']); ?></div>
                                    <small style="color: #999;">(<?php echo $row['jaar']; ?>)</small>
                                </td>
                                <td>
                                    <div class="acteur-info">⭐ <?php echo htmlspecialchars($row['naam']); ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-nationaliteit">
                                        <?php echo htmlspecialchars($row['nationaliteit']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="geen-data">
                    <p>😔 Nog geen koppelingen gemaakt</p>
                    <p>Koppel acteurs aan films met het formulier!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>