<?php
namespace App\Controllers;

use App\Models\OffreModel;
use Twig\Environment;

class OffreController extends Controller {

    private OffreModel $offreModel;

    public function __construct(Environment $twig, OffreModel $model){
        $this->templateEngine = $twig;
        $this->offreModel = $model;
    }

    /*Traitement du formulaire */
    public function create(): void {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée.');
        }

        $data = [
            'email_entreprise' => trim($_POST['email_entreprise'] ?? ''),
            'titre' => trim($_POST['titre'] ?? ''),
            'type_contrat' => trim($_POST['type_contrat'] ?? ''),
            'secteur' => trim($_POST['secteur'] ?? ''),
            'localisation' => trim($_POST['localisation'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'competences' => trim($_POST['competences'] ?? ''),
            'remuneration' => trim($_POST['remuneration'] ?? ''),
            'date_debut' => trim($_POST['date_debut'] ?? '')
        ];

        $typesAutorises = ['stage', 'alternance', 'emploi'];

        // Validation
        if (
            !filter_var($data['email_entreprise'], FILTER_VALIDATE_EMAIL) ||
            $data['titre'] === '' ||
            !in_array($data['type_contrat'], $typesAutorises, true) ||
            $data['secteur'] === '' ||
            $data['localisation'] === '' ||
            $data['description'] === '' ||
            $data['competences'] === ''
        ) {
            exit('Formulaire invalide.');
        }

        try {
            // Récupérer l'entreprise
            $entrepriseId = $this->offreModel->getEntrepriseIdByEmail($data['email_entreprise']);

            if (!$entrepriseId) {
                exit("Aucune entreprise associée à cet email.");
            }

            // Ajouter au tableau
            $data['entreprise_id'] = $entrepriseId;

            // Création offre
            $this->offreModel->createOffre($data);

            echo $this->templateEngine->render('offre_success.html.twig');

        } catch (\Exception $e) {
            exit("Erreur : " . $e->getMessage());
        }
    }
}