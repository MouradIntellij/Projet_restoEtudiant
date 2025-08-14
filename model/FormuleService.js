export class FormuleService {
    constructor(dbName = "RestoEtudiantDB", version = 3) {
        this.dbName = dbName;
        this.version = version;
        this.db = null;
    }

    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);
            

            request.onsuccess = (event) => {
                this.db = event.target.result;
                resolve();
            };

            request.onerror = (event) => {
                reject(event.target.error);
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



    // Ajouter une formule
    async ajouterFormule(formule) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["formules"], "readwrite");
            const magasinFormules = transaction.objectStore("formules");
            const requete = magasinFormules.add(formule);

            requete.onsuccess = () => resolve();
            requete.onerror = (event) => reject(event.target.error);
        });
    }

    // Récupérer toutes les formules
    async getAllFormules() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["formules"], "readonly");
            const magasinFormules = transaction.objectStore("formules");
            const requete = magasinFormules.getAll();

            requete.onsuccess = (event) => resolve(event.target.result);
            requete.onerror = (event) => reject(event.target.error);
        });
    }

    // Supprimer une formule par identifiant
    async supprimerFormule(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["formules"], "readwrite");
            const magasinFormules = transaction.objectStore("formules");
            const requete = magasinFormules.delete(id);

            requete.onsuccess = () => resolve();
            requete.onerror = (event) => reject(event.target.error);
        });
    }

    // Modifier une formule (remplace l'existante si id identique)
    async modifierFormule(formule) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["formules"], "readwrite");
            const magasinFormules = transaction.objectStore("formules");
            const requete = magasinFormules.put(formule);

            requete.onsuccess = () => resolve();
            requete.onerror = (event) => reject(event.target.error);
        });
    }

    // Récupérer une formule par son identifiant
    async getFormuleById(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(["formules"], "readonly");
            const magasinFormules = transaction.objectStore("formules");
            const requete = magasinFormules.get(id);

            requete.onsuccess = (event) => resolve(event.target.result);
            requete.onerror = (event) => reject(event.target.error);
        });
    }
}
