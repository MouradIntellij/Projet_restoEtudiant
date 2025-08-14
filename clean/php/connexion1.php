<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

$role = $_GET['role'] ?? 'etudiant';
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="/Projet_restoEtudiant/Styles/connexion.css">
</head>
<body>
<main class="login-section">
    <h2><?= $role === 'restaurateur' ? 'Connexion Restaurateurs' : 'Connexion Étudiants' ?></h2>

    <?php if ($error): ?>
        <p style="color:red;">Erreur de connexion. Vérifiez vos identifiants.</p>
    <?php endif; ?>

    <form method="POST" action="/Projet_restoEtudiant/php/traitement_connexion.php">
        <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

        <label>Email :</label>
        <input type="email" name="email" required>

        <label>Mot de passe :</label>
        <input type="password" name="password" required>

        <button type="submit">Connexion</button>
    </form>
</main>
</body>
</html>
