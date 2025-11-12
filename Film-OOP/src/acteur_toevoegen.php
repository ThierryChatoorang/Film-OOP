<?php
require_once 'classes/User.php'; 

$database = new Database();
$db = $database->getConnection();

$acteur = new Acteur($db);

$message = "";
$error = "";

if($_POST) {
    $acteur->naam = $_POST['naam'];
    $acteur->geboortedatum = $_POST['geboortedatum'];
    $acteur->nationaliteit = $_POST['nationaliteit'];

    if($acteur->create()) {
        $message = "✅ Acteur succesvol geregistreerd!";
        $_POST = array();
    } else {
        $error = "❌ Er ging iets mis bij het registreren van de acteur.";
    }
}

// Alle acteurs ophalen
$acteurs_stmt = $acteur->readAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Acteur Registreren - Film Database</title>
  
</head>
<body>
    <nav class="navbar">
        <h1>🎬 Film Database</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="film_toevoegen.php">Films</a>
            <a href="acteur_toevoegen.php" class="active">Acteurs</a>
            <a href="koppel_film_acteur.php">Koppelen</a>
        </div>
    </nav>

    <div class="container">
        <!-- FORM SECTION -->
        <div class="form-section">
            <h2>⭐ Acteur Registreren</h2>
            
            <?php if($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="naam">👤 Volledige Naam *</label>
                    <input type="text" id="naam" name="naam" placeholder="Bijv. Leonardo DiCaprio" required>
                </div>

                <div class="form-group">
                    <label for="geboortedatum">🎂 Geboortedatum *</label>
                    <input type="date" id="geboortedatum" name="geboortedatum" required>
                </div>

                <div class="form-group">
                    <label for="nationaliteit">🌍 Nationaliteit *</label>
                    <input type="text" id="nationaliteit" name="nationaliteit" placeholder="Bijv. Amerikaans, Nederlands" required>
                </div>

                <button type="submit">➕ Acteur Registreren</button>
            </form>
        </div>

        <!-- LIST SECTION -->
        <div class="list-section">
            <h2>🌟 Alle Acteurs (<?php echo $acteurs_stmt->rowCount(); ?>)</h2>

            <?php if($acteurs_stmt->rowCount() > 0): ?>
                <table class="acteurs-table">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Geboortedatum</th>
                            <th>Nationaliteit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $acteurs_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['naam']); ?></strong></td>
                                <td><?php echo date('d-m-Y', strtotime($row['geboortedatum'])); ?></td>
                                <td><span class="nationaliteit-badge"><?php echo htmlspecialchars($row['nationaliteit']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="geen-data">
                    <p>😔 Nog geen acteurs geregistreerd</p>
                    <p>Registreer je eerste acteur met het formulier!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>