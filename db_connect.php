<?php
// includes/db_connect.php

$servername = "localhost"; // Généralement 'localhost'
$username = "root"; // Votre nom d'utilisateur MySQL
$password = ""; // Votre mot de passe MySQL
$dbname = "gestion_evenement"; // Le nom de votre base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Optionnel: Définir l'encodage des caractères à UTF-8
$conn->set_charset("utf8mb4");

?>