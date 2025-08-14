export class CommandeService {
    constructor(dbName = "RestoEtudiantDB", version = 3) { // Augmentez la version si nécessaire
        this.dbName = dbName;
        this.version = version;
        this.db = null;
    }
    

    // Initialisation de la base de données IndexedDB
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log("Base de données initialisée avec succès");
                resolve();
            };

            request.onerror = (event) => {
                console.error("Erreur lors de l'ouverture de la base de données:", event.target.error);
                reject("Erreur lors de l'ouverture de la base de données");
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                if (!db.objectStoreNames.contains("plats")) {
                    const storePlats = db.createObjectStore("plats", { keyPath: "id", autoIncrement: true });
                    storePlats.createIndex("emailRestaurateur", "emailRestaurateur", { unique: false });
                }

                if (!db.objectStoreNames.contains("commandes")) {
                    const storeCommandes = db.createObjectStore("commandes", { keyPath: "id", autoIncrement: true });
                    storeCommandes.createIndex("emailRestaurateur", "emailRestaurateur", { unique: false });
                }

                if (!db.objectStoreNames.contains("formules")) {
                    const storeFormules = db.createObjectStore("formules", { keyPath: "id" });
                    storeFormules.createIndex("emailRestaurateur", "emailRestaurateur", { unique: false });
                }
            };

        });
    }

    // Ajouter une commande dans la base de données
    async ajouterCommande(commande) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["commandes"], "readwrite");
            const store = transaction.objectStore("commandes");
            const request = store.add(commande);

            request.onsuccess = () => {
                console.log("Commande ajoutée:", commande);
                resolve(true);
            };

            request.onerror = (event) => {
                console.error("Erreur d'ajout de commande:", event.target.error);
                reject(event.target.error);
            };
        });
    }

    // Récupérer toutes les commandes
    async getAllCommandes() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["commandes"], "readonly");
            const store = transaction.objectStore("commandes");
            const request = store.getAll();

            request.onsuccess = (event) => {
                resolve(event.target.result);
            };

            request.onerror = (event) => {
                console.error("Erreur de récupération des commandes:", event.target.error);
                reject(event.target.error);
            };
        });
    }

    // Récupérer les commandes par email du restaurateur
    async getCommandesByEmailRestaurateur(emailRestaurateur) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["commandes"], "readonly");
            const store = transaction.objectStore("commandes");
            const index = store.index("emailRestaurateur");
            const request = index.getAll(emailRestaurateur);

            request.onsuccess = (event) => {
                resolve(event.target.result);
            };

            request.onerror = (event) => {
                console.error("Erreur de récupération par restaurateur:", event.target.error);
                reject(event.target.error);
            };
        });
    }
}