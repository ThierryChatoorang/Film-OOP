<?php
require_once 'classes/User.php'; 

$database = new Database();
$db = $database->getConnection();


$film = new Film($db);
$acteur = new Acteur($db);
$filmActeur = new FilmActeur($db);

// Statistieken ophalen
$total_films = $film->count();
$total_acteurs = $acteur->count();
$total_koppelingen = $filmActeur->count();

// Recente films
$films_stmt = $film->readAll();
$films_array = $films_stmt->fetchAll(PDO::FETCH_ASSOC);
$recent_films = array_slice($films_array, 0, 5);

// Recente acteurs
$acteurs_stmt = $acteur->readAll();
$acteurs_array = $acteurs_stmt->fetchAll(PDO::FETCH_ASSOC);
$recent_acteurs = array_slice($acteurs_array, 0, 5);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Film Database</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Navigation */
        .navbar {
            background-color: #1a2a6c;
            color: white;
            padding: 1.5rem 0;
        }
        
        .navbar h1 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-links a:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Welcome Section */
        .welcome {
            text-align: center;
            margin: 2rem 0;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .welcome h2 {
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
            color: #1a2a6c;
        }
        
        .welcome p {
            color: #666;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1a2a6c;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        /* Action Cards */
        .action-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .action-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .action-card h3 {
            color: #1a2a6c;
            margin-bottom: 0.5rem;
        }
        
        .action-card p {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Recent Section */
        .recent-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .recent-box {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .recent-box h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        /* Table for Recent Films */
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .recent-table th {
            text-align: left;
            padding: 0.75rem;
            font-weight: 600;
            color: #1a2a6c;
            border-bottom: 1px solid #eee;
        }
        
        .recent-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        .recent-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Recent Actors List */
        .recent-list {
            list-style: none;
        }
        
        .recent-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        /* No Data Message */
        .geen-data {
            text-align: center;
            padding: 1.5rem;
            color: #888;
            font-style: italic;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
            
            .recent-section {
                grid-template-columns: 1fr;
            }
            
            .stats-grid, .action-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🎬 Film Database</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="film_toevoegen.php">Films</a>
            <a href="acteur_toevoegen.php">Acteurs</a>
            <a href="koppel_film_acteur.php">Koppelen</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome">
            <h2>👋 Welkom bij de Film Database</h2>
            <p>Beheer je complete filmcollectie, registreer acteurs en koppel ze aan films</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎥</div>
                <div class="stat-number"><?php echo $total_films; ?></div>
                <div class="stat-label">Films</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-number"><?php echo $total_acteurs; ?></div>
                <div class="stat-label">Acteurs</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🔗</div>
                <div class="stat-number"><?php echo $total_koppelingen; ?></div>
                <div class="stat-label">Koppelingen</div>
            </div>
        </div>

        <div class="action-cards">
            <a href="film_toevoegen.php" class="action-card">
                <div class="action-icon">🎬</div>
                <h3>Film Toevoegen</h3>
                <p>Voeg een nieuwe film toe met genre en beschrijving</p>
            </a>

            <a href="acteur_toevoegen.php" class="action-card">
                <div class="action-icon">🌟</div>
                <h3>Acteur Registreren</h3>
                <p>Registreer een nieuwe acteur in de database</p>
            </a>

            <a href="koppel_film_acteur.php" class="action-card">
                <div class="action-icon">🔗</div>
                <h3>Koppelen</h3>
                <p>Koppel acteurs aan films</p>
            </a>
        </div>

        <div class="recent-section">
            <div class="recent-box">
                <h3>📽️ Recente Films</h3>
                <?php if(count($recent_films) > 0): ?>
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Titel</th>
                                <th>Jaar</th>
                                <th>Genre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_films as $film_item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($film_item['titel']); ?></strong></td>
                                    <td><?php echo $film_item['jaar']; ?></td>
                                    <td><?php echo htmlspecialchars($film_item['genre']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="geen-data">Nog geen films toegevoegd</div>
                <?php endif; ?>
            </div>

            <div class="recent-box">
                <h3>⭐ Recente Acteurs</h3>
                <?php if(count($recent_acteurs) > 0): ?>
                    <ul class="recent-list">
                        <?php foreach($recent_acteurs as $acteur_item): ?>
                            <li class="recent-item">
                                <strong><?php echo htmlspecialchars($acteur_item['naam']); ?></strong>
                                <span><?php echo htmlspecialchars($acteur_item['nationaliteit']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="geen-data">Nog geen acteurs geregistreerd</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>