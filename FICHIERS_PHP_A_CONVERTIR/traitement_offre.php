<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$emailEntreprise = trim($_POST['email_entreprise'] ?? '');
$titre = trim($_POST['titre'] ?? '');
$typeContrat = trim($_POST['type_contrat'] ?? '');
$secteur = trim($_POST['secteur'] ?? '');
$localisation = trim($_POST['localisation'] ?? '');
$description = trim($_POST['description'] ?? '');
$competences = trim($_POST['competences'] ?? '');
$remuneration = trim($_POST['remuneration'] ?? '');
$dateDebut = trim($_POST['date_debut'] ?? '');

$typesAutorises = ['stage', 'alternance', 'emploi'];

if (
    !filter_var($emailEntreprise, FILTER_VALIDATE_EMAIL) ||
    $titre === '' ||
    !in_array($typeContrat, $typesAutorises, true) ||
    $secteur === '' ||
    $localisation === '' ||
    $description === '' ||
    $competences === ''
) {
    exit('Formulaire invalide.');
}

try {
    $stmtEntreprise = $pdo->prepare("
        SELECT entreprises.id
        FROM entreprises
        INNER JOIN comptes ON entreprises.compte_id = comptes.id
        WHERE comptes.email = :email
          AND comptes.role = 'entreprise'
        LIMIT 1
    ");
    $stmtEntreprise->execute([':email' => $emailEntreprise]);
    $entreprise = $stmtEntreprise->fetch();

    if (!$entreprise) {
        exit("Aucune entreprise n'est associée à cet email.");
    }

    $sql = "INSERT INTO offres
            (entreprise_id, titre, type_contrat, secteur, localisation, description, competences, remuneration, date_debut)
            VALUES
            (:entreprise_id, :titre, :type_contrat, :secteur, :localisation, :description, :competences, :remuneration, :date_debut)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':entreprise_id' => $entreprise['id'],
        ':titre' => $titre,
        ':type_contrat' => $typeContrat,
        ':secteur' => $secteur,
        ':localisation' => $localisation,
        ':description' => $description,
        ':competences' => $competences,
        ':remuneration' => $remuneration !== '' ? $remuneration : null,
        ':date_debut' => $dateDebut !== '' ? $dateDebut : null,
    ]);

    echo "Offre publiée avec succès.";
} catch (PDOException $e) {
    exit("Erreur lors de l'enregistrement de l'offre : " . $e->getMessage());
}
