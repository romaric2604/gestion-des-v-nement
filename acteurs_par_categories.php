<?php
// acteurs_par_categories.php
require_once 'includes/db_connect.php'; // Inclure le fichier de connexion à la BD

// Récupérer les catégories distinctes pour le menu déroulant
$categories = [];
$sql_categories = "SELECT DISTINCT categorie FROM acteur ORDER BY categorie ASC"; // Assurez-vous que 'categorie' est le nom de votre colonne
$result_categories = $conn->query($sql_categories);

if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row['categorie'];
    }
}

// Récupérer les acteurs si une catégorie est sélectionnée
$selected_category = '';
$acteurs = [];

if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $selected_category = $conn->real_escape_string($_GET['categorie']); // Protéger contre les injections SQL

    // Requête pour récupérer les acteurs de la catégorie sélectionnée, triés par moyenne
    // Assurez-vous que les noms de tables et de colonnes correspondent à votre BD
    $sql_acteurs = "
        SELECT
            p.id,
            p.nom,
            p.prenom,
            p.numero_telephone,
            p.email,
            p.domicile,
            p.photo_profil,
            AVG(e.note) AS moyenne_etoile
        FROM
            acteur
        LEFT JOIN
            etoiles e ON p.id = e.id_acteur
        WHERE
            p.categorie = '$selected_category'
        GROUP BY
            p.id
        ORDER BY
            moyenne_etoile DESC, p.nom ASC"; // Tri par moyenne décroissante, puis par nom
            // Adaptez 'prestataires', 'etoiles', 'id_prestataire', 'note', 'id', 'nom', 'prenom', etc. à votre BD
    
    $result_acteurs = $conn->query($sql_acteurs);

    if ($result_acteurs && $result_acteurs->num_rows > 0) {
        while ($row = $result_acteurs->fetch_assoc()) {
            $acteurs[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Événements - Acteurs par Catégorie</title>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <header>
        <h1>Trouvez le Professionnel Idéal</h1>
        <nav>
            <a href="index.html">Retour à l'accueil</a>
        </nav>
    </header>

    <main>
        <section class="category-selection">
            <h2>Sélectionnez une catégorie d'événement</h2>
            <form action="acteurs_par_categories.php" method="GET">
                <label for="categorie_select">Catégorie :</label>
                <select name="categorie" id="categorie_select" onchange="this.form.submit()">
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo ($cat == $selected_category) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </section>

        <section class="actors-list">
            <?php if (!empty($selected_category)) : ?>
                <h2>Acteurs pour la catégorie : "<?php echo htmlspecialchars($selected_category); ?>"</h2>
                <?php if (!empty($acteurs)) : ?>
                    <div class="actors-grid">
                        <?php foreach ($acteurs as $acteur) : ?>
                            <div class="actor-card">
                                <img src="<?php echo htmlspecialchars($acteur['photo_profil']); ?>" alt="Photo de <?php echo htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']); ?>" class="profile-photo">
                                <h3><?php echo htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']); ?></h3>
                                <p><strong>Moyenne :</strong> <?php echo number_format($acteur['moyenne_etoile'], 1); ?> / 5</p>
                                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($acteur['numero_telephone']); ?></p>
                                <p><strong>Email :</strong> <a href="mailto:<?php echo htmlspecialchars($acteur['email']); ?>"><?php echo htmlspecialchars($acteur['email']); ?></a></p>
                                <p><strong>Domicile :</strong> <?php echo htmlspecialchars($acteur['domicile']); ?></p>
                                <button class="contact-whatsapp-btn" data-phone="<?php echo htmlspecialchars($acteur['numero_telephone']); ?>">
                                    Contacter sur WhatsApp
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>Aucun acteur trouvé pour cette catégorie.</p>
                <?php endif; ?>
            <?php else : ?>
                <p>Veuillez sélectionner une catégorie pour voir les acteurs.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Votre Appli de Gestion Événements</p>
    </footer>

    <script src="js/script1.js"></script>
</body>
</html>

<?php
$conn->close(); // Fermer la connexion à la base de données
?>