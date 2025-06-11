<?php
// includes/db_connect.php doit être inclus pour la connexion à la BD
require_once 'includes/db_connect.php';

$message_status = ''; // Pour afficher les messages de succès ou d'erreur

// Gérer la suppression d'un utilisateur
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $id_utilisateur_a_supprimer = $conn->real_escape_string($_GET['id']);

    // Début de la transaction pour s'assurer que tout ou rien n'est supprimé
    $conn->begin_transaction();
    try {
        // Supprimer d'abord les entrées liées dans la table 'etoiles' (si elle existe et que la FK est CASCADE)
        // C'est nécessaire si votre clé étrangère n'a pas ON DELETE CASCADE,
        // ou pour une meilleure gestion des erreurs.
        // Si vous avez mis ON DELETE CASCADE, cette étape n'est pas strictement nécessaire mais peut être plus sûre.
        $stmt_etoile = $conn->prepare("DELETE FROM etoile WHERE id_acteur = ?");
        $stmt_etoile->bind_param("i", $id_utilisateur_a_supprimer);
        $stmt_etoile->execute();
        $stmt_etoile->close();

        // Ensuite, supprimer l'utilisateur de la table 'acteurs'
        $stmt_acteur = $conn->prepare("DELETE FROM acteur WHERE id = ?");
        $stmt_acteur->bind_param("i", $id_utilisateur_a_supprimer);

        if ($stmt_acteur->execute()) {
            if ($stmt_acteur->affected_rows > 0) {
                $conn->commit(); // Valider la transaction
                $message_status = '<div class="message-status success">Utilisateur supprimé avec succès !</div>';
            } else {
                $conn->rollback(); // Annuler la transaction
                $message_status = '<div class="message-status error">Aucun utilisateur trouvé avec cet ID.</div>';
            }
        } else {
            throw new Exception("Erreur lors de la suppression de l'utilisateur: " . $stmt_acteur->error);
        }
        $stmt_acteur->close();

    } catch (Exception $e) {
        $conn->rollback(); // Annuler la transaction en cas d'erreur
        $message_status = '<div class="message-status error">Erreur lors de la suppression : ' . $e->getMessage() . '</div>';
    }
}

// Récupérer tous les utilisateurs de la base de données
$utilisateurs = [];
// Assurez-vous que le nom de la table 'acteurs' est correct et existe
$sql = "SELECT id, nom, prenom, email, numero, domicile, photo FROM acteur ORDER BY nom ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $utilisateurs[] = $row;
        }
    } else {
        $message_status = '<div class="message-status info">Aucun utilisateur trouvé dans la base de données.</div>';
    }
} else {
    $message_status = '<div class="message-status error">Erreur lors de la récupération des utilisateurs : ' . $conn->error . '</div>';
}

$conn->close(); // Fermer la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Utilisateurs - Evento</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Styles spécifiques pour cette page */
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 30px;
        }
        .user-list {
            list-style: none;
            padding: 0;
        }
        .user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .user-item:hover {
            background-color: #f0f0f0;
        }
        .user-info {
            display: flex;
            align-items: center;
            flex-grow: 1; /* Prend l'espace disponible */
        }
        .user-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 2px solid #ddd;
        }
        .user-details {
            flex-grow: 1;
        }
        .user-details h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.3rem;
        }
        .user-details p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        .delete-button {
            background-color: #dc3545; /* Rouge pour supprimer */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none; /* Pour le lien */
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .message-status {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .message-status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message-status.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        /* Style pour les erreurs PHP des requêtes (si affichées) */
        .error-message {
            color: red;
            background-color: #ffe0e0;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Utilisateurs</h1>
        <nav>
            <a href="index.html">Accueil</a>
            <a href="acteurs_par_categories.php">Professionnels</a>
            </nav>
    </header>

    <main>
        <div class="container">
            <h2>Liste des Utilisateurs</h2>
            <?php echo $message_status; ?>

            <ul class="user-list">
                <?php if (!empty($utilisateurs)): ?>
                    <?php foreach ($utilisateurs as $user): ?>
                        <li class="user-item">
                            <div class="user-info">
                                <img src="<?php echo htmlspecialchars($user['photo'] ?: 'images/default.png'); ?>" alt="Photo">
                                <div class="user-details">
                                    <h3><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></h3>
                                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                                    <p>Téléphone: <?php echo htmlspecialchars($user['numero']); ?></p>
                                    <p>Domicile: <?php echo htmlspecialchars($user['domicile']); ?></p>
                                </div>
                            </div>
                            <a href="?action=supprimer&id=<?php echo htmlspecialchars($user['id']); ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');"
                               class="delete-button">Supprimer</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="message-status info">Aucun utilisateur à afficher pour le moment.</p>
                <?php endif; ?>
            </ul>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Votre Appli de Gestion Événements</p>
    </footer>
</body>
</html>