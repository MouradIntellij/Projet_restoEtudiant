<?php
session_start();
$count = 0;
if (isset($_SESSION['panier'])) {
    $count = array_sum(array_column($_SESSION['panier'], 'quantite'));
}
header('Content-Type: application/json');
echo json_encode(['count' => $count]);
