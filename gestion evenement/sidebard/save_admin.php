<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
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
if (isset($_POST['pwd'])) {
$pwd = $_POST['pwd'];
// Hasher le mot de passe
$mot_de_passe = password_hash($pwd, PASSWORD_DEFAULT);
} else {
// Gérer le cas où le mot de passe n'est pas fourni
$mot_de_passe = ''; // ou une valeur par défaut ou une erreur
} // ou une autre méthode pour les mots de passe

// Fonction pour uploader un fichier
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

            $targetPath = rtrim($targetDir, '/') . '/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $fileName;
            }
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

// Si pas d'erreurs, insérer en BDD
if (empty($errors)) {
    $sql = "INSERT INTO admin (nom, prenom, numero, email, photo, cv, mot_de_passe, date_ajout) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'ssissss',
            $nom,
            $prenom,
            $numero,
            $email,
            $photoFileName,
            $cvFileName,
            $mot_de_passe
        );

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo '<script>alert("Amin enregistré avec succès!")</script>';
        } else {
            echo '<script>alert("Erreur lors de l\'enregistrement.")</script>';
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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Formulaire d'Enregistrement des Administrateur</title>
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
    background-color: #dc3545;
}
button:hover {
    opacity: 0.9;
}
</style>
</head>
<body>
<div class="container">
<h1>Ajouter des Admins</h1>
<form id="actorForm" method="POST" enctype="multipart/form-data">
    <input type="text" name="nom" class="input-field" placeholder="Nom" required>
    <input type="text" name="prenom" class="input-field" placeholder="Prénom" required>
    <input type="email" name="email" class="input-field" placeholder="Email" required>
    <input type="tel" name="numero" class="input-field" placeholder="Numéro de Téléphone" required>
    <input type="password" name="pwd" class="input-field" placeholder="Mots de passe" required>
    <input type="file" name="photo" class="input-field" accept="image/*" required>
    <input type="file" name="cv" class="input-field" accept=".pdf,.doc,.docx" required>
    <div class="button-container">
        <button type="button" class="cancel" onclick="cancelForm()">Annuler</button>
        <button type="submit">Envoyer</button>
    </div>
</form>
</div>
<script>
function cancelForm() {
    document.getElementById('actorForm').reset();
}
</script>
</body>
</html>