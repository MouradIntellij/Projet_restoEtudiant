<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On récupère soit id_formule soit plat_id
    $id = $_POST['id_formule'] ?? $_POST['plat_id'] ?? null;
    $titre = $_POST['titre'] ?? '';
    $prix = $_POST['prix'] ?? 0;

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $existe = false;
    foreach ($_SESSION['panier'] as &$item) {
        if ($item['id'] == $id) {
            $item['quantite'] += 1;
            $existe = true;
            break;
        }
    }
    unset($item); // Fin de boucle par référence

    if (!$existe && $id !== null) {
        $_SESSION['panier'][] = [
            'id' => $id,
            'titre' => $titre,
            'prix' => $prix,
            'quantite' => 1
        ];
    }

    header('Location: panier.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Votre Panier</h1>
<a href="../dashboard_gestion_formules.php">← Retour</a>
<hr>

<?php if (empty($_SESSION['panier'])): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table>
        <tr><th>Plat</th><th>Prix</th><th>Quantité</th><th>Total</th></tr>
        <?php
        $total = 0;
        foreach ($_SESSION['panier'] as $item):
            $sousTotal = $item['prix'] * $item['quantite'];
            $total += $sousTotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['titre']) ?></td>
                <td><?= number_format($item['prix'], 2) ?> $</td>
                <td><?= $item['quantite'] ?></td>
                <td><?= number_format($sousTotal, 2) ?> $</td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong><?= number_format($total, 2) ?> $</strong></td>
        </tr>
    </table>
    <br>
    <form action="valider_commande.php" method="post">
        <button type="submit">Valider la commande</button>
    </form>
<?php endif; ?>
</body>
</html>
