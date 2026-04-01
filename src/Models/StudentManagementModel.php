<?php

namespace App\Models;

class StudentManagementModel extends Model{
    protected $dbh = null;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    private function isAdmin(array $manager): bool{
        return ($manager["role"] ?? null) === "admin";
    }

    private function isPilot(array $manager): bool{
        return ($manager["role"] ?? null) === "pilote";
    }

    private function getManagedPilotId(array $manager): ?int{
        if (!$this->isPilot($manager)) {
            return null;
        }

        return isset($manager["pilot_id"]) ? (int) $manager["pilot_id"] : null;
    }

    private function normalizePilotId(mixed $pilotId): ?int{
        if ($pilotId === null || $pilotId === "") {
            return null;
        }

        if (!is_numeric($pilotId)) {
            return null;
        }

        $normalizedPilotId = (int) $pilotId;

        return $normalizedPilotId > 0 ? $normalizedPilotId : null;
    }

    private function resolveAssignedPilotId(array $data, array $manager): ?int{
        if ($this->isPilot($manager)) {
            return $this->getManagedPilotId($manager);
        }

        return $this->normalizePilotId($data["pilot_id"] ?? null);
    }

    private function extractAccountId(mixed $value): ?int{
        if ($value === null || $value === "") {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        $accountId = (int) $value;

        return $accountId > 0 ? $accountId : null;
    }

    private function pilotExists(?int $pilotId): bool{
        if ($pilotId === null) {
            return true;
        }

        $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM pilotes WHERE id = :pilot_id");
        $stmt->bindValue(":pilot_id", $pilotId, \PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    private function emailExists(string $email, ?int $excludedAccountId = null, ?int $excludedStudentId = null): bool{
        $accountSql = "SELECT COUNT(*) FROM comptes WHERE email = :email";

        if ($excludedAccountId !== null) {
            $accountSql .= " AND id <> :excluded_account_id";
        }

        $accountStmt = $this->dbh->prepare($accountSql);
        $accountStmt->bindValue(":email", $email);

        if ($excludedAccountId !== null) {
            $accountStmt->bindValue(":excluded_account_id", $excludedAccountId, \PDO::PARAM_INT);
        }

        $accountStmt->execute();

        if ((int) $accountStmt->fetchColumn() > 0) {
            return true;
        }

        $studentSql = "SELECT COUNT(*) FROM etudiants WHERE email = :email";

        if ($excludedStudentId !== null) {
            $studentSql .= " AND id <> :excluded_student_id";
        }

        $studentStmt = $this->dbh->prepare($studentSql);
        $studentStmt->bindValue(":email", $email);

        if ($excludedStudentId !== null) {
            $studentStmt->bindValue(":excluded_student_id", $excludedStudentId, \PDO::PARAM_INT);
        }

        $studentStmt->execute();

        return (int) $studentStmt->fetchColumn() > 0;
    }

    private function validateStudentData(array $data, bool $isCreation, array $manager, ?int $excludedAccountId = null, ?int $excludedStudentId = null): string{
        $nom = trim((string) ($data["nom"] ?? ""));
        $prenom = trim((string) ($data["prenom"] ?? ""));
        $genre = strtolower(trim((string) ($data["genre"] ?? "")));
        $email = trim((string) ($data["email"] ?? ""));
        $password = (string) ($data["password"] ?? "");
        $passwordConfirm = (string) ($data["password_confirm"] ?? "");
        $allowedGenres = ["femme", "homme", "autre"];
        $pilotId = $this->resolveAssignedPilotId($data, $manager);

        if (
            $nom === ""
            || $prenom === ""
            || !in_array($genre, $allowedGenres, true)
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

        if (!$this->pilotExists($pilotId)) {
            return "invalid_pilot";
        }

        if ($this->emailExists($email, $excludedAccountId, $excludedStudentId)) {
            return "email_exists";
        }

        return "valid";
    }

    private function buildStudentListQuery(array $manager, string $search = ""): array{
        $sql = "SELECT
                    etudiants.id,
                    etudiants.nom,
                    etudiants.prenom,
                    etudiants.genre,
                    etudiants.created_at,
                    etudiants.pilote_id,
                    COALESCE(comptes.email, etudiants.email) AS email,
                    TRIM(CONCAT(COALESCE(pilotes.prenom, ''), ' ', COALESCE(pilotes.nom, ''))) AS pilot_name
                FROM etudiants
                LEFT JOIN comptes ON comptes.id = etudiants.compte_id
                LEFT JOIN pilotes ON pilotes.id = etudiants.pilote_id
                WHERE 1 = 1";
        $parameters = [];

        $managedPilotId = $this->getManagedPilotId($manager);

        if ($managedPilotId !== null) {
            $sql .= " AND etudiants.pilote_id = :managed_pilot_id";
            $parameters[":managed_pilot_id"] = [$managedPilotId, \PDO::PARAM_INT];
        }

        if ($search !== "") {
            $sql .= " AND (
                        etudiants.nom LIKE :search
                        OR etudiants.prenom LIKE :search
                        OR COALESCE(comptes.email, etudiants.email) LIKE :search
                    )";
            $parameters[":search"] = ["%" . $search . "%", \PDO::PARAM_STR];
        }

        $sql .= " ORDER BY etudiants.created_at DESC, etudiants.prenom ASC, etudiants.nom ASC";

        return [$sql, $parameters];
    }

    private function getManagedStudentRecord(int $studentId, array $manager): ?array{
        $sql = "SELECT
                    etudiants.id,
                    etudiants.compte_id,
                    etudiants.pilote_id,
                    etudiants.nom,
                    etudiants.prenom,
                    etudiants.genre,
                    etudiants.email,
                    comptes.id AS account_id,
                    comptes.email AS account_email
                FROM etudiants
                LEFT JOIN comptes ON comptes.id = etudiants.compte_id
                WHERE etudiants.id = :student_id";

        $managedPilotId = $this->getManagedPilotId($manager);

        if ($managedPilotId !== null) {
            $sql .= " AND etudiants.pilote_id = :managed_pilot_id";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);

        if ($managedPilotId !== null) {
            $stmt->bindValue(":managed_pilot_id", $managedPilotId, \PDO::PARAM_INT);
        }

        $stmt->execute();
        $student = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$student) {
            return null;
        }

        $student["account_id"] = $this->extractAccountId($student["account_id"] ?? null);
        $student["resolved_email"] = trim((string) ($student["account_email"] ?? $student["email"] ?? ""));

        return $student;
    }

    public function getStudents(array $manager, string $search = ""): array{
        [$sql, $parameters] = $this->buildStudentListQuery($manager, trim($search));
        $stmt = $this->dbh->prepare($sql);

        foreach ($parameters as $name => [$value, $type]) {
            $stmt->bindValue($name, $value, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPilotsForSelect(): array{
        $stmt = $this->dbh->query(
            "SELECT
                pilotes.id,
                pilotes.nom,
                pilotes.prenom,
                comptes.email
             FROM pilotes
             INNER JOIN comptes ON comptes.id = pilotes.compte_id
             ORDER BY pilotes.prenom ASC, pilotes.nom ASC"
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getStudentById(int $studentId, array $manager): ?array{
        $student = $this->getManagedStudentRecord($studentId, $manager);

        if (!$student) {
            return null;
        }

        return [
            "id" => (int) $student["id"],
            "nom" => $student["nom"],
            "prenom" => $student["prenom"],
            "genre" => $student["genre"],
            "email" => $student["resolved_email"],
            "pilot_id" => $student["pilote_id"] !== null ? (int) $student["pilote_id"] : null,
        ];
    }

    public function createStudentAccount(array $data, array $manager): string{
        $validation = $this->validateStudentData($data, true, $manager);

        if ($validation !== "valid") {
            return $validation;
        }

        $pilotId = $this->resolveAssignedPilotId($data, $manager);
        $email = trim((string) $data["email"]);
        $passwordHash = password_hash((string) $data["password"], PASSWORD_DEFAULT);

        $this->dbh->beginTransaction();

        try {
            $accountStmt = $this->dbh->prepare(
                "INSERT INTO comptes (email, mot_de_passe, role_id, actif)
                 VALUES (:email, :password, 3, 1)"
            );
            $accountStmt->bindValue(":email", $email);
            $accountStmt->bindValue(":password", $passwordHash);
            $accountStmt->execute();

            $accountId = (int) $this->dbh->lastInsertId();

            $studentStmt = $this->dbh->prepare(
                "INSERT INTO etudiants (compte_id, pilote_id, nom, prenom, genre, mdp, email)
                 VALUES (:compte_id, :pilote_id, :nom, :prenom, :genre, :mdp, :email)"
            );
            $studentStmt->bindValue(":compte_id", $accountId, \PDO::PARAM_INT);
            $studentStmt->bindValue(":pilote_id", $pilotId, $pilotId === null ? \PDO::PARAM_NULL : \PDO::PARAM_INT);
            $studentStmt->bindValue(":nom", trim((string) $data["nom"]));
            $studentStmt->bindValue(":prenom", trim((string) $data["prenom"]));
            $studentStmt->bindValue(":genre", strtolower(trim((string) $data["genre"])));
            $studentStmt->bindValue(":mdp", $passwordHash);
            $studentStmt->bindValue(":email", $email);
            $studentStmt->execute();

            $this->dbh->commit();

            return "created";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return $this->createLegacyStudentAccount($data, $pilotId);
        }
    }

    private function createLegacyStudentAccount(array $data, ?int $pilotId): string{
        try {
            $stmt = $this->dbh->prepare(
                "INSERT INTO etudiants (compte_id, pilote_id, nom, prenom, genre, mdp, email)
                 VALUES ('etudiant', :pilote_id, :nom, :prenom, :genre, :mdp, :email)"
            );
            $stmt->bindValue(":pilote_id", $pilotId, $pilotId === null ? \PDO::PARAM_NULL : \PDO::PARAM_INT);
            $stmt->bindValue(":nom", trim((string) $data["nom"]));
            $stmt->bindValue(":prenom", trim((string) $data["prenom"]));
            $stmt->bindValue(":genre", strtolower(trim((string) $data["genre"])));
            $stmt->bindValue(":mdp", password_hash((string) $data["password"], PASSWORD_DEFAULT));
            $stmt->bindValue(":email", trim((string) $data["email"]));
            $stmt->execute();

            return "created";
        } catch (\PDOException $exception) {
            return "error";
        }
    }

    public function updateStudentAccount(int $studentId, array $data, array $manager): string{
        $student = $this->getManagedStudentRecord($studentId, $manager);

        if (!$student) {
            return "not_found";
        }

        $validation = $this->validateStudentData($data, false, $manager, $student["account_id"], $studentId);

        if ($validation !== "valid") {
            return $validation;
        }

        $pilotId = $this->resolveAssignedPilotId($data, $manager);
        $email = trim((string) $data["email"]);
        $password = (string) ($data["password"] ?? "");

        $this->dbh->beginTransaction();

        try {
            if ($student["account_id"] !== null) {
                $accountSql = "UPDATE comptes SET email = :email";

                if ($password !== "") {
                    $accountSql .= ", mot_de_passe = :password";
                }

                $accountSql .= " WHERE id = :account_id";

                $accountStmt = $this->dbh->prepare($accountSql);
                $accountStmt->bindValue(":email", $email);

                if ($password !== "") {
                    $accountStmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
                }

                $accountStmt->bindValue(":account_id", $student["account_id"], \PDO::PARAM_INT);
                $accountStmt->execute();
            }

            $studentSql = "UPDATE etudiants
                           SET nom = :nom,
                               prenom = :prenom,
                               genre = :genre,
                               pilote_id = :pilote_id,
                               email = :email";

            if ($password !== "") {
                $studentSql .= ", mdp = :password";
            }

            $studentSql .= " WHERE id = :student_id";

            $studentStmt = $this->dbh->prepare($studentSql);
            $studentStmt->bindValue(":nom", trim((string) $data["nom"]));
            $studentStmt->bindValue(":prenom", trim((string) $data["prenom"]));
            $studentStmt->bindValue(":genre", strtolower(trim((string) $data["genre"])));
            $studentStmt->bindValue(":pilote_id", $pilotId, $pilotId === null ? \PDO::PARAM_NULL : \PDO::PARAM_INT);
            $studentStmt->bindValue(":email", $email);

            if ($password !== "") {
                $studentStmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
            }

            $studentStmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
            $studentStmt->execute();

            $this->dbh->commit();

            return "updated";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return "error";
        }
    }

    public function deleteStudentAccount(int $studentId, array $manager): string{
        $student = $this->getManagedStudentRecord($studentId, $manager);

        if (!$student) {
            return "not_found";
        }

        $this->dbh->beginTransaction();

        try {
            $studentStmt = $this->dbh->prepare("DELETE FROM etudiants WHERE id = :student_id");
            $studentStmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
            $studentStmt->execute();

            if ($student["account_id"] !== null) {
                $accountStmt = $this->dbh->prepare("DELETE FROM comptes WHERE id = :account_id AND role_id = 3");
                $accountStmt->bindValue(":account_id", $student["account_id"], \PDO::PARAM_INT);
                $accountStmt->execute();
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
}
