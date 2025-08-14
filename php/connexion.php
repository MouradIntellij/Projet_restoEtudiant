<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false, // 🔁 à mettre true en HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Rôle par défaut : etudiant
$role = $_GET['role'] ?? 'etudiant';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Connexion - RestoEtudiant</title>

    <!-- ✅ CSS -->
    <link rel="stylesheet" href="/Projet_restoEtudiant/Styles/connexion.css" />

    <!-- ✅ JS -->
    <script type="module" src="/Projet_restoEtudiant/controller/connexion.js"></script>

    <!-- Google Fonts + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
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

    <!-- ✅ Affichage erreur si mauvais identifiants -->
    <?php if (!empty($_SESSION['login_error'])): ?>
        <div class="error-msg">Identifiants incorrects. Veuillez réessayer.</div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <!-- ✅ Sélecteur de rôle -->
    <div class="tabs">
        <button class="tab <?= $role === 'etudiant' ? 'active' : '' ?>" id="etudiants-tab">Étudiants</button>
        <button class="tab <?= $role === 'restaurateur' ? 'active' : '' ?>" id="restaurateurs-tab">Restaurateurs</button>
    </div>

    <!-- ✅ Formulaire -->
    <form method="POST" action="traitement_connexion.php" class="login-form" id="login-form">
        <input type="hidden" name="role" id="role" value="<?= htmlspecialchars($role) ?>" />

        <div class="form-elt">
            <label for="email" id="email-label">
                Email <?= $role === 'restaurateur' ? 'Restaurateur' : 'Étudiant' ?> :
            </label>
            <input type="email" name="email" id="email" required
                   placeholder="<?= $role === 'restaurateur'
                       ? 'restaurateur@exemple.com'
                       : 'etudiant@exemple.com' ?>" />
        </div>

        <div class="form-elt">
            <label for="password" id="password-label">Mot de passe :</label>
            <div class="passwd-wrap">
                <input type="password" name="password" id="password" required placeholder="Votre mot de passe" />
                <span id="togglePassword" class="material-icons toggle-password" style="cursor:pointer;">
                    visibility
                </span>
            </div>
        </div>

        <div class="form-elt checkbox-elt" id="remember-me-elt">
            <input type="checkbox" id="remember-me" name="remember-me" />
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
            <a href="inscription.php">Première utilisation ?</a>
        </div>
    </form>

    <div class="form-bottom">
        <a href="#">Découvrir le RestoEtudiant</a>
    </div>

    <div class="slogan">
        <p><strong>Manger bien, étudier mieux, dépenser moins</strong> – c'est simple et abordable.</p>
    </div>
</main>

</body>
</html>
