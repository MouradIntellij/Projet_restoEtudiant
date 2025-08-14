export class Utilisateur { 
    constructor(nom, prenom, email, motdepasse, univeristé, année_académique, carte_scolaire, photo= null) { 
        this.nom = nom; 
        this.prenom = prenom; 
        this.email = email; 
        this.motdepasse = motdepasse; 
        this.univeristé = univeristé; 
        this.année_académique = année_académique; 
        this.carte_scolaire = carte_scolaire; 
        this.photo = photo; 
    }
    // Méthode pour afficher les informations de l'utilisateur
    afficherInfo() { 
        return `Prénom: ${this.prenom}, Nom: ${this.nom},  Email: ${this.email}, Mot de passe: ${this.motdepasse}, Université: ${this.univeristé}, Année académique: ${this.année_académique}, Carte scolaire: ${this.carte_scolaire}`;
    }
}
