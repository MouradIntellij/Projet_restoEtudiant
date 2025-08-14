import { Plat } from "../model/plat.js";
import { PlatService } from "../model/PlatService.js";
import { CommandeService } from "../model/CommandeService.js";
import { Formule } from "../model/Formule.js";
import { FormuleService } from "../model/FormuleService.js";

const platService = new PlatService();
const commandeService = new CommandeService();
const formuleService = new FormuleService();
const emailRestaurateur = "jean.dupont@test.com"; // Remplacez par l'email du restaurateur connecté


document.addEventListener("DOMContentLoaded", async () => {
    try {
        await platService.init();
        await formuleService.init();
        await commandeService.init();
        activerNavigationDashboard();

        const formAjout = document.getElementById("form-ajout-plats");
        const inputImage = document.getElementById("image-plat");
        const nomFichier = document.getElementById("nom-fichier");
        const aperçuImage = document.getElementById("aperçu-image");
        const btnAnnuler = document.getElementById("btn-annuler");



        const listePlatsCheckboxes = document.getElementById("liste-des-plats");
        const formFormule = document.querySelector(".form-ajout-formule");
        const inputNomFormule = document.getElementById("nom-formule");
        const inputPrixFormule = document.getElementById("prix-formule");
        const inputDescriptionFormule = document.getElementById("description-formule");
        const inputImageFormule = document.getElementById("image-formule");
        const champFichierNomFormule = document.getElementById("nom-fichier-formule");

        const boutonAnnulerFormule = document.getElementById("btn-annuler-formule");

        // Gestion du bouton Annuler la modification pour les formules
        boutonAnnulerFormule.addEventListener("click", () => {
            formFormule.reset();
            formFormule.removeAttribute("data-id-modification");
            champFichierNomFormule.textContent = "Aucun fichier choisi";
            inputImageFormule.value = null;

            // Décocher tous les plats sélectionnés
            const casesCochees = document.querySelectorAll("#liste-des-plats input[type='checkbox']");
            casesCochees.forEach(checkbox => checkbox.checked = false);

            // Réinitialiser le bouton submit
            formFormule.querySelector("button[type='submit']").textContent = "Ajouter la formule";

            // Cacher le bouton Annuler
            boutonAnnulerFormule.style.display = "none";
        });



        // Charger les plats du restaurateur connecté pour la section Formules
        const tousLesPlats = await platService.getAllPlats();

        // On filtre les plats du restaurateur connecté
        const platsRestaurateur = tousLesPlats.filter(p => p.emailRestaurateur === emailRestaurateur);

        // On les injecte dans la liste sous forme de checkboxes
        listePlatsCheckboxes.innerHTML = platsRestaurateur.map(plat => `
            <label>
                <input type="checkbox" value="${plat.id}"> ${plat.nom}
            </label>
        `).join('');


        // Gestion de l'aperçu de l'image
        inputImage.addEventListener("change", () => {
            if (inputImage.files.length > 0) {
                nomFichier.textContent = inputImage.files[0].name;

                const reader = new FileReader();
                reader.onload = (e) => {
                    aperçuImage.innerHTML = `<img src="${e.target.result}" alt="Aperçu de l'image" class="image-preview">`;
                };
                reader.readAsDataURL(inputImage.files[0]);
            } else {
                nomFichier.textContent = "Aucun fichier choisi";
                aperçuImage.innerHTML = "";
            }
        });

        // Gestion de l'aperçu de l'image pour la formule
        inputImageFormule.addEventListener("change", () => {
            if (inputImageFormule.files.length > 0) {
                champFichierNomFormule.textContent = inputImageFormule.files[0].name;
            } else {
                champFichierNomFormule.textContent = "Aucun fichier choisi";
            }
        });

        // Gestion du formulaire d'ajout de formule
        formFormule.addEventListener("submit", async (event) => {
            event.preventDefault();
            await ajouterFormule();
        });


        // Gestion du formulaire d'ajout/modification
        formAjout.addEventListener("submit", async (event) => {
            event.preventDefault();
            await ajouterPlat();


        });

        // Gestion du bouton annuler
        btnAnnuler.addEventListener("click", () => {
            formAjout.reset();
            formAjout.removeAttribute("data-id-modification");
            btnAnnuler.style.display = "none";
            nomFichier.textContent = "Aucun fichier choisi";
            aperçuImage.innerHTML = "";
        });

        // Chargement initial des plats et statistiques


        const btnDeconnexion = document.getElementById("nav-deconnexion");

        btnDeconnexion.addEventListener("click", async (event) => {
            event.preventDefault(); // Empêcher le comportement par défaut du lien

            const confirmation = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
            if (confirmation) {
                // Effacer les données de session
                localStorage.clear();
                sessionStorage.clear();

                // Rediriger vers la page de connexion
                window.location.href = "../login.php";
            }
        });

        await afficherPlats();
        await afficherStatistiques();
        await afficherFormules(); // Afficher les formules au chargement de la page
    } catch (error) {
        console.error("Erreur lors de l'initialisation :", error);
    }
});

