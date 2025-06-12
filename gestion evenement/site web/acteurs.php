<?php
// Connexion à la base de données
$host = 'localhost'; // ou votre hôte
$user = 'root';     // votre utilisateur
$password = '';     // votre mot de passe
$dbname = 'gestion_evenement';

$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Initialisation des variables
$errors = [];
$nom = isset($_POST['nom']) ? $_POST['nom'] : '';
$prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$numero = isset($_POST['numero']) ? $_POST['numero'] : '';
$residence = isset($_POST['residence']) ? $_POST['residence'] : '';
$sexe = isset($_POST['sexe']) ? $_POST['sexe'] : '';
$categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
if (isset($_POST['pwd'])) {
    $pwd = $_POST['pwd'];
    // Hasher le mot de passe
    $mot_de_passe = password_hash($pwd, PASSWORD_DEFAULT);
} else {
    // Gérer le cas où le mot de passe n'est pas fourni
    $mot_de_passe = ''; // ou une valeur par défaut ou une erreur
} // ou une autre méthode pour les mots de passe
// Fonction pour upload de fichier
function uploadFile($file, $targetDir) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $allowedExtensions)) {
            // Créer un nom unique
            $fileName = date('Ymd_His') . '_' . uniqid() . '.' . $extension;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetPath = $targetDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $fileName; // Retourne le nom du fichier
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    return false;
}

// Upload photo
$photoFileName = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $photoFileName = uploadFile($_FILES['photo'], '../images/');
    if (!$photoFileName) {
        $errors[] = "Erreur lors de l'upload de la photo.";
    }
} else {
    $errors[] = "Aucune photo téléchargée.";
}

// Upload CV
$cvFileName = '';
if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
    $cvFileName = uploadFile($_FILES['cv'], '../cv/');
    if (!$cvFileName) {
        $errors[] = "Erreur lors de l'upload du CV.";
    }
} else {
    $errors[] = "Aucun CV téléchargé.";
}
if (empty($errors)) {
    $sql = "INSERT INTO acteur (nom, prenom, sexe, numero, email, photo, domicile, categorie,cv, mot_de_passe, date_ajout) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'sssissssss',
            $nom,
            $prenom,
            $sexe,
            $numero,
            $email,
            $photoFileName,
            $residence,
            $categorie,
            $cvFileName,
            $mot_de_passe
        );
        if ($stmt) {
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Récupère l'ID de l'acteur inséré
        $last_id = $conn->insert_id;

        // Détails de l'acteur inséré
        $nom_acteur = $nom;
        $prenom_acteur = $prenom;
        $categorie_acteur = $categorie;

        // Message de notification
        $message = "Nouveaux collaborateur du nom de $nom_acteur $prenom_acteur de la catégorie $categorie_acteur !!! Allez dans la liste des acteurs pour valider son insertion.";

        // Insertion dans notification_admin
        $sqlNotif = "INSERT INTO notification_admin (message, date_ajout) VALUES (?, NOW())";
        $stmtNotif = $conn->prepare($sqlNotif);
        if ($stmtNotif) {
            $stmtNotif->bind_param('s', $message);
            $stmtNotif->execute();
            $stmtNotif->close();
        } else {
            error_log("Erreur de préparation pour notification: " . $conn->error);
        }

        echo '<script>alert("Acteur enregistré avec succès!")</script>';
    } else {
        echo '<script>alert("Erreur lors de l\'enregistrement.")</script>';
    }
}
        
        $stmt->close();
    } else {
        echo "Erreur de préparation : " . $conn->error;
    }
}

// Fermer la connexion
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Enregistrement des Acteurs</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: transparent;
            margin: 0;
            padding: 40px;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            color: white;
            cursor: pointer;
        }
        button.cancel {
            background-color: #dc3545; /* Couleur pour le bouton Annuler */
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Enregistrement des Acteurs</h1>
        <form id="actorForm"  method="POST" enctype="multipart/form-data">
            <input type="text" name="nom" class="input-field" placeholder="Nom" required>
            <input type="text" name="prenom" class="input-field" placeholder="Prénom" required>
            <input type="email" name="email" class="input-field" placeholder="Email" required>
            <input type="tel" name="numero" class="input-field" placeholder="Numéro de Téléphone" required>
            <input type="password" name="pwd" class="input-field" placeholder="Mots de passe" required>
            
            <select name="residence" id="residence" class="input-field" required>
                <option value="">Résidences</option>
                <option value="Mokolo">Mokolo</option>
                <option value="Nkoabang">Nkoabang</option>
                <option value="Nlongkak">Nlongkak</option>
                <option value="Abong-bang">Abong-bang</option>
                <option value="Mvog-Mbi">Mvog-Mbi</option>
                <option value="Mvog-Ada">Mvog-Ada</option>
                <option value="Bastos">Bastos</option>
                <option value="Centre commercial">Centre commercial</option>
            </select>

            <select name="sexe" id="sexe" class="input-field" required>
                <option value="">Sexe</option>
                <option value="F">F</option>
                <option value="M">M</option>
            </select>

            <select name="categorie" id="categorie" class="input-field" required>
                <option value="">Catégorie</option>
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

            <input type="file" name="photo" class="input-field" accept="image/*" required>
            <input type="file" name="cv" class="input-field" accept=".pdf,.doc,.docx" required>

            <div class="button-container">
                <button type="button" class="cancel" onclick="cancelForm()">Annuler</button>
                <button type="submit">Envoyer</button>
            </div>
        </form>
    </div>

    <!-- Inline JavaScript -->
    <script>
        function cancelForm() {
            document.getElementById('actorForm').reset(); // Réinitialiser le formulaire
        }
    </script>

</body>
</html>