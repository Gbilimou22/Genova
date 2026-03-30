<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Portfolio - " . SITE_NAME;
$pageDescription = "Découvrez nos réalisations et projets";

// Récupérer les projets
$projects = getProjects();

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Notre portfolio</h1>
            <p>Découvrez nos dernières réalisations</p>
        </div>
    </section>
    
    <section class="portfolio-section">
        <div class="container">
            <?php if (empty($projects)): ?>
            <div class="no-projects">
                <p>Aucun projet pour le moment. Revenez bientôt !</p>
            </div>
            <?php else: ?>
            <div class="portfolio-grid">
                <?php foreach($projects as $project): ?>
                <div class="portfolio-item">
                    <div class="portfolio-image">
                        <img src="<?php echo $project['image'] ?: 'images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="portfolio-overlay">
                            <a href="<?php echo $project['link'] ?: '#'; ?>" class="btn btn-primary">
                                Voir le projet
                            </a>
                        </div>
                    </div>
                    <div class="portfolio-info">
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        <span class="portfolio-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <p><?php echo htmlspecialchars($project['description']); ?></p>
                        <?php if($project['client']): ?>
                        <p class="portfolio-client">
                            <strong>Client :</strong> <?php echo htmlspecialchars($project['client']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.portfolio-section {
    padding: 80px 0;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.portfolio-item {
    background: var(--white);
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.portfolio-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.portfolio-image {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
}

.portfolio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.portfolio-item:hover .portfolio-image img {
    transform: scale(1.1);
}

.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.portfolio-item:hover .portfolio-overlay {
    opacity: 1;
}

.portfolio-info {
    padding: 1.5rem;
}

.portfolio-category {
    display: inline-block;
    background: var(--light);
    color: var(--primary);
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
}

.portfolio-client {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
    font-size: 0.875rem;
    color: var(--gray);
}

.no-projects {
    text-align: center;
    padding: 3rem;
    color: var(--gray);
}
</style>

<?php include 'includes/footer.php'; ?>