<?php

// Forcer le cookie de session à être valide sur toute la racine "/"
session_set_cookie_params([
    'lifetime' => 0,       // Durée de vie du cookie, 0 = jusqu'à fermeture du navigateur
    'path' => '/',         // Important: cookie disponible sur toute la racine
    'domain' => '',        // Par défaut le domaine actuel
    'secure' => false,     // true si HTTPS, sinon false (à adapter)
    'httponly' => true,    // Sécurité, interdit l'accès au cookie via JS
    'samesite' => 'Lax'    // Anti-CSRF, 'Strict' ou 'Lax'
]);
session_start();
// 🧪 DEBUG TEMPORAIRE
echo '<pre>';
echo "🎯 Debug INDEX.PHP\n";
var_dump($_SESSION);
echo '</pre>';
exit();


if (!isset($_SESSION['user_id'])) {
    // Pas connecté, rediriger vers connexion
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

// Récupérer les infos session en sécurisant les accès aux clés
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? 'etudiant';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Connexion - RestoEtudiant</title>
    <link rel="stylesheet" href="./Styles/connexion.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<main class="login-section">
    <h2 id="form-title">
        <?= $role === 'restaurateur' ? 'Connexion Restaurateurs' : 'Connexion Étudiants' ?>
    </h2>
    <p class="msg" id="welcome-msg">
        <strong>
            <?= $role === 'restaurateur'
                ? 'Espace réservé aux restaurateurs partenaires'
                : 'Bienvenue sur RestoEtudiant' ?>
        </strong>
    </p>

    <div class="tabs">
        <button class="tab <?= $role === 'etudiant' ? 'active' : '' ?>"
                id="etudiants-tab">Étudiants</button>
        <button class="tab <?= $role === 'restaurateur' ? 'active' : '' ?>"
                id="restaurateurs-tab">Restaurateurs</button>
    </div>

    <form method="POST" action="php/traitement_connexion.php" class="login-form" id="login-form">
        <input type="hidden" name="role" id="role" value="<?= htmlspecialchars($role) ?>">

        <div class="form-elt">
            <label for="email" id="email-label">
                Email <?= $role === 'restaurateur' ? 'Restaurateur' : 'Étudiant' ?>:
            </label>
            <input type="email" name="email" id="email" required
                   placeholder="<?= $role === 'restaurateur'
                       ? 'restaurateur@exemple.com'
                       : 'etudiant@exemple.com' ?>">
        </div>

        <div class="form-elt">
            <label for="password" id="password-label">Mot de passe :</label>
            <div class="passwd-wrap">
                <input type="password" name="password" id="password" required placeholder="Votre mot de passe">
                <span id="togglePassword" class="material-icons toggle-password" style="cursor:pointer;">
                    visibility
                </span>
            </div>
        </div>

        <div class="form-elt checkbox-elt" id="remember-me-elt">
            <input type="checkbox" id="remember-me" name="remember-me">
            <label for="remember-me">Se souvenir de moi</label>
        </div>

        <div class="btn-container">
            <button type="submit" id="btn-connexion" class="btn-connexion">
                <span class="material-icons">login</span>
                Connexion
            </button>
        </div>

        <div class="form-links">
            <a href="modedepasse-oublie.html">Mot de passe oublié ?</a>
            <a href="Inscription.html">Première utilisation?</a>
        </div>
    </form>

    <div class="form-bottom">
        <a href="#">Découvrir le RestoEtudiant</a>
    </div>

    <div class="slogan">
        <p><strong>Manger bien, Étudier mieux, dépensez moins,</strong> c'est simple et abordable</p>
    </div>
</main>

<script type="module" src="./controller/connexion.js"></script>
</body>
</html>
