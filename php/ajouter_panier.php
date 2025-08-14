<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$titre = $data['titre'] ?? '';
$prix = $data['prix'] ?? 0;

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$trouve = false;
foreach ($_SESSION['panier'] as &$item) {
    if ($item['id'] == $id) {
        $item['quantite'] += 1;
        $trouve = true;
        break;
    }
}
unset($item);

if (!$trouve && $id !== null) {
    $_SESSION['panier'][] = [
        'id' => $id,
        'titre' => $titre,
        'prix' => $prix,
        'quantite' => 1
    ];
}

echo json_encode(['success' => true, 'message' => 'Plat ajouté au panier']);
