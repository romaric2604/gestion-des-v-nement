// js/script1.js

document.addEventListener('DOMContentLoaded', function() {
    const whatsappButtons = document.querySelectorAll('.contact-whatsapp-btn');

    whatsappButtons.forEach(button => {
        button.addEventListener('click', function() {
            const phoneNumber = this.dataset.phone; // Récupère le numéro du data-phone
            if (phoneNumber) {
                // Créer le lien WhatsApp.
                // Vous pouvez ajouter un message pré-rempli si vous voulez.
                const whatsappLink = https://wa.me/${phoneNumber}?text=Bonjour, je suis intéressé(e) par vos services pour un événement via votre application de gestion.;
                window.open(whatsappLink, '_blank'); // Ouvre le lien dans un nouvel onglet/fenêtre
            } else {
                alert("Numéro de téléphone non disponible pour cet acteur.");
            }
        });
    });
});