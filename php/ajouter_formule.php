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

if (!isset($data['titre'], $data['description'], $data['prix'], $data['cuisine'])) {
    echo json_encode(['success' => false, 'message' => 'Champs requis manquants']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO formule (titre, description, prix, cuisine, date_ajout) VALUES (?, ?, ?, ?, NOW())");
$success = $stmt->execute([
    $data['titre'],
    $data['description'],
    (float) $data['prix'],
    $data['cuisine']
]);

echo json_encode(['success' => $success]);
