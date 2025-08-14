// index.js
let PlatsGlobal = [];
let CommandesGlobal = []; // Pour les commandes restaurateur

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const dbName = "RestoEtudiantDB";
        const version = 3;
        const request = indexedDB.open(dbName, version);

        request.onsuccess = (event) => {
            const db = event.target.result;

            if (window.role === 'etudiant') {
                // Récupérer plats et formules pour étudiants
                const transaction = db.transaction(["plats"], "readonly");
                const store = transaction.objectStore("plats");
                const getPlats = store.getAll();

                getPlats.onsuccess = (event) => {
                    PlatsGlobal = event.target.result;
                    afficherPlatsAccueil(PlatsGlobal);
                    activerFiltres();
                    rechercherPlats();
                    mettreAJourCompteurPanier();
                };

                const transactionFormules = db.transaction(["formules"], "readonly");
                const storeFormules = transactionFormules.objectStore("formules");
                const requeteFormules = storeFormules.getAll();

                requeteFormules.onsuccess = (event) => {
                    afficherFormulesPubliques(event.target.result);
                };

            } else if (window.role === 'restaurateur') {
                // Récupérer commandes à préparer (supposons stockées dans IndexedDB dans un store "commandes")
                const transactionCommandes = db.transaction(["commandes"], "readonly");
                const storeCommandes = transactionCommandes.objectStore("commandes");
                const getCommandes = storeCommandes.getAll();

                getCommandes.onsuccess = (event) => {
                    CommandesGlobal = event.target.result;
                    afficherCommandesRestaurateur(CommandesGlobal);
                };
            }

            // Gestion flèches recommandations (utile pour étudiant)
            const wrapper = document.querySelector(".highlight-wrapper");
            const btnBack = document.querySelector(".main-fleche .back");
            const btnNext = document.querySelector(".main-fleche .next");
            if (wrapper && btnBack && btnNext) {
                btnBack.addEventListener("click", () => {
                    wrapper.scrollBy({ left: -300, behavior: "smooth" });
                });
                btnNext.addEventListener("click", () => {
                    wrapper.scrollBy({ left: 300, behavior: "smooth" });
                });
            }
        };

        request.onerror = (event) => {
            console.error("Erreur d'ouverture de la base de données:", event.target.error);
        };

    } catch (error) {
        console.error("Erreur lors de l'initialisation de la base de données :", error);
    }
});

// ==== Fonctions pour Etudiant ====

function afficherPlatsAccueil(plats) {
    const container = document.querySelector(".details-wrapper");
    if (!container) return;
    container.innerHTML = "";

    const compteurElement = document.getElementById("compteur-plats");
    compteurElement.textContent = `${plats.length} plats disponibles`;

    plats.forEach((plat) => {
        const card = document.createElement("div");
        card.classList.add("details-card");
        card.innerHTML = `
            <img src="${plat.imageUrl}" alt="${plat.nom}" class="details-img" />
            <div class="detail-desc">
                <div class="details-name">
                    <h4>${plat.nom}</h4>
                    <p class="details-sub">Description : ${plat.description}</p>
                    <p class="plat-prix">Prix : <strong>${plat.prix ?? "?"} $</strong></p>
                    <p class="plat-categorie">Catégorie : <strong>${plat.categorie ?? "Non définie"}</strong></p>
                    <p class="contenu">
                        <a href="#" aria-label="Voir les détails du plat ${plat.nom}">
                            <span class="material-icons">visibility</span>
                        </a>
                    </p>
                    <button class="btn-ajout-panier" data-id="${plat.id}">Ajouter au panier</button>
                </div>
            </div>
        `;
        container.appendChild(card);
    });

    document.querySelectorAll(".btn-ajout-panier").forEach((btn) => {
        btn.addEventListener("click", () => {
            const platId = btn.getAttribute("data-id");
            const plat = PlatsGlobal.find(p => p.id == platId);
            if (plat) {
                ajouterAuPanier(plat);
            }
        });
    });
}

