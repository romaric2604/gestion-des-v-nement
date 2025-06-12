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

// Si action = valider
if ($action === 'valider') {
    // Mettre à jour le statut dans la BD
    $stmtUpdate = $conn->prepare("UPDATE acteur SET statut = ? WHERE id = ?");
    if ($stmtUpdate) {
        $stmtUpdate->bind_param('si', $action, $id);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows > 0) {
            // Récupérer l'id de l'acteur
            $actor_id = $id;

            // Récupérer le nom et prénom pour le message (optionnel, si vous souhaitez personnaliser)
            $sqlFetch = "SELECT nom, prenom FROM acteur WHERE id = ?";
            $stmtFetch = $conn->prepare($sqlFetch);
            if ($stmtFetch) {
                $stmtFetch->bind_param('i', $actor_id);
                $stmtFetch->execute();
                $result = $stmtFetch->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nomPrenom = $row['nom'] . ' ' . $row['prenom'];
                } else {
                    $nomPrenom = 'Acteur'; // fallback
                }
                $stmtFetch->close();
            } else {
                $nomPrenom = 'Acteur';
            }

            // Créer le message
            $message = "Vous avez ete accepte en tant qu'acteur !!!";

            // Insérer dans notification
            $stmtNotif = $conn->prepare("INSERT INTO notification (id_acteur, message, date_envoye) VALUES (?, ?, NOW())");
            if ($stmtNotif) {
                $stmtNotif->bind_param('is', $actor_id, $message);
                $stmtNotif->execute();
                $stmtNotif->close();
            }

            echo json_encode(['message' => "Acteur accepté avec succès."]);
        } else {
            echo json_encode(['message' => "Aucun changement effectué."]);
        }
        $stmtUpdate->close();
    } else {
        echo json_encode(['message' => 'Erreur de préparation de la requête.']);
    }
}

// Si action = refuser
if ($action === 'refuser') {
    // Supprimer l'acteur
    $stmtDelete = $conn->prepare("DELETE FROM acteur WHERE id = ?");
    if ($stmtDelete) {
        $stmtDelete->bind_param('i', $id);
        $stmtDelete->execute();

        if ($stmtDelete->affected_rows > 0) {
            echo json_encode(['message' => "Acteur supprimé avec succès."]);
        } else {
            echo json_encode(['message' => "Aucun acteur trouvé avec cet ID."]);
        }
        $stmtDelete->close();
    } else {
        echo json_encode(['message' => 'Erreur de préparation de la requête.']);
    }
}

$conn->close();
?>