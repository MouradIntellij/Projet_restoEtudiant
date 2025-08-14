<?php
session_start();

// Sécurité : rediriger si pas connecté ou pas étudiant
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'etudiant') {
    header('Location: /Projet_restoEtudiant/php/connexion.php?error=unauthorized');
    exit();
}

// Connexion PDO (adapte ce require selon ta config)
require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

// Récupérer toutes les formules groupées par cuisine
$sql = "SELECT * FROM formule ORDER BY cuisine, titre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$formules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les formules par cuisine
$formules_par_cuisine = [];
foreach ($formules as $formule) {
    $cuisine = $formule['cuisine'] ?? 'Autres';
    if (!isset($formules_par_cuisine[$cuisine])) {
        $formules_par_cuisine[$cuisine] = [];
    }
    $formules_par_cuisine[$cuisine][] = $formule;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Étudiant - RestoEtudiant</title>
    <link rel="stylesheet" href="/Projet_restoEtudiant/Styles/restaurateur.css" />
</head>
<body>
<header>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?></h1>
    <a href="/Projet_restoEtudiant/php/logout.php">Déconnexion</a>
</header>

<main>
    <?php foreach ($formules_par_cuisine as $cuisine => $liste_formules): ?>
        <section>
            <h2><?= htmlspecialchars($cuisine) ?></h2>
            <div class="formules-grid">
                <?php foreach ($liste_formules as $formule): ?>
                    <article class="formule-card">
                        <h3><?= htmlspecialchars($formule['titre']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($formule['description'])) ?></p>
                        <p><strong>Prix :</strong> <?= number_format($formule['prix'], 2, ',', ' ') ?> $</p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>
