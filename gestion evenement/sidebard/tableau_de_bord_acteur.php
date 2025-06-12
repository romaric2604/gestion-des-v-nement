<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour voir votre tableau de bord.");
}

$id_user = $_SESSION['id_user'];

// Connexion à la base de données (version compatible PHP < 7)
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");
if ($mysqli->connect_errno) {
    die("Échec de la connexion : " . $mysqli->connect_error);
}

// Requêtes pour le tableau de bord
$result_events = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_acteur = $id_user"
);
$row_events = $result_events->fetch_assoc();
$nombre_evenements = isset($row_events['total']) ? $row_events['total'] : 0;

$result_notifications = $mysqli->query(
    "SELECT COUNT(*) AS total FROM notification WHERE id_acteur = $id_user"
);
$row_notifications = $result_notifications->fetch_assoc();
$notifications = isset($row_notifications['total']) ? $row_notifications['total'] : 0;

$result_passe = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_acteur = $id_user AND e.date_fin < NOW()"
);
$row_passe = $result_passe->fetch_assoc();
$evenements_passes = isset($row_passe['total']) ? $row_passe['total'] : 0;

$result_avenir = $mysqli->query(
    "SELECT COUNT(*) AS total FROM contrat c
     JOIN evenement e ON c.id_evenement = e.id
     WHERE c.id_acteur = $id_user AND e.date_debut > NOW()"
);
$row_avenir = $result_avenir->fetch_assoc();
$evenements_avenir = isset($row_avenir['total']) ? $row_avenir['total'] : 0;

$result_notes = $mysqli->query(
    "SELECT AVG(nombre) AS moyenne FROM etoile 
     WHERE id_evenement IN (
         SELECT id_evenement FROM contrat WHERE id_acteur = $id_user
     )"
);
$row_notes = $result_notes->fetch_assoc();

$result_apercus = $mysqli->query(
    "SELECT COUNT(*) AS total FROM apercus WHERE id_acteur = $id_user"
);
$row_apercus = $result_apercus->fetch_assoc();
$nombre_apercus = isset($row_apercus['total']) ? $row_apercus['total'] : 0;

$result_notes = $mysqli->query(
    "SELECT AVG(nombre) AS moyenne FROM etoile 
     WHERE id_evenement IN (
         SELECT id_evenement FROM contrat WHERE id_acteur = $id_user
     )"
);
$row_notes = $result_notes->fetch_assoc();
$moyenne = isset($row_notes['moyenne']) ? $row_notes['moyenne'] : 0;
// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- REMIXICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" />

<!-- CSS personnalisé -->
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}
.main-container {
    padding: 2rem;
}
.align {
    display: flex;
    justify-content: center;
}
.stats-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    margin-top: 2rem;
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
    box-shadow: 0 2px 10px 2px rgba(0,0,0,0.1);
    padding: 20px;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.bord-box:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
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
<title>Tableau de bord</title>
</head>
<body>

<!-- Conteneur principal -->
<main class="main-container" id="main">
    <div class="align">
        <div class="stats-container">
            <!-- Nombre d'événements -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Nombre d'événements</h2> 
                <p><?php echo $nombre_evenements; ?></p>
            </div>
            <!-- Notifications -->
            <div class="bord-box">
                <i class="ri-notification-line" style="font-size: 40px;"></i>
                <h2>Notifications</h2>
                <p><?php echo $notifications; ?></p>
            </div>
            <!-- Événements passés -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Événements passés</h2> 
                <p><?php echo $evenements_passes; ?></p>
            </div>
            <!-- Événements à venir -->
            <div class="bord-box">
                <i class="ri-calendar-line" style="font-size: 40px;"></i>
                <h2>Événements à venir</h2>
                <p><?php echo $evenements_avenir; ?></p>
            </div>
            <!-- Nombre d'apercus -->
            <div class="bord-box">
                <i class="ri-image-line" style="font-size: 40px;"></i>
                <h2>Aperçus</h2>
                <p><?php echo $nombre_apercus; ?></p>
            </div>
            <div class="bord-box">
    <i class="ri-star-line" style="font-size: 40px;"></i>
    <h2>Moyenne</h2>
    <p><?php echo round($moyenne, 2); // arrondi à 2 décimales ?></p>
</div>
        </div>
    </div>
</main>

<!-- Script JS si besoin (par exemple, pour interactions futures) -->
<script src="assets/js/main.js"></script>

</body>
</html>