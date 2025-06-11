
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
$sql = "SELECT id,nom, prenom,statut, email, numero, date_ajout, categorie, photo, cv FROM acteur";
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
        'id' => $row['id'],
        'prenom' => $row['prenom'],
        'statut' => $row['statut'],
        'email' => $row['email'],
        'numero' => $row['numero'],
        'dateAjout' => $row['date_ajout'], // Assurez-vous que le champ est bien nommé dans la BD
        'categorie' => $row['categorie'],
        'photoProfile' => $row['photo'], // Photo profil
        'photoLarge' => $row['photo'],   // Même photo pour simplicité, ou une autre si vous avez
        'cv' => $row['cv']
    ];
}
// Fermer la connexion
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des Acteurs par Catégorie</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: none;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
                <option value="traiteur">Traiteur</option>
                <option value="immobilier">Immobilier</option>
                <option value="dj">DJ</option>
                <option value="decorateur">Décorateur</option>
                <option value="securite">Sécurité</option>
                <option value="entretien">Entretien</option>
                <option value="chauffeur">Chauffeur</option>
                <option value="hotesse">Hôtesse</option>
                <option value="photographe">Photographe</option>
            </select>
            <button onclick="displayActors()">Afficher</button>
        </div>

        <div id="actorList"></div>
    </div>

    <!-- Inline JavaScript -->
    <script>
        let actors = <?php echo json_encode($actors);?>;
        let buttonsHTML = '';


        function displayActors() {
            const category = document.getElementById('categorySelect').value;
            const actorList = document.getElementById('actorList');
            actorList.innerHTML = ''; // Réinitialiser la liste

            const filteredActors = actors.filter(actor => actor.categorie === category);

            filteredActors.forEach(actor => {
                const card = document.createElement('div');
                card.className = 'actor-card';

                // Vérification de la date d'ajout
                const dateAjout = new Date(actor.dateAjout);
                const today = new Date();
                const diffDays = Math.ceil((today - dateAjout) / (1000 * 60 * 60 * 24));

                if (diffDays < 5) {
                    card.innerHTML += `<div class="new-label">Nouveau</div>`;
                }
                if (actor.statut === 'valider') {
        // Si validé, afficher seulement "Refuser"
        buttonsHTML = `<button onclick="updateStatus('${actor.id}', 'refuser')">Refuser</button>`;
    } else if (actor.statut === 'refuser') {
        // Si refusé, afficher seulement "Valider"
        buttonsHTML = `<button onclick="updateStatus('${actor.id}', 'valider')">Valider</button>`;
    } else {
        // Si vide, afficher les deux boutons
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
        // Peut-être recharger la liste après validation/refus
        displayActors();
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour.');
    });
}
    </script>
</body>
</html>