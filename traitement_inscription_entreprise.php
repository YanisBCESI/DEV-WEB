<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$nomEntreprise = trim($_POST['company_name'] ?? '');
$typeEntreprise = trim($_POST['company_type'] ?? '');
$secteur = trim($_POST['sector'] ?? '');
$siret = trim($_POST['siret'] ?? '');
$adresse = trim($_POST['address'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

$typesAutorises = ['TPE', 'PME', 'ETI', 'GE'];

if (
    $nomEntreprise === '' ||
    !in_array($typeEntreprise, $typesAutorises, true) ||
    $secteur === '' ||
    $siret === '' ||
    $adresse === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $password === ''
) {
    exit('Formulaire invalide.');
}

if ($password !== $passwordConfirm) {
    exit('Les mots de passe ne correspondent pas.');
}

$motDePasseHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    $stmtCompte = $pdo->prepare("
        INSERT INTO comptes (email, mot_de_passe, role)
        VALUES (:email, :mot_de_passe, 'entreprise')
    ");
    $stmtCompte->execute([
        ':email' => $email,
        ':mot_de_passe' => $motDePasseHash,
    ]);

    $compteId = (int) $pdo->lastInsertId();

    $stmtEntreprise = $pdo->prepare("
        INSERT INTO entreprises (compte_id, nom_entreprise, type_entreprise, secteur, siret, adresse)
        VALUES (:compte_id, :nom_entreprise, :type_entreprise, :secteur, :siret, :adresse)
    ");
    $stmtEntreprise->execute([
        ':compte_id' => $compteId,
        ':nom_entreprise' => $nomEntreprise,
        ':type_entreprise' => $typeEntreprise,
        ':secteur' => $secteur,
        ':siret' => $siret,
        ':adresse' => $adresse,
    ]);

    $pdo->commit();
    echo "Inscription entreprise réussie.";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ((string)$e->getCode() === '23000') {
        exit('Email ou SIRET déjà utilisé.');
    }

    exit('Erreur lors de l’inscription : ' . $e->getMessage());
}
