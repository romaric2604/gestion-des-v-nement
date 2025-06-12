<?php
// acteurs_par_categories.php
require_once '../bd_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupérer la recherche dans la session
$recherche = '';
if (isset($_SESSION['recherche']) && !empty($_SESSION['recherche'])) {
    $recherche = trim($_SESSION['recherche']);
}

// Préparer la requête pour récupérer tous les acteurs
$sql = "SELECT p.id, p.nom, p.prenom, p.numero, p.email, p.domicile, p.photo, AVG(e.nombre) AS moyenne_etoile
        FROM acteur p
        LEFT JOIN etoile e ON p.id = e.id_acteur";

// Si recherche est défini, ajouter une clause WHERE
if (!empty($recherche)) {
    $like_keyword = '%' . $recherche . '%';
    $sql .= " WHERE (p.nom LIKE ? OR p.prenom LIKE ? OR p.email LIKE ? OR p.domicile LIKE ? OR p.numero LIKE ?)";
}

$sql .= " GROUP BY p.id ORDER BY moyenne_etoile DESC, p.nom ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($recherche)) {
        // Lier les paramètres pour la recherche
        $stmt->bind_param("sssss", $like_keyword, $like_keyword, $like_keyword, $like_keyword, $like_keyword);
    }
    $stmt->execute();
    $result_acteurs = $stmt->get_result();

    $acteurs = [];
    if ($result_acteurs && $result_acteurs->num_rows > 0) {
        while ($row = $result_acteurs->fetch_assoc()) {
            $acteurs[] = $row;
        }
    }
}
?>
<?php
// Vérifier si la recherche est définie
$mot_cle = isset($_SESSION['recherche']) ? trim($_SESSION['recherche']) : '';

if (empty($mot_cle)) {
    die("Veuillez entrer un mot-clé pour la recherche.");
}

// Récupérer l'id utilisateur
$id_user = $_SESSION['id_user'];
if ($id_user === null) {
    die("Utilisateur non connecté");
}

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

// Préparer la requête avec recherche sur plusieurs colonnes
// Requête sans COLLATE
$sql = "
    SELECT e.* 
    FROM evenement e
    JOIN contrat c ON e.id = c.id_evenement
    JOIN acteur a ON c.id_acteur = a.id
    LEFT JOIN client cl ON c.id_client = cl.id
    WHERE 
        (a.id = :id_user OR cl.id = :id_user)
        AND (
            e.nom LIKE :search OR
            e.description LIKE :search OR
            e.categorie LIKE :search OR
            e.lieu LIKE :search OR
            e.place LIKE :search
        )
    ORDER BY e.date_debut DESC
";

$stmt = $pdo->prepare($sql);
$searchTerm = '%' . $mot_cle . '%';
$stmt->execute([':id_user' => $id_user, ':search' => $searchTerm]);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Acteurs selon recherche</title>
<style>
/* CSS */
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
main {
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
</style>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <section class="actors-list">
        <h2>Résultats de recherche pour "<?php echo htmlspecialchars($mot_cle); ?>"</h2>
        <?php if (!empty($acteurs)) : ?>
            <div class="actors-grid">
                <?php foreach ($acteurs as $acteur) : ?>
                    <div class="actor-card">
                        <?php
                        $photoPath = "../images/" . htmlspecialchars($acteur['photo']);
                        if (!file_exists($photoPath) || empty($acteur['photo'])) {
                            $photoPath = "../images/default.jpg";
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
            <p style="text-align:center;">Aucun acteur trouvé pour votre recherche.</p>
        <?php endif; ?>
<div class="events">
<?php if (empty($evenements)): ?>
    <p>Aucun événement trouvé.</p>
<?php else: ?>
    <?php foreach ($evenements as $event): ?>
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