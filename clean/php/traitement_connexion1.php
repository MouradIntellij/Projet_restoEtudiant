<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=restoetudiantdb;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

$email = $_POST['email'] ?? '';
$motdepasse = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'etudiant';

if (empty($email) || empty($motdepasse) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../connexion.php?role=$role&error=1");
    exit();
}

$sql = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if ($utilisateur && password_verify($motdepasse, $utilisateur['motdepasse'])) {
    $_SESSION['user_id'] = $utilisateur['id'];
    $_SESSION['prenom'] = $utilisateur['prenom'];

    // Utilisation de json_encode avec JSON_THROW_ON_ERROR pour plus de robustesse
    try {
        $utilisateurJS = json_encode([
            'email'      => $utilisateur['email'],
            'nom'        => $utilisateur['nom'],
            'prenom'     => $utilisateur['prenom'],
            'telephone'  => $utilisateur['telephone'],
            'universite' => $utilisateur['universite'],
            'annee'      => $utilisateur['annee'],
            'adresse'    => $utilisateur['adresse']
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        die('Erreur JSON : ' . $e->getMessage());
    }

    ?>
    <script>
        const utilisateur = <?= $utilisateurJS ?>;
        sessionStorage.setItem('utilisateurConnecter', utilisateur.email);

        (async () => {
            const dbRequest = indexedDB.open('utilisateursDB', 1);

            dbRequest.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('Utilisateurs')) {
                    db.createObjectStore('Utilisateurs', { keyPath: 'email' });
                }
            };

            dbRequest.onsuccess = (event) => {
                const db = event.target.result;
                const tx = db.transaction('Utilisateurs', 'readwrite');
                const store = tx.objectStore('Utilisateurs');
                store.put(utilisateur);

                tx.oncomplete = () => {
                    window.location.href = '../index.php';
                };
            };

            dbRequest.onerror = (event) => {
                alert('Erreur IndexedDB: ' + event.target.error);
                window.location.href = '../index.php';
            };
        })();
    </script>
    <?php
    exit();
} else {
    header("Location: ../connexion.php?role=$role&error=1");
    exit();
}
