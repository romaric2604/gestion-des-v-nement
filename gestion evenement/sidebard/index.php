<?php
session_start();

// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "gestion_evenement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID and status from session
$id_user = $_SESSION['id_user'];
$type = $_SESSION['type'];

// Prepare SQL query based on status
if ($type === 'client') {
    $sql = "SELECT c.nom, c.photo, c.prenom,  c.email, c.mot_de_passe FROM client c WHERE c.id = ?";
} elseif ($type === 'acteur') {
    $sql = "SELECT a.nom, a.prenom, a.photo, a.email, u.mot_de_passe FROM acteur a  WHERE a.id = ?";
} elseif ($type === 'admin') {
    $sql = "SELECT nom, prenom, email, mot_de_passe, photo FROM admin WHERE id = ?";
} else {
    die("Statut non valide.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fermeture des requêtes et de la connexion
$stmt->close();

$conn->close();
?>
<?php
if(isset($_POST['sortir'])){
   header("location: ../site web/index.php");
}
?>
<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <!--=============== REMIXICONS ===============-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

      <!--=============== CSS ===============-->
      <link rel="stylesheet" href="assets/css/styles.css">

      <title>Responsive sidebar Menu | Dark/Light Mode - Bedimcode</title>
      <style>
         .search{
    border-radius: 10px;
    height: 30px;
    width: 300px;
    border-color:white;
   }
   img{
      width: 40px;
      height: 40px;
      border-radius: 100%;
   }
   iframe{
      width: 100%;
      height: 40rem;
      display:none;
   }
   #but{
      position: relative;
      top:7px;
   }
   /* Style pour la zone de sélection de langue */
.language-selector {
  display: flex;
  align-items: center;
  gap: 30px;
  margin-bottom: 30px; /* espace en dessous */
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
  margin:10px;
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
      </style>
      <link href='../boxicons-master/css/boxicons.min.css' rel='stylesheet'>  
   </head>
   <body>
      <!--=============== HEADER ===============-->
      <header class="header" id="header">
         <div class="header__container">
            <a href="#" class="header__logo">
               <form action="" method="post">
               <input type="text" class="search" placeholder="Recherche..." name="recherche">
               <button type='submit' id="but"><i class='bx bx-search-alt-2'></i></button>
               </form>
            </a>
            <a href="#" class="header__logo">
               <img src="logo.png" alt="">
               <span>Keyce <br> Informatique</span>
            </a>
            
            <button class="header__toggle" id="header-toggle">
               <i class='bx bx-menu'></i>
            </button>
         </div>
      </header>

      <!--=============== SIDEBAR ===============-->
      <nav class="sidebar" id="sidebar">
         <div class="sidebar__container">
            <div class="sidebar__user">
               <div class="sidebar__img">
                  <img src="../images/<?php echo"{$userData['photo']}";?>" alt="image">
               </div>
   
               <div class="sidebar__info">
                  <h3><?php echo"{$userData['nom']}" ;?> <?php echo"{$userData['prenom']}" ;?></h3>
                  <span><?php echo"{$userData['email']}";?></span>
               </div>
            </div>

            <div class="sidebar__content">
               <div>
                  <h3 class="sidebar__title">MANAGE</h3>

                  <div class="sidebar__list">
                     
                     <?php 
                        if ($type == 'client' ) {
                            echo '<a href="#" class="sidebar__link active-link"  onclick="showiframe(\'apercus\',\'tableau_de_bord_client.php\')">
                        <i class="ri-pie-chart-2-fill"></i>
                        <span>Tableau de bord</span>
                     </a>';
                        }
                     ?>
                      <?php 
                        if ($type == 'acteur' ) {
                            echo '<a href="#" class="sidebar__link active-link"  onclick="showiframe(\'apercus\',\'tableau_de_bord_acteur.php\')">
                        <i class="ri-pie-chart-2-fill"></i>
                        <span>Tableau de bord</span>
                     </a>';
                        }
                     ?>
                     <a href="#" class="sidebar__link">
                        <i class="ri-wallet-3-fill"></i>
                        <span>Mes évenements</span>
                     </a>
                     <?php 
                        if ($type == 'client' || $type == 'acteur' ) {
                            echo '<a href="#" class="sidebar__link" onclick="showiframe(\'apercus\',\'calendrier.php\')">
                        <i class="ri-calendar-fill"></i>
                        <span>Calendrier</span>
                     </a>';
                        }
                     ?>

                     <a href="#" class="sidebar__link">
                        <i class="ri-arrow-up-down-line"></i>
                        <span>Recents évenaments</span>
                     </a>
                     <?php 
                        if ($type == 'client' || $type == 'acteur' ) {
                            echo '<a href="#" class="sidebar__link" onclick="showiframe(\'apercus\',\'avis.php\')">
                        <i class="ri-calendar-fill"></i>
                        <span>Laisser des avis</span>
                     </a>';
                        }
                     ?>
                     <?php 
                        if ($type == 'admin') {
                            echo '<a href="#" class="sidebar__link" onclick="showiframe(\'apercus\',\'apercus.php\')">
                        <i class="bx bx-line-chart"></i>
                           <span>Ajouter un photo</span>
                     </a>';
                        }
                     ?>
                     <?php 
                        if ($type == 'admin') {
                            echo '<a href="#" class="sidebar__link" onclick="showiframe(\'apercus\',\'acteur_admin.php\')">
                        <i class="bx bx-line-chart"></i>
                           <span>Liste des acteurs</span>
                     </a>';
                        }
                     ?>
                  </div>
               </div>

               <div>
                  <h3 class="sidebar__title">SETTINGS</h3>

                  <div class="sidebar__list">
                     <a href="#" class="sidebar__link" onclick="showiframe('apercus','profiles.php')">
                        <i class="ri-settings-3-fill"></i>
                        <span>Profile</span>
                     </a>

                     <?php 
                        if ($type == 'client' || $type == 'acteur' ) {
                            echo '<a href="#" class="sidebar__link" onclick="showiframe(\'apercus\',\'notification.php\')">
                        <i class="ri-notification-2-fill"></i>
                        <span>Notifications</span>
                     </a> ';
                        }
                     ?>
                  </div>
               </div>
            </div>

            <div class="sidebar__actions">
               <!-- Container pour la sélection de langue -->
<div class="language-selector">
  <select id="languageSelect">
    <option value="fr" selected>Fr</option>
    <option value="en">En</option>
  </select>
<label for="languageSelect">Choisir la langue </label>
</div>
               <button>
                  <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
                     <span>Theme</span>
                  </i>
               </button>

               <form  method="post">
               <button class="sidebar__link" name="sortir">
                  <i class='bx bx-arrow-to-left'></i>
                  <span>Sortir</span>
               </button>
               </form>
            </div>
         </div>
      </nav>

      <!--=============== MAIN ===============-->
      <main class="main container" id="main">
         <iframe src="#" frameborder="0" id="apercus"></iframe>
      </main>
      <div id="google_translate_element" style="display:none;"></div>

<!-- Script d'initialisation et gestion -->
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'fr', 
    includedLanguages: 'fr,en',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');

  // Indiquer que le widget est prêt
  window.googleTranslateReady = true;
}

// Fonction pour changer la langue, en vérifiant si le widget est chargé
document.getElementById('languageSelect').addEventListener('change', function() {
  if (window.googleTranslateReady) {
    var selectedLang = this.value; // 'fr' ou 'en'
    var selectElem = document.querySelector('.goog-te-combo');

    if (selectElem) {
      selectElem.value = selectedLang;
      selectElem.dispatchEvent(new Event('change'));
    }
  } else {
    alert('Chargement en cours, veuillez réessayer dans quelques secondes.');
  }
});
</script>
<!-- Script Google Translate -->
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

      
      <!--=============== MAIN JS ===============-->
      <script src="assets/js/main.js"></script>
   </body>
   <script>
      function showiframe(iframeId, newSrc) {
    const iframe = document.getElementById(iframeId);
    if (iframe) {
        // Ajouter un timestamp pour forcer la rechargement
        const timestamp = Date.now();
        const srcWithTimestamp = newSrc.includes('?') ? `${newSrc}&_=${timestamp}` : `${newSrc}?_=${timestamp}`;

        // Mettre à jour la source
        iframe.src = srcWithTimestamp;

        // Sauvegarder dans sessionStorage
        sessionStorage.setItem(iframeId, srcWithTimestamp);

        // Cacher tous les iframes
        document.querySelectorAll('iframe').forEach(frame => {
            frame.style.display = 'none';
        });

        // Afficher le iframe ciblé
        iframe.style.display = 'block';

    } else {
        console.error(`Iframe with ID '${iframeId}' not found.`);
    }
}
   </script>
</html>