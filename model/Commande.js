export class Commande {
    constructor(numeroCommande, restaurant, plat, date, montant, statut, emailRestaurateur) {
        this.numeroCommande = numeroCommande;
        this.restaurant = restaurant;
        this.plat = plat;
        this.date = date;
        this.montant = montant;
        this.statut = statut;
        this.emailRestaurateur = emailRestaurateur; // pour pouvoir trier par restaurateur !
    }

    afficherDetailsCommande() {
        return `Commande ${this.numeroCommande} : ${this.restaurant} - ${this.plat} - ${this.date} - ${this.montant} $ - Statut: ${this.statut}`;
    }
}
