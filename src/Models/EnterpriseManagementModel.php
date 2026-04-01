<?php

namespace App\Models;

class EnterpriseManagementModel extends Model{
    protected $dbh = null;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->ensureManagementEvaluationsTable();
    }

    private function ensureManagementEvaluationsTable(): void{
        $this->dbh->exec(
            "CREATE TABLE IF NOT EXISTS evaluations_gestion_entreprises (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                entreprise_id INT NOT NULL,
                manager_role VARCHAR(20) NOT NULL,
                manager_account_id INT NOT NULL,
                note TINYINT NOT NULL,
                commentaire TEXT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_manager_evaluation (entreprise_id, manager_role, manager_account_id),
                CONSTRAINT fk_eval_gestion_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE CASCADE,
                CONSTRAINT fk_eval_gestion_compte FOREIGN KEY (manager_account_id) REFERENCES comptes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function normalizeSearch(string $search): string{
        return trim($search);
    }

    private function emailExists(string $email, ?int $excludedAccountId = null): bool{
        $sql = "SELECT COUNT(*) FROM comptes WHERE email = :email";

        if ($excludedAccountId !== null) {
            $sql .= " AND id <> :excluded_account_id";
        }

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(":email", $email);

        if ($excludedAccountId !== null) {
            $stmt->bindValue(":excluded_account_id", $excludedAccountId, \PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    private function siretExists(string $siret, ?int $excludedCompanyId = null): bool{
        $sql = "SELECT COUNT(*) FROM entreprises WHERE siret = :siret";

        if ($excludedCompanyId !== null) {
            $sql .= " AND id <> :excluded_company_id";
        }

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(":siret", $siret);

        if ($excludedCompanyId !== null) {
            $stmt->bindValue(":excluded_company_id", $excludedCompanyId, \PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    private function validateCompanyData(array $data, bool $isCreation, ?int $excludedCompanyId = null, ?int $excludedAccountId = null): string{
        $name = trim((string) ($data["nom_entreprise"] ?? ""));
        $type = trim((string) ($data["type_entreprise"] ?? ""));
        $sector = trim((string) ($data["secteur"] ?? ""));
        $siret = preg_replace('/\D+/', '', (string) ($data["siret"] ?? ""));
        $address = trim((string) ($data["adresse"] ?? ""));
        $email = trim((string) ($data["email"] ?? ""));
        $password = (string) ($data["password"] ?? "");
        $passwordConfirm = (string) ($data["password_confirm"] ?? "");
        $allowedTypes = ["TPE", "PME", "ETI", "GE"];

        if (
            $name === ""
            || !in_array($type, $allowedTypes, true)
            || $sector === ""
            || strlen($siret) !== 14
            || $address === ""
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return "invalid_data";
        }

        if ($isCreation && $password === "") {
            return "invalid_data";
        }

        if ($password !== "" && $password !== $passwordConfirm) {
            return "password_mismatch";
        }

        if ($this->emailExists($email, $excludedAccountId)) {
            return "email_exists";
        }

        if ($this->siretExists($siret, $excludedCompanyId)) {
            return "siret_exists";
        }

        return "valid";
    }

    private function hydrateCompanyList(array $companies): array{
        return array_map(function (array $company): array {
            $company["moyenne_note"] = $company["moyenne_note"] !== null ? number_format((float) $company["moyenne_note"], 2, '.', '') : null;
            $company["offers_count"] = (int) ($company["offers_count"] ?? 0);

            return $company;
        }, $companies);
    }

    public function getCompanies(string $search = ""): array{
        $normalizedSearch = $this->normalizeSearch($search);
        $sql = "SELECT
                    entreprises.id,
                    entreprises.nom_entreprise,
                    entreprises.type_entreprise,
                    entreprises.secteur,
                    entreprises.siret,
                    entreprises.adresse,
                    entreprises.ville,
                    entreprises.code_postal,
                    entreprises.description,
                    entreprises.site_web,
                    entreprises.moyenne_note,
                    entreprises.created_at,
                    comptes.email,
                    (
                        SELECT COUNT(*)
                        FROM offres
                        WHERE offres.entreprise_id = entreprises.id
                    ) AS offers_count,
                    (
                        SELECT COUNT(*)
                        FROM evaluations_gestion_entreprises
                        WHERE evaluations_gestion_entreprises.entreprise_id = entreprises.id
                    ) AS management_evaluations_count
                FROM entreprises
                INNER JOIN comptes ON comptes.id = entreprises.compte_id
                WHERE 1 = 1";

        $parameters = [];

        if ($normalizedSearch !== "") {
            $sql .= " AND (
                        entreprises.nom_entreprise LIKE :search
                        OR entreprises.secteur LIKE :search
                        OR entreprises.ville LIKE :search
                        OR entreprises.description LIKE :search
                        OR comptes.email LIKE :search
                    )";
            $parameters[":search"] = "%" . $normalizedSearch . "%";
        }

        $sql .= " ORDER BY entreprises.nom_entreprise ASC";

        $stmt = $this->dbh->prepare($sql);

        foreach ($parameters as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        $stmt->execute();

        return $this->hydrateCompanyList($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getCompanyById(int $companyId): ?array{
        $stmt = $this->dbh->prepare(
            "SELECT
                entreprises.id,
                entreprises.compte_id,
                entreprises.nom_entreprise,
                entreprises.type_entreprise,
                entreprises.secteur,
                entreprises.siret,
                entreprises.adresse,
                entreprises.ville,
                entreprises.code_postal,
                entreprises.description,
                entreprises.site_web,
                entreprises.moyenne_note,
                entreprises.created_at,
                comptes.email,
                (
                    SELECT COUNT(*)
                    FROM offres
                    WHERE offres.entreprise_id = entreprises.id
                ) AS offers_count
            FROM entreprises
            INNER JOIN comptes ON comptes.id = entreprises.compte_id
            WHERE entreprises.id = :company_id
            LIMIT 1"
        );
        $stmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
        $stmt->execute();

        $company = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$company) {
            return null;
        }

        $company["compte_id"] = (int) $company["compte_id"];
        $company["offers_count"] = (int) ($company["offers_count"] ?? 0);
        $company["moyenne_note"] = $company["moyenne_note"] !== null ? number_format((float) $company["moyenne_note"], 2, '.', '') : null;

        return $company;
    }

    public function getManagementEvaluationsByCompanyId(int $companyId): array{
        $stmt = $this->dbh->prepare(
            "SELECT
                evaluations_gestion_entreprises.note,
                evaluations_gestion_entreprises.commentaire,
                evaluations_gestion_entreprises.created_at,
                evaluations_gestion_entreprises.updated_at,
                evaluations_gestion_entreprises.manager_role,
                comptes.email,
                pilotes.nom,
                pilotes.prenom
             FROM evaluations_gestion_entreprises
             INNER JOIN comptes ON comptes.id = evaluations_gestion_entreprises.manager_account_id
             LEFT JOIN pilotes ON pilotes.compte_id = comptes.id
             WHERE evaluations_gestion_entreprises.entreprise_id = :company_id
             ORDER BY evaluations_gestion_entreprises.updated_at DESC"
        );
        $stmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
        $stmt->execute();

        $evaluations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $evaluation): array {
            $isPilot = ($evaluation["manager_role"] ?? "") === "pilote";
            $pilotName = trim((string) (($evaluation["prenom"] ?? "") . " " . ($evaluation["nom"] ?? "")));

            $evaluation["author_label"] = $isPilot
                ? ($pilotName !== "" ? "Pilote " . $pilotName : "Pilote")
                : "Administrateur";

            return $evaluation;
        }, $evaluations);
    }

    public function getManagementEvaluationForCompany(int $companyId, array $manager): ?array{
        $stmt = $this->dbh->prepare(
            "SELECT note, commentaire
             FROM evaluations_gestion_entreprises
             WHERE entreprise_id = :company_id
               AND manager_role = :manager_role
               AND manager_account_id = :manager_account_id
             LIMIT 1"
        );
        $stmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
        $stmt->bindValue(":manager_role", (string) ($manager["role"] ?? ""));
        $stmt->bindValue(":manager_account_id", (int) ($manager["id"] ?? 0), \PDO::PARAM_INT);
        $stmt->execute();

        $evaluation = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $evaluation ?: null;
    }

    public function createCompanyAccount(array $data): string{
        $validation = $this->validateCompanyData($data, true);

        if ($validation !== "valid") {
            return $validation;
        }

        $email = trim((string) $data["email"]);
        $passwordHash = password_hash((string) $data["password"], PASSWORD_DEFAULT);

        $this->dbh->beginTransaction();

        try {
            $accountStmt = $this->dbh->prepare(
                "INSERT INTO comptes (email, mot_de_passe, role_id, actif)
                 VALUES (:email, :password, 2, 1)"
            );
            $accountStmt->bindValue(":email", $email);
            $accountStmt->bindValue(":password", $passwordHash);
            $accountStmt->execute();

            $accountId = (int) $this->dbh->lastInsertId();

            $companyStmt = $this->dbh->prepare(
                "INSERT INTO entreprises (
                    compte_id,
                    nom_entreprise,
                    type_entreprise,
                    secteur,
                    siret,
                    adresse,
                    ville,
                    code_postal,
                    description,
                    site_web
                ) VALUES (
                    :compte_id,
                    :nom_entreprise,
                    :type_entreprise,
                    :secteur,
                    :siret,
                    :adresse,
                    :ville,
                    :code_postal,
                    :description,
                    :site_web
                )"
            );
            $companyStmt->bindValue(":compte_id", $accountId, \PDO::PARAM_INT);
            $companyStmt->bindValue(":nom_entreprise", trim((string) $data["nom_entreprise"]));
            $companyStmt->bindValue(":type_entreprise", trim((string) $data["type_entreprise"]));
            $companyStmt->bindValue(":secteur", trim((string) $data["secteur"]));
            $companyStmt->bindValue(":siret", preg_replace('/\D+/', '', (string) $data["siret"]));
            $companyStmt->bindValue(":adresse", trim((string) $data["adresse"]));
            $companyStmt->bindValue(":ville", trim((string) ($data["ville"] ?? "")) !== "" ? trim((string) $data["ville"]) : null);
            $companyStmt->bindValue(":code_postal", trim((string) ($data["code_postal"] ?? "")) !== "" ? trim((string) $data["code_postal"]) : null);
            $companyStmt->bindValue(":description", trim((string) ($data["description"] ?? "")) !== "" ? trim((string) $data["description"]) : null);
            $companyStmt->bindValue(":site_web", trim((string) ($data["site_web"] ?? "")) !== "" ? trim((string) $data["site_web"]) : null);
            $companyStmt->execute();

            $this->dbh->commit();

            return "created";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return "error";
        }
    }

    public function updateCompanyAccount(int $companyId, array $data): string{
        $company = $this->getCompanyById($companyId);

        if ($company === null) {
            return "not_found";
        }

        $validation = $this->validateCompanyData($data, false, $companyId, (int) $company["compte_id"]);

        if ($validation !== "valid") {
            return $validation;
        }

        $password = (string) ($data["password"] ?? "");
        $email = trim((string) $data["email"]);

        $this->dbh->beginTransaction();

        try {
            $accountSql = "UPDATE comptes SET email = :email";

            if ($password !== "") {
                $accountSql .= ", mot_de_passe = :password";
            }

            $accountSql .= " WHERE id = :account_id AND role_id = 2";

            $accountStmt = $this->dbh->prepare($accountSql);
            $accountStmt->bindValue(":email", $email);

            if ($password !== "") {
                $accountStmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
            }

            $accountStmt->bindValue(":account_id", (int) $company["compte_id"], \PDO::PARAM_INT);
            $accountStmt->execute();

            $companyStmt = $this->dbh->prepare(
                "UPDATE entreprises
                 SET nom_entreprise = :nom_entreprise,
                     type_entreprise = :type_entreprise,
                     secteur = :secteur,
                     siret = :siret,
                     adresse = :adresse,
                     ville = :ville,
                     code_postal = :code_postal,
                     description = :description,
                     site_web = :site_web
                 WHERE id = :company_id"
            );
            $companyStmt->bindValue(":nom_entreprise", trim((string) $data["nom_entreprise"]));
            $companyStmt->bindValue(":type_entreprise", trim((string) $data["type_entreprise"]));
            $companyStmt->bindValue(":secteur", trim((string) $data["secteur"]));
            $companyStmt->bindValue(":siret", preg_replace('/\D+/', '', (string) $data["siret"]));
            $companyStmt->bindValue(":adresse", trim((string) $data["adresse"]));
            $companyStmt->bindValue(":ville", trim((string) ($data["ville"] ?? "")) !== "" ? trim((string) $data["ville"]) : null);
            $companyStmt->bindValue(":code_postal", trim((string) ($data["code_postal"] ?? "")) !== "" ? trim((string) $data["code_postal"]) : null);
            $companyStmt->bindValue(":description", trim((string) ($data["description"] ?? "")) !== "" ? trim((string) $data["description"]) : null);
            $companyStmt->bindValue(":site_web", trim((string) ($data["site_web"] ?? "")) !== "" ? trim((string) $data["site_web"]) : null);
            $companyStmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
            $companyStmt->execute();

            $this->dbh->commit();

            return "updated";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return "error";
        }
    }

    public function deleteCompanyAccount(int $companyId): string{
        $company = $this->getCompanyById($companyId);

        if ($company === null) {
            return "not_found";
        }

        $this->dbh->beginTransaction();

        try {
            $stmt = $this->dbh->prepare("DELETE FROM comptes WHERE id = :account_id AND role_id = 2");
            $stmt->bindValue(":account_id", (int) $company["compte_id"], \PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new \PDOException("Company account deletion failed.");
            }

            $this->dbh->commit();

            return "deleted";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return "error";
        }
    }

    private function refreshCompanyAverage(int $companyId): void{
        $stmt = $this->dbh->prepare(
            "SELECT ROUND(AVG(note), 2)
             FROM (
                SELECT note
                FROM evaluations_gestion_entreprises
                WHERE entreprise_id = :company_id
                UNION ALL
                SELECT note
                FROM evaluations_entreprises
                WHERE entreprise_id = :company_id_legacy
             ) AS combined_notes"
        );
        $stmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
        $stmt->bindValue(":company_id_legacy", $companyId, \PDO::PARAM_INT);
        $stmt->execute();

        $average = $stmt->fetchColumn();

        $updateStmt = $this->dbh->prepare(
            "UPDATE entreprises
             SET moyenne_note = :average
             WHERE id = :company_id"
        );
        $updateStmt->bindValue(":average", $average !== false ? $average : null);
        $updateStmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
        $updateStmt->execute();
    }

    public function saveManagementEvaluation(int $companyId, array $manager, int $note, string $commentaire): string{
        $company = $this->getCompanyById($companyId);

        if ($company === null) {
            return "not_found";
        }

        if ($note < 1 || $note > 5) {
            return "invalid_note";
        }

        try {
            $stmt = $this->dbh->prepare(
                "INSERT INTO evaluations_gestion_entreprises (
                    entreprise_id,
                    manager_role,
                    manager_account_id,
                    note,
                    commentaire
                 ) VALUES (
                    :company_id,
                    :manager_role,
                    :manager_account_id,
                    :note,
                    :commentaire
                 )
                 ON DUPLICATE KEY UPDATE
                    note = VALUES(note),
                    commentaire = VALUES(commentaire),
                    updated_at = CURRENT_TIMESTAMP"
            );
            $stmt->bindValue(":company_id", $companyId, \PDO::PARAM_INT);
            $stmt->bindValue(":manager_role", (string) ($manager["role"] ?? ""));
            $stmt->bindValue(":manager_account_id", (int) ($manager["id"] ?? 0), \PDO::PARAM_INT);
            $stmt->bindValue(":note", $note, \PDO::PARAM_INT);
            $stmt->bindValue(":commentaire", trim($commentaire) !== "" ? trim($commentaire) : null);
            $stmt->execute();

            $this->refreshCompanyAverage($companyId);

            return "rated";
        } catch (\PDOException $exception) {
            return "error";
        }
    }
}
