<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    die('Autoloader non trouvé');
}

use App\Models\OffersModel;

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

try {
    $base = 'https://www.stage4all.fr';
    $pages = [
        $base . '/',
        $base . '/?uri=offres',
        $base . '/?uri=mentions_legales',
        $base . '/?uri=cookies',
        $base . '/?uri=besoin_aide',
        $base . '/?uri=conseils',
        $base . '/?uri=entreprises',
        $base . '/?uri=postuler',
    ];

    foreach ($pages as $loc) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        echo '    <changefreq>monthly</changefreq>' . PHP_EOL;
        echo '    <priority>0.7</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    $offersModel = new OffersModel();
    $offres = $offersModel->getAllOffers();

    foreach ($offres as $offre) {
        $loc = $base . '/?uri=offres&amp;id_offre=' . $offre['id_offre'];
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.5</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }
} catch (Exception $e) {
    error_log('Erreur sitemap - ' . $e->getMessage());
}

echo '</urlset>';