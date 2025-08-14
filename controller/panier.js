// /controller/panier.js

document.addEventListener("DOMContentLoaded", () => {
    afficherPanier();
});

function getPanier() {
    const panier = localStorage.getItem("panier");
    return panier ? JSON.parse(panier) : [];
}

function afficherPanier() {
    const panier = getPanier();
    const listePanier = document.getElementById("liste-panier");

    if (!listePanier) {
        console.error("Conteneur 'liste-panier' introuvable !");
        return;
    }

    listePanier.innerHTML = ""; // Vider avant de remplir

    if (panier.length === 0) {
        listePanier.innerHTML = "<p style='text-align: center;'>Votre panier est vide.</p>";
        return;
    }

    panier.forEach((plat, index) => {
        const div = document.createElement("div");
        div.classList.add("panier-item");

        div.innerHTML = `
            <img src="${plat.imageUrl}" alt="${plat.nom}" class="panier-img">
            <div class="panier-info">
                <h4>${plat.nom}</h4>
                <p>${plat.description}</p>
                <button class="btn-supprimer" data-index="${index}">
                    <span class="material-icons">delete</span> Supprimer
                </button>
            </div>
        `;

        listePanier.appendChild(div);
    });

    activerSuppression();
}

function activerSuppression() {
    const boutons = document.querySelectorAll(".btn-supprimer");

    boutons.forEach((bouton) => {
        bouton.addEventListener("click", (e) => {
            const index = e.currentTarget.getAttribute("data-index");
            supprimerDuPanier(index);
        });
    });
}

function supprimerDuPanier(index) {
    const panier = getPanier();
    panier.splice(index, 1); // Retirer 1 élément à l'index donné
    localStorage.setItem("panier", JSON.stringify(panier));
    afficherPanier(); // Recharger l'affichage
    mettreAJourCompteurPanier()
}

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
