<?php
// get_plats_par_cuisine.php
require_once __DIR__ . '/../php/db_connect.php';
$pdo = getPDO();

header('Content-Type: application/json');

$cuisine = $_GET['cuisine'] ?? '';

try {
    if ($cuisine === '__all__') {
        $stmt = $pdo->query("SELECT id, titre, description, prix, cuisine, IFNULL(image, 'default.jpg') AS image FROM formule ORDER BY cuisine, titre");
    } else {
        $stmt = $pdo->prepare("SELECT id, titre, description, prix, cuisine, IFNULL(image, 'default.jpg') AS image FROM formule WHERE LOWER(cuisine) = LOWER(?) ORDER BY titre");
        $stmt->execute([$cuisine]);
    }

    $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($plats);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des plats : ' . $e->getMessage()
    ]);
}
