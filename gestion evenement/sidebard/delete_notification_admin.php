<?php
if (!isset($_POST['id'])) {
    echo 'error';
    exit;
}

$id_notification = intval($_POST['id']);

// Connexion à la BDD
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");

// Vérification de la connexion
if ($mysqli->connect_errno) {
    echo 'error';
    exit;
}

// Préparer la requête de suppression
$stmt = $mysqli->prepare("DELETE FROM notification_admin WHERE id = ?");
if (!$stmt) {
    // Échec de la préparation
    echo 'error';
    $mysqli->close();
    exit;
}

// Lier le paramètre et exécuter
$stmt->bind_param("i", $id_notification);
$stmt->execute();

// Vérifier si la suppression a réussi
if ($stmt->affected_rows > 0) {
    echo 'success';
} else {
    echo 'error';
}

// Fermer la déclaration et la connexion
$stmt->close();
$mysqli->close();
?>