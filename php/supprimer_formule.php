<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once 'db_connect.php';
$pdo = getPDO();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM formule WHERE id = ?");
$success = $stmt->execute([$data['id']]);

echo json_encode(['success' => $success]);
