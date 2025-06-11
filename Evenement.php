<?php

$host = 'localhost'; 
$dbname = 'gestion_evenement'; 
$username = 'root'; 
$password = '';

// Connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $date_debut = trim($_POST['start-date'] ?? '');
    $date_fin = trim($_POST['end-date'] ?? '');
    $place = trim($_POST['lieu'] ?? '');


    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($description)) $errors[] = "La description est requise.";
    if (empty($categorie)) $errors[] = "La catégorie est requise.";
    if (empty($date_debut)) $errors[] = "La date de début est requise.";
    if (empty($date_fin)) $errors[] = "La date de fin est requise.";
    if (empty($place)) $errors[] = "Le lieu est requis.";
    
    if ($date_debut && $date_fin && $date_debut > $date_fin) {
        $errors[] = "La date de début doit être antérieure à la date de fin.";
    }

  if (empty($errors)) {
    $sql = "INSERT INTO evenement (nom, description, categorie, date_debut, date_fin, place) VALUES (?, ?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $description, $categorie, $date_debut, $date_fin, $place]);
        $success_message = "L'événement a été enregistré avec succès.";
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de l'insertion : " . $e->getMessage();
        echo "<pre>" . print_r($errors, true) . "</pre>"; // Affiche les erreurs
    } 
}var_dump($_POST);
}
?>
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

/* Bouton carré + */
#addEventBtn {
  width: 80px;
  height: 80px;
  border: 3px dashed #007bff;
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

/* La modale (fenêtre popup) */
#modalOverlay {
  display: none; /* cachée par défaut */
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
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  position: relative;
}
/* Bouton Fermer */
#closeModal {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 1.8rem;
  cursor: pointer;
  color: #007bff;
}
#closeModal:hover {
  color: #0056b3;
}

/* Style du form wizard avec thème bleu */
form#eventWizardForm {
  font-family: Arial, sans-serif;
}
.progress-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.progress-step {
  flex: 3;
  height: 10px;
  margin: 0 5px;
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
textarea {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #007bff;
  border-radius: 8px;
  outline: none;
  transition: border-color 0.2s;
}
input[type="text"]:focus,
input[type="datetime-local"]:focus,
textarea:focus {
  border-color: #0056b3;
}
.checkbox-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
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

/* Ajoutez un style pour le message de notification (optionnel) */
#notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #007bff;
  color: #fff;
  padding: 10px 20px;
  border-radius: 8px;
  display: none;
  z-index: 1000;
}

</style>
</head>
<body>

<!-- Bouton + -->
<div id="addEventBtn" title="Ajouter un événement">+</div>

<!-- Modale -->
<div id="modalOverlay">
  <div id="modalContent">
    <div id="closeModal" title="Fermer">&times;</div>
    <!-- Formulaire wizard -->
    <form id="eventWizardForm" method="POST" action="">
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
          <label>Catégories :</label>
           <select name="categorie" required>
    <option value="">--Choisissez une catégorie--</option>
    <option value="mariage">Mariage</option>
    <option value="anniversaire">Anniversaire</option>
    <option value="funeral">Funeral</option>
    <option value="bapteme">Baptême</option>
    <option value="autre">Autre</option>
  </select><br><br>
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
          <input type="datetime-local" id="start-date" name="start-date" required />
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
        <div class="buttons">
          <button type="button" onclick="prev()">Précédent</button>
          <button type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Notification JS -->
<div id="notification"></div>

<!-- Scripts -->
<script>
// Ouverture / fermeture modale
const btnOpen = document.getElementById('addEventBtn');
const modal = document.getElementById('modalOverlay');
const btnClose = document.getElementById('closeModal');

btnOpen.onclick = () => {
  modal.style.display = 'flex';
  loadCategories(); // charger catégories dynamiques
};
btnClose.onclick = () => { modal.style.display = 'none'; };
window.onclick = (e) => { if (e.target === modal) modal.style.display='none'; };

// Charger catégories dynamiques via fetch
function loadCategories() {
  fetch('load_categories.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('categoriesCheckboxes');
      container.innerHTML = '';
      data.forEach(cat => {
        const lbl = document.createElement('label');
        lbl.innerHTML = `<input type="checkbox" name="categorie[]" value="${cat}"> ${cat}`;
        container.appendChild(lbl);
      });
    });
}

// Wizard navigation
const steps = document.querySelectorAll('.step');
const indicators = document.querySelectorAll('.step-indicator');
let currentStep = 0;

function showStep(index) {
  steps.forEach((s, i) => s.classList.toggle('active', i===index));
  indicators.forEach((ind, i) => ind.classList.toggle('active', i===index));
}
function next() {
  if (currentStep===0) {
    const nom = document.getElementById('nom');
    const desc = document.getElementById('description');
    if (!nom.value || !desc.value) {
      alert('Remplissez tous les champs.');
      nom.reportValidity(); desc.reportValidity(); return;
    }
  }
  if (currentStep<steps.length-1) {
    currentStep++;
    showStep(currentStep);
  }
}
function prev() {
  if (currentStep>0) {
    currentStep--;
    showStep(currentStep);
  }
}
showStep(currentStep);

// Soumission AJAX
document.getElementById('eventWizardForm').onsubmit = function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('save_event.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(msg => {
    // Notification
    showNotification(msg);
    // Fermer modal après 2s
    setTimeout(() => { modal.style.display='none'; }, 2000);
    // Rafraîchir la liste ou autre
    location.reload();
  });
}

// Notification function
function showNotification(msg) {
  const notif = document.getElementById('notification');
  notif.innerText = msg;
  notif.style.display='block';
  setTimeout(() => { notif.style.display='none'; }, 3000);
}
</script>
</body>
</html>
