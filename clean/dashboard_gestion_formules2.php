<?php
session_start();

// 🔐 Sécurité : accès étudiant uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

// Récupérer toutes les formules
$stmt = $pdo->query("SELECT * FROM formule ORDER BY cuisine, titre");
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
                <button class="recherche-btn" aria-label="Rechercher">
                    <span class="material-icons">search</span>
                </button>
                <button id="clear-search" aria-label="Effacer la recherche">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="profile">
                <a href="panier.html" class="cart">
                    <span class="material-icons">shopping_cart</span>
                    <span class="cart-count">0</span>
                </a>
                <a href="connexion.html" class="user">
                    <span class="material-icons">account_circle</span>
                </a>
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
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet braisé.jpg" alt="">
                    <div class="hightlight-desc">
                        <h4>Cuisine Ivoirienne</h4>
                        <p>Dès $105/Semaine</p>
                    </div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/Maroc.jpg" alt="">
                    <div class="hightlight-desc">
                        <h4>Cuisine Marocaine</h4>
                        <p>Dès $105/Semaine</p>
                    </div>
                </div>
                <div class="highlight-card">
                    <img class="highlight-img" src="image/poulet braisé.jpg" alt="">
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
            </div>
        </div>

        <!-- SECTION FILTRE -->
        <div class="main-menus">
            <div class="main-filtrer">
                <div>
                    <h2 class="titre">Formules</h2>
                    <div class="main-arrow">
                        <span class="material-icons back">arrow_back</span>
                        <span class="material-icons next">arrow_forward</span>
                    </div>
                </div>
                <div class="filtre-wrapper wrapper-filtre">
                    <!-- Bouton : toutes les cuisines -->
                    <div class="flitre-card" data-cuisine="toutes">
                        <div class="filtre-icon">
                            <span class="material-icons">restaurant</span>
                        </div>
                        <p>Toutes les Cuisines</p>
                    </div>

                    <!-- Filtres dynamiques -->
                    <?php foreach ($cuisines as $cuisine): ?>
                        <div class="flitre-card" data-cuisine="<?= htmlspecialchars(strtolower($cuisine)) ?>">
                            <div class="filtre-icon">
                                <span class="material-icons">restaurant</span>
                            </div>
                            <p><?= htmlspecialchars($cuisine) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr class="divider">

            <div class="main-details">
                <h2 class="main-titre" id="choix-formules">Menu</h2>
                <p id="compteur-plats" style="margin: 10px 0; font-weight: bold; color: #00796b;">0 plats disponibles</p>
                <div class="details-wrapper">
                    <!-- Les cartes des formules seront insérées ici par JS -->
                </div>
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
                <li><a href="inscription.html">Inscription</a></li>
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
            <p>
                +111 222-4568<br>
                plataflex@gmail.com
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>RestoEtudiant&copy; / 2025 tous droits réservés.</p>
    </div>
</footer>

<div class="overlay"></div>
<script type="module" src="./controller/index.js"></script>
</body>
</html>
