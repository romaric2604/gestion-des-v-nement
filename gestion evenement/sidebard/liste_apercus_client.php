<?php
session_start();

// Connexion à la base de données
$host = 'localhost'; // ou votre hôte
$dbname = 'gestion_evenement'; // votre base
$user = 'root'; // votre utilisateur DB
$pass = ''; // votre mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_acteur'])) {
    die("Non connecté");
}
$id_user = $_SESSION['id_acteur'];

// Récupérer l'acteur correspondant
$stmtActeur = $pdo->prepare("SELECT nom, photo FROM acteur WHERE id = ?");
$stmtActeur->execute([$id_user]);
$acteur = $stmtActeur->fetch(PDO::FETCH_ASSOC);

// Récupérer tous les aperçus liés à cet acteur
$stmtApercus = $pdo->prepare("SELECT nom, description, photo, date_ajout FROM apercus WHERE id_acteur = ?");
$stmtApercus->execute([$id_user]);
$aperçus = $stmtApercus->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Galerie d'Images</title>

<!-- Style CSS amélioré -->
<style>
  :root {
    --primary-color: #333;
    --background-color:none;
    --card-background: #fff;
    --shadow: rgba(0, 0, 0, 0.1);
    --font-family: 'Arial', sans-serif;
    --border-radius: 8px;
    --transition-duration: 0.3s;
  }

  body {
    font-family: var(--font-family);
    background-color: var(--background-color);
    margin: 0;
    padding: 20px;
    color: var(--primary-color);
  }

  h1 {
    text-align: center;
    margin-bottom: 30px;
  }

  /* Grille responsive avec cartes plus petites */
  .container {
    display: flex;
    flex-wrap: wrap; /* Permet aux éléments de passer à la ligne suivante */
    justify-content: flex-start; /* Alignement des éléments */
    gap: 20px;
}


  /* Cartes */
  .card {
    background: var(--card-background);
    border-radius: var(--border-radius);
    box-shadow: 0 2px 10px var(--shadow);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-width: 300px; /* Réduit la largeur des cartes de moitié */
    margin: auto; /* Centrer si besoin */
    transition: transform var(--transition-duration), box-shadow var(--transition-duration);
  }

  /* Hover sur la carte */
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px var(--shadow);
  }

  /* Image principale, adaptée pour voir complètement */
  .main-image {
    width: 100%;
    height: auto; /* La hauteur s'ajuste pour garder le ratio */
    display: block; /* Pour éviter l'espacement indésirable */
  }

  /* Profil petite */
  .profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
    transition: transform 0.3s;
  }

  /* Titre de la photo */
  .photo-title {
    font-weight: bold;
    font-size: 1em;
    color: var(--primary-color);
  }

  /* Section profil + nom avant l'image */
  .profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }

  /* Description */
  .description {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
  }

  /* Date */
  .date {
    font-size: 12px;
    color: #888;
    align-self: flex-end;
  }

  @media (max-width: 600px) {
    .profile-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .date {
      align-self: flex-start;
    }
  }
  /* Entête profil */
.header-profile {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 30px;
  padding: 10px 20px;
  background-color: #f5f5f5; /* une légère couleur de fond */
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.profile-picture {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 20px;
  border: 2px solid #ccc;
}

.actor-name {
  font-size: 1.8em;
  font-weight: bold;
  color: #333;
}
</style>
</head>
<body>

<h1>Galerie d'Images</h1>
<!-- Entête avec photo et nom de l'acteur -->
<div class="header-profile">
  <img src="../images/<?= htmlspecialchars($acteur['photo']) ?>" alt="Photo de <?= htmlspecialchars($acteur['nom']) ?>" class="profile-picture" />
  <h2 class="actor-name"><?= htmlspecialchars($acteur['nom']) ?></h2>
</div>
<div class="container">

<div class="container">
  <?php foreach ($aperçus as $aperçu): ?>
    <div class="card">
      <!-- Nom et photo de profil avant l'image -->
      <div class="profile-header">
        <img src="../images/<?= htmlspecialchars($acteur['photo']) ?>" alt="Profil" class="profile-pic" />
        <div class="photo-title"><?= htmlspecialchars($aperçu['nom']) ?></div>
      </div>
      
      <!-- Image principale -->
      <img src="../images/<?= htmlspecialchars($aperçu['photo']) ?>" alt="<?= htmlspecialchars($aperçu['nom']) ?>" class="main-image" />
      
      <div class="card-content">
        <p class="description"><?= htmlspecialchars($aperçu['description']) ?></p>
        <p class="date">Ajouté le: <?= htmlspecialchars($aperçu['date_ajout']) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</div>

<!-- Script JavaScript (si besoin) -->
</body>
</html>