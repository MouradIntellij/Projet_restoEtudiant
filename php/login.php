<?php
// login.php
require 'db.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($motdepasse, $user['motdepasse'])) {
        // Optionnel : démarrer une session et stocker l'utilisateur connecté
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];

        echo json_encode(['success' => true, 'message' => "Connexion réussie ! Bienvenue " . htmlspecialchars($user['prenom'])]);
    } else {
        echo json_encode(['success' => false, 'message' => "Email ou mot de passe incorrect."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Méthode non autorisée."]);
}
?>
