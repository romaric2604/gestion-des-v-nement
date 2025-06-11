<?php
// Connexion à la BD
$conn = new mysqli('localhost', 'root', '', 'gestion_evenement');

if ($conn->connect_error) {
    die(json_encode(['message' => 'Erreur de connexion à la base de données.']));
}

// Récupérer la requête JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['action'])) {
    echo json_encode(['message' => 'Données manquantes.']);
    exit;
}

$id = (int)$data['id'];
$action = $data['action'];

// Vérifier l'action
if ($action !== 'valider' && $action !== 'refuser') {
    echo json_encode(['message' => 'Action invalide.']);
    exit;
}

// Mettre à jour le statut
$stmt = $conn->prepare("UPDATE acteur SET statut = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param('si', $action, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['message' => "Acteur $action avec succès."]);
    } else {
        echo json_encode(['message' => "Aucun changement effectué."]);
    }
    $stmt->close();
} else {
    echo json_encode(['message' => 'Erreur de préparation de la requête.']);
}

$conn->close();
?>