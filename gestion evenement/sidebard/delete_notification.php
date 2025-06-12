<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    echo 'error';
    exit;
}

if (!isset($_POST['id'])) {
    echo 'error';
    exit;
}

$id_notification = intval($_POST['id']);

// Connexion à la BDD
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");
if ($mysqli->connect_errno) {
    echo 'error';
    exit;
}

// Suppression
$stmt = $mysqli->prepare("DELETE FROM notification WHERE id = ? AND id_acteur = ?");
$stmt->bind_param("ii", $id_notification, $_SESSION['id_user']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
$mysqli->close();
?>