<?php
// [1] Récupère le rôle depuis l'URL (GET). Par défaut, le rôle est "etudiant" si non défini.
$role = $_GET['role'] ?? 'etudiant';


// Forcer le cookie de session à être valide sur toute la racine "/"
session_set_cookie_params([
    'lifetime' => 0,       // Durée de vie du cookie, 0 = jusqu'à fermeture du navigateur
    'path' => '/',         // Important: cookie disponible sur toute la racine
    'domain' => '',        // Par défaut le domaine actuel
    'secure' => false,     // true si HTTPS, sinon false (à adapter)
    'httponly' => true,    // Sécurité, interdit l'accès au cookie via JS
    'samesite' => 'Lax'    // Anti-CSRF, 'Strict' ou 'Lax'
]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- [2] Configuration de base du document HTML -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Connexion - RestoEtudiant</title>

    <!-- [3] Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="./Styles/connexion.css" />

    <!-- [4] Import de la police Google "Open Sans" -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet" />

    <!-- [5] Import des icônes Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>
<body>

<!-- [6] Section principale de la page de connexion -->
<main class="login-section">

    <!-- [7] Titre du formulaire change selon le rôle -->
    <h2 id="form-title">
        <?= $role === 'restaurateur' ? 'Connexion Restaurateurs' : 'Connexion Étudiants' ?>
    </h2>

    <!-- [8] Message d'accueil dynamique selon le rôle -->
    <p class="msg" id="welcome-msg">
        <strong>
            <?= $role === 'restaurateur'
                ? 'Espace réservé aux restaurateurs partenaires'
                : 'Bienvenue sur RestoEtudiant' ?>
        </strong>
    </p>

    <!-- [9] Affichage conditionnel d'un message d'erreur si l'URL contient ?error=... -->
    <?php if (isset($_GET['error'])): ?>
        <div class="error-msg">Identifiants incorrects. Veuillez réessayer.</div>
    <?php endif; ?>

    <!-- [10] Onglets de sélection du rôle -->
    <div class="tabs">
        <button class="tab <?= $role === 'etudiant' ? 'active' : '' ?>" id="etudiants-tab">Étudiants</button>
        <button class="tab <?= $role === 'restaurateur' ? 'active' : '' ?>" id="restaurateurs-tab">Restaurateurs</button>
    </div>

    <!-- [11] Formulaire de connexion -->
    <form method="POST" action="php/traitement_connexion.php" class="login-form" id="login-form">

        <!-- [12] Champ caché pour envoyer le rôle sélectionné au serveur -->
        <input type="hidden" name="role" id="role" value="<?= $role ?>" />

        <!-- [13] Champ email avec libellé et placeholder dynamiques -->
        <div class="form-elt">
            <label for="email" id="email-label">
                Email <?= $role === 'restaurateur' ? 'Restaurateur' : 'Étudiant' ?>:
            </label>
            <input type="email" name="email" id="email" required
                   placeholder="<?= $role === 'restaurateur'
                       ? 'restaurateur@exemple.com'
                       : 'etudiant@exemple.com' ?>" />
        </div>

        <!-- [14] Champ mot de passe avec icône de visibilité -->
        <div class="form-elt">
            <label for="password" id="password-label">Mot de passe :</label>
            <div class="passwd-wrap">
                <input type="password" name="password" id="password" required placeholder="Votre mot de passe" />
                <span id="togglePassword" class="material-icons toggle-password" style="cursor:pointer;">
                    visibility
                </span>
            </div>
        </div>

        <!-- [15] Case à cocher "Se souvenir de moi" -->
        <div class="form-elt checkbox-elt" id="remember-me-elt">
            <input type="checkbox" id="remember-me" name="remember-me" />
            <label for="remember-me">Se souvenir de moi</label>
        </div>

        <!-- [16] Bouton de soumission -->
        <div class="btn-container">
            <button type="submit" id="btn-connexion" class="btn-connexion">
                <span class="material-icons">login</span>
                Connexion
            </button>
        </div>

        <!-- [17] Liens complémentaires -->
        <div class="form-links">
            <a href="modedepasse-oublie.html">Mot de passe oublié ?</a>
            <a href="Inscription.html">Première utilisation?</a>
        </div>
    </form>

    <!-- [18] Lien d'information en bas de formulaire -->
    <div class="form-bottom">
        <a href="#">Découvrir le RestoEtudiant</a>
    </div>

    <!-- [19] Slogan de la plateforme -->
    <div class="slogan">
        <p><strong>Manger bien, Étudier mieux, dépensez moins,</strong> c'est simple et abordable</p>
    </div>
</main>

<!-- [20] Script JS pour gérer les interactions de connexion -->
<script type="module" src="./controller/connexion.js"></script>

</body>
</html>
