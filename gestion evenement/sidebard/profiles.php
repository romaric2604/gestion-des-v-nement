<?php
session_start();

// Connexion à la BD
$servername = "localhost"; // Ajustez si besoin
$username = "root";        // Ajustez si besoin
$password = "";            // Ajustez si besoin
$dbname = "gestion_evenement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user']) || !isset($_SESSION['type'])) {
    die("Utilisateur non connecté");
}

$id_user = $_SESSION['id_user'];
$statut = $_SESSION['type'];

// Préparer la requête selon le statut
if ($statut === 'client') {
    $sql = "SELECT nom, prenom, email, mot_de_passe, photo, numero FROM client WHERE id = ?";
} elseif ($statut === 'acteur') {
    $sql = "SELECT nom, prenom, email, mot_de_passe, photo, numero FROM acteur WHERE id = ?";
} elseif ($statut === 'admin') {
    $sql = "SELECT nom, prenom, email, mot_de_passe, photo FROM admin WHERE id = ?";
} else {
    die("Statut non valide.");
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erreur de préparation : " . $conn->error);
}
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href='boxicons-master/css/boxicons.min.css' rel='stylesheet'>
<title>Profil Utilisateur</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: transparent;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.profile-container {
    display: flex;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    max-width: 600px;
    width: 100%;
}
.profile-photo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    overflow: hidden;
    margin: 20px;
}
.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-info {
    padding: 20px;
    flex: 1;
}
</style>
</head>
<body>
<div class="profile-container">
    <div class='profile-photo'>
        <img src='../images/<?php echo htmlspecialchars($userData['photo']); ?>' alt='Photo de Profil'>
    </div>
    <div class="profile-info">
        <h2>Nom: <?php echo htmlspecialchars($userData['nom']); ?></h2>
        <h3>Prénom: <?php echo htmlspecialchars($userData['prenom']); ?></h3>
        <h4>Email: <?php echo htmlspecialchars($userData['email']); ?></h4>
        <?php if ($statut !== 'admin'): ?>
            <h4>Numéro: <?php echo htmlspecialchars($userData['numero']); ?></h4>
        <?php endif; ?>
        <!-- Pour le mot de passe, affichez une étoile ou un bouton pour le voir -->
    </div>
</div>

<script>
function togglePassword() {
    const span = document.getElementById('password-display');
    if (span.textContent === '••••••••') {
        <?php
        ?>
       
        // span.textContent = '<?php echo htmlspecialchars($userData['mot_de_passe']); ?>';
        alert('Il est déconseillé d\'afficher le mot de passe en clair pour des raisons de sécurité.');
    } else {
        span.textContent = '••••••••';
    }
}
</script>
</body>
</html>