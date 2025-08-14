<?php
session_start();
require_once __DIR__ . '/../php/db_connect.php';
$pdo = getPDO();

header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    http_response_code(403);
    echo json_encode(['error'=>'Accès non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Requête invalide']);
    exit();
}

$id = intval($_POST['id']);
try {
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'préparée' WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'message'=>'Aucune commande mise à jour']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
