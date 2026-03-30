<?php
// Détecter si on est sur Render
$isProduction = getenv('APP_ENV') === 'production' || 
               (isset($_SERVER['RENDER']) && $_SERVER['RENDER'] === 'true') ||
               !empty(getenv('RENDER'));

if ($isProduction) {
    // Configuration pour Render (production)
    define('SITE_NAME', 'Genova');
    define('SITE_TAGLINE', 'Solutions Digitales Innovantes');
    define('SITE_URL', getenv('RENDER_EXTERNAL_URL') ?: 'https://genova-4tld.onrender.com');
    define('SITE_EMAIL', getenv('SITE_EMAIL') ?: 'contact@genova.com');
    define('SITE_PHONE', getenv('SITE_PHONE') ?: '+33 1 23 45 67 89');
    define('SITE_ADDRESS', getenv('SITE_ADDRESS') ?: '123 Avenue des Champs-Élysées, 75008 Paris');
    define('SITE_YEAR', date('Y'));
    
    define('SITE_DESCRIPTION', 'Genova - Agence digitale spécialisée dans la création de sites web');
    define('SITE_KEYWORDS', 'agence digitale, création site web, développement web');
    
    $socials = [
        'facebook' => 'https://facebook.com/genova',
        'instagram' => 'https://instagram.com/genova',
        'linkedin' => 'https://linkedin.com/company/genova'
    ];
    
    $contact = [
        'email' => SITE_EMAIL,
        'phone' => SITE_PHONE,
        'address' => SITE_ADDRESS,
        'schedule' => 'Lun-Ven: 9h00 - 18h00'
    ];
    
    $services = [
        [
            'icon' => 'fa-globe',
            'title' => 'Site Web Sur-Mesure',
            'description' => 'Création de sites web professionnels',
            'features' => ['Design unique', '100% responsive', 'SEO optimisé']
        ],
        [
            'icon' => 'fa-mobile-alt',
            'title' => 'Applications Mobiles',
            'description' => 'Développement d\'applications iOS et Android',
            'features' => ['UI/UX design', 'Performance', 'Support technique']
        ],
        [
            'icon' => 'fa-chart-line',
            'title' => 'Marketing Digital',
            'description' => 'Stratégies digitales pour votre visibilité',
            'features' => ['SEO avancé', 'Réseaux sociaux', 'Analyse de données']
        ]
    ];
    
    $stats = [
        ['number' => '100+', 'label' => 'Projets réalisés'],
        ['number' => '50+', 'label' => 'Clients satisfaits'],
        ['number' => '24/7', 'label' => 'Support client']
    ];
    
} else {
    // Configuration locale (votre configuration existante)
    define('SITE_NAME', 'Genova');
    define('SITE_TAGLINE', 'Solutions Digitales Innovantes');
    define('SITE_URL', 'http://localhost/Genova');
    define('SITE_EMAIL', 'contact@genova.com');
    define('SITE_PHONE', '+33 1 23 45 67 89');
    define('SITE_ADDRESS', '123 Avenue des Champs-Élysées, 75008 Paris');
    define('SITE_YEAR', date('Y'));
    
    define('SITE_DESCRIPTION', 'Genova - Agence digitale spécialisée dans la création de sites web, applications mobiles et stratégie digitale.');
    define('SITE_KEYWORDS', 'agence digitale, création site web, développement web');
    
    $socials = [
        'facebook' => 'https://facebook.com/genova',
        'instagram' => 'https://instagram.com/genova',
        'linkedin' => 'https://linkedin.com/company/genova'
    ];
    
    $contact = [
        'email' => SITE_EMAIL,
        'phone' => SITE_PHONE,
        'address' => SITE_ADDRESS,
        'schedule' => 'Lun-Ven: 9h00 - 18h00'
    ];
    
    $services = [
        [
            'icon' => 'fa-globe',
            'title' => 'Site Web Sur-Mesure',
            'description' => 'Création de sites web professionnels et responsives',
            'features' => ['Design unique', '100% responsive', 'SEO optimisé']
        ],
        [
            'icon' => 'fa-mobile-alt',
            'title' => 'Applications Mobiles',
            'description' => 'Développement d\'applications iOS et Android',
            'features' => ['UI/UX design', 'Performance', 'Support technique']
        ],
        [
            'icon' => 'fa-chart-line',
            'title' => 'Marketing Digital',
            'description' => 'Stratégies digitales pour votre visibilité',
            'features' => ['SEO avancé', 'Réseaux sociaux', 'Analyse de données']
        ]
    ];
    
    $stats = [
        ['number' => '100+', 'label' => 'Projets réalisés'],
        ['number' => '50+', 'label' => 'Clients satisfaits'],
        ['number' => '24/7', 'label' => 'Support client']
    ];
}

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>