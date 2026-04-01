<?php

namespace App\Models;

class OffersModel extends Model {
    public $data;
    public $dbh;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getOffers(string $search = "", ?string $contractType = null): array{
        $sql = "SELECT
                    offres.id_offre,
                    offres.entreprise_id,
                    offres.titre,
                    offres.type_contrat,
                    offres.secteur,
                    offres.localisation,
                    offres.description_offre,
                    offres.competences,
                    offres.remuneration,
                    offres.date_debut,
                    offres.date_fin,
                    offres.statut,
                    offres.nb_places,
                    offres.created_at,
                    offres.updated_at,
                    offres.nb_vues,
                    entreprises.nom_entreprise
                FROM offres
                INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
                WHERE 1 = 1";
        $parameters = [];

        $normalizedSearch = trim($search);

        if ($normalizedSearch !== "") {
            $sql .= " AND (
                        offres.titre LIKE :search
                        OR offres.secteur LIKE :search
                        OR offres.localisation LIKE :search
                        OR offres.description_offre LIKE :search
                        OR entreprises.nom_entreprise LIKE :search
                    )";
            $parameters[":search"] = ["%" . $normalizedSearch . "%", \PDO::PARAM_STR];
        }

        if ($contractType !== null && in_array($contractType, ["stage", "alternance", "emploi"], true)) {
            $sql .= " AND offres.type_contrat = :type_contrat";
            $parameters[":type_contrat"] = [$contractType, \PDO::PARAM_STR];
        }

        $sql .= " ORDER BY offres.created_at DESC, offres.updated_at DESC";

        $stmt = $this->dbh->prepare($sql);

