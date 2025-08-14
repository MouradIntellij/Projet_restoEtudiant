document.addEventListener("DOMContentLoaded", () => {
    const etudiantTab = document.getElementById("etudiants-tab");
    const restaurateurTab = document.getElementById("restaurateurs-tab");
    const roleInput = document.getElementById("role");
    const title = document.getElementById("form-title");
    const emailLabel = document.getElementById("email-label");
    const emailInput = document.getElementById("email");
    const welcomeMsg = document.getElementById("welcome-msg");

    const updateFormForRole = (role, updateURL = true) => {
        const isEtudiant = role === "etudiant";

        title.textContent = isEtudiant ? "Connexion Étudiants" : "Connexion Restaurateurs";
        emailLabel.textContent = `Email ${isEtudiant ? "Étudiant" : "Restaurateur"}:`;
        emailInput.placeholder = isEtudiant ? "etudiant@exemple.com" : "restaurateur@exemple.com";
        welcomeMsg.innerHTML = `<strong>${isEtudiant ? "Bienvenue sur RestoEtudiant" : "Espace réservé aux restaurateurs partenaires"}</strong>`;
        roleInput.value = role;

        etudiantTab.classList.toggle("active", isEtudiant);
        restaurateurTab.classList.toggle("active", !isEtudiant);

        // 🔁 Mettre à jour l'URL
        if (updateURL) {
            const newURL = `${window.location.pathname}?role=${role}`;
            window.history.replaceState({}, '', newURL);
        }
    };

    // 🎯 Réagit aux clics sur les onglets
    etudiantTab.addEventListener("click", () => updateFormForRole("etudiant"));
    restaurateurTab.addEventListener("click", () => updateFormForRole("restaurateur"));

    // 🧠 Charge le rôle depuis l'URL si présent
    const urlParams = new URLSearchParams(window.location.search);
    const currentRole = urlParams.get("role")?.toLowerCase() || "etudiant";
    updateFormForRole(currentRole, false);

    // 👁️ Gestion visibilité mot de passe
    const togglePasswordBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener("click", () => {
            const isVisible = passwordInput.type === "text";
            passwordInput.type = isVisible ? "password" : "text";
            togglePasswordBtn.textContent = isVisible ? "visibility" : "visibility_off";
        });
    }
});
