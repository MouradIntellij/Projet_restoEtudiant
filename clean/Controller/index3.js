let PlatsGlobal = [], FormulesGlobal = [];

document.addEventListener("DOMContentLoaded", async () => {
    const db = await openDB();
    await chargerPlatsFormules(db);

    initEtudiantUI();
});

// Retourne une Promise DB ouverte
function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open("RestoEtudiantDB", 3);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains("plats")) db.createObjectStore("plats", {keyPath: "id"});
            if (!db.objectStoreNames.contains("formules")) db.createObjectStore("formules", {keyPath: "id"});
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror = e => reject(e);
    });
}

async function chargerPlatsFormules(db) {
    const txF = db.transaction("formules", "readonly").objectStore("formules");
    const all = await new Promise(res => txF.getAll().onsuccess = e => res(e.target.result));

    if (all.length === 0) {
        // Si vide : fetch et remplir IndexedDB
        const resp = await fetch("/Projet_restoEtudiant/api/get_data.php");
        const data = await resp.json();
        const tx = db.transaction("formules", "readwrite").objectStore("formules");
        data.formules.forEach(f => tx.put(f));
        await tx.transaction.complete;
        FormulesGlobal = data.formules;
    } else {
        FormulesGlobal = all;
    }
}

function initEtudiantUI() {
    const container = document.querySelector('.details-wrapper');
    const compteur = document.getElementById('compteur-plats');

    function afficherFormules(list) {
        container.innerHTML = '';
        compteur.textContent = `${list.length} plats disponibles`;
        list.forEach(f => {
            const card = document.createElement('div');
            card.className = 'formule-card';
            card.innerHTML = `
        <h3>${f.titre}</h3>
        <p>${f.description}</p>
        <p>Prix: ${f.prix} $</p>
        <p>Cuisine: ${f.cuisine}</p>`;
            container.appendChild(card);
        });
    }

    afficherFormules(FormulesGlobal);

    document.querySelector('.wrapper-filtre').addEventListener('click', e => {
        const card = e.target.closest('.flitre-card');
        if (!card || !card.dataset.cuisine) return;
        const cuisine = card.dataset.cuisine.toLowerCase();
        const liste = cuisine === 'toutes'
            ? FormulesGlobal
            : FormulesGlobal.filter(f => f.cuisine && f.cuisine.toLowerCase().includes(cuisine));
        afficherFormules(liste);
    });
}
