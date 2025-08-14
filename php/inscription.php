<?php
session_start();
require_once 'db_connect.php';
$pdo=getPDO();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    $universite = $_POST['universite'] ?? '';
    $annee_academique = $_POST['annee_academique'] ?? '';
    $carte_scolaire = $_POST['carte_scolaire'] ?? '';
    $role = ($_POST['role'] ?? 'Etudiant') === 'Restaurateur' ? 'Restaurateur' : 'Etudiant';

    if ($nom && $prenom && $email && $motdepasse) {
        // Vérifier que email n'existe pas
        $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email déjà utilisé";
        } else {
            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, motdepasse, universite, annee_academique, carte_scolaire, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $hash, $universite, $annee_academique, $carte_scolaire, $role]);
            header('Location: connexion.php');
            exit;
        }
    } else {
        $error = "Tous les champs obligatoires doivent être remplis";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Inscription</title></head>
<body>
<h2>Inscription</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>" ?>
<form method="post" action="">
    <label>Nom: <input type="text" name="nom" required></label><br><br>
    <label>Prénom: <input type="text" name="prenom" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Mot de passe: <input type="password" name="motdepasse" required></label><br><br>
    <label>Université: <input type="text" name="universite"></label><br><br>
    <label>Année académique: <input type="text" name="annee_academique"></label><br><br>
    <label>Carte scolaire: <input type="text" name="carte_scolaire"></label><br><br>
    <label>Rôle:
        <select name="role">
            <option value="Etudiant" selected>Étudiant</option>
            <option value="Restaurateur">Restaurateur</option>
        </select>
    </label><br><br>
    <button type="submit">S'inscrire</button>
</form>
<p>Déjà inscrit ? <a href="connexion1.php">Connexion</a></p>
</body>
</html>
