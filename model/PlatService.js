export class PlatService {
    constructor(dbName = "RestoEtudiantDB", version = 3) {
        this.dbName = dbName;
        this.version = version;
        this.db = null; // La base de données sera initialisée plus tard
    }

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


    // Fonction pour ajouter un plat à la base de données
    async ajouterPlat(plat) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readwrite");
            const store = transaction.objectStore("plats");
            const request = store.add(plat);

            request.onsuccess = () => {
                console.log("Plat ajouté avec succès:", plat);
                resolve(true);
            };

            request.onerror = (event) => {
                console.error("Erreur lors de l'ajout du plat:", event.target.errorCode);
                reject("Erreur lors de l'ajout du plat");
            };
        });
    }


    // Fonction pour récupérer tous les plats de la base de données
    async getAllPlats() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const request = Platstore.getAll(); // Récupère tous les plats de l'object store

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la récupération des plats:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }

    // function pour recuperer un plat par son id
    async getPlatById(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const request = Platstore.get(id); // Récupère le plat par son ID

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la récupération du plat:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour supprimer un plat de la base de données
    async supprimerPlat(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readwrite"); // Ouvre une transaction en mode lecture/écriture
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const request = Platstore.delete(id); // Supprime le plat par son ID

            request.onsuccess = () => {
                console.log("Plat supprimé avec succès:", id); // Affiche un message de succès dans la console
                resolve(true); // Résout la promesse si la suppression est réussie
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la suppression du plat:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour mettre à jour un plat dans la base de données
    async updatePlat(plat) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readwrite"); // Ouvre une transaction en mode lecture/écriture
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const request = Platstore.put(plat); // Met à jour le plat dans l'object store

            request.onsuccess = () => {
                console.log("Plat mis à jour avec succès:", plat); // Affiche un message de succès dans la console
                resolve(true); // Résout la promesse si la mise à jour est réussie
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la mise à jour du plat:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour rechercher des plats par nom ou email de restaurateur
    async searchPlats(query) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const index = Platstore.index("nom"); // Accède à l'index sur le nom du plat
            const request = index.getAll(query); // Récupère tous les plats correspondant à la requête

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la recherche des plats:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour rechercher des plats par email de restaurateur
    async searchPlatsByEmail(email) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const index = Platstore.index("emailRestaurateur"); // Accède à l'index sur l'email du restaurateur
            const request = index.getAll(email); // Récupère tous les plats correspondant à l'email

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la recherche des plats par email:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour rechercher des plats par prix
    async searchPlatsByPrix(prix) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const index = Platstore.index("prix"); // Accède à l'index sur le prix du plat
            const request = index.getAll(prix); // Récupère tous les plats correspondant au prix

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la recherche des plats par prix:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
    // Fonction pour rechercher des plats par date d'ajout
    async searchPlatsByDateAjout(date) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["plats"], "readonly"); // Ouvre une transaction en mode lecture seule
            const Platstore = transaction.objectStore("plats"); // Accède à l'object store "plats"
            const index = Platstore.index("dateAjout"); // Accède à l'index sur la date d'ajout du plat
            const request = index.getAll(date); // Récupère tous les plats correspondant à la date d'ajout

            request.onsuccess = (event) => {
                resolve(event.target.result); // Résout la promesse avec le résultat de la requête
            };
            request.onerror = (event) => {
                console.error("Erreur lors de la recherche des plats par date d'ajout:", event.target.errorCode); // Affiche un message d'erreur dans la console
                reject(event.target.errorCode); // Rejette la promesse en cas d'erreur
            };
        });
    }
}
