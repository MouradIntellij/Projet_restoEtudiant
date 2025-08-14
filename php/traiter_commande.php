<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    header("Location: connexion.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'], $_POST['action'])) {
    $idCommande = (int)$_POST['id_commande'];
    $action = $_POST['action'];

    // Connexion DB (même config que index.php)
    $dsn = "mysql:host=localhost;dbname=resto_etudiant;charset=utf8mb4";
    $user = "root";
    $pass = "";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        if ($action === 'valider') {
            // Met à jour le statut de la commande
            $stmt = $pdo->prepare("UPDATE commandes SET statut = 'Préparée' WHERE id = ?");
            $stmt->execute([$idCommande]);
        }
    } catch (PDOException $e) {
        die("Erreur base données: " . $e->getMessage());
    }
}

header("Location: index.php");
exit();
