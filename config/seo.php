<?php
// Configuration SEO avancée - Sans redéfinir les constantes existantes

// Définir les constantes SEO seulement si elles n'existent pas déjà
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://localhost/Genova');
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Genova');
}

if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', 'Genova - Agence digitale spécialisée dans la création de sites web, applications mobiles et stratégie digitale. Solutions innovantes pour votre entreprise.');
}

if (!defined('SITE_KEYWORDS')) {
    define('SITE_KEYWORDS', 'agence digitale, création site web, développement web, application mobile, SEO, marketing digital, Genova');
}

if (!defined('SITE_AUTHOR')) {
    define('SITE_AUTHOR', 'Genova Agency');
}

if (!defined('SITE_LANG')) {
    define('SITE_LANG', 'fr_FR');
}

// Données structurées (JSON-LD)
$organizationData = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => SITE_NAME,
    'url' => SITE_URL,
    'logo' => SITE_URL . '/images/logo.png',
    'description' => SITE_DESCRIPTION,
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
        'telephone' => '+33123456789',
        'contactType' => 'customer service',
        'email' => 'contact@genova.com'
    ],
    'sameAs' => [
        'https://facebook.com/genova',
        'https://twitter.com/genova',
        'https://linkedin.com/company/genova',
        'https://instagram.com/genova'
    ]
];

// Cache configuration
if (!defined('CACHE_ENABLED')) {
    define('CACHE_ENABLED', true);
}

if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', __DIR__ . '/../cache/');
}

if (!defined('CACHE_TIME')) {
    define('CACHE_TIME', 3600); // 1 heure
}

// Compression
if (!defined('COMPRESS_ENABLED')) {
    define('COMPRESS_ENABLED', true);
}

// Sitemap
if (!defined('SITEMAP_URL')) {
    define('SITEMAP_URL', SITE_URL . '/sitemap.xml');
}

if (!defined('SITEMAP_CHANGE_FREQ')) {
    define('SITEMAP_CHANGE_FREQ', 'weekly');
}

if (!defined('SITEMAP_PRIORITY')) {
    define('SITEMAP_PRIORITY', [
        'index' => 1.0,
        'services' => 0.9,
        'portfolio' => 0.8,
        'about' => 0.7,
        'contact' => 0.6,
        'blog' => 0.8
    ]);
}
?>