async function afficherPlats() {
    try {
        const plats = await platService.getAllPlats();
        const container = document.getElementById("liste-plats");
        container.innerHTML = "";

        plats.forEach((plat) => {
            const div = document.createElement("div");
            div.classList.add("plat");

            div.innerHTML = `
                <img src="${plat.imageUrl}" alt="${plat.nom}" width="100">
                <h3>${plat.nom}</h3>
                <p>${plat.description}</p>
                <p><strong>Prix :</strong> $${plat.prix}</p>
                <p><strong>Date d'ajout :</strong> ${new Date(plat.dateAjout).toLocaleDateString()}</p>
                <button class="btn-supprimer" data-id="${plat.id}">Supprimer</button>
                <button class="btn-modifier" data-id="${plat.id}">Modifier</button>
            `;
            container.appendChild(div);
        });

        // Gestion des boutons supprimer
        const boutonsSupprimer = container.querySelectorAll(".btn-supprimer");
        boutonsSupprimer.forEach((button) => {
            button.addEventListener("click", async (event) => {
                const idPlat = Number(event.target.getAttribute("data-id"));
                if (confirm("Êtes-vous sûr de vouloir supprimer ce plat ?")) {
                    await platService.supprimerPlat(idPlat);
                    await afficherPlats();
                    await afficherStatistiques();


                }
            });
        });

        // Gestion des boutons modifier
        const boutonsModifier = container.querySelectorAll(".btn-modifier");

        boutonsModifier.forEach((button) => {
            button.addEventListener("click", async (event) => {
                const idPlat = Number(event.target.getAttribute("data-id"));
                const plat = await platService.getPlatById(idPlat);

                document.getElementById("nom-plat").value = plat.nom;
                document.getElementById("prix-plat").value = plat.prix;
                document.getElementById("description-plat").value = plat.description;

                const form = document.getElementById("form-ajout-plats");
                form.setAttribute("data-id-modification", plat.id);
                document.getElementById("btn-annuler").style.display = "block";
            });
        });
    } catch (error) {
        console.error("Erreur lors de l'affichage des plats :", error);
        alert("Une erreur est survenue lors de l'affichage des plats.");
    }
}

async function ajouterPlat() {
    const nom = document.getElementById("nom-plat").value;
    const prix = parseFloat(document.getElementById("prix-plat").value);
    const description = document.getElementById("description-plat").value;
    const imageInput = document.getElementById("image-plat");
    const fichier = imageInput.files[0];
    const form = document.getElementById("form-ajout-plats");
    const idModification = form.getAttribute("data-id-modification");

    if (!validerChampsFormulaire(nom, prix, description)) {
        return;
    }

    const reader = new FileReader();
    reader.onload = async (event) => {
        const imageUrl = event.target.result;

        if (idModification) {
            const platExistant = await platService.getPlatById(Number(idModification));
            const platModifie = {
                ...platExistant,
                nom,
                prix,
                description,
                imageUrl: fichier ? imageUrl : platExistant.imageUrl
            };

            await platService.updatePlat(platModifie);
            form.removeAttribute("data-id-modification");
            form.reset();
            await afficherPlats();
            await afficherFormules(); // Mettre à jour l'affichage des formules
            afficherMessageSucces("Plat modifié avec succès !");
        } else {
            const plat = new Plat({
                nom,
                prix,
                description,
                imageUrl,
                emailRestaurateur: emailRestaurateur
            });

            await platService.ajouterPlat(plat);
            form.reset();
            await afficherPlats();
            afficherMessageSucces("Plat ajouté avec succès !");
        }
    };
    reader.readAsDataURL(fichier);
}


