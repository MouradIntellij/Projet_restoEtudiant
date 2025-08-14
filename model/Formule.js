export class Formule { 
    constructor({ id = Date.now(), nom, prix, description, plats =[], imageUrl, emailRestaurateur }) {
        this.id = id; // Génère un ID unique basé sur le timestamp
        this.nom = nom;
        this.prix = prix;
        this.description = description;
        this.plats = plats; // Liste de plats
        this.imageUrl = imageUrl;
        this.emailRestaurateur = emailRestaurateur;
        this.dateAjout = new Date().toISOString(); // Date format ISO
    }
}