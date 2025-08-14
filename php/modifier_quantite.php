<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['panier'])) {
    echo json_encode(['success' => false, 'message' => 'Panier vide']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id_formule'] ?? null;
$action = $data['action'] ?? null;

if (!$id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

$totalGeneral = 0;
foreach ($_SESSION['panier'] as $index => &$item) {
    if ($item['id_formule'] == $id) {
        if ($action === 'increase') {
            $item['quantite']++;
        } elseif ($action === 'decrease') {
            $item['quantite']--;
            if ($item['quantite'] <= 0) {
                unset($_SESSION['panier'][$index]);
                // Recalcul total général
                foreach ($_SESSION['panier'] as $i) {
                    $totalGeneral += $i['quantite'] * $i['prix'];
                }
                echo json_encode([
                    'success' => true,
                    'removed' => true,
                    'id' => $id,
                    'totalGeneral' => number_format($totalGeneral, 2)
                ]);
                exit;
            }
        }
        $sousTotal = $item['quantite'] * $item['prix'];
        // Recalcul total général
        foreach ($_SESSION['panier'] as $i) {
            $totalGeneral += $i['quantite'] * $i['prix'];
        }

        echo json_encode([
            'success' => true,
            'quantite' => $item['quantite'],
            'sousTotal' => number_format($sousTotal, 2),
            'totalGeneral' => number_format($totalGeneral, 2),
            'id' => $id
        ]);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Article introuvable']);
