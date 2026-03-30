<?php
header("HTTP/1.0 404 Not Found");
require_once 'config/config.php';
$pageTitle = "Page non trouvée - " . SITE_NAME;
include 'includes/header.php';
?>

<section class="error-page">
    <div class="container">
        <div class="error-content">
            <h1>404</h1>
            <h2>Page non trouvée</h2>
            <p>Désolé, la page que vous cherchez n'existe pas ou a été déplacée.</p>
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                <a href="contact.php" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    text-align: center;
    padding: 80px 0;
}

.error-content h1 {
    font-size: 120px;
    margin: 0;
    background: linear-gradient(135deg, #10b981, #059669);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.error-content h2 {
    font-size: 32px;
    margin: 20px 0;
}

.error-content p {
    color: #6b7280;
    margin-bottom: 30px;
}

.error-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
}
</style>

<?php include 'includes/footer.php'; ?>