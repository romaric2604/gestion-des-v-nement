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

$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $date_debut = trim($_POST['start-date'] ?? '');
    $date_fin = trim($_POST['end-date'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $place = trim($_POST['place'] ?? '');

    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($description)) $errors[] = "La description est requise.";
    if (empty($categorie)) $errors[] = "La catégorie est requise.";
    if (empty($date_debut)) $errors[] = "La date de début est requise.";
    if (empty($date_fin)) $errors[] = "La date de fin est requise.";
    if (empty($lieu)) $errors[] = "Le lieu est requis.";
    if (empty($place)) $errors[] = "Le nombre de place est requis.";

    $currentDateTime = date('Y-m-d\TH:i');

    if ($date_debut && $date_debut < $currentDateTime) {
        $errors[] = "La date de début doit être supérieure ou égale à la date actuelle.";
    }

    if ($date_debut && $date_fin && $date_fin < $date_debut) {
        $errors[] = "La date de fin doit être supérieure ou égale à la date de début.";
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO evenement (nom, description, categorie, date_debut, date_fin, lieu, place)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $description, $categorie, $date_debut, $date_fin, $lieu, $place]);
            $success = "Événement enregistré avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

$today = date('Y-m-d H:i:s');
$evenements = $pdo->query("SELECT * FROM evenement WHERE date_debut >= '$today' ORDER BY date_debut DESC")->fetchAll(PDO::FETCH_ASSOC);
$en_cours = array_filter($evenements, fn($e) => $e['date_fin'] >= $today);
$passes = array_filter($evenements, fn($e) => $e['date_fin'] < $today);

$acteurs = [];
try {
    $acteurs = $pdo->query("SELECT * FROM acteur")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // En cas d'erreur SQL, garder $acteurs vide
    $acteurs = [];
}

// Vérification si la variable est bien un tableau
if (!is_array($acteurs)) {
    $acteurs = [];
}

?>

<!DOCTYPE html>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Form Wizard - Enregistrer Événements</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f4f4;
  padding: 20px;
  position: relative;
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
#btnContrat{
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
#btnContrat:hover{
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
button { padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; margin:5px; }
#acteursListe { max-height:300px; overflow-y:auto; margin-top:10px; }
#acteursListe div { display:flex; align-items:center; margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:10px; }
#acteursListe img { width:60px; height:60px; object-fit:cover; border-radius:50%; margin-right:10px; }

</style>
</head>
<body>

<!-- Boutons côte à côte -->
<div class="boutons-container">
  <!-- Bouton ajouter événement -->
  <button id="addEventBtn" class="btn-style" title="Ajouter un événement">+</button>

  <!-- Bouton créer un contrat avec icône -->
  <button id="btnContrat" class="btn-style" title="Créer un contrat">📝</button>
