<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Test Google Translate avec menu déroulant</title>

<!-- Style simple et beau pour la liste déroulante -->
<style>
body {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: #f4f4f4;
}

/* Container pour la sélection de langue */
.language-selector {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  font-family: Arial, sans-serif;
  font-size: 1em;
}

/* Style pour le label */
.language-selector label {
  font-weight: bold;
}

/* Style pour la liste déroulante */
#languageSelect {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  background-color: #fff;
  font-size: 1em;
  cursor: pointer;
  transition: border-color 0.3s, box-shadow 0.3s;
}

#languageSelect:hover {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

/* Optionnel: style pour le reste du contenu */
h1 {
  text-align: center;
}
</style>
</head>
<body>

<h1>Test Google Translate avec menu déroulant</h1>

<!-- Sélecteur de langue stylisé -->
<div class="language-selector">
  <label for="languageSelect">Choisir la langue :</label>
  <select id="languageSelect">
    <option value="fr" selected>Français</option>
    <option value="en">English</option>
  </select>
</div>

<!-- Contenu à traduire -->
<div id="content">
  <h2>Bienvenue sur la page</h2>
  <p>Ceci est un exemple de contenu pour tester la traduction automatique avec Google Translate.</p>
  <p>Changez la langue dans le menu pour voir la traduction en direct.</p>
</div>

<!-- Div caché pour le widget Google Translate -->
<div id="google_translate_element" style="display:none;"></div>

<!-- Scripts -->
<script type="text/javascript">
var googleTranslateReady = false; // Variable pour indiquer si le widget est prêt

// Fonction d'initialisation de Google Translate
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'fr', 
    includedLanguages: 'fr,en',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
  googleTranslateReady = true; // Le widget est prêt
}

// Gestionnaire pour changer la langue via la liste déroulante
document.getElementById('languageSelect').addEventListener('change', function() {
  if (googleTranslateReady) {
    var selectedLang = this.value; // 'fr' ou 'en'
    var selectElem = document.querySelector('.goog-te-combo');
    if (selectElem) {
      selectElem.value = selectedLang; // Change la langue dans le widget
      selectElem.dispatchEvent(new Event('change')); // Déclenche la traduction
    }
  } else {
    alert('Chargement en cours, veuillez attendre un instant.');
  }
});
</script>

<!-- Charger le script Google Translate -->
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>