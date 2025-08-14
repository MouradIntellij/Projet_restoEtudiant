let PlatsGlobal = [], CommandesGlobal = [];

document.addEventListener("DOMContentLoaded", async () => {
    const dbName = "RestoEtudiantDB", version = 3;
    const request = indexedDB.open(dbName, version);

    request.onupgradeneeded = event => {
        const db = event.target.result;
        if (!db.objectStoreNames.contains("plats")) db.createObjectStore("plats", { keyPath: "id" });
        if (!db.objectStoreNames.contains("formules")) db.createObjectStore("formules", { keyPath: "id" });
        if (!db.objectStoreNames.contains("commandes")) db.createObjectStore("commandes", { keyPath: "id" });
    };

    request.onerror = e => console.error("IndexedDB open error:", e);

    request.onsuccess = async event => {
        const db = event.target.result;

        if (window.role === "etudiant") {
            await chargerPlatsFormules(db);

            afficherPlatsAccueil(PlatsGlobal);
            activerFiltres();
            rechercherPlats();
            afficherFormulesPubliques(PlatsGlobal);
            mettreAJourCompteurPanier();

            // Initialisation des formules une fois chargées
            initEtudiantUI();
        } else if (window.role === "restaurateur") {
            await chargerCommandes(db);
        }
    };
});

async function chargerPlatsFormules(db) {
    const txP = db.transaction("plats", "readonly"), storeP = txP.objectStore("plats");
    const countP = await new Promise(res => storeP.count().onsuccess = e => res(e.target.result));
    if (countP === 0) {
        const resp = await fetch("/Projet_restoEtudiant/api/get_data.php");
        const data = await resp.json();
        const txInP = db.transaction("plats", "readwrite"), storeInP = txInP.objectStore("plats");
        data.plats.forEach(p => storeInP.put(p));
        const txInF = db.transaction("formules", "readwrite"), storeInF = txInF.objectStore("formules");
        data.formules.forEach(f => storeInF.put(f));
        await Promise.all([txInP.complete, txInF.complete]);
    }
    storeP.getAll().onsuccess = e => PlatsGlobal = e.target.result;

    const txF = db.transaction("formules", "readonly"), storeF = txF.objectStore("formules");
    storeF.getAll().onsuccess = e => {
        window.FormulesGlobal = e.target.result;
    };
}

async function chargerCommandes(db) {
    const tx = db.transaction("commandes", "readonly"), store = tx.objectStore("commandes");
    const count = await new Promise(res => store.count().onsuccess = e => res(e.target.result));
    if (count === 0) {
        const resp = await fetch("/Projet_restoEtudiant/api/get_commandes.php");
        const data = await resp.json();
        if (data.commandes) {
            const txIn = db.transaction("commandes", "readwrite"), storeIn = txIn.objectStore("commandes");
            data.commandes.forEach(c => storeIn.put(c));
            await txIn.complete;
            CommandesGlobal = data.commandes;
            afficherCommandesRestaurateur(CommandesGlobal);
        }
    } else {
        store.getAll().onsuccess = e => {
            CommandesGlobal = e.target.result;
            afficherCommandesRestaurateur(CommandesGlobal);
        };
    }
}

function afficherCommandesRestaurateur(commandes) {
    const container = document.getElementById("liste-commandes");
    container.innerHTML = "";
    if (commandes.length === 0) {
        container.textContent = "Aucune commande en attente.";
        return;
    }
    commandes.forEach(cmd => {
        const div = document.createElement("div");
        div.className = "commande-card";
        div.innerHTML = `
            <h4>Commande #${cmd.id}</h4>
            <p>Étudiant : ${cmd.nomEtudiant}</p>
            <p>Adresse : ${cmd.adresseLivraison}</p>
            <p>Plats :</p><ul>${cmd.plats.map(p => `<li>${p.nom} (x${p.quantite})</li>`).join('')}</ul>
            <p>Statut : <strong>${cmd.statut}</strong></p>
            <button class="btn-valider-commande" data-id="${cmd.id}">Marquer comme préparée</button>
        `;
        container.appendChild(div);
    });
    container.querySelectorAll(".btn-valider-commande").forEach(btn => {
        btn.addEventListener("click", () => validerCommande(btn.getAttribute("data-id")));
    });
}

async function validerCommande(id) {
    try {
        const resp = await fetch("/Projet_restoEtudiant/api/valider_commande.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}`
        });
        const data = await resp.json();
        if (data.success) {
            alert(`Commande #${id} marquée comme préparée.`);
            const cmd = CommandesGlobal.find(c => c.id == id);
            if (cmd) cmd.statut = 'préparée';
            afficherCommandesRestaurateur(CommandesGlobal);
        } else {
            alert(`Erreur : ${data.message || data.error}`);
        }
    } catch (err) {
        console.error("Erreur validation commande", err);
    }
}

// ✅ Nouvelle fonction : appelée une fois les données bien chargées
function initEtudiantUI() {
    const container = document.querySelector('.details-wrapper');
    const compteur = document.getElementById('compteur-plats');

    function afficherFormules(liste) {
        container.innerHTML = '';

       compteur.textContent = `${liste.length} plats disponibles` ;

        liste.forEach(f => {
            const card = document.createElement('div');
            card.className = 'formule-card';
            card.innerHTML = `
                <h3>${f.titre}</h3>
                <p>${f.description}</p>
                <p>Prix: ${f.prix} $</p>
                <p>Cuisine: ${f.cuisine}</p>
            `;
            container.appendChild(card);
        });
    }

    afficherFormules(window.FormulesGlobal); // toutes les formules au chargement

    const filtreWrapper = document.querySelector('.wrapper-filtre');
    filtreWrapper.addEventListener('click', e => {
        const card = e.target.closest('.flitre-card');
        if (!card || !card.dataset.cuisine) return;

        const cuisine = card.dataset.cuisine.trim().toLowerCase();
        const toutes = 'toutes';

        let liste;
        if (cuisine === toutes) {
            liste = window.FormulesGlobal;
        } else {
            liste = window.FormulesGlobal.filter(f =>
                f.cuisine && f.cuisine.toLowerCase().includes(cuisine)
            );
        }
        afficherFormules(liste);
    });
}
