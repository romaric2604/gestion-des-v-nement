<?php
// Connexion à la base de données (même configuration que précédemment)
$host = 'localhost';
$dbname = 'gestion_evenement';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer toutes les images et descriptions
$stmt = $pdo->query("SELECT nom, description, photo FROM photo_evenement");
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Responsive Event Organizer Website Design Tutorial</title>

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

     <style>
        .modal {
            display: none; /* Masquer par défaut */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 40px;
            color: white;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }
        .bx{
            color: purple;
            font-size: 30px;
            background:white;
            border-radius:100%;
            transition: 0.5s;
        }
        .bx:hover{
            color: white;
            background:purple;
        }
    </style>
<link href='../boxicons-master/css/boxicons.min.css' rel='stylesheet'>  
</head>
<body>
    
<!-- header section starts  -->

<header class="header">

    <a href="#" class="logo"><span>e</span>vento</a>

    <nav class="navbar">
        <a href="#home">home</a>
        <a href="#service">service</a>
        <a href="#about">about</a>  
        <a href="#gallery">gallery</a>
        <a href="#review">review</a>
        <a href="#contact">contact</a>
    </nav>
    <a href="../page de connexion/"><i class='bx bx-user'></i></a>

    <div id="menu-bars" class="fas fa-bars"></div>

</header>

<!-- header section ends -->

<!-- home section starts  -->

<section class="home" id="home">

    <div class="content">
        <h3>its time to celebrate! the best <span> event organizers </span></h3>
        <a href="#contact" class="btn">contact us</a>
    </div>

    <div class="swiper-container home-slider">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="images/aniv1.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-1.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/mariage1.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-2.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/mariage2.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-3.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/enter2.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-4.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/mariage3.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-5.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/aniv12.png" alt=""></div>
            <div class="swiper-slide"><img src="images/slide-6.jpg" alt=""></div>
            <div class="swiper-slide"><img src="images/enter1.png" alt=""></div>
        </div>
    </div>

</section>

<!-- home section ends -->

<!-- service section starts  -->

<section class="service" id="service">

    <h1 class="heading"> our <span>services</span> </h1>

    <div class="box-container">

        <div class="box">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Salle de reservation</h3>
            <p>Réservez votre salle facilement et rapidement pour tous vos événements spéciaux.</p>
        </div>

        <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>invitation card</h3>
            <p>Créez des cartes d'invitation élégantes et personnalisées pour impressionner vos invités.</p>
        </div>

        <div class="box">
            <i class="fas fa-music"></i>
            <h3>Musique et animation</h3>
            <p>Profitez d'animations musicales dynamiques pour animer votre célébration avec style.</p>
        </div>

        <div class="box">
            <i class="fas fa-utensils"></i>
            <h3>food and drinks</h3>
            <p>Dégustez une sélection raffinée de mets et de boissons pour satisfaire tous les goûts.</p>
        </div>

        <div class="box">
            <i class="fas fa-photo-video"></i>
            <h3>photos and videos</h3>
            <p>Capturez chaque instant précieux avec notre service professionnel de photographie et vidéo.</p>
        </div>

        <div class="box">
            <i class="fas fa-birthday-cake"></i>
            <h3>custom food</h3>
            <p>Offrez à vos invités une cuisine personnalisée et délicieuse adaptée à votre événement.</p>
        </div>

        <div class="box">
            <i class="fas fa-car"></i>
            <h3>Voiture et chauffeurs</h3>
            <p>Transportez-vous avec élégance grâce à nos voitures de luxe et nos chauffeurs professionnels.</p>
        </div>

       <div class="box">
    <i class="fas fa-shield-alt"></i>
    <h3>Sécurité</h3>
    <p>Assurez la protection de vos invités avec nos services de sécurité professionnels et discrets pour un événement en toute tranquillité.</p>
</div>

    </div>

</section>
<!-- service section ends -->

<!-- about section starts  -->

<section class="about" id="about">

<h1 class="heading"><span>about</span> us </h1>

<div class="row">

    <div class="image">
        <img src="images/about-img.jpg" alt="">
    </div>

    <div class="content">
    <h3>We will create a truly memorable celebration for you</h3>
    <p>Notre équipe dévouée met tout en œuvre pour organiser un événement exceptionnel, adapté à vos envies et à votre style. Que ce soit une fête intime ou une grande réception, chaque détail sera pensé pour vous offrir une expérience inoubliable.</p>
    <p>Confiez-nous votre projet et laissez-nous transformer votre vision en réalité, avec professionnalisme, créativité et passion. Ensemble, faisons de votre événement un moment magique que vous et vos invités n'oublieront jamais.</p>
    <p>Si vous souhaitez être notre colaborateur cliquer sur ce boutons pour laisser votre CV et vos information !!!</p>
    <a href="acteurs.php" class="btn">Contactez-nous</a>
</div>
</div>

</section>

<!-- about section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

    <h1 class="heading">our <span>gallery</span></h1>

    <div class="box-container">

       <?php
