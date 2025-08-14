<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

$stmt = $pdo->query("SELECT * FROM formule ORDER BY cuisine, titre");
$formules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour debug temporaire, décommente ensuite
// echo '<pre>'; var_dump($formules); echo '</pre>';

$cuisines = array_unique(array_column($formules, 'cuisine'));
sort($cuisines);


//setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra'); // Assure que les noms de jour/mois sont en français
//$date = strftime("%A %d %B %Y"); // Exemple : "lundi 11 août 2025"

$formatter = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM y'
);
$date = $formatter->format(new DateTime());
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>RestoEtudiant</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<span class="material-icons menu-toggle">menu</span>
<main>
    <div class="leftbar">
        <h1 class="logo">RestoEtudiant</h1>
        <div class="leftbar-menus">
            <a href="index.php"><span class="material-icons">storefront</span>Accueil</a>
            <a href="Inscription.html">Inscriptions</a>
        </div>
    </div>

    <div class="main">
        <div class="main-navbar">
            <div class="barre-recherche">
                <input type="search" id="search-input" placeholder="Que voulez-vous manger ?" autocomplete="off">
                <button class="recherche-btn" aria-label="Rechercher"><span class="material-icons">search</span></button>
                <button id="clear-search" aria-label="Effacer la recherche"><span class="material-icons">close</span></button>
            </div>
            <div class="profile">
                <a href="panier.html" class="cart"><span class="material-icons">shopping_cart</span><span class="cart-count">0</span></a>
                <a href="connexion.html" class="user"><span class="material-icons">account_circle</span></a>
            </div>
        </div>

        <div class="main-highlight">
            <div class="main-header">
                <h2 class="main-title">Recommandations</h2>
                <div class="main-fleche">
                    <span class="material-icons back">arrow_back</span>
                    <span class="material-icons next">arrow_forward</span>
                </div>
            </div>
            <div class="highlight-wrapper">
                <!-- exemples statiques -->
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet_braise.jpg" alt="">
                    <div class="hightlight-desc"><h4>Cuisine Ivoirienne</h4><p>Dès $105/Semaine</p></div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/Maroc.jpg" alt="">
                    <div class="hightlight-desc"><h4>Cuisine Marocaine</h4><p>Dès $105/Semaine</p></div>
                </div>


            <div class="highlight-card">
                <img class="highlight-img" src="image/poulet_braise.jpg" alt="">
                <div class="hightlight-desc">
                    <h4>Cuisine Sénégalaise</h4>
                    <p>Dès $105/Semaine</p>
                </div>
            </div>

            <div class="highlight-card">
                <img class="highlight-img" src="image/poulet braisé.jpg" alt="">
                <div class="hightlight-desc">
                    <h4>Cuisine Algérienne</h4>
                    <p>Dès $105/Semaine</p>
                </div>
            </div>

                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet braisé.jpg" alt="">
                    <div class="hightlight-desc">
                        <h4>Cuisine Tunisienne</h4>
                        <p>Dès $105/Semaine</p>
                    </div>
                </div>


            </div>
        </div>

        <div class="main-menus">
            <div class="main-filtrer">
                <div><h2 class="titre">Formules</h2></div>
                <div class="filtre-wrapper wrapper-filtre">

                    <!--  rendre les card cliquable pour afficher un popup de menu de chaque type de cuisine -->


<!--                    <div class="filtre-card" data-cuisine="toutes">-->

<!--                    <div class="filtre-card popup-cuisine" data-cuisine="--><?php //= htmlspecialchars($cuisines) ?><!--" style="cursor: pointer;"></div>-->
<!--                        <div class="filtre-icon"><span class="material-icons">restaurant</span></div>-->
<!--                        <p>Toutes les Cuisines </p>-->
<!--                    </div>-->

                <div class="filtre-card popup-cuisine" data-cuisine="toutes" style="cursor: pointer;">
                    <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                    <p>Toutes les Cuisines</p>
                </div>



