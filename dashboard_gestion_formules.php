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

$cuisines = array_unique(array_column($formules, 'cuisine'));
sort($cuisines);

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>RestoEtudiant</title>
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <style>
        .popup-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0,0,0,0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background: white;
            padding: 20px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 8px;
            position: relative;
        }
        .popup-content img {
            border-radius: 5px;
        }
        .formule-card form {
            margin-top: 10px;
        }
        .formule-card button {
            background-color: #00796b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        /* Animation icône panier */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .cart.bounce {
            animation: bounce 0.5s;
        }
        /* Style total dans popup */
        #popup-total {
            font-weight: bold;
            margin-top: 15px;
            text-align: right;
            font-size: 1.1em;
            color: #00796b;
        }
    </style>
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
                <input type="search" id="search-input" placeholder="Que voulez-vous manger ?" autocomplete="off" />
                <button class="recherche-btn" aria-label="Rechercher"><span class="material-icons">search</span></button>
                <button id="clear-search" aria-label="Effacer la recherche"><span class="material-icons">close</span></button>
            </div>
            <div class="profile">
                <a href="php/panier.php" class="cart" id="cart-icon"><span class="material-icons">shopping_cart</span><span class="cart-count" id="cart-count">0</span></a>
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
                    <img class="highlight-img" src="image/poulet_braise.jpg" alt="" />
                    <div class="hightlight-desc"><h4>Cuisine Ivoirienne</h4><p>Dès $105/Semaine</p></div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/Maroc.jpg" alt="" />
                    <div class="hightlight-desc"><h4>Cuisine Marocaine</h4><p>Dès $105/Semaine</p></div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet_braise.jpg" alt="" />
                    <div class="hightlight-desc">
                        <h4>Cuisine Sénégalaise</h4>
                        <p>Dès $105/Semaine</p>
                    </div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet braisé.jpg" alt="" />
                    <div class="hightlight-desc">
                        <h4>Cuisine Algérienne</h4>
                        <p>Dès $105/Semaine</p>
                    </div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet braisé.jpg" alt="" />
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
                    <div class="filtre-card popup-cuisine" data-cuisine="toutes" style="cursor: pointer;">
                        <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                        <p>Toutes les Cuisines</p>
                    </div>
                    <?php foreach ($cuisines as $cuisine): ?>
                        <div class="filtre-card popup-cuisine" data-cuisine="<?= htmlspecialchars(strtolower($cuisine)) ?>" style="cursor: pointer;">
                            <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                            <p><?= htmlspecialchars($cuisine) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr class="divider" />

            <div class="main-details">
                <h2 class="main-titre" id="choix-formules">Menu</h2>

                <p id="compteur-plats" style="font-weight:bold;color:#00796b;">
                    Plats disponibles aujourd’hui (<?= ucfirst($date) ?>)
                </p>

                <div class="details-wrapper">
                    <?php foreach ($formules as $formule): ?>
                        <div class="formule-card" data-cuisine="<?= htmlspecialchars(strtolower($formule['cuisine'])) ?>">
                            <img src="image/<?= htmlspecialchars($formule['image']) ?>" alt="<?= htmlspecialchars($formule['titre']) ?>" />
                            <h3><?= htmlspecialchars($formule['titre']) ?></h3>
                            <p><?= htmlspecialchars($formule['description']) ?></p>
                            <p><strong><?= number_format($formule['prix'], 2) ?> $</strong></p>
                            <form action="php/panier.php" method="post" style="margin-top: 10px;">
                                <input type="hidden" name="id_formule" value="<?= $formule['id'] ?>" />
                                <input type="hidden" name="titre" value="<?= htmlspecialchars($formule['titre']) ?>" />
                                <input type="hidden" name="prix" value="<?= $formule['prix'] ?>" />
                                <button type="submit" class="btn-ajouter-panier">Ajouter au panier</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Popup pour détails cuisine -->
        <div id="popup-cuisine" class="popup-overlay">
            <div class="popup-content">
                <span id="popup-close" style="float:right; cursor:pointer;">&times;</span>
                <h2 id="popup-titre">Cuisine</h2>
                <div id="popup-description">Chargement...</div>
                <div id="popup-total">Total : 0.00 $</div>
                <div style="text-align: right; margin-top: 20px;">
                    <a href="php/panier.php" class="btn-panier" style="
                    background-color: #00796b;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;">
                        🛒 Voir le panier
                    </a>
                </div>
            </div>
        </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById('popup-cuisine');
        const titre = document.getElementById('popup-titre');
        const desc = document.getElementById('popup-description');
        const closeBtn = document.getElementById('popup-close');
        const totalDisplay = document.getElementById('popup-total');
        const cartIcon = document.getElementById('cart-icon');
        let platsCount = {};  // objet pour stocker les quantités ajoutées par plat
        let totalPrix = 0;

        document.querySelectorAll('.popup-cuisine').forEach(card => {
            card.addEventListener('click', async () => {
                const cuisine = card.dataset.cuisine;
                titre.textContent = cuisine === "toutes" ? "Toutes les cuisines" : cuisine;
                desc.innerHTML = "Chargement...";
                totalPrix = 0;
                platsCount = {};
                updateTotal();

                try {
                    const res = await fetch(`php/get_plats_par_cuisine.php?cuisine=${cuisine === "toutes" ? "__all__" : encodeURIComponent(cuisine)}`);
                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        desc.innerHTML = `<p>Aucun plat disponible pour cette cuisine.</p>`;
                    } else {
                        desc.innerHTML = data.map(p => `
                        <div class="plat-fiche" style="margin-bottom:20px; border-bottom:1px solid #ccc; padding:10px;">
                            <img src="image/${p.image}" alt="${p.titre}" style="max-width:100px;" />
                            <strong>${p.titre}</strong>
                            <p>${p.description}</p>
                            <em>Prix: ${parseFloat(p.prix).toFixed(2)} $</em><br>
                            <button class="btn-ajouter-panier" data-id="${p.id}" data-titre="${p.titre}" data-prix="${p.prix}">Ajouter au panier</button>
                            <span class="confirmation-message" data-count="0" style="display:none; color:green; margin-left:10px;"></span>
                        </div>
                    `).join('');
                    }
                    popup.style.display = 'flex';
                } catch (e) {
                    desc.innerHTML = "Erreur lors du chargement.";
                    console.error(e);
                }
            });
        });

        // Mise à jour du total dans popup
        function updateTotal() {
            totalDisplay.textContent = `Total : ${totalPrix.toFixed(2)} $`;
        }

        // Animation icône panier
        function animateCart() {
            cartIcon.classList.add('bounce');
            setTimeout(() => {
                cartIcon.classList.remove('bounce');
            }, 500);
        }

        // Ajout au panier dans popup (AJAX + gestion compteur + limite 10)
        desc.addEventListener('click', async (e) => {
            if (e.target.classList.contains('btn-ajouter-panier')) {
                const btn = e.target;
                const id = btn.dataset.id;
                const titre = btn.dataset.titre;
                const prix = parseFloat(btn.dataset.prix);

                // Vérifier limite 10
                const countCurrent = platsCount[id] || 0;
                if (countCurrent >= 10) {
                    alert("Vous avez atteint la limite de 10 exemplaires pour ce plat.");
                    return;
                }

                // Appel fetch POST pour ajouter dans panier (simulé)
                try {
                    const formData = new FormData();
                    formData.append("id_formule", id);
                    formData.append("titre", titre);
                    formData.append("prix", prix);

                    const res = await fetch("php/panier.php", {
                        method: "POST",
                        body: formData,
                    });

                    if (res.ok) {
                        // Mettre à jour compteur local
                        platsCount[id] = countCurrent + 1;

                        // Mettre à jour total prix
                        totalPrix += prix;
                        updateTotal();

                        // Mise à jour message ajout
                        const msg = btn.nextElementSibling;
                        msg.dataset.count = platsCount[id];
                        msg.textContent = `✔ Ajouté (${platsCount[id]})`;
                        msg.style.display = 'inline';

                        // Mise à jour compteur panier global (exemple simple)
                        const cartCountEl = document.getElementById('cart-count');
                        let cartCount = parseInt(cartCountEl.textContent || "0");
                        cartCount++;
                        cartCountEl.textContent = cartCount;

                        animateCart();
                    } else {
                        alert("Erreur lors de l'ajout au panier.");
                    }
                } catch (e) {
                    console.error(e);
                    alert("Erreur lors de la requête.");
                }
            }
        });

        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
            desc.innerHTML = "Chargement...";
            totalPrix = 0;
            platsCount = {};
            updateTotal();
        });

        // Fermer popup en cliquant en dehors
        popup.addEventListener('click', e => {
            if (e.target === popup) {
                popup.style.display = 'none';
                desc.innerHTML = "Chargement...";
                totalPrix = 0;
                platsCount = {};
                updateTotal();
            }
        });

        // Search input simple (filtrer visible)
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            document.querySelectorAll('.formule-card').forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const desc = card.querySelector('p').textContent.toLowerCase();
                card.style.display = title.includes(filter) || desc.includes(filter) ? '' : 'none';
            });
        });

        // Clear search
        document.getElementById('clear-search').addEventListener('click', () => {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        });
    });
</script>
</body>
</html>
