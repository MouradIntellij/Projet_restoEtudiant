


export function getCookie(cle) {
    const nomEgal = cle + "="
    const cookies = document.cookie.split(";")
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim()
        if (cookie.indexOf(nomEgal) === 0) {
            return cookie.substring(nomEgal.length, cookie.length)
        }

    }
    return null;
}

export function setCookie(cle, valeur, jours = 1) {
    const dateExpiration = new Date();
    dateExpiration.setTime(dateExpiration.getTime() + (jours * 24 * 60 * 60 * 1000));
    const expires = "expires=" + dateExpiration.toUTCString()
    document.cookie = cle + "=" + valeur + ";" + expires + ";path=/";
}

export function deleteCookie(cle) {
    document.cookie = cle + "=;expires=Thu, 01 Jan 2000 00:00:00 UTC; path=/;";
}

// setCookie("username","bob",7)
const username = getCookie("username")
console.log(username);

// setCookie("lang","fr",2)
getCookie("lang")

function setTitre(nom) {
    console.log("-------------- " + nom + " --------------");

}

// let test = " Bonjour    "
// console.log(test.length);
// test = test.trim()
// console.log(test.length);
// console.log(test.substring(2,test.length -3));

setTitre("localStorage")
// localStorage.setItem("panier","flan")
const valeur = localStorage.getItem("panier")
console.log(valeur);
localStorage.removeItem("panier")
localStorage.clear()

setTitre("sessionStorage")
const utilisateur = {
    id: 1,
    nom: "Dupont",
    preferences: {
        theme: "sombre",
        langue: "fr"
    }
}

sessionStorage.setItem('utilisateur', JSON.stringify(utilisateur))

const utilisateurRecupere = JSON.parse(sessionStorage.getItem('utilisateur'))

console.log(utilisateur.preferences.langue);

setTitre("IndexedDB")

const request = indexedDB.open("MaBaseDeDonnees", 1)
request.onupgradeneeded = function (event) {
    const db = event.target.result;
    const objectStore = db.createObjectStore("utilisateurs", { keyPath: "id" });
    objectStore.createIndex("nom", "nom", { unique: false })
    objectStore.createIndex("email", "email", { unique: true })
    objectStore.createIndex("categorie", "categorie", { unique: false })
}

setTitre("Cache API")

caches.open('v1').then(cache => {
    cache.add('/style/main.css');
    //  cache.addAll([
    //     './index.html',
    //     "./Vehicule.js"
    //  ])   
})













