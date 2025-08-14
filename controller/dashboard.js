import { DataBaseService } from "../model/DatabaseService.js";
import { Commande } from "../model/CommandeService.js";
import { Utilisateur } from "../model/Utilisateur.js";




const dbService = new DataBaseService("utilisateursDB", 1);

//Etape 1: initialisation de la base de donnees
async function initDatabase() {
    //if (!verifierConnexion()) return; // Vérifie si l'utilisateur est connecté
    try {

        await dbService.init("Commandes", "numeroCommande", [
            { name: "restaurant", key: "restaurant", unique: false },
            { name: "plat", key: "plat", unique: false },
            { name: "date", key: "date", unique: false },
            { name: "montant", key: "montant", unique: false },
            { name: "statut", key: "statut", unique: false },
        ]);

        const email = sessionStorage.getItem('utilisateurConnecter')
        document.getElementById("utilisateur-connecte").textContent = email

        const commandes = await dbService.getAll("Commandes")
        afficherCommandes(commandes);

    } catch (error) {
        console.error("Erreur lors de l'initialisation de la base de donnees:", error);
    }
}

// Etape 2: Deconnexion de l'utilisateur
async function deconnexion() {
    sessionStorage.removeItem("utilisateurConnecter")
    window.location.href = "connexion.html"
}

// Etape 3: verification de connnexion de l'utilisateur
function verifierConnexion() {
    const email = sessionStorage.getItem("utilisateurConnecter")
    if (!email) {
        alert("Vous devez vous connecter pour acceder a cette page.")
        window.location.href = "connexion.html";
        return false;
    }
    return true;
}


// Etape 4: Reccuperation/affichage des commandes 
function afficherCommandes(commandes) {
    const tbody = document.getElementById("orders-list")
    tbody.innerHTML = ""; // vider le tableau avant de l'afficher
    console.log("commandes ", commandes);

    if (!commandes || commandes.length === 0) {
        const tr = document.createElement("tr")
        tr.innerHTML = "<td colspan='6'>Aucune commande trouvée</td>";
        tbody.appendChild(tr);
        return;
    }
    commandes.forEach((commande) => {
        const tr = document.createElement("tr")
        tr.innerHTML =
            `
            <td>${commande.numeroCommande}</td>
            <td>${commande.restaurant}</td>
            <td>${commande.plat}</td>
            <td>${commande.date}</td>
            <td>${commande.montant}</td>
            <td class="${commande.statut.toLowerCase()}">${commande.statut}</td>
            <td class="actions">
                <button class="btn-modifier" onclick="redirigerVersChoix('${commande.numeroCommande}')">Modifier</button>
                <button class="btn-supprimer" onclick="supprimerCommande('${commande.numeroCommande}')">Supprimer</button>
            </td>
        `
        tbody.appendChild(tr)
    });
    return
}

// Etape 5: Suppression d'une commande
async function supprimerCommande(numeroCommande) {
    const confirmation = confirm("Voulez-vous vraiment supprimer cette commande ?")
    if (confirmation) {
        try {
            await dbService.deleteById("Commandes", numeroCommande)
            alert("Commande supprimée avec succès")
            const commandes = await dbService.getAll("Commandes")
            afficherCommandes(commandes)
        } catch (error) {
            console.error("Erreur lors de la suppression de la commande:", error)
        }
    }
}


// Etape : affichage les info de l'utilisateurs connecté
function afficherUtilisateurConnecte() {
    const email = sessionStorage.getItem("utilisateurConnecter")
    const utilisateurElem = document.getElementById("utilisateur-connecte");
    if (utilisateurElem && email) {
        utilisateurElem.textContent = email;
    }
}

// Etape : affichage du message de bienvenue sur la page d'accueil
async function afficherBienvenueAccueil() {
    const email = sessionStorage.getItem("utilisateurConnecter");

    if (email) {

        try {
            const utilisateur = await dbService.getById("Utilisateurs", email);
            if (!utilisateur) return;

            const prenom = utilisateur.prenom;
            const message = `Bienvenue ${prenom} dans votre espace personnel`;
            document.getElementById("accueil-bienvenue").textContent = message;

        } catch (error) {
            console.error("Erreur lors du chargement du profil:", error)
        }

    }

}


