<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/blog-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();

// Statistiques générales
$stats = [];

// Nombre total de messages
$stmt = $db->query("SELECT COUNT(*) FROM contacts");
$stats['total_messages'] = $stmt->fetchColumn();

// Messages non lus
$stmt = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'non lu'");
$stats['unread_messages'] = $stmt->fetchColumn();

// Nombre d'articles
$stmt = $db->query("SELECT COUNT(*) FROM blog_posts");
$stats['total_posts'] = $stmt->fetchColumn();

// Articles publiés
$stmt = $db->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
$stats['published_posts'] = $stmt->fetchColumn();

// Nombre de commentaires
$stmt = $db->query("SELECT COUNT(*) FROM blog_comments");
$stats['total_comments'] = $stmt->fetchColumn();

// Commentaires en attente
$stmt = $db->query("SELECT COUNT(*) FROM blog_comments WHERE status = 'pending'");
$stats['pending_comments'] = $stmt->fetchColumn();

// Nombre de catégories
$stmt = $db->query("SELECT COUNT(*) FROM blog_categories");
$stats['total_categories'] = $stmt->fetchColumn();

// Nombre d'abonnés newsletter
$stmt = $db->query("SELECT COUNT(*) FROM newsletter");
$stats['newsletter_subscribers'] = $stmt->fetchColumn();

// Vues totales des articles
$stmt = $db->query("SELECT SUM(views) FROM blog_posts");
$stats['total_views'] = $stmt->fetchColumn() ?: 0;

