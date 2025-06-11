<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- Remix Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" />

<!-- CSS personnalisé -->
<style>
body {
    font-family: Arial, sans-serif;
    background-color: none;
    margin: 0;
    padding: 20px;
}
.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin: 10px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.notification img {
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin-right: 15px;
}
.message {
    flex-grow: 1;
}
.actions {
    display: flex;
    align-items: center;
}
.icon {
    cursor: pointer;
    margin-left: 10px;
    color: #007bff;
}
.full-message {
    display: none;
    margin-top: 10px;
    color: #333;
}
.timestamp {
    font-size: 0.9em;
    color: #777;
    margin-top: 5px;
}
</style>
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour voir vos notifications.");
}
$id_user = $_SESSION['id_user'];

// Connexion à la BDD
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");
if ($mysqli->connect_errno) {
    die("Échec de la connexion : " . $mysqli->connect_error);
}

// Récupérer toutes les notifications de l'utilisateur
$result_notifications = $mysqli->query(
    "SELECT * FROM notification WHERE id_acteur = $id_user ORDER BY id DESC"
);

// Stocker les notifications dans un tableau
$notifications = [];
while ($row = $result_notifications->fetch_assoc()) {
    // Vous pouvez ajouter d'autres champs si besoin, par ex. message, date, etc.
    // Par exemple, si vous avez une colonne 'message' dans notification :
    $notifications[] = $row;
}

// Fermer la connexion
$mysqli->close();
?>
<?php if (empty($notifications)): ?>
    <p>Aucune notification.</p>
<?php else: ?>
    <?php foreach ($notifications as $notif): ?>
        <div class="notification" data-id="<?php echo $notif['id']; ?>">
            <img src="logo.png" alt="Profile Picture" />
            <div class="message">
                <strong>Eventon</strong><br>
                <span id="msg<?php echo $notif['id']; ?>">Ceci est une notification...</span>
                <span class="full-message" id="fullMsg<?php echo $notif['id']; ?>">
                    <?php echo htmlspecialchars($notif['message']); ?>
                </span>
                <div class="timestamp">Envoyé le : <?php echo htmlspecialchars(isset($notif['date_envoye']) ? $notif['date_envoye'] : ''); ?></div>
            </div>
            <div class="actions">
                <span class="icon" onclick="toggleMessage('fullMsg<?php echo $notif['id']; ?>')">👁️</span>
                <span class="icon" onclick="deleteNotification(<?php echo $notif['id']; ?>, this)">🗑️</span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Script pour toggle et suppression -->
<script>
function toggleMessage(fullMsgId) {
    const fullMessage = document.getElementById(fullMsgId);
    fullMessage.style.display = fullMessage.style.display === 'block' ? 'none' : 'block';
}

function deleteNotification(id, element) {
    if (!confirm("Voulez-vous vraiment supprimer cette notification ?")) return;

    // Requête AJAX pour supprimer dans la BDD
    fetch('delete_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'success') {
            // Supprimer la notification du DOM
            const notification = element.closest('.notification');
            notification.remove();
        } else {
            alert('Erreur lors de la suppression.');
        }
    });
}
</script>

</body>
</html>