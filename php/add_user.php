<?php
$pdo = new PDO("mysql:host=localhost;dbname=RestoEtudiantDB;charset=utf8", "root", "");

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$universite = trim($_POST['universite'] ?? '');
$annee = trim($_POST['annee_academique'] ?? '');
$carte = trim($_POST['carte_scolaire'] ?? '');
$role = trim($_POST['role'] ?? 'Etudiant');

if (!$nom || !$prenom || !$email || !$password || !$role) {
    echo "Champs obligatoires manquants.";
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    echo "Cet email est déjà utilisé.";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO utilisateur (nom, prenom, email, motdepasse, universite, annee_academique, carte_scolaire, role)
        VALUES (:nom, :prenom, :email, :motdepasse, :universite, :annee, :carte, :role)";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([
    'nom' => $nom,
    'prenom' => $prenom,
    'email' => $email,
    'motdepasse' => $hashedPassword,
    'universite' => $universite,
    'annee' => $annee,
    'carte' => $carte,
    'role' => $role
]);

echo $success ? "success" : "Erreur lors de l'insertion.";
