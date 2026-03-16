<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$nom = trim($_POST['lastname'] ?? '');
$prenom = trim($_POST['surname'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$role = trim($_POST['role'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

$genresAutorises = ['femme', 'homme', 'autre'];
$rolesAutorises = ['etudiant'];

if (
    $nom === '' ||
    $prenom === '' ||
    !in_array($genre, $genresAutorises, true) ||
    !in_array($role, $rolesAutorises, true) ||
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

    $stmtRole = $pdo->prepare("
        SELECT id
        FROM roles
        WHERE nom = :nom
        LIMIT 1
    ");
    $stmtRole->execute([
        ':nom' => $role
    ]);

    $roleData = $stmtRole->fetch(PDO::FETCH_ASSOC);

    if (!$roleData) {
        $pdo->rollBack();
        exit('Rôle introuvable.');
    }

    $roleId = (int) $roleData['id'];

    $stmtCompte = $pdo->prepare("
        INSERT INTO comptes (email, mot_de_passe, role_id, actif)
        VALUES (:email, :mot_de_passe, :role_id, 1)
    ");
    $stmtCompte->execute([
        ':email' => $email,
        ':mot_de_passe' => $motDePasseHash,
        ':role_id' => $roleId,
    ]);

    $compteId = (int) $pdo->lastInsertId();

    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
        $pdo->rollBack();
        exit('Le CV est obligatoire pour un étudiant.');
    }

    $fichier = $_FILES['cv'];

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $pdo->rollBack();
        exit('Erreur pendant l’envoi du fichier.');
    }

    $tailleMax = 2 * 1024 * 1024;

    if ($fichier['size'] > $tailleMax) {
        $pdo->rollBack();
        exit('Le CV dépasse 2 Mo.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fichier['tmp_name']);
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

    if ($mimeType !== 'application/pdf' || $extension !== 'pdf') {
        $pdo->rollBack();
        exit('Le fichier doit être un PDF.');
    }

    $dossierUpload = __DIR__ . '/../storage/cv/';

    if (!is_dir($dossierUpload) && !mkdir($dossierUpload, 0755, true)) {
        $pdo->rollBack();
        exit('Impossible de créer le dossier de stockage.');
    }

    $nomOriginal = $fichier['name'];
    $nomStocke = bin2hex(random_bytes(16)) . '.pdf';
    $cheminAbsolu = $dossierUpload . $nomStocke;
    $cheminRelatif = 'storage/cv/' . $nomStocke;

    if (!move_uploaded_file($fichier['tmp_name'], $cheminAbsolu)) {
        $pdo->rollBack();
        exit('Impossible d’enregistrer le CV.');
    }

    $stmtEtudiant = $pdo->prepare("
        INSERT INTO etudiants (
            compte_id,
            nom,
            prenom,
            genre,
            cv_nom_original,
            cv_nom_stocke,
            cv_chemin,
            cv_taille,
            cv_type_mime
        )
        VALUES (
            :compte_id,
            :nom,
            :prenom,
            :genre,
            :cv_nom_original,
            :cv_nom_stocke,
            :cv_chemin,
            :cv_taille,
            :cv_type_mime
        )
    ");
    $stmtEtudiant->execute([
        ':compte_id' => $compteId,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':genre' => $genre,
        ':cv_nom_original' => $nomOriginal,
        ':cv_nom_stocke' => $nomStocke,
        ':cv_chemin' => $cheminRelatif,
        ':cv_taille' => $fichier['size'],
        ':cv_type_mime' => $mimeType,
    ]);

    $pdo->commit();
    echo 'Inscription réussie.';
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ((string) $e->getCode() === '23000') {
        exit('Cet email existe déjà.');
    }

    exit('Erreur lors de l’inscription : ' . $e->getMessage());
}