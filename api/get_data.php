<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../php/db_connect.php';
$pdo = getPDO();

// Sécurité : accès étudiant uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

try {
    // Récupération des plats
    $platsStmt = $pdo->query("SELECT * FROM plat");
    $plats = $platsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des formules
    $formulesStmt = $pdo->query("SELECT * FROM formule WHERE etat = 'public'");
    $formules = $formulesStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'plats' => $plats,
        'formules' => $formules
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
