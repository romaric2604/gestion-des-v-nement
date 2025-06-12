<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

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

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Utiliser isset() pour compatibilité avec PHP < 7.0
    $imageName = isset($_POST['imageName']) ? trim($_POST['imageName']) : '';
    $imageDescription = isset($_POST['imageDescription']) ? trim($_POST['imageDescription']) : '';

    if (isset($_FILES['imageInput']) && $_FILES['imageInput']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageInput']['tmp_name'];
        $fileName = $_FILES['imageInput']['name'];
        $fileType = $_FILES['imageInput']['type'];

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileType, $allowedMimeTypes)) {
            die("<script>alert('Type de fichier non autorisé. Veuillez télécharger une image.'); window.history.back();</script>");
        }

        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        // Générer un nom unique
        $uniqueString = md5(uniqid(rand(), true));
        $newFileName = date('Ymd_His') . '_' . $uniqueString . '.' . $fileExtension;

        $destPath = '../images/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $stmt = $pdo->prepare("INSERT INTO apercus (photo, id_acteur, date_ajout, nom, description) VALUES (:photo, :id_acteur, NOW(), :nom, :description)");
            $stmt->execute([
                ':photo' => $newFileName,
                ':id_acteur' => $id_user,
                ':nom' => $imageName,
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
<!-- Le reste du code HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Ajouter une Image</title>
<style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f0f0f0;
}
.container {
    display: flex;
    width: 90%;
    max-width: 900px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
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
        <h2>Ajouter une Image</h2>
        <form id="imageForm" action="" method="POST" enctype="multipart/form-data">
            <label for="imageName">Nom de l'image :</label>
            <input type="text" id="imageName" name="imageName" placeholder="Nom de l'image..." required>

            <label for="imageDescription">Description :</label>
            <textarea id="imageDescription" name="imageDescription" placeholder="Description..." required></textarea>

            <label for="imageInput">Choisir une image :</label>
            <input type="file" name="imageInput" id="imageInput" accept="image/*" required>

            <div style="margin-top:15px;">
                <button type="submit">Envoyer</button>
                <button type="button" class="cancel" id="cancelButton">Annuler</button>
            </div>
        </form>
    </div>
    <div class="preview-section">
        <img id="imagePreview" src="" alt="Aperçu de l'image" style="display:none;">
    </div>
</div>

<script>
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const cancelButton = document.getElementById('cancelButton');

imageInput.addEventListener('change', () => {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.style.display = 'none';
        imagePreview.src = '';
    }
});

cancelButton.addEventListener('click', () => {
    document.getElementById('imageForm').reset();
    imagePreview.style.display = 'none';
    imagePreview.src = '';
});
</script>
</body>
</html>