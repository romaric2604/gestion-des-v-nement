<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connexion à la base
$mysqli = new mysqli('localhost', 'root', '', 'gestion_evenement');

if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion : ' . $mysqli->connect_error]);
    exit;
}

// Vérification que la session contient bien 'id_evenement'
if (!isset($_SESSION['id_evenement'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID événement non défini dans la session.']);
    exit;
}
$id_evenement = $_SESSION['id_evenement'];

// Récupération des données POST
$id_acteur = isset($_POST['id_acteur']) ? intval($_POST['id_acteur']) : null;
$nombre = isset($_POST['nombre']) ? intval($_POST['nombre']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Vérification des données
if (!$id_acteur || !$nombre) {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes ou invalides.']);
    exit;
}

// Préparer la requête d'insertion
$stmt = $mysqli->prepare("INSERT INTO etoile (id_acteur, id_evenement, nombre, description) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation : ' . $mysqli->error]);
    exit;
}

// Lier les paramètres : i = int, s = string
$stmt->bind_param('iiis', $id_acteur, $id_evenement, $nombre, $description);

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement : ' . $stmt->error]);
}

// Fermer la déclaration et la connexion
$stmt->close();
$mysqli->close();
?>