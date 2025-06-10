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

// Récupérer toutes les catégories existantes dans la colonne `categorie` de `evenement`
$stmt_cats = $pdo->query("SELECT DISTINCT categorie FROM `evenement` WHERE categorie IS NOT NULL AND categorie != ''");
$categories_raw = $stmt_cats->fetchAll(PDO::FETCH_COLUMN);

// Extraire et dédupliquer
$categories_set = [];
foreach ($categories_raw as $catString) {
    $cats = explode(',', $catString);
    foreach ($cats as $c) {
        $c = trim($c);
        if ($c !== '') {
            $categories_set[$c] = true;
        }
    }
}
$categories_list = array_keys($categories_set);

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $categories = $_POST['categorie'] ?? [];
    $date_debut = $_POST['start-date'] ?? '';
    $date_fin = $_POST['end-date'] ?? '';
    $place = $_POST['lieu'] ?? '';

    if ($nom && $description && $date_debut && $date_fin && $place && !empty($categories)) {
        $categorie_str = implode(',', $categories);
        $stmt = $pdo->prepare("INSERT INTO `evenement` (`nom`, `description`, `categorie`, `date_debut`, `date_fin`, `place`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $description, $categorie_str, $date_debut, $date_fin, $place]);
        $message = "Événement enregistré avec succès.";
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupérer tous les événements
$stmt_evt = $pdo->query("SELECT * FROM `evenement` ORDER BY `date_debut` DESC");
$evenements = $stmt_evt->fetchAll(PDO::FETCH_ASSOC);
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
  padding: 2rem;
}
.wizard {
  max-width: 600px;
  margin: auto;
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  position: relative;
}
.progress-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2rem;
}
.progress-step {
  flex: 1;
  text-align: center;
  position: relative;
}
.progress-step::before {
  content: '';
  display: inline-block;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: #ccc;
  transition: background 0.3s;
}
.progress-step.active::before {
  background: #4CAF50;
}
.step {
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.5s ease;
  position: absolute;
  top: 4rem;
  left: 0;
  width: 100%;
  pointer-events: none;
}
.step.active {
  opacity: 1;
  transform: translateX(0%);
  position: relative;
  pointer-events: auto;
}
.form-group {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.3rem;
}
input[type="text"],
input[type="date"],
input[type="datetime-local"],
textarea {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.checkbox-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
}
button {
  padding: 0.6rem 1rem;
  background: #4CAF50;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  border: none;
  transition: background 0.3s;
}
button:hover {
  background: #388e3c;
}
</style>
</head>
<body>

<h2>Ajouter un événement</h2>
<?php if (isset($message)) echo "<p>$message</p>"; ?>

<form id="eventForm" method="POST" action="">
  <!-- Barre de progression -->
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
      <div>
    <?php foreach ($categories_list as $cat): ?>
        <label>
            <input type="checkbox" name="categorie[]" value="<?= htmlspecialchars($cat) ?>" />
            <?= htmlspecialchars($cat) ?>
        </label>
    <?php endforeach; ?>
</div>
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

<h2>Liste des événements</h2>
<table border="1" cellpadding="5" cellspacing="0">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Description</th>
      <th>Catégories</th>
      <th>Date début</th>
      <th>Date fin</th>
      <th>Lieu</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($evenements as $ev): ?>
      <tr>
        <td><?= htmlspecialchars($ev['nom']) ?></td>
        <td><?= htmlspecialchars($ev['description']) ?></td>
        <td><?= htmlspecialchars($ev['categorie']) ?></td>
        <td><?= htmlspecialchars($ev['date_debut']) ?></td>
        <td><?= htmlspecialchars($ev['date_fin']) ?></td>
        <td><?= htmlspecialchars($ev['place']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
const steps = document.querySelectorAll('.step');
const indicators = document.querySelectorAll('.step-indicator');
let currentStep = 0;

function showStep(index) {
  steps.forEach((step, i) => {
    if (i === index) {
      step.classList.add('active');
    } else {
      step.classList.remove('active');
    }
  });
  indicators.forEach((ind, i) => {
    if (i === index) {
      ind.classList.add('active');
    } else {
      ind.classList.remove('active');
    }
  });
}

function next() {
  if (currentStep < steps.length - 1) {
    // Validation étape 1
    if (currentStep === 0) {
      const nom = document.getElementById('nom');
      const desc = document.getElementById('description');
      if (!nom.value || !desc.value) {
        alert('Veuillez remplir tous les champs.');
        nom.reportValidity();
        desc.reportValidity();
        return;
      }
    }
    if (currentStep === 2) {
      // Vérification des dates si nécessaire
    }
    currentStep++;
    showStep(currentStep);
  }
}

function prev() {
  if (currentStep > 0) {
    currentStep--;
    showStep(currentStep);
  }
}

// Au submit, le formulaire est envoyé normalement
// Le PHP traitera l'insertion dans la base
</script>
<script>
showStep(currentStep);
</script>
</body>
</html>
