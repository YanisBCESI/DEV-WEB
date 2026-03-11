<?php

$entreprises = [
['nom' => 'TechNova', 'secteur' => 'Technologie', 'ville' => 'Paris'],
['nom' => 'GreenWave', 'secteur' => 'Énergie renouvelable', 'ville' => 'Lyon'],
['nom' => 'FinanciaPlus', 'secteur' => 'Finance', 'ville' => 'Londres'],
['nom' => 'HealthBridge', 'secteur' => 'Santé', 'ville' => 'Berlin'],
['nom' => 'AgroNova', 'secteur' => 'Agroalimentaire', 'ville' => 'Toulouse'],
['nom' => 'SkyLogistics', 'secteur' => 'Transport', 'ville' => 'Marseille'],
['nom' => 'UrbanBuild', 'secteur' => 'Construction', 'ville' => 'Nantes'],
['nom' => 'DataSphere', 'secteur' => 'Technologie', 'ville' => 'Amsterdam'],
['nom' => 'AquaPure', 'secteur' => 'Environnement', 'ville' => 'Bruxelles'],
['nom' => 'MediCore', 'secteur' => 'Santé', 'ville' => 'Genève'],
['nom' => 'CodeFactory', 'secteur' => 'Informatique', 'ville' => 'Bordeaux'],
['nom' => 'SolarisTech', 'secteur' => 'Énergie', 'ville' => 'Madrid'],
['nom' => 'EcoTrans', 'secteur' => 'Transport durable', 'ville' => 'Copenhague'],
['nom' => 'FoodExpress', 'secteur' => 'Restauration', 'ville' => 'Rome'],
['nom' => 'SecureIT', 'secteur' => 'Cybersécurité', 'ville' => 'Dublin'],
['nom' => 'MarketLead', 'secteur' => 'Marketing', 'ville' => 'New York'],
['nom' => 'EduSmart', 'secteur' => 'Éducation', 'ville' => 'Montréal'],
['nom' => 'BioLife', 'secteur' => 'Biotechnologie', 'ville' => 'Zurich'],
['nom' => 'AutoDrive', 'secteur' => 'Automobile', 'ville' => 'Stuttgart'],
['nom' => 'CloudNet', 'secteur' => 'Technologie', 'ville' => 'San Francisco'],
['nom' => 'SteelWorks', 'secteur' => 'Industrie', 'ville' => 'Lille'],
['nom' => 'FreshMarket', 'secteur' => 'Distribution', 'ville' => 'Barcelone'],
['nom' => 'BrightMedia', 'secteur' => 'Médias', 'ville' => 'Los Angeles'],
['nom' => 'OceanicTrade', 'secteur' => 'Commerce international', 'ville' => 'Singapour'],
['nom' => 'NeoPharma', 'secteur' => 'Pharmaceutique', 'ville' => 'Bâle'],
['nom' => 'InnoDesign', 'secteur' => 'Design', 'ville' => 'Milan'],
['nom' => 'FastDelivery', 'secteur' => 'Logistique', 'ville' => 'Hambourg'],
['nom' => 'EnergyPlus', 'secteur' => 'Énergie', 'ville' => 'Oslo'],
['nom' => 'CyberWave', 'secteur' => 'Cybersécurité', 'ville' => 'Tel Aviv'],
['nom' => 'BuildSmart', 'secteur' => 'Immobilier', 'ville' => 'Luxembourg'],
['nom' => 'GlobalConsult', 'secteur' => 'Conseil', 'ville' => 'Chicago'],
['nom' => 'NextGenAI', 'secteur' => 'Intelligence artificielle', 'ville' => 'Toronto'],
['nom' => 'PureWaterTech', 'secteur' => 'Traitement de l’eau', 'ville' => 'Stockholm'],
['nom' => 'CityTransport', 'secteur' => 'Transport', 'ville' => 'Vienne'],
['nom' => 'FashionTrend', 'secteur' => 'Mode', 'ville' => 'Paris'],
['nom' => 'SafeHome', 'secteur' => 'Sécurité', 'ville' => 'Munich'],
['nom' => 'FoodLab', 'secteur' => 'Agroalimentaire', 'ville' => 'Bruxelles'],
['nom' => 'TechSolutions', 'secteur' => 'Informatique', 'ville' => 'Lisbonne'],
['nom' => 'DigitalBoost', 'secteur' => 'Marketing digital', 'ville' => 'Berlin'],
['nom' => 'SmartEnergy', 'secteur' => 'Énergie renouvelable', 'ville' => 'Helsinki'],
['nom' => 'UrbanMobility', 'secteur' => 'Mobilité', 'ville' => 'Amsterdam'],
['nom' => 'BioGreen', 'secteur' => 'Environnement', 'ville' => 'Copenhague'],
['nom' => 'FinExpert', 'secteur' => 'Finance', 'ville' => 'Francfort'],
['nom' => 'MedFuture', 'secteur' => 'Santé', 'ville' => 'Lyon'],
['nom' => 'LogiChain', 'secteur' => 'Supply Chain', 'ville' => 'Rotterdam'],
['nom' => 'AgriTechPro', 'secteur' => 'Agriculture', 'ville' => 'Valence'],
['nom' => 'DataVision', 'secteur' => 'Analyse de données', 'ville' => 'Dubaï'],
['nom' => 'CreativeStudio', 'secteur' => 'Communication', 'ville' => 'Sydney'],
['nom' => 'NextBuild', 'secteur' => 'Construction', 'ville' => 'Montréal'],
['nom' => 'FutureRetail', 'secteur' => 'Commerce', 'ville' => 'Tokyo'],
];

$parPage = 10;
$nbEntreprises = count($entreprises);
$nbPages = ceil($nbEntreprises / $parPage);

/*coté verif num page*/
if (isset($_GET["page"]) && is_numeric($_GET["page"])){
    $pageActuelle = (int) $_GET["page"];
}
else{
    $pageActuelle = 1;
}

if ($pageActuelle < 1){
    $pageActuelle = 1;
}

if ($pageActuelle > $nbPages){
    $pageActuelle = $nbPages;
}

$starti = ($pageActuelle - 1) * $parPage;
$affichage = array_slice($entreprises, $starti, $parPage);

/*creation de l'affichage des entreprises*/
echo "<table border='1'>";
echo "<tr><th>Nom</th><th>Secteur</th><th>Ville</th></tr>";
foreach ($affichage as $boites){
    echo "<tr>";
    echo "<td>{$boites['nom']}</td>";
    echo "<td>{$boites['secteur']}</td>";
    echo "<td>{$boites['ville']}</td>";
    echo "</tr>";
}
echo "</table>";

/*boutons de nav*/
echo "<nav><ul>";
/*btn precedent*/
if ($pageActuelle > 1){
    echo "<li><a href='?page=".($pageActuelle - 1)."'>Précédent</a></li>";
}
/*num de page*/
for ($i = 1; $i <= $nbPages; $i++){
    if ($i == $pageActuelle){
        echo "<li>$i</li>";
    }
    else{
        echo "<li><a href='?page=$i'>$i</a></li>";
    }
}
/*btn suivant*/
if ($pageActuelle < $nbPages){
    echo "<li><a href='?page=".($pageActuelle + 1)."'>Suivant</a></li>";
}
echo "</ul></nav>";
echo "<a href='index.html'>Accueil</a>"

?>