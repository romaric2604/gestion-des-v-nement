<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Evento</title>
    <link rel="stylesheet" href="css/style.css"> <style>
        /* Ajoutez des styles spécifiques pour la page de contact si nécessaire */
        .contact-form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .contact-form-container h2 {
            text-align: center;
            color: #4CAF50; /* Ou la couleur de votre marque */
            margin-bottom: 30px;
        }
        .contact-form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .contact-form-container input[type="text"],
        .contact-form-container input[type="email"],
        .contact-form-container textarea {
            width: calc(100% - 22px); /* Ajuster la largeur en fonction du padding */
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box; /* Inclure padding et border dans la largeur */
        }
        .contact-form-container textarea {
            resize: vertical; /* Permet de redimensionner verticalement */
            min-height: 120px;
        }
        .contact-form-container button {
            background-color: #4CAF50; /* Couleur du bouton Envoyer */
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .contact-form-container button:hover {
            background-color: #45a049;
        }
        .message-status {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .message-status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <h1>Contacto Evento</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="acteurs.php">Professionnels</a>
            </nav>
    </header>

    <main>
        <div class="contact-form-container">
            <h2>CONTACT US</h2>
            <?php
            // Initialisation des messages de statut
            $message_status = '';

            // Le code PHP pour l'envoi d'email sera ici
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Inclure PHPMailer

                require 'libs/PHPMailer-master/src/Exception.php';
                require 'libs/PHPMailer-master/src/PHPMailer.php';
                require 'libs/PHPMailer-master/src/SMTP.php';

                $mail = new PHPMailer(true); // Passer 'true' active les exceptions

                // Récupérer les données du formulaire et les sécuriser
                $name = htmlspecialchars(trim($_POST['name']));
                $email = htmlspecialchars(trim($_POST['email']));
                $number = htmlspecialchars(trim($_POST['number'])); // Champ 'Number'
                $subject = htmlspecialchars(trim($_POST['subject']));
                $user_message = htmlspecialchars(trim($_POST['message'])); // Renommé pour éviter confusion

                // Vérifier si les champs obligatoires sont remplis
                if (empty($name) || empty($email) || empty($subject) || empty($user_message)) {
                    $message_status = '<div class="message-status error">Veuillez remplir tous les champs du formulaire.</div>';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message_status = '<div class="message-status error">L\'adresse email n\'est pas valide.</div>';
                } else {
                    try {
                        // Configuration du serveur SMTP (Gmail en exemple)
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';         // Serveur SMTP
                        $mail->SMTPAuth   = true;                     // Activer l'authentification SMTP
                        $mail->Username   = 'dimifopa2@gmail.com'; // Votre email Gmail
                        $mail->Password   = 'mahw zdzf gdmi pwxr';    // Votre mot de passe d'application Gmail
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
                        $mail->Port       = 587;                      // Port SMTP (587 pour TLS, 465 pour SSL)

                        // Destinataires
                        $mail->setFrom('dimifopa2@gmail.com', 'Votre Nom/Nom de l\'application'); // L'expéditeur
                        $mail->addAddress($email, $name); // Envoyer l'email À l'adresse que l'utilisateur a fournie dans le formulaire
                        // Si vous voulez envoyer une copie A VOUS-MÊME en plus, ajoutez :
                        // $mail->addAddress('votre_email_a_vous@example.com', 'Admin');

                        // Contenu de l'email
                        $mail->isHTML(true); // Définir le format de l'email en HTML
                        $mail->Subject = 'Nouveau message de contact: ' . $subject;
                        $mail->Body    = "
                            <p><strong>De :</strong> {$name}</p>
                            <p><strong>Email :</strong> {$email}</p>
                            <p><strong>Téléphone :</strong> {$number}</p>
                            <p><strong>Sujet :</strong> {$subject}</p>
                            <p><strong>Message :</strong><br>{$user_message}</p>
                        ";
                        $mail->AltBody = "De: {$name}\nEmail: {$email}\nTéléphone: {$number}\nSujet: {$subject}\nMessage: {$user_message}"; // Version texte brut

                        $mail->send();
                        $message_status = '<div class="message-status success">Votre message a été envoyé avec succès !</div>';
                    } catch (Exception $e) {
                        $message_status = '<div class="message-status error">Le message n\'a pas pu être envoyé. Erreur du serveur de messagerie : ' . $mail->ErrorInfo . '</div>';
                        // Pour le débogage, vous pouvez log l'erreur complète : error_log("Erreur PHPMailer: " . $e->getMessage());
                    }
                }
            }
            echo $message_status;
            ?>

            <form action="contact.php" method="POST">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" placeholder="Votre nom" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" placeholder="Votre email" required>

                <label for="number">Numéro :</label>
                <input type="text" id="number" name="number" placeholder="Votre numéro de téléphone">

                <label for="subject">Sujet :</label>
                <input type="text" id="subject" name="subject" placeholder="Sujet du message" required>

                <label for="message">Votre Message :</label>
                <textarea id="message" name="message" placeholder="Tapez votre message ici..." required></textarea>

                <button type="submit">Envoyer Message</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Votre Appli de Gestion Événements</p>
    </footer>
</body>
</html>