// controller/inscription.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('inscription-form');
    const messageDiv = document.getElementById('message');
    const submitButton = document.getElementById('btn-inscription');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Désactiver le bouton
        submitButton.disabled = true;
        submitButton.innerText = "En cours...";

        messageDiv.style.color = 'black';
        messageDiv.textContent = "Traitement de votre inscription...";

        const formData = new FormData(form);

        // Validation côté client
        const email = formData.get('email').trim();
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');

        if (!email || !password || password.length < 6) {
            messageDiv.style.color = 'red';
            messageDiv.textContent = "Veuillez entrer un email valide et un mot de passe d'au moins 6 caractères.";
            resetButton();
            return;
        }

        if (password !== confirmPassword) {
            messageDiv.style.color = 'red';
            messageDiv.textContent = "Les mots de passe ne correspondent pas.";
            resetButton();
            return;
        }

        try {
            const response = await fetch('php/register.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            console.log("Réponse du serveur:", text);

            if (response.ok && text.trim() === 'success') {
                messageDiv.style.color = 'green';
                messageDiv.textContent = "Inscription réussie ! Redirection en cours...";
                setTimeout(() => {
                    window.location.href = 'connexion.html';
                }, 2000);
            } else {
                messageDiv.style.color = 'red';
                messageDiv.textContent = text.trim();
            }
        } catch (error) {
            console.error("Erreur lors de l'inscription:", error);
            messageDiv.style.color = 'red';
            messageDiv.textContent = "Erreur technique lors de l'inscription.";
        } finally {
            resetButton();
        }
    });

    function resetButton() {
        submitButton.disabled = false;
        submitButton.innerHTML = `<span class="material-icons">person_add</span> S'inscrire`;
    }
});
