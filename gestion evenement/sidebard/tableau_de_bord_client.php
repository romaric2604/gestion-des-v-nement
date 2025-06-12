<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- REMIXICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" />

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/styles.css" />

    <title>Tableau de bord</title>
    <style>
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-top: 8rem;
            margin-left: 300px;
        }

        .bord-box {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #f2f2f2;
            width: 280px;
            height: 200px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px 2px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .bord-box:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            transform: scale(1.01);
        }

        .bord-box h2 {
            margin-top: 10px;
            font-size: 18px;
        }

        .bord-box p {
            margin-top: 8px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    // Rediriger ou afficher un message
    die("Vous devez être connecté pour voir votre tableau de bord.");
}

$id_user = $_SESSION['id_user'];

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");
if ($mysqli->connect_errno) {
    die("Échec de la connexion : " . $mysqli->connect_error);
}

// Requête pour compter le nombre total d'événements
$result_events = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_client = $id_user"
);
$row_events = $result_events->fetch_assoc();
$nombre_evenements = isset($row_events['total']) ? $row_events['total'] : 0;

// Requête pour compter les notifications (exemple, à adapter selon votre structure)
$result_notifications = $mysqli->query(
    "SELECT COUNT(*) AS total FROM notification WHERE id_acteur = $id_user"
);
$row_notifications = $result_notifications->fetch_assoc();
$notifications = isset($row_notifications['total']) ? $row_notifications['total'] : 0;

// Requête pour compter les événements passés
$result_passe = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_client = $id_user AND e.date_fin < NOW()"
);
$row_passe = $result_passe->fetch_assoc();
$evenements_passes = isset($row_passe['total']) ? $row_passe['total'] : 0;

// Requête pour compter les événements à venir
$result_avenir = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_client = $id_user AND e.date_debut > NOW()"
);
$row_avenir = $result_avenir->fetch_assoc();
$evenements_avenir = isset($row_avenir['total']) ? $row_avenir['total'] : 0;

// Requête pour calculer la moyenne de notes (exemple, à adapter)
$result_notes = $mysqli->query(
    "SELECT AVG(nombre) AS moyenne FROM etoile 
     WHERE id_evenement IN (
         SELECT id_evenement FROM contrat WHERE id_client = $id_user
     )"
);
$row_notes = $result_notes->fetch_assoc();


// Fermer la connexion
$mysqli->close();
?>
<!--=============== MAIN ===============-->
<main class="main-container" id="main">
    <div class="align">
        <div class="stats-container">
            <!-- Carte : Nombre d'événements -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Nombre d'événements</h2> 
                <p><?php echo $nombre_evenements; ?></p>
            </div>

            <!-- Carte : Notifications -->
            <div class="bord-box">
                <i class="ri-notification-line" style="font-size: 40px;"></i>
                <h2>Notifications</h2>
                <p><?php echo $notifications; ?></p>
            </div>

            <!-- Carte : Événements passés -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Événements passés</h2> 
                <p><?php echo $evenements_passes; ?></p>
            </div>

            <!-- Carte : Événements à venir -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Événements à venir</h2>
                <p><?php echo $evenements_avenir; ?></p>
            </div>
        </div>
    </div>
</main>
<script src="assets/js/main.js"></script>
</body>
</html>