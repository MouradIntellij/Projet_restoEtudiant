<?php
session_start(); // Démarre la session s’il y en a une

// Vide les variables de session
$_SESSION = [];

// Supprime le cookie de session (optionnel mais recommandé)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruit la session
session_destroy();

// Redirige vers la page de login ou renvoie une réponse JSON
// Exemple 1 : Redirection
header('Location: login.php'); // ou login.php si tu as un formulaire là
exit;

// Exemple 2 (si tu veux utiliser AJAX ou Fetch pour logout)
// echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
