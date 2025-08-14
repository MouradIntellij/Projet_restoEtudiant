<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: php/connexion.php');
    exit;
}


require_once 'php/db.php';
$pdo=getPDO();

$user = $_SESSION['user'];

if ($user['role'] === 'Etudiant') {
    $stmt = $pdo->query("SELECT * FROM formule ORDER BY date_ajout DESC");
    $formules = $stmt->fetchAll();

    // Récupérer commandes de cet utilisateur
    $stmt = $pdo->prepare("
        SELECT c.*, GROUP_CONCAT(f.titre SEPARATOR ', ') AS titres_formules
        FROM commande c
        LEFT JOIN commande_formule cf ON cf.commande_id = c.id
        LEFT JOIN formule f ON f.id = cf.formule_id
        WHERE c.utilisateur_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$user['id']]);
    $commandes = $stmt->fetchAll();

} elseif ($user['role'] === 'Restaurateur') {
    // Toutes les commandes avec infos utilisateurs et formules
    $stmt = $pdo->query("
        SELECT c.id AS commande_id, c.date_commande, c.statut,
               u.prenom, u.nom, u.email,
               GROUP_CONCAT(CONCAT(f.titre, ' (x', cf.quantite, ')') SEPARATOR ', ') AS details_formules
        FROM commande c
        JOIN utilisateur u ON c.utilisateur_id = u.id
        JOIN commande_formule cf ON cf.commande_id = c.id
        JOIN formule f ON f.id = cf.formule_id
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $commandes = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page principale</title>
    <style>
        body {font-family: Arial,sans-serif; margin: 20px;}
        .formule-card, .commande-card { border:1px solid #ddd; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .btn-ajout-panier, .btn-maj-statut { cursor: pointer; padding: 6px 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; }
        .btn-maj-statut { background-color: #007bff; }
        #panier { margin-top: 20px; }
    </style>
</head>
<body>

<h1>Bienvenue <?=htmlspecialchars($user['prenom'])?></h1>
<p><a href="deconnexion.php">Se déconnecter</a></p>

<?php if ($user['role'] === 'Etudiant'): ?>

    <h2>Formules disponibles</h2>
    <div id="formules">
        <?php foreach ($formules as $formule): ?>
            <div class="formule-card">
                <h3><?=htmlspecialchars($formule['titre'])?></h3>
                <p><?=htmlspecialchars($formule['description'])?></p>
                <p><strong>Prix : <?=number_format($formule['prix'], 2)?> $</strong></p>
                <button class="btn-ajout-panier" data-id="<?= $formule['id'] ?>">Ajouter au panier</button>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="panier">
        <h2>Votre panier</h2>
        <ul id="panier-list"></ul>
        <button id="valider-commande">Valider la commande</button>
    </div>

    <h2>Vos commandes</h2>
<?php if (count($commandes) === 0): ?>
    <p>Aucune commande passée.</p>
<?php else: ?>
    <?php foreach ($commandes as $commande): ?>
    <div class="commande-card">
        <p><strong>Commande #<?= $commande['id'] ?></strong></p>
        <p>Date : <?= $commande['date_commande'] ?></p>
        <p>Formules : <?= htmlspecialchars($commande['titres_formules'] ?? '—') ?></p>
        <p>Statut : <?= $commande['statut'] ?></p>
    </div>
<?php endforeach; ?>
<?php endif; ?>

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
            panier.forEach((id, idx) => {
                const li = document.createElement('li');
                li.textContent = 'Formule #' + id;
                const btnSuppr = document.createElement('button');
                btnSuppr.textContent = 'Supprimer';
                btnSuppr.style.marginLeft = '10px';
                btnSuppr.onclick = () => {
                    panier.splice(idx, 1);
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
            fetch('valider_commande.php', {
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
                        alert('Erreur : ' + (data.message || ''));
                    }
                })
                .catch(() => alert('Erreur réseau'));
        });
    </script>

<?php elseif ($user['role'] === 'Restaurateur'): ?>

    <h2>Commandes des étudiants</h2>
<?php if (count($commandes) === 0): ?>
    <p>Aucune commande pour le moment.</p>
<?php else: ?>
    <?php foreach ($commandes as $commande): ?>
    <div class="commande-card" data-id="<?= $commande['commande_id'] ?>">
        <p><strong>Commande #<?= $commande['commande_id'] ?></strong></p>
        <p>Étudiant : <?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?> (<?= htmlspecialchars($commande['email']) ?>)</p>
        <p>Date : <?= $commande['date_commande'] ?></p>
        <p>Formules commandées : <?= htmlspecialchars($commande['details_formules']) ?></p>
        <p>Statut : <span class="statut"><?= $commande['statut'] ?></span></p>
        <select class="select-statut">
            <option value="en cours" <?= $commande['statut'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
            <option value="validée" <?= $commande['statut'] === 'validée' ? 'selected' : '' ?>>Validée</option>
            <option value="livrée" <?= $commande['statut'] === 'livrée' ? 'selected' : '' ?>>Livrée</option>
            <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée</option>
        </select>
        <button class="btn-maj-statut">Mettre à jour</button>
    </div>
<?php endforeach; ?>
<?php endif; ?>

    <script>
        document.querySelectorAll('.btn-maj-statut').forEach(btn => {
            btn.addEventListener('click', () => {
                const divCommande = btn.closest('.commande-card');
                const commandeId = divCommande.dataset.id;
                const selectStatut = divCommande.querySelector('.select-statut');
                const nouveauStatut = selectStatut.value;

                fetch('maj_statut_commande.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({commande_id: commandeId, statut: nouveauStatut})
                })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.success) {
                            alert('Statut mis à jour');
                            divCommande.querySelector('.statut').textContent = nouveauStatut;
                        } else {
                            alert('Erreur : ' + (data.message || ''));
                        }
                    })
                    .catch(() => alert('Erreur réseau'));
            });
        });
    </script>

<?php endif; ?>

</body>
</html>
