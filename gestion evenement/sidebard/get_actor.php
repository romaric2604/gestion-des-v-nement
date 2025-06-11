
<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gestion_evenement';

$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour récupérer tous les acteurs
$sql = "SELECT nom, prenom, email, numero, date_ajout, categorie, photo, cv FROM acteur";
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {
    die("Erreur dans la requête SQL : " . $conn->error);
}

// Créer un tableau pour stocker les acteurs
$actors = [];

while ($row = $result->fetch_assoc()) {
    $actors[] = [
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'email' => $row['email'],
        'numero' => $row['numero'],
        'dateAjout' => $row['date_ajout'], // Assurez-vous que le champ est bien nommé dans la BD
        'categorie' => $row['categorie'],
        'photoProfile' => $row['photo'], // Photo profil
        'photoLarge' => $row['photo'],   // Même photo pour simplicité, ou une autre si vous avez
        'cv' => $row['cv']
    ];
}

// Encoder en JSON
header('Content-Type: application/json');
echo json_encode($actors);

// Fermer la connexion
$conn->close();
?>