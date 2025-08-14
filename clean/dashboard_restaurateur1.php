<?php
session_start();

// 🔒 Sécurité : rediriger si l'utilisateur n'est pas restaurateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Restaurateur - RestoEtudiant</title>
    <link rel="stylesheet" href="./Styles/connexion.css" />
</head>
<body>
<header>
    <h1>Bienvenue, <?= htmlspecialchars($prenom) ?> ! 👨‍🍳</h1>
    <p>Voici votre espace restaurateur.</p>
    <a href="/Projet_restoEtudiant/php/deconnexion.php" class="logout-btn">Se déconnecter</a>
</header>

<main>
    <section>
        <h2>Mes offres de repas 🧾</h2>
        <p>Gérez vos plats proposés aux étudiants.</p>
        <!-- À compléter avec ton back-office -->
    </section>

    <section>
        <h2>Profil</h2>
        <p><strong>Nom :</strong> <?= htmlspecialchars($nom) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
    </section>
</main>
</body>
</html>
