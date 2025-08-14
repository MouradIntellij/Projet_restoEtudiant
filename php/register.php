<?php
// php/register.php

require_once 'db.php'; // utilise la version propre de ton système
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom         = trim($_POST['nom'] ?? '');
    $prenom      = trim($_POST['prenom'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $motdepasse  = $_POST['password'] ?? ''; // pas besoin de trim sur mot de passe
    $universite  = trim($_POST['universite'] ?? '');
    $annee       = trim($_POST['annee_academique'] ?? '');
    $carte       = trim($_POST['carte_scolaire'] ?? '');
    $role        = trim($_POST['role'] ?? 'Etudiant');

    // Validation basique
    if (!$nom || !$prenom || !$email || !$motdepasse || !$role) {
        http_response_code(400);
        echo "Champs obligatoires manquants.";
        exit;
    }

    // Vérifie que l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Email invalide.";
        exit;
    }

    // Vérifier si l’email est déjà utilisé
    $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->fetch()) {
        http_response_code(409);
        echo "Cet email est déjà utilisé.";
        exit;
    }

    // Hasher le mot de passe
    $hashedPassword = password_hash($motdepasse, PASSWORD_DEFAULT);

    // Insertion dans la base
    $sql = "INSERT INTO utilisateur 
        (nom, prenom, email, motdepasse, universite, annee_academique, carte_scolaire, role)
        VALUES 
        (:nom, :prenom, :email, :motdepasse, :universite, :annee, :carte, :role)";

    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        ':nom'        => $nom,
        ':prenom'     => $prenom,
        ':email'      => $email,
        ':motdepasse' => $hashedPassword,
        ':universite' => $universite,
        ':annee'      => $annee,
        ':carte'      => $carte,
        ':role'       => $role
    ]);

    if ($success) {
        http_response_code(200);
        echo "success";
    } else {
        http_response_code(500);
        echo "Erreur lors de l'inscription.";
    }
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}
