import { Utilisateur } from "../model/Utilisateur.js";
import { DataBaseService } from "../model/DatabaseService.js";
import { Restaurateur } from "../model/Restaurateur.js";

// Initialisation du service IndexedDB
const dbService = new DataBaseService("RestoEtudiantDB", 3);

async function initDatabase() {
    try {
        await dbService.init("Utilisateurs", "email");
        console.log("Base de données initialisée avec succès.");
    } catch (error) {
        console.error("Erreur lors de l'initialisation de la base de données :", error);
    }
}

// Fonction d'inscription appelée lors de la soumission du formulaire
window.inscription = async function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const cpassword = document.getElementById("cpassword").value;
    const role = document.getElementById("role").value;
    const tel =
        document.getElementById("tel1").value +
        document.getElementById("tel2").value +
        document.getElementById("tel3").value;

    let prenom = "";
    let nom = "";
    let universite = "";
    let annee = "";
    let carte = "";
    let nomRestaurant = "";
    let adresseRestaurant = "";

    if (role === "Etudiant") {
        prenom = document.getElementById("prenom").value.trim();
        nom = document.getElementById("nomEtudiant").value.trim();
        universite = document.getElementById("universite").value.trim();
        annee = document.getElementById("année_académique").value.trim();
        const fichierCarte = document.getElementById("carte_scolaire").files[0];
        carte = fichierCarte ? fichierCarte.name : "";
    } else if (role === "Restaurateur") {
        nom = document.getElementById("nomResponsable").value.trim();
        nomRestaurant = document.getElementById("nomRestaurant").value.trim();
        adresseRestaurant = document.getElementById("adresseRestaurant").value.trim();
    }

    const erreur = document.getElementById("erreur");
    const confirmation = document.getElementById("confirmation");
    erreur.textContent = "";
    confirmation.textContent = "";

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        erreur.textContent = "Adresse email invalide";
        return;
    }

    if (password !== cpassword) {
        erreur.textContent = "Les mots de passe ne correspondent pas";
        return;
    }

    if (!nom || !email || !password || !role || !tel) {
        erreur.textContent = "Veuillez remplir tous les champs obligatoires.";
        return;
    }

    let nouvelUtilisateur;
    if (role === "Etudiant") {
        if (!universite || !annee || !carte) {
            erreur.textContent = "Champs étudiant incomplets.";
            return;
        }
        nouvelUtilisateur = new Utilisateur({
            prenom,
            nom,
            email,
            motdepasse: password,
            telephone: tel,
            role,
            université: universite,
            annee,
            carte_scolaire: carte
        });
    } else if (role === "Restaurateur") {
        if (!nomRestaurant || !adresseRestaurant) {
            erreur.textContent = "Champs du restaurant manquants.";
            return;
        }
        nouvelUtilisateur = new Restaurateur({
            nomRestaurant,
            adresseRestaurant,
            email,
            numeroTelephone: tel,
            password
        });
    }

    try {
        await dbService.ajouter("Utilisateurs", nouvelUtilisateur);
        confirmation.textContent = "Inscription réussie.";
        document.getElementById("btn-submit").disabled = true;
        setTimeout(() => window.location.href = "connexion.html", 2000);
    } catch (error) {
        erreur.textContent = "Email déjà utilisé ou erreur d'inscription.";
        console.error(error);
        document.getElementById("btn-submit").disabled = false;
    }
};

// Fonction pour réinitialiser le formulaire
window.effacer = function () {
    document.getElementById("role").value = " ";
    document.getElementById("nomEtudiant")?.value = "";
    document.getElementById("nomResponsable")?.value = "";
    document.getElementById("prenom")?.value = "";
    document.getElementById("email").value = "";
    document.getElementById("password").value = "";
    document.getElementById("cpassword").value = "";
    document.getElementById("universite")?.value = "";
    document.getElementById("année_académique")?.value = "";
    document.getElementById("carte_scolaire").value = "";
    document.getElementById("nomRestaurant").value = "";
    document.getElementById("adresseRestaurant").value = "";
    document.getElementById("erreur").textContent = "";
    document.getElementById("confirmation").textContent = "";
    window.afficherChampsParRole();
};

// Fonction pour afficher dynamiquement les bons champs
window.afficherChampsParRole = function () {
    const role = document.getElementById("role").value;
    const afficher = (id, visible) => {
        const el = document.getElementById(id);
        if (el) el.style.display = visible ? "block" : "none";
    };
    afficher("fieldset-infos-personnelles", role);
    afficher("groupe-etudiant", role === "Etudiant");
    afficher("groupe-restaurateur", role === "Restaurateur");
    afficher("section-etudiant", role === "Etudiant");
    afficher("statut-etudiant", role === "Etudiant");
    afficher("section-restaurateur", role === "Restaurateur");
    afficher("message-indication", role === " ");
    const formActions = document.querySelector(".form-actions");
    if (formActions) {
        formActions.style.display = role === " " ? "none" : "block";
    }
};

// Lancer à l’ouverture de la page
window.addEventListener("DOMContentLoaded", () => {
    initDatabase();
    window.afficherChampsParRole();
});


// ==================== TEST MANUEL ====================
window.addEventListener("load", async () => {
    await initDatabase(); // Assure que la base est bien initialisée

    // Création manuelle d'un étudiant
    const utilisateurTest = new Utilisateur({
        prenom: "Jean",
        nom: "Dupont",
        email: "jean.dupont@test.com",
        motdepasse: "Test@1234",
        telephone: "5141234567",
        role: "Etudiant",
        université: "Université de Montréal",
        annee: "2024-2025",
        carte_scolaire: "carte.pdf"
    });

    try {
        await dbService.ajouter("Utilisateurs", utilisateurTest);
        console.log("✅ Utilisateur ajouté manuellement avec succès !");
    } catch (err) {
        console.error(" Erreur lors de l'ajout manuel :", err);
    }

    // Tu peux aussi tester un restaurateur :
    
    const restaurateurTest = new Restaurateur({
        nomRestaurant: "Pizza Pro",
        adresseRestaurant: "123 Rue des Gourmets",
        email: "chef@pizza.com",
        numeroTelephone: "5147654321",
        password: "Pizza@123"
    });

    try {
        await dbService.ajouter("Utilisateurs", restaurateurTest);
        console.log(" Restaurateur ajouté manuellement avec succès !");
    } catch (err) {
        console.error(" Erreur lors de l'ajout manuel :", error);
    }
    
});