async function afficherStatistiquesCommandes() {
    const email = sessionStorage.getItem("utilisateurConnecter");

    if (!email) return; // Vérifie si l'utilisateur est connecté

    try {
        const commandes = await dbService.getAll("Commandes")

        const commandesUtilisateur = commandes.filter(commande => commande.email === email);

        const total = commandesUtilisateur.length;
        const enCours = commandesUtilisateur.filter(commande => commande.statut === "en cours").length;
        const livree = commandesUtilisateur.filter(commande => commande.statut === "livrée").length;

        const compteurResto = {};
        commandesUtilisateur.forEach(commande => {
            compteurResto[commande.restaurant] = (compteurResto[commande.restaurant] || 0) + 1;
        });

        const restoFavori = Object.entries(compteurResto).sort(((a, b) => b[1] - a[1]))[0]?.[0] || "Aucun favori trouvé";

        document.getElementById("total").textContent = total;
        document.getElementById("en-cours").textContent = enCours;
        document.getElementById("livree").textContent = livree;
        document.getElementById("resto-favori").textContent = restoFavori;
    } catch (error) {
        console.error("Erreur lors du calcul des statistiques:", error);
    }
}

// Etape : Redirection vers la page de choix de commande
function redirigerVersChoix(numeroCommande) {

    if (confirm("Voulez-vous modifier cette commande ?")) {

        sessionStorage.setItem("commandeAModifier", numeroCommande);
        window.location.href = "index.html"
    }
}

/**
 * gerer le bouton profil 
 * Quand tu cliques sur le lien Profil, a section profil-utilisateur s’affiche,
 * et la section commandes disparait.
 */

document.getElementById("profil-clic").addEventListener("click", (event) => {
    event.preventDefault(); // Empêche le comportement par défaut du lien

    //masquer toutes les sections
    document.querySelectorAll("section").forEach((section) => {
        section.style.display = "none"; // Cache toutes les sections
    });

    // Afficher la section profil-utilisateur
    document.querySelector(".profil-utilisateur").style.display = "block"; // Affiche la section profil-utilisateur

    // charger les informations de l'utilisateur connecté
    chargerProfilUtilisateur()
});


document.getElementById("commandes-clic").addEventListener("click", event => {
    event.preventDefault(); // Empêche le comportement par défaut du lien

    document.querySelectorAll("section").forEach(section => {
        section.style.display = "none"; // Cache toutes les sections
    });

    document.querySelector(".commandes-section").style.display = "block"; // Affiche la section commandes
    dbService.getAll("Commandes").then(commandes => {
        afficherCommandes(commandes); // Affiche les commandes
    });
})



async function chargerProfilUtilisateur() {
    if (!verifierConnexion()) return; // Vérifie si l'utilisateur est connecté

    const email = sessionStorage.getItem("utilisateurConnecter");

    try {
        const utilisateur = await dbService.getById("Utilisateurs", email)
        if (!utilisateur) return; // Si l'utilisateur n'existe pas, on ne fait rien{

        document.getElementById("profil-université").textContent = utilisateur.universite
        document.getElementById("profil-annee").textContent = utilisateur.annee
        document.getElementById("profil-nom").textContent = utilisateur.nom
        document.getElementById("profil-prenom").textContent = utilisateur.prenom
        document.getElementById("profil-email").textContent = utilisateur.email
        document.getElementById("profil-telephone").textContent = utilisateur.telephone
        document.getElementById("profil-adresse").textContent = utilisateur.adresse

    } catch (error) {
        console.error("Erreur lors du chargement du profil:", error)
    }
}


//Partie ACCUEIL

document.getElementById("accueil-clic").addEventListener("click", (event) => {
    event.preventDefault(); // Empêche le comportement par défaut du lien

    //Masquer toutes les sections
    document.querySelectorAll("section").forEach(section => {
        section.style.display = "none"; // Cache toutes les sections
    });

    // Afficher la section accueil
    document.querySelector(".accueil-section").style.display = "block"; // Affiche la section accueil

    afficherBienvenueAccueil(); // Affiche le message de bienvenue
    afficherStatistiquesCommandes(); // Affiche les statistiques des commandes
});












window.addEventListener("DOMContentLoaded", async () => {
    await initDatabase(); // Initialise la base de données
    afficherUtilisateurConnecte(); // Affiche l'utilisateur connecté
    afficherBienvenueAccueil(); // Affiche le message de bienvenue
    afficherStatistiquesCommandes(); // Affiche les statistiques des commandes

    document.querySelectorAll("section").forEach(section => {
        section.style.display = "none"; // Cache toutes les sections
    });

    document.querySelector(".accueil-section").style.display = "block"; // Affiche la section accueil par défaut

});

window.deconnexion = deconnexion
window.afficherUtilisateurConnecte = afficherUtilisateurConnecte
window.afficherCommandes = afficherCommandes
window.redirigerVersChoix = redirigerVersChoix
window.supprimerCommande = supprimerCommande