        foreach ($parameters as $name => [$value, $type]) {
            $stmt->bindValue($name, $value, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOfferStats(): array{
        $statsStmt = $this->dbh->query(
            "SELECT
                COUNT(*) AS total_offers,
                COALESCE(SUM(CASE WHEN statut = 'ouverte' THEN 1 ELSE 0 END), 0) AS open_offers,
                COALESCE(SUM(nb_vues), 0) AS total_views,
                COALESCE(SUM(nb_places), 0) AS total_places
             FROM offres"
        );

        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        $typesStmt = $this->dbh->query(
            "SELECT type_contrat, COUNT(*) AS total
             FROM offres
             GROUP BY type_contrat"
        );

        $typeCounts = [
            "stage" => 0,
            "alternance" => 0,
            "emploi" => 0,
        ];

        foreach ($typesStmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $type = (string) ($row["type_contrat"] ?? "");

            if (array_key_exists($type, $typeCounts)) {
                $typeCounts[$type] = (int) ($row["total"] ?? 0);
            }
        }

        return [
            "total_offers" => (int) ($stats["total_offers"] ?? 0),
            "open_offers" => (int) ($stats["open_offers"] ?? 0),
            "total_views" => (int) ($stats["total_views"] ?? 0),
            "total_places" => (int) ($stats["total_places"] ?? 0),
            "type_counts" => $typeCounts,
        ];
    }

    public function getOfferById(int $id, bool $incrementViews = false): ?array{
        if ($incrementViews) {
            $viewStmt = $this->dbh->prepare(
                "UPDATE offres
                 SET nb_vues = nb_vues + 1
                 WHERE id_offre = :offer_id"
            );
            $viewStmt->bindValue(":offer_id", $id, \PDO::PARAM_INT);
            $viewStmt->execute();
        }

        $stmt = $this->dbh->prepare(
            "SELECT
                offres.*,
                entreprises.nom_entreprise,
                entreprises.site_web
             FROM offres
             INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
             WHERE offres.id_offre = :offer_id
             LIMIT 1"
        );
        $stmt->bindValue(":offer_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $offer = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $offer ?: null;
    }

    public function getCompaniesForSelect(): array{
        $stmt = $this->dbh->query(
            "SELECT id, nom_entreprise
             FROM entreprises
             ORDER BY nom_entreprise ASC"
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createOffer(array $data): bool{
        $stmt = $this->dbh->prepare(
            "INSERT INTO offres (
                entreprise_id,
                titre,
                type_contrat,
                secteur,
                localisation,
                description_offre,
                competences,
                remuneration,
                date_debut,
                date_fin,
                statut,
                nb_places
            ) VALUES (
                :entreprise_id,
                :titre,
                :type_contrat,
                :secteur,
                :localisation,
                :description_offre,
                :competences,
                :remuneration,
                :date_debut,
                :date_fin,
                :statut,
                :nb_places
            )"
        );
        $stmt->bindValue(":entreprise_id", (int) $data["entreprise_id"], \PDO::PARAM_INT);
        $stmt->bindValue(":titre", $data["titre"]);
        $stmt->bindValue(":type_contrat", $data["type_contrat"]);
        $stmt->bindValue(":secteur", $data["secteur"]);
        $stmt->bindValue(":localisation", $data["localisation"]);
        $stmt->bindValue(":description_offre", $data["description_offre"]);
        $stmt->bindValue(":competences", $data["competences"]);
        $stmt->bindValue(":remuneration", $data["remuneration"] !== "" ? $data["remuneration"] : null);
        $stmt->bindValue(":date_debut", $data["date_debut"] !== "" ? $data["date_debut"] : null);
        $stmt->bindValue(":date_fin", $data["date_fin"] !== "" ? $data["date_fin"] : null);
        $stmt->bindValue(":statut", $data["statut"]);
        $stmt->bindValue(":nb_places", (int) $data["nb_places"], \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateOffer(int $offerId, array $data): bool{
        $stmt = $this->dbh->prepare(
            "UPDATE offres
             SET entreprise_id = :entreprise_id,
                 titre = :titre,
                 type_contrat = :type_contrat,
                 secteur = :secteur,
                 localisation = :localisation,
                 description_offre = :description_offre,
                 competences = :competences,
                 remuneration = :remuneration,
                 date_debut = :date_debut,
                 date_fin = :date_fin,
                 statut = :statut,
                 nb_places = :nb_places
             WHERE id_offre = :offer_id"
        );
        $stmt->bindValue(":entreprise_id", (int) $data["entreprise_id"], \PDO::PARAM_INT);
        $stmt->bindValue(":titre", $data["titre"]);
        $stmt->bindValue(":type_contrat", $data["type_contrat"]);
        $stmt->bindValue(":secteur", $data["secteur"]);
        $stmt->bindValue(":localisation", $data["localisation"]);
        $stmt->bindValue(":description_offre", $data["description_offre"]);
        $stmt->bindValue(":competences", $data["competences"]);
        $stmt->bindValue(":remuneration", $data["remuneration"] !== "" ? $data["remuneration"] : null);
        $stmt->bindValue(":date_debut", $data["date_debut"] !== "" ? $data["date_debut"] : null);
        $stmt->bindValue(":date_fin", $data["date_fin"] !== "" ? $data["date_fin"] : null);
        $stmt->bindValue(":statut", $data["statut"]);
        $stmt->bindValue(":nb_places", (int) $data["nb_places"], \PDO::PARAM_INT);
        $stmt->bindValue(":offer_id", $offerId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteOffer(int $offerId): bool{
        $stmt = $this->dbh->prepare("DELETE FROM offres WHERE id_offre = :offer_id");
        $stmt->bindValue(":offer_id", $offerId, \PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function getDataFormed(){
        $sql = $this->dbh->prepare("SELECT Max(id) FROM candidatures");
        $sql->execute();
        $id = $sql->fetch(\PDO::FETCH_ASSOC)["Max(id)"] ?? 0;
        $id += 1;
        $etudiant = $_SESSION["student"]["id"];
        $data = [$id, $etudiant];
        return $data;
    }

    public function write_postuler($id, $etudiant_id, $offre_id, $statut, $commentaire, $date_candidature){
        $sql = $this->dbh->prepare("INSERT INTO candidatures (id, etudiant_id, offre_id, statut, comaire, date_candidature)
        VALUES (:id, :etudiant_id, :offre_id, :statut, :comaire, :date_candidature)");
        $sql->bindValue(":id", $id);
        $sql->bindValue(":etudiant_id",$etudiant_id);
        $sql->bindValue(":offre_id",$offre_id);
        $sql->bindValue(":statut",$statut);
        $sql->bindValue(":comaire", $commentaire);
        $sql->bindValue(":date_candidature",$date_candidature);
        return $sql->execute();
    }
}