function activerFiltres() {
    const filtres = document.querySelectorAll(".filtre-wrapper .flitre-card p");
    filtres.forEach((filtre) => {
        filtre.addEventListener("click", () => {
            const categorie = filtre.textContent.trim().toLowerCase();
            if (categorie === "toutes les cuisines") {
                afficherPlatsAccueil(PlatsGlobal);
            } else {
                const platsFiltres = PlatsGlobal.filter(plat =>
                    plat.categorie?.toLowerCase().includes(categorie)
                );
                afficherPlatsAccueil(platsFiltres);
            }
        });
    });
}

function rechercherPlats() {
    const inputRecherche = document.getElementById("search-input");
    const btnEffacer = document.getElementById("clear-search");

    inputRecherche.addEventListener("input", () => {
        const recherche = inputRecherche.value.trim().toLowerCase();
        if (recherche === "") {
            afficherPlatsAccueil(PlatsGlobal);
            btnEffacer.style.display = "none";
        } else {
            const platsFiltres = PlatsGlobal.filter(plat =>
                plat.nom.toLowerCase().includes(recherche) ||
                plat.description.toLowerCase().includes(recherche) ||
                plat.categorie?.toLowerCase().includes(recherche)
            );
            afficherPlatsAccueil(platsFiltres);
            btnEffacer.style.display = "inline";
        }
    });

    btnEffacer.addEventListener("click", () => {
        inputRecherche.value = "";
        afficherPlatsAccueil(PlatsGlobal);
        btnEffacer.style.display = "none";
    });
}

function afficherFormulesPubliques(formules) {
    const container = document.getElementById("liste-formules-public");
    if (!container) return;
    container.innerHTML = "";

    formules.forEach((formule) => {
        if (formule.etat === "public") {
            const card = document.createElement("div");
            card.classList.add("formule-card");
            card.innerHTML = `
                <h3>${formule.nom}</h3>
                <p>${formule.description}</p>
                <p>Prix: ${formule.prix} $</p>
            `;
            container.appendChild(card);
        }
    });
}

function ajouterAuPanier(plat) {
    // Exemple simple de panier dans localStorage
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    panier.push(plat);
    localStorage.setItem("panier", JSON.stringify(panier));
    mettreAJourCompteurPanier();
    alert(`Plat "${plat.nom}" ajouté au panier !`);
}

function mettreAJourCompteurPanier() {
    const compteur = document.querySelector(".cart-count");
    if (!compteur) return;
    const panier = JSON.parse(localStorage.getItem("panier")) || [];
    compteur.textContent = panier.length;
}

// ==== Fonctions pour Restaurateur ====

function afficherCommandesRestaurateur(commandes) {
    const container = document.getElementById("liste-commandes");
    if (!container) return;

    if (commandes.length === 0) {
        container.innerHTML = "<p>Aucune commande en attente.</p>";
        return;
    }

    container.innerHTML = "";
    commandes.forEach((commande) => {
        const div = document.createElement("div");
        div.classList.add("commande-card");
        div.innerHTML = `
            <h4>Commande #${commande.id}</h4>
            <p>Étudiant: ${commande.nomEtudiant}</p>
            <p>Adresse de livraison: ${commande.adresseLivraison}</p>
            <p>Plats commandés:</p>
            <ul>
                ${commande.plats.map(p => `<li>${p.nom} (x${p.quantite})</li>`).join("")}
            </ul>
            <p>Statut: <strong>${commande.statut}</strong></p>
            <button class="btn-valider-commande" data-id="${commande.id}">Marquer comme préparée</button>
        `;
        container.appendChild(div);
    });

    document.querySelectorAll(".btn-valider-commande").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const idCommande = e.target.getAttribute("data-id");
            validerCommande(idCommande);
        });
    });
}

function validerCommande(idCommande) {
    // Ici tu peux ajouter le code pour mettre à jour la commande en DB IndexedDB ou via API
    alert(`Commande #${idCommande} marquée comme préparée.`);
    // Puis actualiser la liste des commandes (à implémenter)
}

