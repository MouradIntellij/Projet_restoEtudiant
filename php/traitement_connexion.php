<?php
// 🔐 Configuration de la session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false, // 🔁 à mettre true si HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/db_connect.php';
$pdo = getPDO();

// 🔎 Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$motdepasse = $_POST['password'] ?? '';
$role = ucfirst(strtolower($_POST['role'] ?? 'etudiant'));  // Format : Etudiant ou Restaurateur

// 🔍 Recherche de l'utilisateur
$sql = "SELECT * FROM utilisateur WHERE email = :email AND role = :role LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':role', $role);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// 🛡 Vérification du mot de passe
if ($utilisateur && password_verify($motdepasse, $utilisateur['motdepasse'])) {
    // 🟢 Authentification réussie
    $_SESSION['user_id'] = $utilisateur['id'];
    $_SESSION['prenom'] = $utilisateur['prenom'];
    $_SESSION['nom'] = $utilisateur['nom'];
    $_SESSION['email'] = $utilisateur['email'];
    $_SESSION['role'] = strtolower($utilisateur['role']);

    // 🔁 Redirection selon rôle
    if ($_SESSION['role'] === 'etudiant') {
        header("Location: /Projet_restoEtudiant/dashboard_gestion_formules.php");
    } elseif ($_SESSION['role'] === 'restaurateur') {
        header("Location: /Projet_restoEtudiant/dashboard_restaurateur.php");
    } else {
        header("Location: /Projet_restoEtudiant/php/connexion.php?error=role_invalide");
    }
    exit();
} else {
    // 🔴 Échec de connexion
    $_SESSION['login_error'] = true;
    $role_url = urlencode(strtolower($role));
    header("Location: /Projet_restoEtudiant/php/connexion.php?role={$role_url}&error=1");
    exit();
}
