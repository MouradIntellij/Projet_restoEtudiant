
export class Restaurateur {
    constructor({ nomRestaurant, adresseRestaurant, email, numeroTelephone, password }) {
        this.nomRestaurant = nomRestaurant;
        this.adresseRestaurant = adresseRestaurant;
        this.email = email;
        this.numeroTelephone = numeroTelephone;
        this.password = password;
        this.role = "Restaurateur";
    }
}