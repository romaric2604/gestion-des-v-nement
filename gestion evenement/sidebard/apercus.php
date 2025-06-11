<?php
// Configuration de la connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_evenement';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = $_POST['imageName'];
    $imageDescription = $_POST['imageDescription'];

    // Vérifie si un fichier a été téléchargé
    if (isset($_FILES['imageInput']) && $_FILES['imageInput']['error'] == 0) {
        $fileTmpPath = $_FILES['imageInput']['tmp_name'];
        $fileName = $_FILES['imageInput']['name'];

        // Renommage du fichier avec la date et l'heure actuelle
        $newFileName = date('Ymd_His') . '_' . basename($fileName);
        $destPath = '../images/' . $newFileName; // Assurez que le dossier 'images' existe et est accessible en écriture

        // Déplace le fichier téléchargé
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Prépare la requête d'insertion
            $stmt = $pdo->prepare("INSERT INTO photo_evenement (nom, photo, description) VALUES (:nom, :photo, :description)");
            $stmt->execute([
                ':nom' => $imageName,
                ':photo' => $newFileName,
                ':description' => $imageDescription
            ]);
            echo "<script>alert('Image téléchargée et informations enregistrées avec succès.');</script>";
        } else {
            echo "<script>alert('Erreur lors du téléchargement de l'image.');</script>";
        }
    } else {
         echo "<script>alert('Aucun fichier téléchargé ou une erreur est survenue.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Choisir une Image</title>
<style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: none;
}
.container {
    display: flex;
    width: 90%;
    max-width: 900px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.form-section, .preview-section {
    flex: 1;
    padding: 20px;
}
.preview-section {
    display: flex;
    justify-content: center;
    align-items: center;
    border-left: 1px solid #ddd;
}
input[type="text"], textarea, input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}
textarea {
    resize: none;
    height: 100px;
}
button {
    margin-top: 10px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    font-size: 16px;
}
button:hover {
    background-color: #0056b3;
}
.cancel {
    background-color: #dc3545;
}
.cancel:hover {
    background-color: #c82333;
}
img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
}
</style>
</head>
<body>
<div class="container">
    <div class="form-section">
        <h2>Choisir une Image</h2>
        <form id="imageForm" action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="imageName" id="imageName" placeholder="Nom de l'image" required>
            <textarea name="imageDescription" id="imageDescription" placeholder="Description de l'image" required></textarea>
            <input type="file" name="imageInput" id="imageInput" accept="image/*" required>
            <button type="button" id="chooseImage">Choisir Image</button>
            <button type="button" class="cancel" id="cancelButton">Annuler</button>
            <button type="submit" id="submitButton">Envoyer</button>
        </form>
    </div>
    <div class="preview-section">
        <img id="imagePreview" src="" alt="Aperçu de l'image" style="display:none;">
    </div>
</div>
<script>
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const chooseImageButton = document.getElementById('chooseImage');
const cancelButton = document.getElementById('cancelButton');
const imageName = document.getElementById('imageName');
const imageDescription = document.getElementById('imageDescription');

chooseImageButton.addEventListener('click', () => {
    imageInput.click();
});

imageInput.addEventListener('change', () => {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

cancelButton.addEventListener('click', () => {
    imageInput.value = '';
    imagePreview.style.display = 'none';
    imagePreview.src = '';
    imageName.value = '';
    imageDescription.value = '';
});

// Si vous souhaitez forcer la soumission du formulaire via le bouton Envoyer, aucune action supplémentaire n'est nécessaire
</script>
</body>
</html>