<?php
session_start();

// 🔐 Sécurité : accès étudiant uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

// Récupérer toutes les formules disponibles
$stmt = $pdo->query("SELECT * FROM formule WHERE disponible = 1 ORDER BY cuisine, titre");
$formules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extraire cuisines uniques
$cuisines = array_unique(array_column($formules, 'cuisine'));
sort($cuisines);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoEtudiant - Choisir vos formules</title>
    <link rel="stylesheet" href="/Styles/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .formule-card {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .formule-card img {
            max-width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .formule-actions button {
            margin: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-ajouter-panier {
            background-color: #00796b;
            color: white;
        }
        .btn-ajouter-panier:hover {
            background-color: #005a50;
        }
        .filtre-wrapper {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 10px;
        }
        .filtre-card {
            text-align: center;
            padding: 10px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 120px;
        }
        .filtre-card.active {
            background-color: #00796b;
            color: white;
        }
        .details-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .main-details {
            margin-top: 20px;
        }
        .cart-count {
            background: #00796b;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            position: absolute;
            top: -10px;
            right: -10px;
        }
    </style>
</head>
<body>
<span class="material-icons menu-toggle">menu</span>
<main>
    <!-- Leftbar -->
    <div class="leftbar">
        <h1 class="logo">RestoEtudiant</h1>
        <div class="leftbar-menus">
            <a href="index.php"><span class="material-icons">storefront</span>Accueil</a>
            <a href="inscription.php">Inscription</a>
            <a href="panier.php"><span class="material-icons">shopping_cart</span>Panier</a>
        </div>
    </div>

    <div class="main">
        <!-- Main Navbar -->
        <div class="main-navbar">
            <div class="barre-recherche">
                <input type="search" id="search-input" placeholder="Que voulez-vous manger?" autocomplete="off">
                <button class="recherche-btn" aria-label="Rechercher">
                    <span class="material-icons">search</span>
                </button>
                <button id="clear-search" aria-label="Effacer la recherche">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="profile">
                <a href="panier.php" class="cart">
                    <span class="material-icons">shopping_cart</span>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
                <a href="connexion.php" class="user">
                    <span class="material-icons">account_circle</span>
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <div class="main-filtrer">
            <div>
                <h2 class="titre">Formules</h2>
                <div class="main-arrow">
                    <span class="material-icons back">arrow_back</span>
                    <span class="material-icons next">arrow_forward</span>
                </div>
            </div>
            <div class="filtre-wrapper wrapper-filtre">
                <div class="filtre-card" data-cuisine="*">
                    <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                    <p>Toutes les Cuisines</p>
                </div>
                <?php foreach ($cuisines as $cuisine): ?>
                    <div class="filtre-card" data-cuisine="<?= htmlspecialchars($cuisine) ?>">
                        <div class="filtre-icon"><span class="material-icons">restaurant</span></div>
                        <p><?= htmlspecialchars($cuisine) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <hr class="divider">

        <!-- Liste des formules -->
        <div class="main-details">
            <h2 class="main-titre" id="choix-formules">Menu</h2>
            <p id="compteur-plats" style="margin: 10px 0; font-weight: bold; color: #00796b;">
                <?= count($formules) ?> plats disponibles
            </p>
            <div class="details-wrapper" id="liste-formules-public">
                <?php foreach ($formules as $formule): ?>
                    <div class="formule-card" data-id="<?= $formule['id'] ?>" data-cuisine="<?= htmlspecialchars($formule['cuisine']) ?>">
                        <img class="highlight-img" src="<?= htmlspecialchars($formule['image'] ?? 'image/default.jpg') ?>" alt="<?= htmlspecialchars($formule['titre']) ?>">
                        <div class="hightlight-desc">
                            <h4><?= htmlspecialchars($formule['titre']) ?></h4>
                            <p><?= htmlspecialchars($formule['description']) ?></p>
                            <p><strong>Prix :</strong> <?= number_format($formule['prix'], 2) ?> $</p>
                            <p><strong>Cuisine :</strong> <?= htmlspecialchars($formule['cuisine']) ?></p>
                            <div class="formule-actions">
                                <button class="btn-ajouter-panier" data-id="<?= $formule['id'] ?>">Ajouter au panier</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="footer-container">
        <div class="footer-content">
            <h3>Menu</h3>
            <ul class="footer-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            </ul>
        </div>
        <div class="footer-content">
            <h3>Liens utiles</h3>
            <ul class="footer-links">
                <li><a href="#about">C'est quoi Resto Etudiant ?</a></li>
                <li><a href="#inscription">Comment ça fonctionne ?</a></li>
                <li><a href="#presentation">Histoire du Resto-Etudiant</a></li>
                <li><a href="#avantage">Les avantages du Resto</a></li>
                <li><a href="#Offres-abonnement">Nos offres d'abonnement</a></li>
            </ul>
        </div>
        <div class="footer-content">
            <h3>Contact</h3>
            <p>+111 222-4568<br>plataflex@gmail.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>RestoEtudiant&copy; / 2025 tous droits réservés.</p>
    </div>
</footer>

<div class="overlay"></div>

<script>
    // Gestion du panier
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function updateCartCount() {
        const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
        document.getElementById('cart-count').textContent = cartCount;
    }

    // Ajouter au panier
    document.querySelectorAll('.btn-ajouter-panier').forEach(btn => {
        btn.addEventListener('click', () => {
            const formuleId = btn.dataset.id;
            const existingItem = cart.find(item => item.id === formuleId);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id: formuleId, quantity: 1 });
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            alert('Formule ajoutée au panier !');
        });
    });

    // Filtrer par cuisine
    document.querySelectorAll('.filtre-card').forEach(card => {
        card.addEventListener('click', () => {
            const cuisine = card.dataset.cuisine;
            document.querySelectorAll('.filtre-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            document.querySelectorAll('.formule-card').forEach(formule => {
                formule.style.display = (cuisine === '*' || formule.dataset.cuisine === cuisine) ? 'block' : 'none';
            });
        });
    });

    // Recherche
    document.getElementById('search-input').addEventListener('input', e => {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.formule-card').forEach(formule => {
            const titre = formule.querySelector('h4').textContent.toLowerCase();
            const description = formule.querySelector('p').textContent.toLowerCase();
            formule.style.display = (titre.includes(searchTerm) || description.includes(searchTerm)) ? 'block' : 'none';
        });
    });

    document.getElementById('clear-search').addEventListener('click', () => {
        document.getElementById('search-input').value = '';
        document.querySelectorAll('.formule-card').forEach(formule => formule.style.display = 'block');
    });

    // Navigation des flèches (simplifiée)
    document.querySelectorAll('.main-arrow .back, .main-arrow .next').forEach(arrow => {
        arrow.addEventListener('click', () => {
            const wrapper = arrow.closest('.main-filtrer, .main-details').querySelector('.filtre-wrapper, .details-wrapper');
            const scrollAmount = arrow.classList.contains('next') ? 200 : -200;
            wrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    });

    // Initialisation
    updateCartCount();
</script>
</body>
</html>