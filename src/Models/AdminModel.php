<?php

namespace App\Models;

class AdminModel extends Model{
    protected $dbh = null;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    private function passwordMatches(string $plainPassword, string $storedPassword): bool{
        if ($storedPassword === "") {
            return false;
        }

        return password_verify($plainPassword, $storedPassword) || hash_equals($storedPassword, $plainPassword);
    }

    public function authenticateAdmin(string $email, string $password): ?array{
        $stmt = $this->dbh->prepare(
            "SELECT id, email, mot_de_passe, actif
             FROM comptes
             WHERE email = :email AND role_id = 1
             LIMIT 1"
        );
        $stmt->bindValue(":email", trim($email));
        $stmt->execute();

        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$admin) {
            return null;
        }

        if ((int) ($admin["actif"] ?? 0) !== 1) {
            return null;
        }

        if (!$this->passwordMatches($password, (string) ($admin["mot_de_passe"] ?? ""))) {
            return null;
        }

        return [
            "id" => (int) $admin["id"],
            "email" => $admin["email"],
            "role" => "admin",
        ];
    }

    public function getAllPilots(): array{
        $stmt = $this->dbh->query(
            "SELECT
                pilotes.id,
                pilotes.nom,
                pilotes.prenom,
                pilotes.genre,
                pilotes.telephone,
                pilotes.created_at,
                comptes.email
            FROM pilotes
            INNER JOIN comptes ON comptes.id = pilotes.compte_id
            ORDER BY pilotes.created_at DESC, pilotes.prenom ASC, pilotes.nom ASC"
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function emailExists(string $email): bool{
        $stmt = $this->dbh->prepare("SELECT COUNT(*) FROM comptes WHERE email = :email");
        $stmt->bindValue(":email", trim($email));
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function createPilotAccount(array $data): string{
        $email = trim((string) ($data["email"] ?? ""));

        if ($this->emailExists($email)) {
            return "email_exists";
        }

        $this->dbh->beginTransaction();

        try {
            $accountStmt = $this->dbh->prepare(
                "INSERT INTO comptes (email, mot_de_passe, role_id, actif)
                 VALUES (:email, :password, 4, 1)"
            );
            $accountStmt->bindValue(":email", $email);
            $accountStmt->bindValue(":password", password_hash((string) $data["password"], PASSWORD_DEFAULT));
            $accountStmt->execute();

            $accountId = (int) $this->dbh->lastInsertId();

            $pilotStmt = $this->dbh->prepare(
                "INSERT INTO pilotes (compte_id, nom, prenom, genre, telephone)
                 VALUES (:compte_id, :nom, :prenom, :genre, :telephone)"
            );
            $pilotStmt->bindValue(":compte_id", $accountId, \PDO::PARAM_INT);
            $pilotStmt->bindValue(":nom", trim((string) $data["nom"]));
            $pilotStmt->bindValue(":prenom", trim((string) $data["prenom"]));
            $pilotStmt->bindValue(":genre", $data["genre"]);
            $pilotStmt->bindValue(":telephone", trim((string) ($data["telephone"] ?? "")) !== "" ? trim((string) $data["telephone"]) : null);
            $pilotStmt->execute();

            $this->dbh->commit();

            return "created";
        } catch (\PDOException $exception) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }

            return "error";
        }
    }
}
