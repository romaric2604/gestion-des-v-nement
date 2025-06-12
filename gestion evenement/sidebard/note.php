<?php
session_start();

// Définissez votre $id_user ici, par exemple:
$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Remplacez par votre logique d'authentification

$id_evenement = 1; // ou $_SESSION['id_evenement'] si stocké en session

$conn = new mysqli('localhost', 'root', '', 'gestion_evenement');

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête SQL avec paramètres
$sql = "
    SELECT acteur.id, acteur.nom, acteur.prenom, acteur.photo, acteur.categorie
    FROM acteur
    JOIN contrat ON acteur.id = contrat.id_acteur
    JOIN client ON client.id = contrat.id_client
    WHERE contrat.id_evenement = ? AND contrat.id_client = ?
";

// Préparer la requête
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erreur dans la préparation : " . $conn->error);
}

// Lier les paramètres
$stmt->bind_param('ii', $id_evenement, $id_user);

// Exécuter la requête
$stmt->execute();

// Récupérer le résultat
$result = $stmt->get_result();

$acteurs = [];
while ($row = $result->fetch_assoc()) {
    $acteurs[] = [
        'id' => $row['id'],
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'photo' => '../images/' . $row['photo'], // Vérifiez ce chemin
        'categorie' => $row['categorie']
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Notation des Utilisateurs</title>

<!-- Style CSS -->
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 40px;
}
.container {
    max-width: 800px;
    margin: auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 20px;
}
h1 {
    text-align: center;
    color: #333;
}
.user-list {
    margin-bottom: 20px;
}
.user-item {
    display: flex;
    align-items: center;
    margin: 10px 0;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
.user-item:last-child {
    border-bottom: none;
}
.user-item img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
}
.rating {
    display: flex;
    margin-top: 10px;
}
.star {
    font-size: 25px;
    color: #ccc;
    cursor: pointer;
    margin-right: 5px;
}
.star.selected {
    color: #ffcc00;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}
.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 8px;
}
textarea {
    width: 97%;
    height: 100px;
    padding: 10px;
    border: 1px solid #007bff;
    border-radius: 5px;
    margin-top: 10px;
    resize: none;
}
.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
    font-size: 1em;
}
button.cancel {
    background-color: #dc3545;
}
button:hover {
    opacity: 0.9;
}
</style>
</head>
<body>

<div class="container">
<h1>Notation des Utilisateurs</h1>
<div class="user-list" id="userList">
<?php foreach ($acteurs as $acteur): ?>
    <div class="user-item">
        <img src="<?php echo htmlspecialchars($acteur['photo']); ?>" alt="Photo de Profil" />
        <div>
            <strong><?php echo htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']); ?></strong> - <em><?php echo htmlspecialchars($acteur['categorie']); ?></em>
        </div>
        <input type="checkbox" class="user-checkbox" value="<?php echo htmlspecialchars($acteur['id']); ?>" style="margin-left:auto;">
    </div>
<?php endforeach; ?>
</div>
<button onclick="openModal()">Noter un Utilisateur</button>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
<div class="modal-content">
<h2>Notation</h2>
<!-- Stocker plusieurs IDs séparés par virgules -->
<input type="hidden" id="selectedUsers">
<textarea id="description" placeholder="Description (facultatif)"></textarea>
<div class="rating" id="starRating">
    <span class="star" data-value="1">★</span>
    <span class="star" data-value="2">★</span>
    <span class="star" data-value="3">★</span>
    <span class="star" data-value="4">★</span>
    <span class="star" data-value="5">★</span>
</div>
<div class="button-container">
    <button class="cancel" onclick="closeModal()">Annuler</button>
    <button onclick="submitRating()">Envoyer</button>
</div>
</div>
</div>

<!-- JavaScript -->
<script>
let selectedStars = 0;

// Ouvrir la modal et récupérer les utilisateurs sélectionnés
function openModal() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const selectedUsersInput = document.getElementById('selectedUsers');
    selectedUsersInput.value = '';

    const selectedIds = [];
    checkboxes.forEach(cb => {
        selectedIds.push(cb.value);
    });

    if (selectedIds.length === 0) {
        alert("Veuillez sélectionner au moins un utilisateur.");
        return;
    }

    // Stocker les IDs séparés par virgules
    selectedUsersInput.value = selectedIds.join(',');

    // Réinitialiser l'étoile
    selectedStars = 0;
    updateStars();

    document.getElementById('description').value = '';

    document.getElementById('myModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('myModal').style.display = 'none';
    selectedStars = 0;
    updateStars();
    document.getElementById('description').value = '';
}

// Gérer la sélection des étoiles
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', () => {
        selectedStars = parseInt(star.getAttribute('data-value'));
        updateStars();
    });
});

function updateStars() {
    document.querySelectorAll('.star').forEach(star => {
        if (parseInt(star.getAttribute('data-value')) <= selectedStars) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
}

// Envoyer la note
function submitRating() {
    if (selectedStars === 0) {
        alert("Veuillez sélectionner une note.");
        return;
    }

    const description = document.getElementById('description').value;
    const selectedUsersStr = document.getElementById('selectedUsers').value.trim();

    if (!selectedUsersStr) {
        alert("Aucun utilisateur sélectionné.");
        return;
    }

    // Séparer les IDs par virgules
    const actorIds = selectedUsersStr.split(',').map(id => id.trim()).filter(id => id !== '');

    // Envoyer une requête pour chaque acteur
    Promise.all(
        actorIds.map(id_acteur =>
            fetch('enregistrer_note.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'id_acteur': id_acteur,
                    'nombre': selectedStars,
                    'description': description
                }).toString()
            }).then(res => res.json())
        )
    ).then(results => {
        // Vérification des résultats
        results.forEach(res => {
            if (res.status !== 'success') {
                console.error('Erreur:', res.message);
            }
        });
        alert('Avis soumis pour tous les acteurs.');
        closeModal();
    }).catch(error => {
        alert('Erreur lors de l\'envoi : ' + error);
    });
}
// Fermer la modal si clic en dehors
window.onclick = function(event) {
    const modal = document.getElementById('myModal');
    if (event.target === modal) {
        closeModal();
    }
};
</script>

</body>
</html>