

// Ce fichier gère l'affichage des plats sur la page d'accueil, les filtres et le panier
// Il utilise IndexedDB pour récupérer les plats et les afficher dynamiquement
let PlatsGlobal = [];

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const dbName = "RestoEtudiantDB";
        const version = 3;
        const request = indexedDB.open(dbName, version);

        request.onsuccess = (event) => {
            const db = event.target.result;

            const transaction = db.transaction(["plats"], "readonly");
            const store = transaction.objectStore("plats");
            const getPlats = store.getAll();

            getPlats.onsuccess = (event) => {
                PlatsGlobal = event.target.result;
                afficherPlatsAccueil(PlatsGlobal);
                activerFiltres();
                activerFiltres();
                rechercherPlats(); // Appel de la fonction de recherche
                mettreAJourCompteurPanier();
            };
            // Chargement des formules publiques
            const transactionFormules = db.transaction(["formules"], "readonly");
            const storeFormules = transactionFormules.objectStore("formules");
            const requeteFormules = storeFormules.getAll();

            requeteFormules.onsuccess = (event) => {
                const formules = event.target.result;
                afficherFormulesPubliques(formules);
            };

            requeteFormules.onerror = (event) => {
                console.error("Erreur lors de la récupération des formules :", event.target.error);
            };


            //bloc pour gerer les feches de recommandation 
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
            getPlats.onerror = (event) => {
                console.error("Erreur de récupération des plats :", event.target.error);
            };
        };

        request.onerror = (event) => {
            console.error("Erreur d'ouverture de la base de données:", event.target.error);
        };

    } catch (error) {
        console.error("Erreur lors de l'initialisation de la base de données :", error);
    }
});

// Fonction pour afficher les plats sur la page d'accueil
// Cette fonction prend en paramètre un tableau de plats et les affiche dans le conteneur approprié
function afficherPlatsAccueil(plats) {
    const container = document.querySelector(".details-wrapper");

    if (!container) {
        console.error("Conteneur '.details-wrapper' introuvable !");
        return;
    }

    container.innerHTML = ""; // Vide les anciennes cartes

    const compteurElement = document.getElementById("compteur-plats");
    compteurElement.textContent = `${plats.length} plats disponibles`;

    plats.forEach((plat) => {
        const card = document.createElement("div");
        card.classList.add("details-card");

        card.innerHTML = `
            <img src="${plat.imageUrl}" alt="${plat.nom}" class="details-img">
            <div class="detail-desc">
                <div class="details-name">
                    <h4> ${plat.nom}</h4>
                    <p class="details-sub">Description : ${plat.description}</p>
                    <p class="plat-prix">Prix : <strong>${plat.prix || "?"} $</strong></p>
                    <p class="plat-categorie">Catégorie : <strong>${plat.categorie || "Non definie"}</strong></p>
                    <p class="contenu">
                        <a href="#" aria-label="Voir les détails du plat ${plat.nom}">
                            <span class="material-icons">visibility</span>
                        </a>
                    </p>
                    <button class = "btn-ajout-panier" data-id="${plat.id}">Ajouter au panier</button>
                </div>
                
            </div>
        `;

        container.appendChild(card);
    });

    const boutonsAjouter = document.querySelectorAll(".btn-ajout-panier");

    boutonsAjouter.forEach((btn, index) => {
        btn.addEventListener("click", () => {
            const plat = plats[index];
            ajouterAuPanier(plat);


        });
    });

}

// Fonction pour activer les filtres
// Cette fonction ajoute des écouteurs d'événements aux filtres pour filtrer les plats affichés
function activerFiltres() {
    const filtres = document.querySelectorAll(".filtre-wrapper .flitre-card p");

    filtres.forEach((filtre) => {
        filtre.addEventListener("click", () => {
            const categorie = filtre.textContent.trim();

            if (categorie.toLowerCase() === "toutes les cuisines") {
                afficherPlatsAccueil(PlatsGlobal); // Affiche tous les plats
            } else {
                const platsFiltres = PlatsGlobal.filter((plat) =>
                    plat.categorie && plat.categorie.toLowerCase().includes(categorie.toLowerCase())
                );
                afficherPlatsAccueil(platsFiltres); // Affiche les plats filtrés
            }
        });
    });
}

// Fonction pour gérer le panier
// Cette fonction gère l'ajout de plats au panier et la mise à jour du compteur de panier
function getPanier() {
    const panier = localStorage.getItem("panier");
    return panier ? JSON.parse(panier) : [];
}


// Fonction pour ajouter un plat au panier
// Cette fonction ajoute un plat au panier et met à jour le compteur de panier
function ajouterAuPanier(plat) {
    const panier = getPanier();
    panier.push(plat);
    localStorage.setItem("panier", JSON.stringify(panier));
    mettreAJourCompteurPanier(); // Ajout ici pour mettre à jour après ajout
    alert("Plat ajouté au panier !");
}


// Fonction pour mettre à jour le compteur de panier
// Cette fonction met à jour le compteur de panier affiché sur la page
function mettreAJourCompteurPanier() {
    const panier = getPanier();
    const compteur = document.querySelector(".cart-count");

    if (compteur) {
        compteur.textContent = panier.length;

        if (panier.length > 0) {
            compteur.style.display = "inline-block";
        } else {
            compteur.style.display = "none";
        }
    }
}

//fonction recherche

function rechercherPlats() {
    const inputRecherche = document.getElementById("search-input");
    const boutonRecherche = document.querySelector(".recherche-btn");

    const boutonClear = document.getElementById("clear-search");

    const filtrer = () => {
        const terme = inputRecherche.value.trim().toLowerCase();

        boutonClear.style.display = terme.length > 0 ? "block" : "none"; // Affiche ou cache le bouton clear

        if (terme === "") {
            afficherPlatsAccueil(PlatsGlobal); // Affiche tous les plats si le champ de recherche est vide
            return;
        }

        const platsFiltres = PlatsGlobal.filter((plat) =>
            plat.nom?.toLowerCase().includes(terme) ||
            plat.description?.toLowerCase().includes(terme) ||
            plat.categorie?.toLowerCase().includes(terme)
        );

        afficherPlatsAccueil(platsFiltres); // Affiche les plats filtrés
    };

    inputRecherche.addEventListener("input", filtrer); // Filtre à chaque saisie dans le champ de recherche
    boutonRecherche?.addEventListener("click", filtrer); // Filtre lorsque le bouton de recherche est cliqué

    if (boutonClear) {
        boutonClear.addEventListener("click", () => {
            inputRecherche.value = ""; // Vide le champ de recherche
            boutonClear.style.display = "none"; // Cache le bouton clear
            afficherPlatsAccueil(PlatsGlobal); // Affiche tous les plats
        });
    }
}

function afficherFormulesPubliques(formules) {
    const container = document.getElementById("liste-formules-public");
    if (!container) {
        console.warn("Conteneur #liste-formules-public introuvable.");
        return;
    }

    container.innerHTML = "";

    formules.forEach(formule => {
        const div = document.createElement("div");
        div.classList.add("carte-formule");

        div.innerHTML = `
            <img src="${formule.imageUrl}" alt="${formule.nom}" style="width: 120px; border-radius: 8px;">
            <h3>${formule.nom}</h3>
            <p><strong>${formule.prix.toFixed(2)} $</strong></p>
            <p>${formule.description}</p>
        `;
        container.appendChild(div);
    });
}



