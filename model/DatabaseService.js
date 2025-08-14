export class DataBaseService {
    constructor(nomBD, version = 1) {
        this.nomBD = nomBD;
        this.version = version;
        this.db = null;
    }

   

    /**
     * Methode pour initialiser la BD
     * Elle cree un objectStore (table) si besoin avec un index
     * @param (string) storeName - le nom de la table
     * @param (string) keyPath - Le champ qui sert de cle primaire
     * @param (Array) indexes - liste d'index a creer [{nom,key,unique}]
     */
    async init(storeName, keyPath = "id", indexes = []) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.nomBD, this.version);
            // connexion reussie a la base
            request.onsuccess = (event) => {
                this.db = event.target.result
                resolve();
            }
            request.onerro = () => {
                reject("Erreur d'ouverture de la base IndexedDb")
            }
            request.onupgradeneeded = (event) => {
                this.db = event.target.result;
                //verifie si la store existe deje pour ne pas le recreer
                if (!this.db.objectStoreNames.contains(storeName)) {
                    const store = this.db.createObjectStore(storeName, { keyPath });
                    indexes.forEach(index => {
                        store.createIndex(index.name, index.key, {
                            unique: index.unique || false
                        })
                    })
                }
            }

        })

    }

    // Fonction pour ajouter un utilisateur à la base de données
    // ajouter un objet dans store
    /**
     * *@param {string} storeName - Nom de la store(table) dans laquelle ajouter l'objet
     * *@param {object} obj - Objet à ajouter
     * *@returns {Promise} - Promise qui se résout lorsque l'objet est ajouté
     * *@throws {Error} - Si l'objet n'est pas ajouté
     */

    ajouter(storeName, objet) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readwrite")
            const store = transaction.objectStore(storeName)
            const request = store.add(objet)

            request.onsuccess = () => resolve(true)
            request.onerror = () => reject("Erreur lors de l'ajout")
        })
    }
    /**
     * Recuperer un objet pqr sq cle primaire
     * 
     * *@param {string} storeName - Nom de la store(table) dans laquelle ajouter l'objet
     * *@param {*} key - Clé primaire de l'objet à récupérer
     * *@returns {Promise} - Promise qui se résout avec l'objet récupéré
     */

    getById(storeName, key) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readonly") //connexion a la base de donnees en mode lecture simple
            const store = transaction.objectStore(storeName)
            const request = store.get(key)

            request.onsuccess = () => {
                if (request.result) {
                    resolve(request.result)
                }
                else {
                    reject(null)
                }
            }
            request.onerror = () => reject("Erreur lors de la récupération de l'objet.")
        })
    }

    // Méthode de connexion (email + mot de passe)
    async connexion(storeName, email, motdepasse) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readonly");
            const store = transaction.objectStore(storeName);
            const request = store.get(email);

            request.onsuccess = () => {
                const utilisateur = request.result;
                if (utilisateur && utilisateur.motdepasse === motdepasse) {
                    resolve(utilisateur);
                } else {
                    resolve(null); // mauvais mot de passe ou utilisateur introuvable
                }
            };

            request.onerror = () => reject("Erreur lors de la recherche de l'utilisateur.");
        });
    }



    /**
    * Recuperer un object par sa cle primaire
    * @param {string} storeName - le nom du store
    * @return {Promise} - un tableau d'objets
    */
    getAll(storeName) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readonly")
            const store = transaction.objectStore(storeName)
            const request = store.getAll()

            request.onsuccess = () => {
                resolve(request.result)
            }
            request.onerror = () => reject("Erreur lors de la recuperation de tous les objects")

        })
    }

    /**
     * Supprimer un objet par sa cle primaire 
     * *@param {string} storeName - le nom de la store
     * *@param {*} key - valeur de la cle primaire 
     * *@return {promise} - un tableau d'objet
     */

    deleteById(storeName, key) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readwrite")
            const store = transaction.objectStore(storeName)
            const request = store.delete(key)

            request.onsuccess = () => {
                resolve(true)
            }
            request.onerror = () => reject("Erreur lors de la suppression")
        })
    }

    /**
    * Modifier un object dans store
    * @param (string) strorename - le nom du store
    * @param (Object) objet - l'objet a enregister
    * @returns {Promise} - succes ou echec
    */

    modifier(storeName, objet) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], "readwrite")

            const store = transaction.objectStore(storeName)
            const request = store.put(objet)

            request.onsuccess = () => resolve(true)
            request.onerror = () => reject("Erreur lors de la modification")
        })
    }
}