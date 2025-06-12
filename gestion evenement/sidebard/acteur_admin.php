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
$sql = "SELECT id, nom, prenom, statut, email, numero, date_ajout, categorie, photo, cv FROM acteur";
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {
    die("Erreur dans la requête SQL : " . $conn->error);
}

// Stocker tous les acteurs
$actors = [];
while ($row = $result->fetch_assoc()) {
    $actors[] = [
        'nom' => $row['nom'],
        'id' => $row['id'],
        'prenom' => $row['prenom'],
        'statut' => $row['statut'],
        'email' => $row['email'],
        'numero' => $row['numero'],
        'dateAjout' => $row['date_ajout'],
        'categorie' => $row['categorie'],
        'photoProfile' => $row['photo'],
        'photoLarge' => $row['photo'], // si vous avez une autre photo, utilisez-la
        'cv' => $row['cv']
    ];
}
$conn->close();

// Extraire les catégories qui ont au moins un acteur
$categories = [];
foreach ($actors as $actor) {
    if (!in_array($actor['categorie'], $categories)) {
        $categories[] = $actor['categorie'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Affichage des Acteurs par Catégorie</title>

<!-- Styles -->
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 40px;
    background-color: #f0f0f0;
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
.category-selector {
    text-align: center;
    margin-bottom: 20px;
}
select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1em;
}
button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
}
button:hover {
    opacity: 0.9;
}
.actor-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin: 15px 0;
    padding: 15px;
    position: relative;
}
.actor-card img.profile {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-bottom: 10px;
}
.actor-card img.large {
    width: 100%;
    border-radius: 8px;
    margin: 10px 0;
}
.new-label {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #28a745;
    color: white;
    padding: 5px;
    border-radius: 5px;
    transform: rotate(-15deg);
    font-size: 0.8em;
}
.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}
</style>
</head>
<body>

<div class="container">
    <h1>Affichage des Acteurs par Catégorie</h1>
    <div class="category-selector">
        <select id="categorySelect">
            <option value="">Choisissez une catégorie</option>
            <?php
            // Charger dynamiquement les catégories disponibles
            foreach ($categories as $cat) {
                echo "<option value=\"".htmlspecialchars($cat)."\">".htmlspecialchars($cat)."</option>";
            }
            ?>
        </select>
    </div>
    <div id="actorList"></div>
</div>

<!-- Script -->
<script>
    const actors = <?php echo json_encode($actors); ?>;
    const categorySelect = document.getElementById('categorySelect');
    const actorList = document.getElementById('actorList');

    // Fonction pour afficher les acteurs d'une catégorie
    function displayActors(category) {
        actorList.innerHTML = ''; // Réinitialiser
        if (!category) return;

        const filteredActors = actors.filter(actor => actor.categorie === category);

        filteredActors.forEach(actor => {
            const card = document.createElement('div');
            card.className = 'actor-card';

            // Vérifier si c'est "Nouveau"
            const dateAjout = new Date(actor.dateAjout);
            const today = new Date();
            const diffDays = Math.ceil((today - dateAjout) / (1000 * 60 * 60 * 24));
            if (diffDays < 5) {
                card.innerHTML += `<div class="new-label">Nouveau</div>`;
            }

            // Buttons HTML selon statut
            let buttonsHTML = '';
            if (actor.statut === 'valider') {
                buttonsHTML = `<button onclick="updateStatus('${actor.id}', 'refuser')">Refuser</button>`;
            } else if (actor.statut === 'refuser') {
                buttonsHTML = `<button onclick="updateStatus('${actor.id}', 'valider')">Valider</button>`;
            } else {
                buttonsHTML = `
                    <button onclick="updateStatus('${actor.id}', 'valider')">Valider</button>
                    <button onclick="updateStatus('${actor.id}', 'refuser')">Refuser</button>
                `;
            }

            card.innerHTML += `
                <img src="../images/${actor.photoProfile}" class="profile" alt="Photo de Profil">
                <strong>${actor.nom} ${actor.prenom}</strong>
                <img src="../images/${actor.photoLarge}" class="large" alt="Photo Grande">
                <div>Numéro : ${actor.numero}</div>
                <div>Email : ${actor.email}</div>
                <div>Date d'Ajout : ${actor.dateAjout}</div>
                <div class="button-container">
                    <button onclick="window.open('../cv/${actor.cv}', '_blank')">Afficher le CV</button>
                    <div class="button-container">
                        ${buttonsHTML}
                    </div>
                </div>
            `;
            actorList.appendChild(card);
        });
    }

    // Événement changement de catégorie
    categorySelect.addEventListener('change', () => {
        const selectedCategory = categorySelect.value;
        displayActors(selectedCategory);
    });

    // Au chargement, sélectionner la première catégorie disponible
    window.onload = () => {
        if (categorySelect.options.length > 1) {
            // Si une catégorie est présente, la sélectionner
            categorySelect.selectedIndex = 1;
            displayActors(categorySelect.value);
        }
    };

    // Fonction pour mettre à jour le statut via fetch
    function updateStatus(id, action) {
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id, action: action })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            // Recharger la liste pour voir le changement
            const currentCategory = categorySelect.value;
            displayActors(currentCategory);
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour.');
        });
    }
</script>

</body>
</html>