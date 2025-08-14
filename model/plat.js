
export class Plat { 
    constructor({nom, prix, description, imageUrl,emailRestaurateur}) {
        this.id = Date.now(); // Génère un ID unique basé sur le timestamp
        this.nom = nom;
        this.prix = prix;
        this.description = description;
        this.imageUrl = imageUrl;
        this.emailRestaurateur = emailRestaurateur;
        this.dateAjout = new Date().toISOString(); // Date format ISO
    }  
}