// Statistiques mensuelles pour le graphique (6 derniers mois)
$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('M Y', strtotime("-$i months"));
    
    // Articles publiés ce mois
    $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts 
                          WHERE status = 'published' 
                          AND DATE_FORMAT(published_at, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $postsCount = $stmt->fetchColumn();
    
    // Messages reçus ce mois
    $stmt = $db->prepare("SELECT COUNT(*) FROM contacts 
                          WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $messagesCount = $stmt->fetchColumn();
    
    $monthlyStats[] = [
        'month' => $monthName,
        'posts' => (int)$postsCount,
        'messages' => (int)$messagesCount
    ];
}

// Derniers messages
$stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
$recentMessages = $stmt->fetchAll();

// Derniers commentaires
$stmt = $db->query("SELECT c.*, p.title as post_title 
                    FROM blog_comments c
                    LEFT JOIN blog_posts p ON c.post_id = p.id
                    ORDER BY c.created_at DESC LIMIT 5");
$recentComments = $stmt->fetchAll();

// Articles les plus vus
$stmt = $db->query("SELECT * FROM blog_posts 
                    WHERE status = 'published' 
                    ORDER BY views DESC LIMIT 5");
$topPosts = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="dashboard">
    <!-- En-tête avec bienvenue -->
    <div class="welcome-section">
        <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h1>
        <p>Bienvenue dans votre espace d'administration. Voici un aperçu de votre activité.</p>
        <div class="date-time">
            <i class="fas fa-calendar"></i> <?php echo date('l d F Y'); ?>
            <i class="fas fa-clock"></i> <?php echo date('H:i'); ?>
        </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_posts']; ?></h3>
                <p>Articles</p>
                <small><?php echo $stats['published_posts']; ?> publiés</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_views']); ?></h3>
                <p>Vues totales</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_messages']; ?></h3>
                <p>Messages</p>
                <small><?php echo $stats['unread_messages']; ?> non lus</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-comment"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_comments']; ?></h3>
                <p>Commentaires</p>
                <small><?php echo $stats['pending_comments']; ?> en attente</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_categories']; ?></h3>
                <p>Catégories</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ec489a, #db2777);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['newsletter_subscribers']; ?></h3>
                <p>Abonnés newsletter</p>
            </div>
        </div>
    </div>
    
    <!-- Graphiques d'activité -->
    <div class="charts-row">
        <div class="chart-card">
            <h3><i class="fas fa-chart-line"></i> Activité des 6 derniers mois</h3>
            <canvas id="activityChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <div class="dashboard-row">
        <!-- Derniers messages -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-envelope"></i> Derniers messages</h3>
                <a href="messages.php" class="btn-link">Voir tous →</a>
            </div>
            <?php if (empty($recentMessages)): ?>
                <p class="empty-message">Aucun message</p>
            <?php else: ?>
                <div class="message-list">
                    <?php foreach($recentMessages as $msg): ?>
                        <div class="message-item <?php echo $msg['status'] == 'non lu' ? 'unread' : ''; ?>">
                            <div class="message-info">
                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                                <span><?php echo htmlspecialchars($msg['email']); ?></span>
                                <small><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></small>
                            </div>
                            <div class="message-preview">
                                <?php echo htmlspecialchars(substr($msg['message'], 0, 80)) . '...'; ?>
                            </div>
                            <div class="message-actions">
                                <a href="view-message.php?id=<?php echo $msg['id']; ?>" class="btn-sm btn-view">Voir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Derniers commentaires -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-comments"></i> Derniers commentaires</h3>
                <a href="comments.php" class="btn-link">Voir tous →</a>
            </div>
            <?php if (empty($recentComments)): ?>
                <p class="empty-message">Aucun commentaire</p>
            <?php else: ?>
                <div class="comment-list">
                    <?php foreach($recentComments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-info">
                                <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                                <span>sur "<?php echo htmlspecialchars($comment['post_title']); ?>"</span>
                                <small><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <div class="comment-preview">
                                <?php echo htmlspecialchars(substr($comment['content'], 0, 60)) . '...'; ?>
                            </div>
                            <?php if($comment['status'] == 'pending'): ?>
                                <div class="comment-status pending">En attente</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dashboard-row">
        <!-- Articles les plus vus -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-fire"></i> Articles populaires</h3>
                <a href="posts.php" class="btn-link">Voir tous →</a>
            </div>
            <?php if (empty($topPosts)): ?>
                <p class="empty-message">Aucun article</p>
            <?php else: ?>
                <div class="popular-list">
                    <?php foreach($topPosts as $post): ?>
                        <div class="popular-item">
                            <div class="popular-rank">
                                <i class="fas fa-chart-simple"></i>
                            </div>
                            <div class="popular-info">
                                <a href="../blog/article.php?slug=<?php echo $post['slug']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                                <span><?php echo number_format($post['views']); ?> vues</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Actions rapides -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Actions rapides</h3>
            </div>
            <div class="quick-actions">
                <a href="add-post.php" class="quick-action">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvel article</span>
                </a>
                <a href="categories.php" class="quick-action">
                    <i class="fas fa-tag"></i>
                    <span>Nouvelle catégorie</span>
                </a>
                <a href="messages.php" class="quick-action">
                    <i class="fas fa-envelope"></i>
                    <span>Voir messages</span>
                </a>
                <a href="backup.php" class="quick-action">
                    <i class="fas fa-database"></i>
                    <span>Sauvegarder</span>
                </a>
                <a href="export-data.php" class="quick-action">
                    <i class="fas fa-file-export"></i>
                    <span>Exporter</span>
                </a>
                <a href="settings.php" class="quick-action">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard {
    padding: 0;
}

.welcome-section {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.welcome-section h1 {
    margin: 0 0 0.5rem 0;
    font-size: 1.8rem;
}

.welcome-section p {
    margin: 0;
    opacity: 0.9;
}

.date-time {
    margin-top: 1rem;
    font-size: 0.9rem;
    display: flex;
    gap: 1rem;
}

.date-time i {
    margin-right: 0.25rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.75rem;
    color: white;
}

.stat-info h3 {
    font-size: 1.75rem;
    margin: 0;
    color: #1f2937;
}

.stat-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.stat-info small {
    font-size: 0.75rem;
    color: #9ca3af;
}

.charts-row {
    margin-bottom: 2rem;
}

.chart-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.chart-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.2rem;
}

.chart-card h3 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.dashboard-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.card-header h3 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.btn-link {
    color: #10b981;
    text-decoration: none;
    font-size: 0.875rem;
}

.btn-link:hover {
    text-decoration: underline;
}

.message-list,
.comment-list,
.popular-list {
    padding: 0.5rem;
}

.message-item,
.comment-item,
.popular-item {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.3s;
}

.message-item:hover,
.comment-item:hover {
    background: #f9fafb;
}

.message-item.unread {
    background: #fef3c7;
}

.message-info,
.comment-info {
    margin-bottom: 0.5rem;
}

.message-info strong,
.comment-info strong {
    color: #1f2937;
    margin-right: 0.5rem;
}

.message-info span,
.comment-info span {
    color: #6b7280;
    font-size: 0.875rem;
    margin-right: 0.5rem;
}

.message-info small,
.comment-info small {
    color: #9ca3af;
    font-size: 0.75rem;
}

.message-preview,
.comment-preview {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.message-actions {
    text-align: right;
}

.btn-sm {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    text-decoration: none;
}

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-view:hover {
    background: #2563eb;
}

.comment-status {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    background: #fef3c7;
    color: #92400e;
    margin-top: 0.5rem;
}

.popular-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.popular-rank {
    width: 30px;
    color: #f59e0b;
}

.popular-info {
    flex: 1;
}

.popular-info a {
    display: block;
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.popular-info a:hover {
    color: #10b981;
}

.popular-info span {
    font-size: 0.75rem;
    color: #9ca3af;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    padding: 1rem;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s;
}

.quick-action:hover {
    background: #10b981;
    color: white;
}

.quick-action i {
    width: 20px;
}

.empty-message {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-row {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique d'activité
const ctx = document.getElementById('activityChart').getContext('2d');
const monthlyData = <?php echo json_encode($monthlyStats); ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [
            {
                label: 'Articles publiés',
                data: monthlyData.map(item => item.posts),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Messages reçus',
                data: monthlyData.map(item => item.messages),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include 'includes/admin-footer.php'; ?>