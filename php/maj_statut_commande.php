<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role']!=='restaurateur') {
    http_response_code(403); echo json_encode(['success'=>false,'message'=>'Accès non autorisé']); exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if(!isset($data['commande_id'], $data['statut'])) {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Données incomplètes']); exit;
}
require_once __DIR__.'/db_connect.php';
$pdo = getPDO();
$stmt = $pdo->prepare("UPDATE commande SET statut = ? WHERE id = ?");
$stmt->execute([$data['statut'], intval($data['commande_id'])]);
$updated = $stmt->rowCount()>0;
if ($updated) {
    // récupérer email étudiant
    $stmt2 = $pdo->prepare("SELECT u.email FROM commande c JOIN utilisateur u ON c.utilisateur_id=u.id WHERE c.id = ?");
    $stmt2->execute([intval($data['commande_id'])]);
    $user = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($user && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $to = $user['email'];
        $subject = "Votre commande #{$data['commande_id']} est « {$data['statut']} »";
        $message = "Bonjour,\n\nVotre commande #{$data['commande_id']} a été mise à jour avec le statut « {$data['statut']} ».\n\nCordialement,\nRestoÉtudiant";
        $headers = "From: no-reply@restoetudiant.com\r\n";
        mail($to, $subject, $message, $headers);
    }
}
echo json_encode(['success'=>$updated]);
