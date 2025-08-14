<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();


//echo "<pre>";
//echo "Session ID: " . session_id() . "\n";
//print_r($_SESSION);
//echo "</pre>";

echo "Session ID: " . session_id();
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si pas connecté
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

// Rediriger vers le bon dashboard selon le rôle
$role = $_SESSION['role'] ?? 'etudiant';

if ($role === 'etudiant') {
    header("Location: /Projet_restoEtudiant/dashboard_gestion_formules.php");
} elseif ($role === 'restaurateur') {
    header("Location: /Projet_restoEtudiant/dashboard_restaurateur.php");
} else {
    echo "Rôle inconnu.";
}
exit();
