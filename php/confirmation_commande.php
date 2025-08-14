<?php
session_start();
require_once 'db_connect.php';
$pdo = getPDO();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$commandeId = $_GET['commande_id'] ?? null;
if (!$commandeId) {
    echo "<p>Aucune commande à afficher.</p>";
    exit();
}

$userId = $_SESSION['user_id'];
// Récupération utilisateur
$stmtUser = $pdo->prepare("SELECT prenom, nom, email FROM utilisateur WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Récap commande
$stmt = $pdo->prepare("
  SELECT f.titre, ci.quantite, ci.prix
  FROM commande_items ci
  JOIN formule f ON ci.formule_id = f.id
  WHERE ci.commande_id = ?");
$stmt->execute([$commandeId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT date_commande FROM commandes WHERE id = ?");
$stmt2->execute([$commandeId]);
$commande = $stmt2->fetch(PDO::FETCH_ASSOC);

$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Commande</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>🎉 Merci pour votre commande, <?= htmlspecialchars($user['prenom']) ?> !</h1>
<p>Commande n° <strong><?= htmlspecialchars($commandeId) ?></strong> passée le <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?>.</p>
<h2>Détails :</h2>
<table>
    <tr><th>Formule</th><th>Quantité</th><th>Prix unitaire</th><th>Sous-total</th></tr>
    <?php foreach ($items as $item):
        $sousTotal = $item['quantite'] * $item['prix'];
        $total += $sousTotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['titre']) ?></td>
            <td><?= $item['quantite'] ?></td>
            <td><?= number_format($item['prix'], 2) ?> $</td>
            <td><?= number_format($sousTotal, 2) ?> $</td>
        </tr>
    <?php endforeach; ?>
    <tr><td colspan="3"><strong>Total</strong></td><td><strong><?= number_format($total, 2) ?> $</strong></td></tr>
</table>
<br>
<a href="../index.php">← Retour à l’accueil</a>
</body>
</html>
