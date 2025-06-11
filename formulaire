<?php include 'config.php'; ?>
<?php
$lang = isset($_POST['lang']) ? $_POST['lang'] : 'fr';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $numero = $_POST['numero'];
    $email = $_POST['email'];
    $domicile = $_POST['domicile'];
    $categorie = $_POST['categorie'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $photo = $_FILES['photo']['name'];
    $cv = $_FILES['cv']['name'];
    $targetPhoto = "uploads/photos/" . basename($photo);
    $targetCV = "uploads/cv/" . basename($cv);

    move_uploaded_file($_FILES['photo']['tmp_name'], $targetPhoto);
    move_uploaded_file($_FILES['cv']['tmp_name'], $targetCV);

    $stmt = $pdo->prepare("INSERT INTO acteur (nom, prenom, sexe, numero, email, photo, domicile, categorie, mot_de_passe)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $sexe, $numero, $email, $photo, $domicile, $categorie, $mot_de_passe]);

    echo "<p style='color:green'>Acteur enregistré avec succès !</p>";
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $lang === 'en' ? 'Register Actor' : 'Enregistrement Acteur' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2><?= $lang === 'en' ? 'Actor Registration Form' : 'Formulaire d\'inscription des Acteurs' ?></h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label><?= $lang === 'en' ? 'First Name' : 'Prénom' ?> :</label>
        <input type="text" name="prenom" required>
        <label><?= $lang === 'en' ? 'Last Name' : 'Nom' ?> :</label>
        <input type="text" name="nom" required>
        <label><?= $lang === 'en' ? 'Phone Number' : 'Numéro' ?> :</label>
        <input type="text" name="numero" required>
        <label>Email :</label>
        <input type="email" name="email" required>
        <label><?= $lang === 'en' ? 'Gender' : 'Sexe' ?> :</label>
        <select name="sexe" required>
            <option value="">--<?= $lang === 'en' ? 'Select' : 'Sélectionner' ?>--</option>
            <option value="M"><?= $lang === 'en' ? 'Male' : 'Homme' ?></option>
            <option value="F"><?= $lang === 'en' ? 'Female' : 'Femme' ?></option>
        </select>
        <label><?= $lang === 'en' ? 'Location' : 'Domicile' ?> :</label>
        <select name="domicile" required>
            <option value="">--<?= $lang === 'en' ? 'Select' : 'Sélectionner' ?>--</option>
            <option value="Yaoundé">Yaoundé</option>
            <option value="Douala">Douala</option>
            <option value="Nkoabang">Nkoabang</option>
            <option value="Bafoussam">Bafoussam</option>
        </select>
        <label><?= $lang === 'en' ? 'Category' : 'Catégorie' ?> :</label>
        <select name="categorie" required>
            <option value="">--<?= $lang === 'en' ? 'Select' : 'Sélectionner' ?>--</option>
            <option value="traiteur">Traiteur</option>
            <option value="immobilier">Immobilier</option>
            <option value="dj">DJ</option>
            <option value="decorateur">Décorateur</option>
            <option value="securite">Sécurité</option>
            <option value="entretien">Entretien</option>
            <option value="chauffeur">Chauffeur</option>
            <option value="hotesse">Hôtesse</option>
            <option value="photographe">Photographe</option>
        </select>
        <label><?= $lang === 'en' ? 'Password' : 'Mot de passe' ?> :</label>
        <input type="password" name="mot_de_passe" required>
        <label><?= $lang === 'en' ? 'Upload Photo' : 'Téléverser une Photo' ?> :</label>
        <input type="file" name="photo" accept="image/*" required>
        <label><?= $lang === 'en' ? 'Upload CV' : 'Téléverser un CV' ?> :</label>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
        <label><?= $lang === 'en' ? 'Select Language' : 'Choisir la langue' ?> :</label>
        <select name="lang" onchange="this.form.submit()">
            <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>Français</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
        </select>
        <button type="submit" name="submit"><?= $lang === 'en' ? 'Add' : 'Ajouter' ?></button>
        <button type="reset"><?= $lang === 'en' ? 'Reset' : 'Réinitialiser' ?></button>
    </form>
</body>
</html>