function activerNavigationDashboard() {
    const sections = document.querySelectorAll(".dashboard-section");

    // Fonction pour afficher une section spécifique
    function afficherSection(id) {
        sections.forEach(section => {
            if (section.id === id) {
                section.classList.add("section-visible");
                section.classList.remove("section-hidden");
            } else {
                section.classList.add("section-hidden");
                section.classList.remove("section-visible");
            }
        });
    }

    // Vérifiez l'existence des boutons de navigation
    const navAccueil = document.getElementById("nav-accueil");
    const navPlats = document.getElementById("nav-plats");
    const navProfil = document.getElementById("nav-profil");
    const navFormules = document.getElementById("nav-formules");

    if (navAccueil) {
        navAccueil.addEventListener("click", () => afficherSection("section-accueil"));
    } else {
        console.warn("Le bouton de navigation 'Accueil' est introuvable.");
    }

    if (navPlats) {
        navPlats.addEventListener("click", () => afficherSection("section-plats"));
    } else {
        console.warn("Le bouton de navigation 'Plats' est introuvable.");
    }

    if (navProfil) {
        navProfil.addEventListener("click", () => afficherSection("section-profil"));
    } else {
        console.warn("Le bouton de navigation 'Profil' est introuvable.");
    }

    if (navFormules) {
        navFormules.addEventListener("click", () => afficherSection("section-formules"));
    } else {
        console.warn("Le bouton de navigation 'Formules' est introuvable.");
    }

    // Afficher une section par défaut (par exemple, "section-accueil")
    afficherSection("section-accueil");
}


async function afficherStatistiques() {
    try {
        const plats = await platService.getAllPlats();
        console.log("Plats récupérés :", plats);

        const commandes = await commandeService.getAllCommandes();
        console.log("Commandes récupérées :", commandes);

        // Mise à jour du nombre de plats
        const nbPlats = plats.length;
        document.getElementById("stat-plats").textContent = nbPlats;

        // Mise à jour du nombre de commandes
        const nbCommandes = commandes.length;
        document.getElementById("stat-commandes").textContent = nbCommandes;

        // Calcul du nombre total de plats vendus
        const nbPlatsVendus = commandes.reduce((total, commande) => total + (commande.quantite || 0), 0);
        document.getElementById("stat-commandes-vendus").textContent = nbPlatsVendus;

        // Calcul du plat préféré
        const compteurPlats = {};
        commandes.forEach(commande => {
            if (!compteurPlats[commande.plat]) {
                compteurPlats[commande.plat] = 0;
            }
            compteurPlats[commande.plat] += (commande.quantite || 0);
        });

        let platPrefere = "-";
        let maxCommande = 0;
        for (const plat in compteurPlats) {
            if (compteurPlats[plat] > maxCommande) {
                maxCommande = compteurPlats[plat];
                platPrefere = plat;
            }
        }

        document.getElementById("stat-plat-prefere").textContent = platPrefere;

        console.log("Statistiques mises à jour correctement");
    } catch (error) {
        console.error("Erreur dans afficherStatistiques :", error);
    }
}


function validerChampsFormulaire(nom, prix, description) {
    if (!nom || nom.trim() === "") {
        alert("Le nom du plat est requis.");
        return false;
    }
    if (isNaN(prix) || prix <= 0) {
        alert("Le prix doit être un nombre positif.");
        return false;
    }
    if (!description || description.trim() === "") {
        alert("La description est requise.");
        return false;
    }
    return true;
}

function afficherMessageSucces(texte) {
    const messageSucces = document.getElementById("message-succes");
    messageSucces.textContent = texte;
    messageSucces.style.display = "block";

    setTimeout(() => {
        messageSucces.style.display = "none";
    }, 2000);
}