<!--                    --><?php //foreach ($cuisines as $cuisine): ?>
<!--                        <div class="flitre-card" data-cuisine="--><?php //= htmlspecialchars(strtolower($cuisine)) ?><!--">-->
<!--                            <div class="filtre-icon"><span class="material-icons">restaurant</span></div>-->
<!--                            <p>--><?php //= htmlspecialchars($cuisine) ?><!--</p>-->
<!--                        </div>-->
<!--                    --><?php //endforeach; ?>

                        <?php foreach ($cuisines as $cuisine): ?>
                            <div class="filtre-card popup-cuisine" data-cuisine="<?= htmlspecialchars(strtolower($cuisine)) ?>" style="cursor: pointer;">
                                <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                                <p><?= htmlspecialchars($cuisine) ?></p>
                            </div>
                        <?php endforeach; ?>

                    </div>
            </div>

            <hr class="divider">

            <div class="main-details">
                <h2 class="main-titre" id="choix-formules">Menu</h2>

               <!-- ajout de la date aujourd'hui  date système pour afficher les plats disponibles aujourd'hui-->
                <p id="compteur-plats" style="font-weight:bold;color:#00796b;">
                    Plats disponibles aujourd’hui (<?= ucfirst($date) ?>)
                </p>

          <!--      <p id="compteur-plats" style="font-weight:bold;color:#00796b;"> plats disponibles</p> -->





                <div class="details-wrapper"></div>
                <?php foreach ($formules as $formule): ?>
                    <div class="formule-card" data-cuisine="<?= htmlspecialchars(strtolower($formule['cuisine'])) ?>">
                        <img src="image/<?= htmlspecialchars($formule['image']) ?>" alt="<?= htmlspecialchars($formule['titre']) ?>">
                        <h3><?= htmlspecialchars($formule['titre']) ?></h3>
                        <p><?= htmlspecialchars($formule['description']) ?></p>
                        <p><strong><?= number_format($formule['prix'], 2) ?> $</strong></p>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div>



    <!-- Popup Cuisine -->
    <div id="popup-cuisine" class="popup-overlay" style="display:none;">
        <div class="popup-content">
            <span id="popup-close" style="float:right; cursor:pointer;">&times;</span>
            <h2 id="popup-titre">Cuisine</h2>
            <div id="popup-description">Chargement...</div>
        </div>
    </div>



</main>

<footer>
    <div class="footer-container">
        <div class="footer-content"><h3>Menu</h3><ul class="footer-links"><li><a href="index.php">Accueil</a></li><li><a href="inscription.html">Inscription</a></li></ul></div>
        <div class="footer-content"><h3>Liens utiles</h3><ul class="footer-links"><li><a href="#about">C'est quoi Resto Etudiant ?</a></li><li><a href="#inscription">Comment ça fonctionne ?</a></li><li><a href="#presentation">Histoire du Resto‑Etudiant</a></li><li><a href="#avantage">Les avantages du Resto</a></li><li><a href="#Offres-abonnement">Nos offres d'abonnement</a></li></ul></div>
        <div class="footer-content"><h3>Contact</h3><p>+111 222‑4568<br>plataflex@gmail.com</p></div>
    </div>
    <div class="footer-bottom"><p>RestoEtudiant© / 2025 tous droits réservés.</p></div>
</footer>

<div class="overlay"></div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById('popup-cuisine');
        const titre = document.getElementById('popup-titre');
        const desc = document.getElementById('popup-description');
        const closeBtn = document.getElementById('popup-close');
        console.log("Cartes détectées :", document.querySelectorAll('.popup-cuisine').length);

        document.querySelectorAll('.popup-cuisine').forEach(card => {
            card.addEventListener('click', async () => {
                const cuisine = card.dataset.cuisine;
                titre.textContent = cuisine === "toutes" ? "Toutes les cuisines" : cuisine;
                desc.innerHTML = "Chargement...";

                let apiUrl = "php/get_plats_par_cuisine.php";
                if (cuisine === "toutes") {
                    apiUrl += "?cuisine=__all__"; // cas spécial
                } else {
                    apiUrl += `?cuisine=${encodeURIComponent(cuisine)}`;
                }

                try {
                    const res = await fetch(apiUrl);
                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        desc.innerHTML = "<p>Aucun plat disponible pour cette cuisine.</p>";
                    } else {
                        desc.innerHTML = data.map(p => `
                            <div style="margin-bottom:15px;border-bottom:1px solid #ccc;padding-bottom:10px;">
                                <img src="image/${p.image}" alt="${p.titre}" style="max-width:100px;float:right;margin-left:10px;">
                                <strong>${p.titre}</strong><br>
                                ${p.description}<br>
                                <em>Prix: ${parseFloat(p.prix).toFixed(2)} $</em>
                            </div>
                        `).join('');
                    }
                } catch (e) {
                    desc.innerHTML = "Erreur lors du chargement.";
                    console.error("Erreur lors de l'appel à l'API :", e);
                }

                popup.style.display = 'flex';
            });
        });

        // Gestion fermeture popup
        closeBtn.addEventListener('click', () => popup.style.display = 'none');
        window.addEventListener('click', e => {
            if (e.target === popup) popup.style.display = 'none';
        });
    });
</script>
<script type="module" src="./controller/index.js"></script>






</body>
</html>
