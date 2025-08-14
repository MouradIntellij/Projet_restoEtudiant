<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=localhost;dbname=RestoEtudiantDB;charset=utf8", "root", "");

$role = $_GET['role'] ?? 'Etudiant';
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateur WHERE role = :role ORDER BY nom");
$stmt->execute(['role' => $role]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
