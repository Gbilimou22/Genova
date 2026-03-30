<?php
require_once 'config/config.php';

$pageTitle = "Nos Services - " . SITE_NAME;
$pageDescription = "Découvrez nos services digitaux sur mesure";

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Nos services</h1>
            <p>Des solutions adaptées à vos besoins</p>
        </div>
    </section>
    
    <section class="services">
        <div class="container">
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
</main>

<?php include 'includes/footer.php'; ?>