</div>
<!-- Modal création contrat -->
<div id="modalContrat" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;">
  <div style="background:#fff; padding:20px; max-width:500px; width:90%; border-radius:8px; position:relative;">
    <div id="closeContrat" onclick="fermerModalContrat()" style="position:absolute; top:10px; right:10px; cursor:pointer; font-size:20px;">×</div>
    <h3>Créer un contrat</h3>
    <form id="formContrat" method="POST">
      <input type="hidden" name="type" value="contrat" />
      <div>
        <label>Événement :</label>
        <select name="evenement_id" required>
          <option value="">--Sélectionnez un événement--</option>
          <?php foreach($evenements as $ev): ?>
            <option value="<?= $ev['id_evenement'] ?>"><?= htmlspecialchars($ev['nom']) ?> (<?= date('d/m/Y', strtotime($ev['date_debut'])) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Acteur :</label>
        <select name="acteur_id" required>
          <option value="">--Sélectionnez un acteur--</option>
          <?php foreach($acteurs as $a): ?>
            <option value="<?= $a['id_acteur'] ?>"><?= htmlspecialchars($a['nom']) ?> (<?= htmlspecialchars($a['categorie']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="margin-top:10px;">
        <button type="submit">Enregistrer le contrat</button>
        <button type="button" onclick="fermerModalContrat()">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script>
// Ouvre le modal quand on clique sur le bouton
document.getElementById('btnContrat').onclick = () => {
  document.getElementById('modalContrat').style.display='flex';
};

// Ferme le modal
function fermerModalContrat() {
  document.getElementById('modalContrat').style.display='none';}
</script>
<!-- Liste des événements en cours -->
<h2>Événements en cours</h2>
<div class="events">
  <?php
  $today = date('Y-m-d H:i:s');
  $en_cours = array_filter($evenements, fn($e) => $e['date_fin'] >= $today);
  if (empty($en_cours)):
  ?>
    <p>Aucun événement en cours.</p>
  <?php else: ?>
    <?php foreach($en_cours as $event): ?>
      <div class="event" style="background:#fff; padding:10px; margin-bottom:10px; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
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

<!-- Liste des événements passés -->
<h2>Événements passés</h2>
<div class="events">
  <?php
  $passes = array_filter($evenements, fn($e) => $e['date_fin'] < $today);
  if (empty($passes)):
  ?>
    <p>Aucun événement passé.</p>
  <?php else: ?>
    <?php foreach($passes as $event): ?>
      <div class="event past" style="background:#eee; padding:10px; margin-bottom:10px; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
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

<!-- Optional: ajouter une notification -->
<div id="notification" style="display:none; position:fixed; bottom:10px; left:50%; transform:translateX(-50%); background:#28a745; color:#fff; padding:10px 20px; border-radius:5px; z-index:999;"></div>
<script>
function showNotification(msg) {
  const notif = document.getElementById('notification');
  notif.innerText = msg;
  notif.style.display='block';
  setTimeout(() => { notif.style.display='none'; }, 3000);
}
</script>
<!-- Modale -->
<div id="modalOverlay">
  <div id="modalContent">
    <div id="closeModal" title="Fermer">&times;</div>
    <!-- Formulaire wizard -->
    <form id="eventWizardForm" method="POST">
      <div class="progress-bar">
        <div class="progress-step step-indicator">1</div>
        <div class="progress-step step-indicator">2</div>
        <div class="progress-step step-indicator">3</div>
        <div class="progress-step step-indicator">4</div>
      </div>

      <!-- Étape 1 -->
      <div class="step active" data-step="0">
        <div class="form-group">
          <label for="nom">Nom de l'événement :</label>
          <input type="text" id="nom" name="nom" required />
        </div>
        <div class="form-group">
          <label for="description">Description :</label>
          <textarea id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="buttons">
          <span></span>
          <button type="button" onclick="next()">Suivant</button>
        </div>
      </div>

      <!-- Étape 2 -->
      <div class="step" data-step="1">
        <div class="form-group">
          <label>Catégorie :</label>
          <select name="categorie" required>
            <option value="">--Choisissez une catégorie--</option>
            <option value="mariage">Mariage</option>
            <option value="anniversaire">Anniversaire</option>
            <option value="funeral">Funérailles</option>
            <option value="bapteme">Baptême</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="buttons">
          <button type="button" onclick="prev()">Précédent</button>
          <button type="button" onclick="next()">Suivant</button>
        </div>
      </div>

      <!-- Étape 3 -->
      <div class="step" data-step="2">
        <div class="form-group">
          <label for="start-date">Date de début :</label>
          <input type="datetime-local" id="start-date" name="start-date" required min="<?= date('Y-m-d\TH:i') ?>" />
        </div>
        <div class="form-group">
          <label for="end-date">Date de fin :</label>
          <input type="datetime-local" id="end-date" name="end-date" required />
        </div>
        <div class="buttons">
          <button type="button" onclick="prev()">Précédent</button>
          <button type="button" onclick="next()">Suivant</button>
        </div>
      </div>

      <!-- Étape 4 -->
      <div class="step" data-step="3">
        <div class="form-group">
          <label for="lieu">Lieu :</label>
          <input type="text" id="lieu" name="lieu" required />
        </div>
        <div class="form-group">
          <label for="place">Nombre de places :</label>
          <input type="text" id="place" name="place" required />
        </div>
        <div class="buttons">
          <button type="button" onclick="prev()">Précédent</button>
          <button type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Notification -->
<div id="notification" style="display:none; position:fixed; bottom:10px; left:50%; transform:translateX(-50%); background:#28a745; color:#fff; padding:10px 20px; border-radius:5px; z-index:1000;"></div>

<script>
const btnOpen = document.getElementById('addEventBtn');
const modal = document.getElementById('modalOverlay');
const btnClose = document.getElementById('closeModal');

btnOpen.onclick = () => { modal.style.display = 'flex'; };
btnClose.onclick = () => { modal.style.display = 'none'; };
window.onclick = (e) => { if (e.target === modal) modal.style.display='none'; };

const steps = document.querySelectorAll('.step');
const indicators = document.querySelectorAll('.step-indicator');
let currentStep = 0;

function showStep(index) {
  steps.forEach((s, i) => s.classList.toggle('active', i === index));
  indicators.forEach((ind, i) => ind.classList.toggle('active', i === index));
}
function next() {
  if (currentStep === 0) {
    const nom = document.getElementById('nom');
    const desc = document.getElementById('description');
    if (!nom.value || !desc.value) {
      alert('Remplissez tous les champs.');
      nom.reportValidity(); desc.reportValidity(); return;
    }
  }
  if (currentStep === 2) {
    const start = document.getElementById('start-date');
    const end = document.getElementById('end-date');
    const now = new Date();
    const startDate = new Date(start.value);
    const endDate = new Date(end.value);

    if (!start.value || !end.value) {
      alert('Remplissez les deux dates.');
      start.reportValidity(); end.reportValidity();
      return;
    }

    if (startDate < now) {
      alert("La date de début doit être dans le futur ou aujourd'hui.");
      start.reportValidity();
      return;
    }

    if (endDate < startDate) {
      alert("La date de fin doit être postérieure ou égale à la date de début.");
      end.reportValidity();
      return;
    }
  }

  if (currentStep < steps.length - 1) {
    currentStep++;
    showStep(currentStep);
  }
}

document.getElementById('start-date').addEventListener('change', function () {
  document.getElementById('end-date').min = this.value;
});
function prev() {
  if (currentStep > 0) {
    currentStep--;
    showStep(currentStep);
  }
}
showStep(currentStep);

function showNotification(msg) {
  const notif = document.getElementById('notification');
  notif.innerText = msg;
  notif.style.display = 'block';
  setTimeout(() => { notif.style.display = 'none'; }, 4000);
}
</script>
</body>
</html>
