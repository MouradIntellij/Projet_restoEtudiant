<?php
# panier.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formuleId = $_POST['id_formule'] ?? null;
    $titre = $_POST['titre'] ?? '';
    $prix = $_POST['prix'] ?? 0;

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $existe = false;
    foreach ($_SESSION['panier'] as &$item) {
        if ($item['id_formule'] == $formuleId) {
            $item['quantite'] += 1;
            $existe = true;
            break;
        }
    }
    unset($item);

    if (!$existe) {
        $_SESSION['panier'][] = [
            'id_formule' => $formuleId,
            'titre' => $titre,
            'prix' => $prix,
            'quantite' => 1
        ];
    }

    header('Location: panier.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        button.btn-modifier {
            padding: 4px 10px;
            font-size: 16px;
            margin: 0 5px;
            cursor: pointer;
        }
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<h1>Votre Panier</h1>
<a href="../dashboard_gestion_formules.php">← Retour</a>
<hr>

<?php if (empty($_SESSION['panier'])): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table>
        <tr><th>Formule</th><th>Prix</th><th>Quantité</th><th>Total</th></tr>
        <?php
        $total = 0;
        foreach ($_SESSION['panier'] as $item):
            $sousTotal = $item['prix'] * $item['quantite'];
            $total += $sousTotal;
            $id = $item['id_formule'];
            ?>
            <tr id="ligne-<?= $id ?>">
                <td><?= htmlspecialchars($item['titre']) ?></td>
                <td><?= number_format($item['prix'], 2) ?> $</td>
                <td>
                    <button class="btn-modifier" data-id="<?= $id ?>" data-action="decrease">−</button>
                    <span class="quantite" id="quantite-<?= $id ?>"><?= $item['quantite'] ?></span>
                    <button class="btn-modifier" data-id="<?= $id ?>" data-action="increase">+</button>
                </td>
                <td id="total-<?= $id ?>"><?= number_format($sousTotal, 2) ?> $</td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong id="total-general"><?= number_format($total, 2) ?> $</strong></td>
        </tr>
    </table>
    <br>
    <form action="valider_commande.php" method="post">
        <button type="submit">Valider la commande</button>
    </form>
    <form method="post" action="vider_panier.php" onsubmit="return confirm('Êtes-vous sûr de vouloir vider le panier ?');">
        <button type="submit" style="background:red;color:white;">Vider le panier</button>
    </form>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const boutons = document.querySelectorAll('.btn-modifier');

        boutons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const action = this.dataset.action;

                fetch('modifier_quantite.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ id_formule: id, action: action })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.removed) {
                                const ligne = document.getElementById('ligne-' + id);
                                ligne.remove();
                            } else {
                                document.getElementById('quantite-' + id).textContent = data.quantite;
                                document.getElementById('total-' + id).textContent = data.sousTotal + ' $';
                            }

                            // Met à jour le total général
                            if (data.totalGeneral !== undefined) {
                                document.getElementById('total-general').textContent = data.totalGeneral + ' $';
                            } else {
                                // Recalcul via reload en secours
                                location.reload();
                            }
                        } else {
                            alert(data.message || "Erreur.");
                        }
                    });
            });
        });
    });
</script>
</body>
</html>
