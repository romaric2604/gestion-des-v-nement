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
    JOIN acteur a ON c.id_acteur = a.id
    WHERE a.id = :id_user
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
<style>
body {
  font-family: Arial, sans-serif;
  background: none;
  padding: 20px;
  position: relative;
}
.event.future {
  border-left-color: #28a745; /* vert */
}
#addEventBtn {
  width: 180px;
  height: 180px;
  border: 3px solid #007bff;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  cursor: pointer;
  background: #fff;
  margin: 20px auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  transition: background 0.3s, transform 0.2s;
}
#addEventBtn:hover {
  background: #e6f0ff;
  transform: scale(1.05);
}

#modalOverlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  z-index: 999;
}
#modalContent {
  background: #fff;
  width: 100%;
  max-width: 420px;
  max-height: 100%;
  overflow-y: auto;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  position: relative;
}
#closeModal {
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 1.8rem;
  cursor: pointer;
  color: fff;
}
#closeModal:hover {
  color:rgb(247, 25, 36);
}

form#eventWizardForm {
  font-family: Arial, sans-serif;
}
.progress-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.progress-step {
  flex: 2;
  height: 15px;
  margin: 0 8px;
  background: #cce5ff;
  border-radius: 5px;
  transition: background 0.3s;
}
.progress-step.active {
  background: #007bff;
}
.step {
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.5s ease;
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  display: block;
}
.step.active {
  opacity: 1;
  transform: translateX(0);
  position: relative;
  pointer-events: auto;
}
.form-group {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: bold;
}
input[type="text"],
input[type="datetime-local"],
textarea, select {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #007bff;
  border-radius: 8px;
  outline: none;
  transition: border-color 0.2s;
}
input[type="text"]:focus,
input[type="datetime-local"]:focus,
textarea:focus, select:focus {
  border-color: #0056b3;
}
button {
  padding: 0.6rem 1.2rem;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}
button:hover {
  background-color: #0056b3;
}

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
.event h3 {
  margin-top: 0;
}
</style>
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