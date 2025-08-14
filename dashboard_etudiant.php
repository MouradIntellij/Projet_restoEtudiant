<?php
session_start();

// Sécurité : rediriger si pas connecté ou pas étudiant
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'etudiant') {
    header('Location: /Projet_restoEtudiant/php/connexion.php?error=unauthorized');
    exit();
}

// Connexion PDO (à adapter selon ta structure)
require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

// Récupérer les infos utilisateur
$userId = $_SESSION['user_id'];
$prenom = $_SESSION['prenom'] ?? 'Utilisateur';

// Récupérer toutes les formules groupées par cuisine
$sql = "SELECT * FROM formule ORDER BY cuisine, titre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$formules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les formules par cuisine
$formules_par_cuisine = [];
foreach ($formules as $formule) {
    $cuisine = $formule['cuisine'] ?? 'Autres';
    $formules_par_cuisine[$cuisine][] = $formule;
}

// Récupérer les commandes de l'étudiant
$sqlCommandes = "
    SELECT c.*, GROUP_CONCAT(f.titre SEPARATOR ', ') AS titres_formules
    FROM commande c
    LEFT JOIN commande_formule cf ON cf.commande_id = c.id
    LEFT JOIN formule f ON f.id = cf.formule_id
    WHERE c.utilisateur_id = ?
    GROUP BY c.id
    ORDER BY c.date_commande DESC
";
$stmt = $pdo->prepare($sqlCommandes);
$stmt->execute([$userId]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Étudiant - RestoEtudiant</title>
    <link rel="stylesheet" href="/Styles/connexion.css" />
    <style>
        .formule-card, .commande-card {
            border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;
        }
        .btn-ajout-panier, #valider-commande {
            background-color: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px;
            cursor: pointer;
        }
        #panier { margin-top: 20px; }
    </style>
</head>
<body>
<header>
    <h1>Bienvenue, <?= htmlspecialchars($prenom) ?></h1>
    <a href="/php/deconnexion.php">Déconnexion</a>
</header>

<main>
    <h2>Formules disponibles</h2>
    <?php foreach ($formules_par_cuisine as $cuisine => $liste_formules): ?>
        <section>
            <h3><?= htmlspecialchars($cuisine) ?></h3>
            <div class="formules-grid">
                <?php foreach ($liste_formules as $formule): ?>
                    <article class="formule-card">
                        <h4><?= htmlspecialchars($formule['titre']) ?></h4>
                        <p><?= nl2br(htmlspecialchars($formule['description'])) ?></p>
                        <p><strong>Prix :</strong> <?= number_format($formule['prix'], 2, ',', ' ') ?> $</p>
                        <button class="btn-ajout-panier" data-id="<?= $formule['id'] ?>">Ajouter au panier</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <div id="panier">
        <h2>Votre panier</h2>
        <ul id="panier-list"></ul>
        <button id="valider-commande">Valider la commande</button>
    </div>

    <h2>Vos commandes</h2>
    <?php if (empty($commandes)): ?>
        <p>Aucune commande passée.</p>
    <?php else: ?>
        <?php foreach ($commandes as $commande): ?>
            <div class="commande-card">
                <p><strong>Commande #<?= $commande['id'] ?></strong></p>
                <p>Date : <?= $commande['date_commande'] ?></p>
                <p>Formules : <?= htmlspecialchars($commande['titres_formules']) ?></p>
                <p>Statut : <?= htmlspecialchars($commande['statut']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script>
    let panier = [];

    document.querySelectorAll('.btn-ajout-panier').forEach(btn => {
        btn.addEventListener('click', () => {
            const idFormule = btn.dataset.id;
            panier.push(idFormule);
            afficherPanier();
        });
    });

    function afficherPanier() {
        const ul = document.getElementById('panier-list');
        ul.innerHTML = '';
        panier.forEach((id, index) => {
            const li = document.createElement('li');
            li.textContent = 'Formule #' + id;

            const btnSuppr = document.createElement('button');
            btnSuppr.textContent = 'Supprimer';
            btnSuppr.style.marginLeft = '10px';
            btnSuppr.onclick = () => {
                panier.splice(index, 1);
                afficherPanier();
            };

            li.appendChild(btnSuppr);
            ul.appendChild(li);
        });
    }

    document.getElementById('valider-commande').addEventListener('click', () => {
        if (panier.length === 0) {
            alert('Votre panier est vide.');
            return;
        }

        fetch('/Projet_restoEtudiant/php/valider_commande.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({formules: panier})
        })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    alert('Commande validée avec succès !');
                    panier = [];
                    afficherPanier();
                    location.reload();
                } else {
                    alert('Erreur : ' + (data.message || 'Une erreur est survenue'));
                }
            })
            .catch(() => alert('Erreur réseau'));
    });
</script>

</body>
</html>