// Fonction pour ajouter une formule
async function ajouterFormule() {
    const nom = inputNomFormule.value;
    const prix = parseFloat(inputPrixFormule.value);
    const description = inputDescriptionFormule.value;
    const fichier = inputImageFormule.files[0];
    const identifiantFormule = formFormule.getAttribute("data-id-modification");

    if (!nom || isNaN(prix) || !description || (!fichier && !identifiantFormule)) {
        alert("Tous les champs sont obligatoires.");
        return;
    }

    // Récupération des plats cochés
    const casesCochees = document.querySelectorAll("#liste-des-plats input[type='checkbox']:checked");
    const identifiantsPlats = Array.from(casesCochees).map(cb => Number(cb.value));

    if (identifiantsPlats.length === 0) {
        alert("Veuillez sélectionner au moins un plat.");
        return;
    }

    const traiterFormule = async (imageUrl) => {
        const nouvelleFormule = new Formule({
            id: identifiantFormule ? Number(identifiantFormule) : Date.now(),
            nom,
            prix,
            description,
            plats: identifiantsPlats,
            imageUrl,
            emailRestaurateur
        });

        if (identifiantFormule) {
            await formuleService.modifierFormule(nouvelleFormule);
            afficherMessageSucces("Formule modifiée avec succès !");
        } else {
            await formuleService.ajouterFormule(nouvelleFormule);
            afficherMessageSucces("Formule ajoutée avec succès !");
        }

        formFormule.reset();
        champFichierNomFormule.textContent = "Aucun fichier choisi";
        formFormule.removeAttribute("data-id-modification");
        formFormule.querySelector("button[type='submit']").textContent = "Ajouter la formule";
        await afficherFormules();
    };

    if (fichier) {
        const lecteur = new FileReader();
        lecteur.onload = (event) => {
            traiterFormule(event.target.result);
        };
        lecteur.readAsDataURL(fichier);
    } else {
        const formuleExistante = await formuleService.getFormuleById(Number(identifiantFormule));
        traiterFormule(formuleExistante.imageUrl);
    }
}


async function afficherFormules() {
    const container = document.getElementById("liste-formules");
    container.innerHTML = "";

    const formules = await formuleService.getAllFormules();
    const formulesRestaurateur = formules.filter(f => f.emailRestaurateur === emailRestaurateur);
    console.log("Formules restaurateur récupérées :", formulesRestaurateur);
    const plats = await platService.getAllPlats(); // Récupérer les plats pour afficher les noms

    formulesRestaurateur.forEach(formule => {
        const div = document.createElement("div");
        div.classList.add("formule");

        div.innerHTML = `
            <img src="${formule.imageUrl}" alt="${formule.nom}" width="100">
            <h3>${formule.nom}</h3>
            <p><strong>Prix :</strong> $${formule.prix}</p>
            <p><strong>Description :</strong> ${formule.description}</p>
            <p><strong>Plats associés :</strong> ${formule.plats.map(id => {
            const plat = plats.find(p => p.id === id);
            return plat ? plat.nom : "(plat introuvable)";
        }).join(', ')}</p>
            <button class="btn-supprimer-formule" data-id="${formule.id}">Supprimer</button>
            <button class="btn-modifier-formule" data-id="${formule.id}">Modifier</button>
            
        `;

        container.appendChild(div);

        // Gestion des boutons supprimer
        const boutonSupprimer = div.querySelector(".btn-supprimer-formule");
        boutonSupprimer.addEventListener("click", async () => {
            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette formule ?");
            if (confirmation) {
                const id = Number(boutonSupprimer.dataset.id);
                await formuleService.supprimerFormule(id);
                await afficherFormules(); // Rafraîchir la liste
            }
        });

        // Gestion des boutons modifier

        const boutonModifier = div.querySelector(".btn-modifier-formule");

        boutonModifier.addEventListener("click", async () => {
            const id = Number(boutonModifier.dataset.id);
            const formuleAModifier = await formuleService.getFormuleById(id);

            inputNomFormule.value = formuleAModifier.nom;
            inputPrixFormule.value = formuleAModifier.prix;
            inputDescriptionFormule.value = formuleAModifier.description;

            // Réinitialiser le champ fichier (remettre à vide)
            inputImageFormule.value = null;
            champFichierNomFormule.textContent = "Aucun fichier choisi";

            // Coche les cases correspondant aux plats de la formule
            const casesCheckbox = document.querySelectorAll("#liste-des-plats input[type='checkbox']");
            casesCheckbox.forEach(checkbox => {
                checkbox.checked = formuleAModifier.plats.includes(Number(checkbox.value));
            });

            // Signaler qu'on est en mode modification
            formFormule.setAttribute("data-id-modification", id);
            formFormule.querySelector("button[type='submit']").textContent = "Modifier la formule";
            boutonAnnulerFormule.style.display = "block";

        });


    });
}


