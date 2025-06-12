<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    die("Vous n'êtes pas connecté.");
}

// Vérifier si le POST est reçu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le texte de l'avis
    $review = trim($_POST['review']); // ou $_POST['reviewText'] si vous utilisez ce nom

    // Vérifier que le commentaire n'est pas vide
    if (empty($review)) {
        die("Le commentaire ne peut pas être vide.");
    }

    // Connexion à la base
    $conn = new mysqli('localhost', 'root', '', 'gestion_evenement');

    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // Préparer la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO avis (id_user, avis, types,date_ajout) VALUES (?, ?,?, NOW())");

    if ($stmt) {
        $id_user = $_SESSION['id_user'];
        $type = $_SESSION['type'];
        $stmt->bind_param('iss', $id_user,$review,$type);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo '<script>alert("Avis enregistré avec succès.");</script>';
        } else {
             echo '<script>alert("Erreur lors de l\'enregistrement de l\'avis.");</script>';
        }
        $stmt->close();
    } else {
        echo "Erreur de préparation : " . $conn->error;
    }

    $conn->close();
} else {
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laisser un Avis</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: none;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 700px;
            margin: auto auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        textarea {
            width: 97%;
            height: 150px;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            margin-bottom: 20px;
            resize: none;
            font-size: 1em;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff; /* Couleur vive */
            color: white;
            cursor: pointer;
            font-size: 1em;
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
        <h1>Laisser un Avis</h1>
       <form id="reviewForm"  method="POST">
    <textarea id="reviewText" name="review" placeholder="Écrivez votre avis ici..." required></textarea>
    <div class="button-container">
        <button type="button" class="cancel" onclick="cancelReview()">Annuler</button>
        <button type="submit">Envoyer</button>
    </div>
</form>
    </div>

    <!-- Inline JavaScript -->
    <script>
        function cancelReview() {
            document.getElementById('reviewForm').reset(); // Réinitialiser le formulaire
        }
    </script>

</body>
</html>