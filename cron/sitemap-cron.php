<?php
// Script à exécuter quotidiennement pour générer le sitemap
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/seo-functions.php';

if (createSitemapFile()) {
    echo "Sitemap généré avec succès le " . date('Y-m-d H:i:s') . "\n";
    
    // Optionnel : notifier Google
    $googleUrl = "https://www.google.com/ping?sitemap=" . urlencode(SITE_URL . '/sitemap.xml');
    file_get_contents($googleUrl);
    
    echo "Google notifié\n";
} else {
    echo "Erreur lors de la génération du sitemap\n";
}
?>