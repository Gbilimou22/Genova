<?php
// Fonctions SEO avancées - Sans redéfinir les constantes

// Vérifier si les constantes SEO sont définies, sinon les définir
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}

// Générer les meta tags dynamiques
function generateMetaTags($pageTitle = null, $pageDescription = null, $pageKeywords = null, $pageImage = null) {
    $title = $pageTitle ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE;
    $description = $pageDescription ?: (defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Site Genova');
    $keywords = $pageKeywords ?: (defined('SITE_KEYWORDS') ? SITE_KEYWORDS : 'Genova, agence digitale');
    $image = $pageImage ?: (defined('SITE_URL') ? SITE_URL . '/images/og-image.jpg' : '/images/og-image.jpg');
    $url = (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . $_SERVER['REQUEST_URI'];
    
    $meta = [];
    $meta[] = '<meta charset="UTF-8">';
    $meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">';
    $meta[] = '<meta name="description" content="' . htmlspecialchars($description) . '">';
    $meta[] = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">';
    $meta[] = '<meta name="author" content="' . (defined('SITE_AUTHOR') ? SITE_AUTHOR : 'Genova') . '">';
    $meta[] = '<meta name="robots" content="index, follow">';
    $meta[] = '<meta name="language" content="fr">';
    $meta[] = '<meta name="revisit-after" content="7 days">';
    
    // Open Graph
    $meta[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
    $meta[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
    $meta[] = '<meta property="og:type" content="website">';
    $meta[] = '<meta property="og:url" content="' . $url . '">';
    $meta[] = '<meta property="og:image" content="' . $image . '">';
    $meta[] = '<meta property="og:site_name" content="' . SITE_NAME . '">';
    $meta[] = '<meta property="og:locale" content="fr_FR">';
    
    // Twitter Card
    $meta[] = '<meta name="twitter:card" content="summary_large_image">';
    $meta[] = '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">';
    $meta[] = '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">';
    $meta[] = '<meta name="twitter:image" content="' . $image . '">';
    
    // Canonical URL
    $meta[] = '<link rel="canonical" href="' . $url . '">';
    
    // Title
    $meta[] = '<title>' . htmlspecialchars($title) . '</title>';
    
    return implode("\n    ", $meta);
}

// Générer les données structurées JSON-LD
function generateStructuredData() {
    $orgData = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => SITE_NAME,
        'url' => defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova',
        'description' => defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Agence digitale',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '123 Avenue des Champs-Élysées',
            'addressLocality' => 'Paris',
            'addressRegion' => 'Île-de-France',
            'postalCode' => '75008',
            'addressCountry' => 'FR'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => defined('SITE_PHONE') ? SITE_PHONE : '+33123456789',
            'contactType' => 'customer service'
        ]
    ];
    return '<script type="application/ld+json">' . json_encode($orgData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

// Le reste des fonctions reste identique...
function generateSitemap() {
    $pages = [
        ['url' => (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['url' => (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . '/services.php', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['url' => (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . '/portfolio.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . '/about.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['url' => (defined('SITE_URL') ? SITE_URL : 'https://localhost/Genova') . '/contact.php', 'priority' => '0.6', 'changefreq' => 'monthly']
    ];
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($pages as $page) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $page['url'] . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    $xml .= '</urlset>';
    return $xml;
}

function createSitemapFile() {
    $sitemapContent = generateSitemap();
    $cacheDir = __DIR__ . '/../';
    file_put_contents($cacheDir . 'sitemap.xml', $sitemapContent);
    return true;
}

// Le reste des fonctions (minifyHtml, startCache, etc.) reste identique
?>