// Vérifier si on a des résultats
if ($photos) {
    foreach ($photos as $photo) {
        // Chemin complet de l'image
        $imgPath = '../images/' . htmlspecialchars($photo['photo']);
        // Nom et description
        $nom = htmlspecialchars($photo['nom']);
        $description = htmlspecialchars($photo['description']);
        echo '<div class="box">';
        echo '<img src="' . $imgPath . '" alt="' . $nom . '">';
        echo '<h3 class="title">' . $nom . '';
        echo '<p>' . $description . '</p>';
        echo '</h3>';
        echo '<div class="icons">';
        echo '<a href="#" class="fas fa-heart"></a>';
        echo '<a href="#s" class="fas fa-eye" onclick="openModal(\'' . $imgPath . '\')"></a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>Aucune photo trouvée.</p>";
}
?>

<div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="img01">
    </div>

</section>

<section class="reivew" id="review"> 
    
    <h1 class="heading">client's <span>review</span></h1>

    <div class="review-slider swiper-container">

        <div class="swiper-wrapper">

   <?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_evenement');

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour récupérer tous les avis avec infos utilisateur (client ou acteur)
$sql = "
SELECT avis.*, 
       IF(avis.types='client', client.nom, acteur.nom) AS nom_user,
       IF(avis.types='client', client.prenom, acteur.prenom) AS prenom_user,
       IF(avis.types='client', client.email, acteur.email) AS email_user,
       IF(avis.types='client', client.photo, acteur.photo) AS user_photo,
       avis.date_ajout
FROM avis
LEFT JOIN client ON avis.id_user = client.id AND avis.types='client'
LEFT JOIN acteur ON avis.id_user = acteur.id AND avis.types='acteur'
ORDER BY avis.date_ajout DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur dans la requête : " . $conn->error);
}

// Parcourir et afficher chaque avis
while ($row = $result->fetch_assoc()) {
    $nomPrenom = htmlspecialchars($row['nom_user'] . ' ' . $row['prenom_user']);
    $email = htmlspecialchars($row['email_user']);
    $message = htmlspecialchars($row['avis']);
    $photo = htmlspecialchars($row['user_photo']); // nom ou chemin de l'image

    // Construire le chemin de l'image
    $imgSrc = '../images/' . $photo; // Assurez-vous que le chemin et le nom sont corrects

    echo '
    <div class="swiper-slide box">
        <i class="fas fa-quote-right"></i>
        <div class="user">
            <img src="' . $imgSrc . '" alt="">
            <div class="user-info">
                <h3>' . $nomPrenom . '</h3>
                <span>' . $email . '</span>
            </div>
        </div>
        <p>' . $message . '</p>
    </div>
    ';
}

$conn->close();
?>

    
</div>

        </div>

    </div>

</section>

<!-- review section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

    <h1 class="heading"> <span>contact</span> us </h1>

    <form action="">
        <div class="inputBox">
            <input type="text" placeholder="name">
            <input type="email" placeholder="email">
        </div>
        <div class="inputBox">
            <input type="number" placeholder="number">
            <input type="text" placeholder="subject">
        </div>
        <textarea name="" placeholder="your message" id="" cols="30" rows="10"></textarea>
        <input type="submit" value="send message" class="btn">
    </form>

</section>

<!-- contact section ends -->

<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>branches</h3>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> mumbai </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> jogeshwari </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> goregaon </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> navi mumbai </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> andheri </a>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#"> <i class="fas fa-arrow-right"></i> home </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> service </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> about </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> gallery </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> reivew </a>
            <a href="#"> <i class="fas fa-arrow-right"></i> contact </a>
        </div>

        <div class="box">
            <h3>contact info</h3>
            <a href="#"> <i class="fas fa-phone"></i> +237 691809314 </a>
            <a href="#"> <i class="fas fa-phone"></i> +237  658208944 </a>
            <a href="#"> <i class="fas fa-envelope"></i> evinazedieubeniromaric@gmail.com </a>
            <a href="#"> <i class="fas fa-envelope"></i> juniorolinga01@gmail.com </a>
            <a href="#"> <i class="fas fa-map-marker-alt"></i> yaoundé, Cameroun </a>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://web.facebook.com/?_rdc=1&_rdr#"> <i class="fab fa-facebook-f"></i> facebook </a>
            <a href="https://x.com/i/flow/single_sign_on"> <i class="fab fa-twitter"></i> twitter </a>
            <a href="https://www.instagram.com/?hl=fr"> <i class="fab fa-instagram"></i> instagram </a>
            <a href="https://www.linkedin.com/feed/?trk=guest_homepage-basic_google-one-tap-submit"> <i class="fab fa-linkedin"></i> linkedin </a>
        </div>

    </div>

    <div class="credit"> created by <span>MD.RAZI-SHAH</span> | all rights reserved </div>

</section>

<!-- footer section ends -->

<!-- theme toggler  -->

<div class="theme-toggler">

    <div class="toggle-btn">
        <i class="fas fa-cog"></i>
    </div>

    <h3>choose color</h3>

    <div class="buttons">
        <div class="theme-btn" style="background: #3867d6;"></div>
        <div class="theme-btn" style="background: #f7b731;"></div>
        <div class="theme-btn" style="background: #ff0033;"></div>
        <div class="theme-btn" style="background: #20bf6b;"></div>
        <div class="theme-btn" style="background: #fa8231;"></div>
        <div class="theme-btn" style="background: #FC427B;"></div>
    </div>

</div>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script>
        function openModal(src) {
            document.getElementById("myModal").style.display = "flex"; // Afficher la modale
            document.getElementById("img01").src = src; // Changer l'image dans la modale
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none"; // Masquer la modale
        }

        // Fermer la modale lorsque l'utilisateur clique en dehors de l'image
        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>

</body>
</html>
