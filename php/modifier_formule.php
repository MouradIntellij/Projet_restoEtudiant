<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

require_once 'db_connect.php';
$pdo = getPDO();

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$sql = "UPDATE formule SET titre = ?, description = ?, prix = ?, cuisine = ?, disponible = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([
    $data['titre'],
    $data['description'],
    $data['prix'],
    $data['cuisine'],
    isset($data['disponible']) && $data['disponible'] ? 1 : 0,
    $data['id']
]);

echo json_encode(['success' => $success]);
