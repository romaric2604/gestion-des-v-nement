<?php
// Clé API Google Gemini (Gardez-la secrète !)
// NE PAS LA LAISSER DIRECTEMENT DANS UN REPO PUBLIC.
// Idéalement, utilisez une variable d'environnement ou un fichier de config non accessible web.
define('GEMINI_API_KEY', 'AIzaSyAYxzeqGBdcyquD7OFDTGfEdvZ_3VIR7d4'); // <<< REMPLACER CECI !

$ai_response = ''; // Pour stocker la réponse de l'IA
$user_question = ''; // Pour stocker la question de l'utilisateur

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_question'])) {
    $user_question = htmlspecialchars(trim($_POST['user_question']));

    if (!empty($user_question)) {
        $api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => "En tant qu'assistant de planification d'événements, je vais te donner des idées et des conseils. " . $user_question]
                    ]
                ]
            ]
        ];

        $ch = curl_init($api_endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourne la réponse au lieu de l'afficher directement
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Temporaire pour les problèmes de certificat en local, à enlever en production!
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Temporaire pour les problèmes de certificat en local, à enlever en production!

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $ai_response = '<div class="ai-response-error">Erreur cURL : ' . curl_error($ch) . '</div>';
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                $decoded_response = json_decode($response, true);
                if (isset($decoded_response['candidates'][0]['content']['parts'][0]['text'])) {
                    $ai_response = '<div class="ai-response-success">' . nl2br(htmlspecialchars($decoded_response['candidates'][0]['content']['parts'][0]['text'])) . '</div>';
                } else {
                    $ai_response = '<div class="ai-response-error">Impossible d\'obtenir une réponse de l\'IA. Code: ' . $http_code . ' Réponse: ' . htmlspecialchars($response) . '</div>';
                }
            } else {
                 $ai_response = '<div class="ai-response-error">Erreur API: Code HTTP ' . $http_code . ' Réponse: ' . htmlspecialchars($response) . '</div>';
            }
        }
        curl_close($ch);
    } else {
        $ai_response = '<div class="ai-response-info">Veuillez poser une question.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA de Planification - Evento</title>
    <link rel="stylesheet" href="css/style.css"> <style>
        /* Styles spécifiques pour l'assistant IA */
        .ai-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .ai-container h2 {
            text-align: center;
            color: #6a1b9a; /* Couleur violette pour l'IA */
            margin-bottom: 30px;
        }
        .ai-form textarea {
            width: calc(100% - 22px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
            min-height: 100px;
            resize: vertical;
        }
        .ai-form button {
            background-color: #7b1fa2; /* Violet plus foncé */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .ai-form button:hover {
            background-color: #6a1b9a;
        }
        .ai-response-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #f3e5f5; /* Violet très clair */
            border-left: 5px solid #9c27b0; /* Bordure violette */
            border-radius: 5px;
        }
        .ai-response-container p {
            margin: 0;
            line-height: 1.6;
            color: #333;
            white-space: pre-wrap; /* Préserve les retours à la ligne du texte de l'IA */
        }
        .ai-response-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .ai-response-success {
            background-color: #e6ffe6; /* Vert clair pour le succès */
            color: #218838;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .ai-response-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Assistant IA de Planification</h1>
        <nav>
            <a href="index.html">Accueil</a>
            <a href="acteurs_par_categorie.php">Professionnels</a>
            <a href="gerer_utilisateurs.php">Gérer les Utilisateurs</a>
            <a href="admin_dashboard.php">Tableau de Bord</a>
            <a href="ai_assistant.php">Assistant IA</a> </nav>
    </header>

    <main>
        <div class="ai-container">
            <h2>Discutez avec l'IA pour vos Événements !</h2>
            <form action="ai_assistant.php" method="POST" class="ai-form">
                <label for="user_question">Posez votre question ou demandez des idées :</label>
                <textarea id="user_question" name="user_question" placeholder="Ex: Quelles sont les étapes pour organiser un mariage ? Ou : Idées de traiteurs pour un événement d'entreprise." required><?php echo htmlspecialchars($user_question); ?></textarea>
                <button type="submit">Demander à l'IA</button>
            </form>

            <?php if (!empty($ai_response)): ?>
                <div class="ai-response-container">
                    <h3>Réponse de l'IA :</h3>
                    <?php echo $ai_response; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Votre Appli de Gestion Événements</p>
    </footer>
</body>
</html>