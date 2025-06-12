<?php
session_start(); // Assurez que la session est démarrée

// Récupérer l'id utilisateur depuis la session
$id_user = $_SESSION['id_user'];

if ($id_user === null) {
    die("Utilisateur non connecté");
}

$host = 'localhost'; 
$dbname = 'gestion_evenement'; 
$username = 'root'; 
$password = '';

try {
    // Connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// La date limite pour la période récente (il y a une semaine)
$date_limit = date('Y-m-d H:i:s', strtotime('-7 days'));

// Récupérer tous les événements liés à l'utilisateur
$sql = "
    SELECT e.* 
    FROM evenement e
    JOIN contrat c ON e.id = c.id_evenement
    JOIN client cl ON c.id_client = cl.id
    WHERE cl.id = :id_user
    ORDER BY e.date_debut DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_user' => $id_user]);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrer pour ne garder que ceux dans la dernière semaine
$recent_events = array_filter($evenements, function($e) use ($date_limit) {
    return ($e['date_debut'] >= $date_limit) || ($e['date_fin'] >= $date_limit);
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Form Wizard - Enregistrer Événements</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Événements récents (derière semaine)</h2>
<div class="events">
  <?php if (empty($recent_events)): ?>
    <p>Aucun événement récent dans la dernière semaine.</p>
  <?php else: ?>
    <?php foreach ($recent_events as $event): ?>
      <div class="event">
        <h3><?= htmlspecialchars($event['nom']) ?></h3>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <p><strong>Catégorie :</strong> <?= htmlspecialchars($event['categorie']) ?></p>
        <p><strong>Du :</strong> <?= date('d/m/Y H:i', strtotime($event['date_debut'])) ?></p>
        <p><strong>Au :</strong> <?= date('d/m/Y H:i', strtotime($event['date_fin'])) ?></p>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($event['lieu']) ?></p>
        <p><strong>Place :</strong> <?= htmlspecialchars($event['place']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- autres éléments comme le bouton d'ajout, modal, etc. si nécessaire -->

</body>
</html>