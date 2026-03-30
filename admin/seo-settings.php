<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/seo-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Générer le sitemap manuellement
if (isset($_POST['generate_sitemap'])) {
    if (createSitemapFile()) {
        $message = "Sitemap généré avec succès !";
    } else {
        $error = "Erreur lors de la génération du sitemap.";
    }
}

// Vider le cache
if (isset($_POST['clear_cache'])) {
    $files = glob(CACHE_DIR . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    $message = "Cache vidé avec succès !";
}

include 'includes/admin-header.php';
?>

<div class="seo-settings">
    <div class="page-header">
        <h2><i class="fas fa-chart-line"></i> SEO & Performance</h2>
        <p>Optimisez votre référencement et les performances du site</p>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="seo-grid">
        <!-- Sitemap -->
        <div class="seo-card">
            <h3><i class="fas fa-sitemap"></i> Sitemap XML</h3>
            <p>Générez automatiquement votre sitemap pour les moteurs de recherche.</p>
            <form method="POST">
                <button type="submit" name="generate_sitemap" class="btn-primary">
                    <i class="fas fa-refresh"></i> Générer le sitemap
                </button>
            </form>
            <div class="info">
                <strong>URL du sitemap :</strong>
                <a href="<?php echo SITE_URL; ?>/sitemap.xml" target="_blank">
                    <?php echo SITE_URL; ?>/sitemap.xml
                </a>
            </div>
        </div>
        
        <!-- Cache -->
        <div class="seo-card">
            <h3><i class="fas fa-database"></i> Gestion du cache</h3>
            <p>Videz le cache pour voir les dernières modifications.</p>
            <form method="POST">
                <button type="submit" name="clear_cache" class="btn-warning">
                    <i class="fas fa-trash"></i> Vider le cache
                </button>
            </form>
        </div>
        
        <!-- Robots.txt -->
        <div class="seo-card">
            <h3><i class="fas fa-robot"></i> Robots.txt</h3>
            <p>Configurez l'exploration de votre site par les moteurs.</p>
            <a href="<?php echo SITE_URL; ?>/robots.txt" target="_blank" class="btn-info">
                <i class="fas fa-eye"></i> Voir robots.txt
            </a>
        </div>
        
        <!-- Meta Tags -->
        <div class="seo-card">
            <h3><i class="fas fa-tags"></i> Meta Tags</h3>
            <p>Vérifiez vos meta tags pour chaque page.</p>
            <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn-info">
                <i class="fas fa-check"></i> Vérifier les meta tags
            </a>
        </div>
        
        <!-- Performance -->
        <div class="seo-card">
            <h3><i class="fas fa-tachometer-alt"></i> Performance</h3>
            <p>Testez la performance de votre site.</p>
            <a href="https://pagespeed.web.dev/?url=<?php echo urlencode(SITE_URL); ?>" target="_blank" class="btn-info">
                <i class="fas fa-chart-line"></i> Tester sur PageSpeed
            </a>
        </div>
        
        <!-- Google Search Console -->
        <div class="seo-card">
            <h3><i class="fab fa-google"></i> Google Search Console</h3>
            <p>Ajoutez votre site à Google Search Console.</p>
            <div class="code-block">
                <code>&lt;meta name="google-site-verification" content="VOTRE_CODE"&gt;</code>
            </div>
        </div>
    </div>
</div>

<style>
.seo-settings {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.page-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.seo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
}

.seo-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.seo-card h3 {
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.seo-card h3 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.seo-card p {
    color: #6b7280;
    margin-bottom: 1rem;
}

.btn-primary, .btn-warning, .btn-info {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-primary {
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.info {
    margin-top: 1rem;
    padding: 0.5rem;
    background: #f3f4f6;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.code-block {
    background: #1f2937;
    color: #10b981;
    padding: 0.5rem;
    border-radius: 0.5rem;
    font-family: monospace;
    font-size: 0.75rem;
    margin-top: 0.5rem;
    overflow-x: auto;
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
}

.alert-error {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #991b1b;
}
</style>

<?php include 'includes/admin-footer.php'; ?>