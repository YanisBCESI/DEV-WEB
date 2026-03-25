<?php
namespace App\Models;

use PDO;
use PDOException;

class OffreModel extends Model {

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    /* Récupère l'id de l'entreprise via son email */
    public function getEntrepriseIdByEmail(string $email): ?int {
        $stmt = $this->pdo->prepare("
            SELECT entreprises.id
            FROM entreprises
            INNER JOIN comptes ON entreprises.compte_id = comptes.id
            WHERE comptes.email = :email
            LIMIT 1
        ");

        $stmt->execute([':email' => $email]);
        $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

        return $entreprise ? (int)$entreprise['id'] : null;
    }

    /* Crée une offre */
    public function createOffre(array $data): bool {
        $sql = "INSERT INTO offres
            (entreprise_id, titre, type_contrat, secteur, localisation, description, competences, remuneration, date_debut)
            VALUES
            (:entreprise_id, :titre, :type_contrat, :secteur, :localisation, :description, :competences, :remuneration, :date_debut)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':entreprise_id' => $data['entreprise_id'],
            ':titre' => $data['titre'],
            ':type_contrat' => $data['type_contrat'],
            ':secteur' => $data['secteur'],
            ':localisation' => $data['localisation'],
            ':description' => $data['description'],
            ':competences' => $data['competences'],
            ':remuneration' => $data['remuneration'] ?: null,
            ':date_debut' => $data['date_debut'] ?: null,
        ]);
    }
}