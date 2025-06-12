<?php
// Inclure le fichier de connexion à la base de données
require_once '../bd_connection.php';

// Initialiser les variables pour le tableau de bord
$nombre_clients = 0; // Si clients sont dans une table séparée, sinon compte 'acteurs'
$nombre_acteurs = 0;
$nombre_evenements_semaine = 0;
$nombre_evenements_mois = 0;
$moyenne_notes_acteurs = 'N/A';
$top_acteurs_notes = []; // Pour les acteurs les mieux notés

// --- 1. Nombre de clients / acteurs ---
// Hypothèse: Les "clients" et "acteurs" sont dans la même table 'acteurs' pour cet exemple
// Si vous avez une table 'clients' distincte, changez la requête en conséquence.
$sql_nombre_acteurs = "SELECT COUNT(id) AS total_acteurs FROM acteur";
$result_acteurs = $conn->query($sql_nombre_acteurs);
if ($result_acteurs && $result_acteurs->num_rows > 0) {
    $row = $result_acteurs->fetch_assoc();
    $nombre_acteurs = $row['total_acteurs'];
    $nombre_clients = $row['total_acteurs']; // Si clients = acteurs pour l'instant
}

// --- 2. Nombre d'événements (semaine et mois) ---
// Assurez-vous que votre table 'evenements' existe et a une colonne 'date_evenement' de type DATE ou DATETIME

// Événements cette semaine (du début de la semaine au début de la semaine prochaine)
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));
$sql_evenements_semaine = "SELECT COUNT(id) AS total FROM evenement WHERE date_debut BETWEEN '$start_of_week' AND '$end_of_week'";
$result_evenements_semaine = $conn->query($sql_evenements_semaine);
if ($result_evenements_semaine && $result_evenements_semaine->num_rows > 0) {
    $row = $result_evenements_semaine->fetch_assoc();
    $nombre_evenements_semaine = $row['total'];
}

// Événements ce mois-ci (du premier jour du mois au dernier jour du mois)
$start_of_month = date('Y-m-01');
$end_of_month = date('Y-m-t'); // 't' renvoie le nombre de jours dans le mois donné
$sql_evenements_mois = "SELECT COUNT(id) AS total FROM evenement WHERE date_debut BETWEEN '$start_of_month' AND '$end_of_month'";
$result_evenements_mois = $conn->query($sql_evenements_mois);
if ($result_evenements_mois && $result_evenements_mois->num_rows > 0) {
    $row = $result_evenements_mois->fetch_assoc();
    $nombre_evenements_mois = $row['total'];
}


// --- 3. Moyenne générale des notes des acteurs ---
// Assurez-vous que les tables 'acteurs' et 'etoiles' existent et sont correctement liées
$sql_moyenne_notes = "SELECT AVG(e.nombre) AS moyenne_globale FROM etoile e";
$result_moyenne = $conn->query($sql_moyenne_notes);
if ($result_moyenne && $result_moyenne->num_rows > 0) {
    $row = $result_moyenne->fetch_assoc();
    // Arrondir à 2 décimales si une moyenne existe
    $moyenne_notes_acteurs = $row['moyenne_globale'] !== null ? round($row['moyenne_globale'], 2) : 'N/A';
}

// --- 4. Top 5 des acteurs par moyenne de note ---
$sql_top_acteurs = "
    SELECT
        a.nom,
        a.prenom,
        AVG(e.nombre) AS moyenne_note
    FROM
        acteur a
    JOIN
        etoile e ON a.id = e.id_acteur -- Assurez-vous que 'id_acteur' est la bonne FK
    GROUP BY
        a.id, a.nom, a.prenom
    ORDER BY
        moyenne_note DESC
    LIMIT 5";
$result_top_acteurs = $conn->query($sql_top_acteurs);
if ($result_top_acteurs) {
    while ($row = $result_top_acteurs->fetch_assoc()) {
        $top_acteurs_notes[] = $row;
    }
} else {
    // Gérer l'erreur si la requête échoue (par exemple, tables non trouvées)
    // echo "<div class='error-message'>Erreur SQL pour le top acteurs: " . $conn->error . "</div>";
}


$conn->close(); // Fermer la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Evento</title>
     <style>
        /* Styles spécifiques pour le tableau de bord */
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .dashboard-container h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 40px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background-color: #e8f5e9; /* Vert très clair */
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #2e7d32; /* Vert foncé */
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .stat-card p.number {
            font-size: 3rem;
            font-weight: bold;
            color: #4CAF50; /* Vert principal */
            margin: 0;
        }
        .stat-card p.label {
            font-size: 1rem;
            color: #555;
            margin-top: 10px;
        }

        .top-actors-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .top-actors-section h3 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 30px;
        }
        .top-actors-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .top-actors-list li {
            background-color: #f0f4c3; /* Jaune très clair */
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-align: center;
            flex: 0 1 calc(33% - 20px); /* 3 colonnes sur des écrans larges */
            max-width: 300px;
        }
        .top-actors-list li .name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .top-actors-list li .rating {
            color: #e6b800; /* Or */
            font-size: 1.2rem;
        }
        .error-message, .info-message {
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-message {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr; /* Une seule colonne sur petits écrans */
            }
            .top-actors-list li {
                flex: 0 1 calc(50% - 20px); /* 2 colonnes sur tablettes */
            }
        }
        @media (max-width: 480px) {
            .top-actors-list li {
                flex: 0 1 100%; /* Une seule colonne sur téléphones */
            }
        }
    </style>
</head>
<body>

    <main>
        <div class="dashboard-container">
            <h2>Tableau de bord</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Nombre de Clients</h3>
                    <p class="number"><?php echo $nombre_clients; ?></p>
                    <p class="label">Total</p>
                </div>
                <div class="stat-card">
                    <h3>Nombre d'Acteurs/Prestataires</h3>
                    <p class="number"><?php echo $nombre_acteurs; ?></p>
                    <p class="label">Total</p>
                </div>
                <div class="stat-card">
                    <h3>Événements cette Semaine</h3>
                    <p class="number"><?php echo $nombre_evenements_semaine; ?></p>
                    <p class="label">Du <?php echo date('d/m', strtotime($start_of_week)); ?> au <?php echo date('d/m', strtotime($end_of_week)); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Événements ce Mois-ci</h3>
                    <p class="number"><?php echo $nombre_evenements_mois; ?></p>
                    <p class="label">Mois de <?php echo date('F Y'); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Moyenne des Notes des Acteurs</h3>
                    <p class="number"><?php echo $moyenne_notes_acteurs; ?></p>
                    <p class="label">Sur 5 étoiles</p>
                </div>
            </div>

            <section class="top-actors-section">
                <h3>Top 5 des Acteurs les Mieux Notés</h3>
                <?php if (!empty($top_acteurs_notes)): ?>
                    <ul class="top-actors-list">
                        <?php foreach ($top_acteurs_notes as $actor): ?>
                            <li>
                                <div class="name"><?php echo htmlspecialchars($actor['nom'] . ' ' . $actor['prenom']); ?></div>
                                <div class="rating">Note Moyenne: <?php echo htmlspecialchars(round($actor['moyenne_note'], 2)); ?> / 5</div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="info-message">Aucune note disponible pour le moment ou pas assez de données.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>