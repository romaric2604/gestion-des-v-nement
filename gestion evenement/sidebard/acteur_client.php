<?php
session_start();
// acteurs_par_categories.php
require_once '../bd_connection.php';

// Récupérer les catégories distinctes
$categories = [];
$sql_categories = "SELECT DISTINCT categorie FROM acteur ORDER BY categorie ASC";
$result_categories = $conn->query($sql_categories);

if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row['categorie'];
    }
}

// Variable pour la catégorie sélectionnée
$selected_category = '';
$acteurs = [];

if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    // Utiliser une requête préparée pour la sécurité
    $stmt = $conn->prepare("SELECT p.id, p.nom, p.prenom, p.numero, p.email, p.domicile, p.photo, AVG(e.nombre) AS moyenne_etoile
        FROM acteur p
        LEFT JOIN etoile e ON p.id = e.id_acteur
        WHERE p.categorie = ?
        GROUP BY p.id
        ORDER BY moyenne_etoile DESC, p.nom ASC");
    $stmt->bind_param("s", $_GET['categorie']);
    $stmt->execute();
    $result_acteurs = $stmt->get_result();

    if ($result_acteurs && $result_acteurs->num_rows > 0) {
        while ($row = $result_acteurs->fetch_assoc()) {
            $acteurs[] = $row;
        }
    }
    $selected_category = $_GET['categorie'];
}
if (isset($_POST['id'])) {
    $x = $_POST['id'];
    $_SESSION['id_acteur']=$x;
    echo "<script>
            window.location.href='liste_apercus_client.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Gestion Événements - Acteurs par Catégorie</title>
<link rel="stylesheet" href="css/style1.css" />
<style>
/* Inclure votre CSS ici, inchangé, ou garder le lien externe si préféré */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: none;
    color: #333;
}
header {
    background-color: rgb(107, 92, 165);
    color: white;
    padding: 1rem 0;
    text-align: center;
}
header h1 {
    margin: 0;
}
nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
}
main {
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.category-selection {
    text-align: center;
    margin-bottom: 30px;
}
.category-selection select {
    padding: 10px 15px;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 10px;
}
.actors-list h2 {
    text-align: center;
    color: rgb(99, 86, 182);
    margin-bottom: 25px;
}
.actors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    justify-content: center;
}
.actor-card {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s ease-in-out;
}
.actor-card:hover {
    transform: translateY(-5px);
}
.profile-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 3px solid rgb(84, 60, 131);
}
.actor-card h3 {
    margin-top: 0;
    color: #333;
    font-size: 1.3rem;
}
.actor-card p {
    margin: 5px 0;
    font-size: 0.95rem;
}
.actor-card p strong {
    color: #555;
}
.actor-card a {
    color: rgb(70, 59, 138);
    text-decoration: none;
}
.actor-card a:hover {
    text-decoration: underline;
}
button.contact-whatsapp-btn {
    background-color: rgb(84, 57, 204);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 15px;
    transition: background-color 0.2s ease;
}
button.contact-whatsapp-btn:hover {
    background-color: rgb(94, 75, 215);
}
footer {
    text-align: center;
    padding: 20px;
    background-color: #333;
    color: white;
    margin-top: 30px;
}
.icon {
            position: relative; /* Positionner l'icône */
            top: 10%; /* Distance du haut */
            right: 10%; /* Distance de la droite */
            font-size: 24px; /* Taille de l'icône */
            color: white; /* Couleur de l'icône */
        }
</style>
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
        <form method="GET">
            <label for="categorie_select">Catégorie :</label>
            <select name="categorie" id="categorie_select" onchange="this.form.submit()">
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($cat == $selected_category) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </section>

   <section class="actors-list">
    <form method="post">
        <?php if (!empty($selected_category)) : ?>
            <h2>Acteurs pour la catégorie : "<?php echo htmlspecialchars($selected_category); ?>"</h2>
            <?php if (!empty($acteurs)) : ?>
                <div class="actors-grid">
                    <?php foreach ($acteurs as $acteur) : ?>
                        <div class="actor-card"><br>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($acteur['id']) ?>">
                            <button class="icon" type="submit">+</button>
                            <?php
                            $photoPath = "../images/" . htmlspecialchars($acteur['photo']);
                            // Vérifier si la photo existe, sinon utiliser une image par défaut
                            if (!file_exists($photoPath) || empty($acteur['photo'])) {
                                $photoPath = "../images/default.jpg"; // Assurez d'avoir cette image
                            }
                            ?>
                            <img src="<?php echo $photoPath; ?>" alt="Photo de <?php echo htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']); ?>" class="profile-photo" />
                            <h3><?php echo htmlspecialchars($acteur['nom'] . ' ' . $acteur['prenom']); ?></h3>
                            <p><strong>Moyenne :</strong> <?php echo number_format($acteur['moyenne_etoile'], 1); ?> / 5</p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($acteur['numero']); ?></p>
                            <p><strong>Email :</strong> <a href="mailto:<?php echo htmlspecialchars($acteur['email']); ?>"><?php echo htmlspecialchars($acteur['email']); ?></a></p>
                            <p><strong>Domicile :</strong> <?php echo htmlspecialchars($acteur['domicile']); ?></p>
                            <button class="contact-whatsapp-btn" data-phone="<?php echo htmlspecialchars($acteur['numero']); ?>" data-nom="<?php echo htmlspecialchars($acteur['nom']); ?>" data-prenom="<?php echo htmlspecialchars($acteur['prenom']); ?>">
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
    </form>
</section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.contact-whatsapp-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const phone = this.dataset.phone;
            const nom = this.dataset.nom;
            const prenom = this.dataset.prenom;
            if (phone) {
                // Encode le message
                const message = encodeURIComponent(`Bonjour Mr/Mme, je suis ${nom} ${prenom} et j'aurais besoin de vos services pour mon évènement.`);
                const url = `https://wa.me/237${phone}?text=${message}`;
                window.open(url, '_blank');
            } else {
                alert("Numéro de téléphone non disponible pour cet acteur.");
            }
        });
    });
});
</script>
</body>
</html>

<?php
$conn->close();
?>