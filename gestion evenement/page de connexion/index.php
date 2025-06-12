<?php
session_start();
require '../bd_connection.php';

// Vérification de la connexion à la base
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}

// Gestion de la connexion
if (isset($_POST['connection'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Veuillez entrer une adresse email valide.');</script>";
    } else {
        $sql = "
        SELECT c.id, c.nom, c.prenom, c.email, c.mot_de_passe, c.photo, 'client' AS type 
        FROM client c  
        WHERE c.email = ?

        UNION ALL

        SELECT a.id, a.nom, a.prenom, a.email, a.mot_de_passe, a.photo, 'admin' AS type 
        FROM admin a 
        WHERE a.email = ?

        UNION ALL

        SELECT l.id, l.nom, l.prenom, l.email, l.mot_de_passe, l.photo, 'acteur' AS type 
        FROM acteur l 
        WHERE l.email = ?
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt->bind_param("sss", $email, $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Vérification du mot de passe
            if (password_verify($password, $user['mot_de_passe'])) {
                session_regenerate_id(true);
                $_SESSION['id_user'] = $user['id'];
                $_SESSION['type'] = $user['type'];
                $_SESSION['nom'] = $user['nom'];

                if ($user['type'] == "client") {
                    header("Location: ../sidebard/index.php");
                    exit();
                } elseif ($user['type'] == "admin") {
                    header("Location: ../sidebard/index.php");
                    exit();
                } else {
                    header("Location: ../sidebard/index.php");
                    exit();
                }
            } else {
                echo "<script>alert('Mot de passe incorrect.');</script>";
            }
        } else {
            echo "<script>alert('Aucun utilisateur trouvé avec cet email.');</script>";
        }
        $stmt->close();
    }
}

// Gestion de l'inscription
if (isset($_POST['sinscrire'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $numero = trim($_POST['numero']);
    $age = trim($_POST['age']);
    $sexe = trim($_POST['sexe']);
    $residence = trim($_POST['residence']);
    $pwd = trim($_POST['password']);
    $confirmPwd = trim($_POST['confirmPwd']);
    $errors = [];

    // Validation
    if (empty($nom)) {
        $errors[] = "Le champ nom est obligatoire.";
    }
    if (empty($prenom)) {
        $errors[] = "Le champ prénom est obligatoire.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide.";
    }
    if (empty($residence)) {
        $errors[] = "Veuillez sélectionner une résidence.";
    }
    if (empty($numero)) {
        $errors[] = "Veuillez entrer votre numéro de téléphone.";
    }
    if (empty($age) || !is_numeric($age) || intval($age) <= 0) {
        $errors[] = "Veuillez entrer un âge valide.";
    }
    if (empty($sexe)) {
        $errors[] = "Veuillez entrer votre genre.";
    }
    if (strlen($pwd) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($pwd !== $confirmPwd) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Gestion du fichier photo
    $photoName = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $photo = $_FILES['photo'];

        if ($photo['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $photoExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

            if (in_array($photoExtension, $allowedExtensions)) {
                // Générer un nom unique
                $photoName = date('Ymd_His') . '_' . uniqid() . '.' . $photoExtension;
                $targetDir = '../images/';
                $targetFilePath = $targetDir . $photoName;

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                if (move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
                    // Fichier uploadé avec succès
                } else {
                    $errors[] = "Erreur lors du déplacement du fichier.";
                }
            } else {
                $errors[] = "Le fichier doit être au format jpg, jpeg, png ou gif.";
            }
        } else {
            switch ($photo['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = "Le fichier dépasse la taille maximale autorisée.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = "Aucun fichier téléchargé.";
                    break;
                default:
                    $errors[] = "Erreur lors du téléchargement de la photo.";
            }
        }
    } else {
        $errors[] = "Aucune photo téléchargée.";
    }
// Si pas d'erreurs, insérer en DB
if (empty($errors)) {
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    // Vérifier si l'email existe déjà (optionnel mais recommandé)
    $stmtCheck = $conn->prepare("SELECT id FROM client WHERE email = ?");
    if (!$stmtCheck) {
        die("Erreur de préparation de la requête de vérification email : " . $conn->error);
    }
    if (!$stmtCheck->bind_param("s", $email)) {
        die("Erreur de bind_param pour vérification email : " . $stmtCheck->error);
    }
    if (!$stmtCheck->execute()) {
        die("Erreur d'exécution de la vérification email : " . $stmtCheck->error);
    }
    $resultCheck = $stmtCheck->get_result();
    if ($resultCheck->num_rows > 0) {
        $errors[] = "Cet email est déjà utilisé.";
        $stmtCheck->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO client (nom, prenom, domicile, email, numero, mot_de_passe, photo, date_ajout, age, sexe) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
        if (!$stmt) {
            die("Erreur de préparation de la requête d'insertion : " . $conn->error);
        }
        if (!$stmt->bind_param("ssssssssi", $nom, $prenom, $residence, $email, $numero, $hashedPwd, $photoName, $age, $sexe)) {
            die("Erreur de bind_param pour insertion : " . $stmt->error);
        }
        if (!$stmt->execute()) {
            die("Erreur d'exécution de l'insertion : " . $stmt->error);
        } else {
            echo "<script>alert('Inscription réussie. Vous pouvez maintenant vous connecter.'); window.location.href='index.php';</script>";
            $stmt->close();
            exit();
        }
    }
}

    // Si erreurs, les afficher
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="../boxicons-master/css/boxicons.min.css">
    <title>Page de connexion</title>
  </head>
  <body>
    <div class="container">
      <div class="forms-container">
        <div class="signin-signup">
        <form class="sign-in-form" method="POST">
            <h2 class="title">Connexion</h2>
            <div class="input-field">
              <i class='bx bx-user'></i>
              <input type="text" placeholder="email" name="email" required />
            </div>
            <div class="input-field">
              <i class='bx bx-lock'></i>
              <input type="password" placeholder="mot de passe" name="password" required />
            </div>
            <input type="submit" value="Se connecter" class="btn solid" name="connection"/>
            <p class="social-text">Se connecter avec d'autres plateformes</p>
            <div class="social-media">
              <a href="https://www.facebook.com" class="social-icon" target="_blank">
                <i class='bx bxl-facebook'></i>
              </a>
              <a href="https://twitter.com" class="social-icon" target="_blank">
                <i class='bx bxl-twitter'></i>
              </a>
              <a href="https://www.google.com" class="social-icon" target="_blank">
                <i class='bx bxl-google'></i>
              </a>
              <a href="https://www.linkedin.com" class="social-icon" target="_blank">
                <i class='bx bxl-linkedin'></i>
              </a>
            </div>
            <a href="../site web/index.php">je suis un visiteur</a>
          </form>
          <form class="sign-up-form" method="POST" enctype="multipart/form-data">
    <h2 class="title">Créer un Compte</h2>
    
    <div class="input-field">
        <i class='bx bx-user'></i>
        <input type="text" placeholder="Nom" required name="nom"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-user'></i>
        <input type="text" placeholder="Prénom" required name="prenom"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-envelope'></i>
        <input type="email" placeholder="Email" required name="email"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-phone'></i>
        <input type="text" placeholder="Numéro de téléphone" required name="numero"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-calendar'></i>
        <input type="number" placeholder="Âge" required name="age"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-lock'></i>
        <input type="password" placeholder="Mot de passe" required name="password"/>
    </div>
    
    <div class="input-field">
        <i class='bx bx-lock'></i>
        <input type="password" placeholder="Confirmer le mot de passe" required name="confirmPwd"/>
    </div>
    
    <select name="residence" id="residence" class="input-field" required>
        <option value="">Résidences</option>
        <option value="Mokolo">Mokolo</option>
        <option value="Nkoabang">Nkoabang</option>
        <option value="Nlongkak">Nlongkak</option>
        <option value="Abong-bang">Abong-bang</option>
        <option value="Mvog-Mbi">Mvog-Mbi</option>
        <option value="Mvog-Ada">Mvog-Ada</option>
        <option value="Bastos">Bastos</option>
        <option value="Centre commercial">Centre commercial</option>
    </select>
    
    <select name="sexe" id="sexe" class="input-field" required>
        <option value="">Sexe</option>
        <option value="F">F</option>
        <option value="M">M</option>
    </select>
    
    <div class="input-field">
        <i class='bx bx-upload'></i>
        <input type="file" name="photo" accept="image/*">
    </div>
    
    <input type="submit" class="btn" value="S'inscrire" name="sinscrire"/>
</form>       </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>Vous êtes nouveau ici ?</h3>
            <p>
              Créer votre compte en cliquant sue le boutons s'inscrire
            </p>
            <button class="btn transparent" id="sign-up-btn">
              S'inscrire
            </button>
          </div>
          <img src="img/log.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Un de nous ?</h3>
            <p>
              Acceder a votre tableau de bord en vous connectant
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Se connecter
            </button>
          </div>
          <img src="img/register.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="app.js"></script>
  </body>
</html>