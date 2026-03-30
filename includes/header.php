<?php
// Inclure d'abord config.php (qui contient les constantes de base)
require_once __DIR__ . '/../config/config.php';

// Ensuite inclure seo.php (qui utilise les constantes sans les redéfinir)
require_once __DIR__ . '/../config/seo.php';

// Enfin inclure les fonctions
require_once __DIR__ . '/../includes/seo-functions.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Définir les meta tags selon la page
$pageMeta = [
    'index' => [
        'title' => SITE_NAME . ' - ' . SITE_TAGLINE,
        'description' => SITE_DESCRIPTION,
        'keywords' => SITE_KEYWORDS
    ],
    'services' => [
        'title' => 'Nos Services - ' . SITE_NAME,
        'description' => 'Découvrez nos services digitaux : sites web, applications mobiles, marketing digital. Des solutions sur mesure pour votre entreprise.',
        'keywords' => 'services digitaux, création site web, application mobile, marketing'
    ],
    'portfolio' => [
        'title' => 'Portfolio - ' . SITE_NAME,
        'description' => 'Découvrez nos réalisations et projets. Des sites web, applications et solutions digitales innovantes.',
        'keywords' => 'portfolio, réalisations, projets web, applications'
    ],
    'about' => [
        'title' => 'À propos - ' . SITE_NAME,
        'description' => 'Découvrez notre histoire, notre équipe et nos valeurs. Une agence digitale passionnée par l\'innovation.',
        'keywords' => 'agence digitale, équipe, valeurs, histoire'
    ],
    'contact' => [
        'title' => 'Contact - ' . SITE_NAME,
        'description' => 'Contactez notre équipe pour discuter de votre projet digital. Un devis gratuit et personnalisé.',
        'keywords' => 'contact, devis gratuit, projet digital'
    ]
];

$currentMeta = $pageMeta[$current_page] ?? $pageMeta['index'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php echo generateMetaTags($currentMeta['title'], $currentMeta['description'], $currentMeta['keywords']); ?>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- CSS principal -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    
    <!-- Données structurées -->
    <?php echo generateStructuredData(); ?>
    
    <!-- Preload critical assets -->
    <link rel="preload" href="css/style.css" as="style">
    <link rel="preload" href="js/main.js" as="script">
</head>
<body>
    <!-- Skip to content (accessibilité) -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>
    
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="index.php" aria-label="Accueil Genova">
                        <span class="logo-text">Genova</span>
                        <span class="logo-dot">.</span>
                    </a>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <ul class="nav-menu" id="navMenu" role="navigation">
                <li><a href="index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">Accueil</a></li>
                <li><a href="services.php" class="<?php echo $current_page == 'services' ? 'active' : ''; ?>">Services</a></li>
                <li><a href="portfolio.php" class="<?php echo $current_page == 'portfolio' ? 'active' : ''; ?>">Portfolio</a></li>
                <li><a href="blog/index.php" class="<?php echo $current_page == 'blog' ? 'active' : ''; ?>">Blog</a></li>
                <li><a href="about.php" class="<?php echo $current_page == 'about' ? 'active' : ''; ?>">À propos</a></li>
                <li><a href="contact.php" class="<?php echo $current_page == 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="contact.php" class="btn btn-primary btn-sm">Devis gratuit</a>
                </div>
            </div>
        </nav>
    </header>
    
    <main id="main-content">