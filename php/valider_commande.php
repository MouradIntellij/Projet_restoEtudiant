<?php
session_start();
require_once 'db_connect.php';
$pdo = getPDO();

if (!isset($_SESSION['user_id']) || empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit();
}

$userId = $_SESSION['user_id'];
$dateCommande = date('Y-m-d H:i:s');

try {
    // Démarrer transaction
    $pdo->beginTransaction();

    // Insérer la commande
    $stmt = $pdo->prepare("INSERT INTO commandes (user_id, date_commande) VALUES (?, ?)");
    $stmt->execute([$userId, $dateCommande]);
    $commandeId = $pdo->lastInsertId();

    // Insérer les items du panier
    $stmtItem = $pdo->prepare("INSERT INTO commande_items (commande_id, formule_id, quantite, prix) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['panier'] as $item) {
        $stmtItem->execute([
            $commandeId,
            $item['id_formule'],
            $item['quantite'],
            $item['prix']
        ]);
    }

    // Valider la transaction
    $pdo->commit();

    // Vider le panier
    unset($_SESSION['panier']);

    // Redirection vers une page de confirmation
    header("Location: confirmation_commande.php?commande_id=$commandeId");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
    exit();
}
