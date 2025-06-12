<?php
$serveur = "localhost";
$bd = "gestion_evenement";
$user = "root";
$password = "";

// Création de la connexion
$conn = new mysqli($serveur, $user, $password, $bd);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}


// Fermer la connexion (facultatif)
// $conn->close();
?>