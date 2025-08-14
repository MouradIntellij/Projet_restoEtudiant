
const hauteur = window.innerHeight


console.log(hauteur);

const nouvelleTab = window.open('https://amazon.ca', '_blank')


// window.scrollTo(0,500)
// window.scrollBy(0.100)

const timeout = window.setTimeout(() => {
    console.log("apres 2 seconde");
}, 2000);

const interval = window.setInterval(() => {
    const largeur = window.innerWidth
    console.log(largeur);
}, 3000)

window.clearTimeout(timeout)
window.clearInterval(interval)

// window.alert('Ceci est une alerte')
// const confirmation = window.confirm('Etes-vous sur ?')
// console.log(confirmation);

// const saisie = window.prompt('Entrez votre nom',"Toto")
// console.log(saisie);

// information sur le navigateur
console.log('User Agent:', navigator.userAgent);
console.log('Nom du navigateur:', navigator.appName);
console.log('Version du navigateur:', navigator.appVersion);
console.log('Platforme:', navigator.platform);
console.log('Langue du navigateur:', navigator.language);

console.log('Cookies actives:', navigator.cookieEnabled);
console.log('En Ligne:', navigator.onLine);

// Geolocalisation
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        position => {
            console.log('Latitude', position.coords.latitude);
            console.log('Longitude', position.coords.longitude);

        },
        error => {
            console.log('Erreur de geolocalisation', error.message);

        }
    )
}
console.log('Largeur', screen.width);
console.log('Hauteur', screen.height);

// history.back()
// history.forward()
// history.go(-2)
// history.go(1)

console.log('URL complete:', location.href);
console.log('Protocole:', location.protocol);
console.log('Port:', location.port);
console.log('Path:', location.pathname);
// location.reload(true)













