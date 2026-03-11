<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$sql = "SELECT 
            offres.id,
            offres.titre,
            offres.type_contrat,
            offres.secteur,
            offres.localisation,
            offres.description,
            offres.competences,
            offres.remuneration,
            offres.date_debut,
            offres.created_at,
            entreprises.nom_entreprise
        FROM offres
        INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
        ORDER BY offres.created_at DESC";

$stmt = $pdo->query($sql);
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Liste des offres</h1>

        <?php if (empty($offres)): ?>
            <p>Aucune offre disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($offres as $offre): ?>
                <div class="offre">
                    <h2><?= htmlspecialchars($offre['titre']) ?></h2>
                    <p><strong>Entreprise :</strong> <?= htmlspecialchars($offre['nom_entreprise']) ?></p>
                    <p><strong>Type :</strong> <?= htmlspecialchars($offre['type_contrat']) ?></p>
                    <p><strong>Secteur :</strong> <?= htmlspecialchars($offre['secteur']) ?></p>
                    <p><strong>Localisation :</strong> <?= htmlspecialchars($offre['localisation']) ?></p>
                    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                    <p><strong>Compétences :</strong> <?= nl2br(htmlspecialchars($offre['competences'])) ?></p>
                    <p><strong>Rémunération :</strong> <?= htmlspecialchars((string)($offre['remuneration'] ?? 'Non précisée')) ?></p>
                    <p><strong>Date de début :</strong> <?= htmlspecialchars((string)($offre['date_debut'] ?? 'Non précisée')) ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
