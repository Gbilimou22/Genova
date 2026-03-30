<?php
require_once 'config/config.php';

$pageTitle = SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDescription = SITE_DESCRIPTION;

include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">Agence digitale</span>
                <h1 class="hero-title">
                    Créons ensemble votre 
                    <span class="hero-title-gradient">réussite digitale</span>
                </h1>
                <p class="hero-subtitle">
                    <?php echo SITE_TAGLINE; ?> - Nous transformons vos idées en solutions digitales performantes.
                </p>
                <div class="hero-buttons">
                    <a href="contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i> Démarrer un projet
                    </a>
                    <a href="services.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-eye"></i> Découvrir nos services
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="section-header">
                <h2>Nos services</h2>
                <p>Des solutions sur mesure pour répondre à tous vos besoins</p>
            </div>
            <div class="services-grid">
                <?php foreach($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas <?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $service['title']; ?></h3>
                    <p><?php echo $service['description']; ?></p>
                    <ul class="service-features">
                        <?php foreach($service['features'] as $feature): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <?php foreach($stats as $stat): ?>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $stat['number']; ?></div>
                    <div class="stat-label"><?php echo $stat['label']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à donner vie à votre projet ?</h2>
                <p>Contactez-nous dès aujourd'hui pour discuter de vos besoins</p>
                <a href="contact.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar-alt"></i> Prendre rendez-vous
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>