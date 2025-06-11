<?php
$host = 'localhost';
$dbname = 'gestion_evenement';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$today = date('Y-m-d H:i:s');
$evenements = $pdo->query("SELECT * FROM evenement ORDER BY date_debut DESC")->fetchAll(PDO::FETCH_ASSOC);
$en_cours = array_filter($evenements, fn($e) => $e['date_fin'] >= $today);
$passes = array_filter($evenements, fn($e) => $e['date_fin'] < $today);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des événements</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f8f8; padding: 20px; }
    h2 { color: #007bff; }
    .events {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .event {
      background: #ffffff;
      padding: 15px;
      border-left: 5px solid #007bff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .event.past {
      opacity: 0.6;
      border-left-color: #999;
    }
    .event h3 { margin-top: 0; }
  </style>
</head>
<body>

<h2>Événements en cours</h2>
<div class="events">
  <?php if (empty($en_cours)): ?>
    <p>Aucun événement en cours.</p>
  <?php else: ?>
    <?php foreach ($en_cours as $event): ?>
      <div class="event">
        <h3><?= htmlspecialchars($event['nom']) ?></h3>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <p><strong>Catégorie :</strong> <?= htmlspecialchars($event['categorie']) ?></p>
        <p><strong>Du :</strong> <?= date('d/m/Y H:i', strtotime($event['date_debut'])) ?></p>
        <p><strong>Au :</strong> <?= date('d/m/Y H:i', strtotime($event['date_fin'])) ?></p>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($event['lieu']) ?></p>
        <p><strong>Places :</strong> <?= htmlspecialchars($event['place']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<h2>Événements passés</h2>
<div class="events">
  <?php if (empty($passes)): ?>
    <p>Aucun événement passé.</p>
  <?php else: ?>
    <?php foreach ($passes as $event): ?>
      <div class="event past">
        <h3><?= htmlspecialchars($event['nom']) ?></h3>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <p><strong>Catégorie :</strong> <?= htmlspecialchars($event['categorie']) ?></p>
        <p><strong>Du :</strong> <?= date('d/m/Y H:i', strtotime($event['date_debut'])) ?></p>
        <p><strong>Au :</strong> <?= date('d/m/Y H:i', strtotime($event['date_fin'])) ?></p>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($event['lieu']) ?></p>
        <p><strong>Places :</strong> <?= htmlspecialchars($event['place']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
