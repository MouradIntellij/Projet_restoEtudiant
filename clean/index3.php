<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Pas connecté → redirection vers la page de connexion
    header('Location: connexion.php');
    exit();
}

// L'utilisateur est connecté, on peut afficher le dashboard selon le rôle
$role = $_SESSION['role'] ?? 'etudiant'; // ou 'restaurateur'

// Ici, tu affiches ton contenu de dashboard
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - RestoEtudiant</title>
    <!-- Tes CSS et scripts -->
</head>
<body>
<h1>Bienvenue <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?> !</h1>

<?php if ($role === 'restaurateur'): ?>
    <p>Voici le tableau de bord des restaurateurs.</p>
    <!-- Contenu spécifique restaurateur -->
<?php else: ?>
    <p>Voici le tableau de bord des étudiants.</p>
    <!-- Contenu spécifique étudiant -->
<?php endif; ?>

<a href="php/deconnexion.php">Se déconnecter</a>
</body>
</html>
