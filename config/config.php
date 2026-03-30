<?php
// Configuration du site Genova
define('SITE_NAME', 'Genova');
define('SITE_TAGLINE', 'Solutions Digitales Innovantes');
define('SITE_URL', 'https://localhost/Genova');
define('SITE_EMAIL', 'contact@genova.com');
define('SITE_PHONE', '+224 624 92 95 47');
define('SITE_ADDRESS', 'Rue 15 de Dixinn, 00224 Guinée Conakry');
define('SITE_YEAR', date('Y'));

// SEO
define('SITE_DESCRIPTION', 'Genova - Agence digitale spécialisée dans la création de sites web et solutions innovantes.');
define('SITE_KEYWORDS', 'agence digitale, création site web, développement web, Genova');

// Configuration email
define('SMTP_HOST', 'smtp.gmail.com'); // Pour Gmail
define('SMTP_PORT', 587);
define('SMTP_USER', 'gbilimouz4@gmail.com');
define('SMTP_PASS', '624929547@');
define('SMTP_SECURE', 'tls');

// Version du site
define('SITE_VERSION', '2.0.0');

// Réseaux sociaux
$socials = [
    'facebook' => 'https://facebook.com/genova',
    'instagram' => 'https://instagram.com/genova',
    'linkedin' => 'https://linkedin.com/company/genova',
    'twitter' => 'https://twitter.com/genova'
];

// Coordonnées
$contact = [
    'email' => SITE_EMAIL,
    'phone' => SITE_PHONE,
    'address' => SITE_ADDRESS,
    'schedule' => 'Lundi - Vendredi: 9h00 - 18h00',
    'map' => 'https://maps.google.com/?q=Rue+15+de+Dixinn,+00224+Guinée+Conakry'
];

// Services proposés
$services = [
    [
        'icon' => 'fa-globe',
        'title' => 'Création de Sites Web',
        'description' => 'Sites vitrine, e-commerce et applications web sur mesure',
        'features' => ['Design responsive', 'SEO optimisé', 'CMS personnalisé']
    ],
    [
        'icon' => 'fa-mobile-alt',
        'title' => 'Applications Mobiles',
        'description' => 'Applications iOS et Android natives et cross-platform',
        'features' => ['UI/UX moderne', 'Performance', 'Support continu']
    ],
    [
        'icon' => 'fa-chart-line',
        'title' => 'Stratégie Digitale',
        'description' => 'Accompagnement pour votre transformation digitale',
        'features' => ['Audit SEO', 'Marketing digital', 'Analyse data']
    ]
];

// Statistiques
$stats = [
    ['number' => '150+', 'label' => 'Projets réalisés'],
    ['number' => '80+', 'label' => 'Clients satisfaits'],
    ['number' => '10+', 'label' => 'Experts passionnés'],
    ['number' => '24/7', 'label' => 'Support client']
];

// Équipe
$team = [
    [
        'name' => 'Sophie Martin',
        'position' => 'CEO & Fondatrice',
        'bio' => 'Experte en stratégie digitale avec plus de 10 ans d\'expérience',
        'image' => 'images/team-1.jpg'
    ],
    [
        'name' => 'Thomas Bernard',
        'position' => 'Directeur Technique',
        'bio' => 'Expert en développement full-stack et architecture cloud',
        'image' => 'images/team-2.jpg'
    ],
    [
        'name' => 'Julie Petit',
        'position' => 'Lead Designer',
        'bio' => 'Spécialiste en UI/UX et design thinking',
        'image' => 'images/team-3.jpg'
    ]
];

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>