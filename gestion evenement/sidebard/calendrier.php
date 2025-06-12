<?php
session_start();

// Connexion à la BDD
$mysqli = new mysqli("localhost", "root", "", "gestion_evenement");

// Vérification
if ($mysqli->connect_errno) {
    die("Échec de la connexion : " . $mysqli->connect_error);
}

// Récupérer l'ID utilisateur
$id_user = $_SESSION['id_user'];

// Requête pour obtenir les événements liés à cet utilisateur
$query = "SELECT e.date_debut, e.date_fin
          FROM contrat c
          JOIN evenement e ON c.id_evenement = e.id
          WHERE c.id_client = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
    // Convertir en objets DateTime pour manipuler facilement
    $dateDebut = new DateTime($row['date_debut']);
    $dateFin = new DateTime($row['date_fin']);

    // Ajouter toutes les dates entre date_debut et date_fin
    $period = new DatePeriod($dateDebut, new DateInterval('P1D'), $dateFin->modify('+1 day'));
    foreach ($period as $date) {
        $events[] = $date->format('Y-m-d');
    }
}

$stmt->close();
$mysqli->close();

// Encoder en JSON pour passer au JS
$dates_json = json_encode($events);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier Interactif</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: none;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .calendar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px; /* Augmenté la largeur */
            text-align: center;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        .day {
            padding: 15px; /* Augmenté le padding */
            background: #e0e0e0;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 1.2em; /* Agrandi le texte */
        }
        .day:hover {
            background: #d0d0d0;
        }
        .today {
            background: #ffcc00;
        }
        .holiday {
            color: red; /* Couleur rouge pour les jours fériés */
            font-weight: bold;
        }
        .day-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        /* Jour avec événement */
.event-day {
    background-color: #3399ff; /* Bleu */
    color: white; /* Si vous souhaitez aussi changer la couleur du texte */
}
    </style>
</head>
<body>

    <div class="calendar">
        <div class="header">
            <button id="prev">❮</button>
            <h2 id="monthYear"></h2>
            <button id="next">❯</button>
        </div>
        <div class="days" id="days"></div>
    </div>

    <!-- Inline JavaScript -->
    <script>
        // Variables globales
const monthYear = document.getElementById('monthYear');
const daysContainer = document.getElementById('days');
const prevButton = document.getElementById('prev');
const nextButton = document.getElementById('next');

// Récupérer la liste des dates d'événements liées à l'utilisateur (transmise par PHP)
const userEventDates = <?php echo $dates_json; ?> || []; // Si vide, tableau vide

// Fonction pour rendre le calendrier
function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    // Mettre à jour le mois et l'année
    monthYear.textContent = currentDate.toLocaleString('fr-FR', { month: 'long', year: 'numeric' });
    // Effacer le contenu précédent
    daysContainer.innerHTML = '';

    // Noms des jours de la semaine
    const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    dayNames.forEach(name => {
        const dayNameDiv = document.createElement('div');
        dayNameDiv.textContent = name;
        dayNameDiv.classList.add('day-name');
        daysContainer.appendChild(dayNameDiv);
    });

    // Calcul du premier jour du mois (0=Dimanche, 1=Lundi, ...)
    const firstDay = new Date(year, month, 1).getDay();
    // Nombre de jours dans le mois
    const lastDate = new Date(year, month + 1, 0).getDate();

    // Ajouter des jours vides pour aligner le début du mois (en fonction du premier jour)
    // La semaine commence lundi, donc on ajuste
    const emptyDaysCount = (firstDay === 0 ? 6 : firstDay - 1);
    for (let i = 0; i < emptyDaysCount; i++) {
        const emptyDay = document.createElement('div');
        daysContainer.appendChild(emptyDay);
    }

    // Ajouter les jours du mois
    for (let date = 1; date <= lastDate; date++) {
        const day = document.createElement('div');
        day.textContent = date;
        day.classList.add('day');

        // Format de la date : AAAA-MM-JJ
        const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;

        // Vérifier si cette date est dans la liste des événements de l'utilisateur
        if (userEventDates.includes(dateStr)) {
            day.classList.add('event-day');
        }

        // Vérifier si c'est un jour férié
        if (holidays[`${date}-${month + 1}`]) {
            day.classList.add('holiday');
            day.title = holidays[`${date}-${month + 1}`];
        }

        // Surbrillance pour aujourd'hui
        const today = new Date();
        if (
            date === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
        ) {
            day.classList.add('today');
        }

        daysContainer.appendChild(day);
    }
}

// Événements pour navigation
prevButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

nextButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Initialisation
const currentDate = new Date();

// Définition des jours fériés (exemple)
const holidays = {
    '1-1': 'Nouvel An',
    '5-1': 'Fête du Travail',
    '14-7': 'Fête Nationale',
    '25-12': 'Noël'
};

// Appel initial
renderCalendar();
    </script>

</body>
</html>