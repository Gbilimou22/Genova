<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/blog-functions.php';

// Vérifier la connexion admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Supprimer un article
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Article supprimé avec succès';
    } catch(PDOException $e) {
        $error = 'Erreur lors de la suppression';
    }
}

// Changer le statut (publier/dépublier)
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    if (in_array($status, ['draft', 'published'])) {
        try {
            $published_at = ($status == 'published') ? date('Y-m-d H:i:s') : null;
            $stmt = $db->prepare("UPDATE blog_posts SET status = :status, published_at = :published_at WHERE id = :id");
            $stmt->execute([
                ':status' => $status,
                ':published_at' => $published_at,
                ':id' => $id
            ]);
            $message = ($status == 'published') ? 'Article publié avec succès' : 'Article mis en brouillon';
        } catch(PDOException $e) {
            $error = 'Erreur lors du changement de statut';
        }
    }
}

// Récupérer tous les articles
$stmt = $db->query("SELECT p.*, c.name as category_name 
                    FROM blog_posts p
                    LEFT JOIN blog_categories c ON p.category_id = c.id
                    ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="posts-management">
    <div class="page-header">
        <h2><i class="fas fa-newspaper"></i> Gestion des articles</h2>
        <a href="add-post.php" class="btn-add">
            <i class="fas fa-plus"></i> Nouvel article
        </a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <p>Aucun article pour le moment</p>
            <a href="add-post.php" class="btn-primary">Créer votre premier article</a>
        </div>
    <?php else: ?>
        <div class="posts-table-container">
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Vues</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                        <tr>
                            <td class="post-title">
                                <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                <small><?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 80)) . '...'; ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($post['category_name'] ?? 'Non catégorisé'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo $post['status'] == 'published' ? 'Publié' : 'Brouillon'; ?>
                                </span>
                            </td>
                            <td><?php echo $post['views']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                            <td class="actions">
                                <a href="../blog/article.php?slug=<?php echo $post['slug']; ?>" target="_blank" class="btn-view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($post['status'] == 'draft'): ?>
                                    <a href="?status=published&id=<?php echo $post['id']; ?>" class="btn-publish" title="Publier">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?status=draft&id=<?php echo $post['id']; ?>" class="btn-draft" title="Mettre en brouillon">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=1&id=<?php echo $post['id']; ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer cet article ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.posts-management {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.page-header h2 {
    margin: 0;
}

.page-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.btn-add {
    background: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.3s;
}

.btn-add:hover {
    background: #059669;
}

.alert {
    padding: 0.75rem;
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

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.posts-table-container {
    overflow-x: auto;
}

.posts-table {
    width: 100%;
    border-collapse: collapse;
}

.posts-table th,
.posts-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.posts-table th {
    background: #f9fafb;
    font-weight: 600;
}

.post-title strong {
    display: block;
    margin-bottom: 0.25rem;
}

.post-title small {
    color: #6b7280;
    font-size: 0.75rem;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-published {
    background: #d1fae5;
    color: #065f46;
}

.status-draft {
    background: #fef3c7;
    color: #92400e;
}

.actions {
    display: flex;
    gap: 0.5rem;
    white-space: nowrap;
}

.actions a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-view { background: #3b82f6; color: white; }
.btn-edit { background: #f59e0b; color: white; }
.btn-publish { background: #10b981; color: white; }
.btn-draft { background: #8b5cf6; color: white; }
.btn-delete { background: #ef4444; color: white; }

.btn-view:hover, .btn-edit:hover, .btn-publish:hover, .btn-draft:hover, .btn-delete:hover {
    transform: translateY(-2px);
    filter: brightness(0.9);
}

.btn-primary {
    background: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: inline-block;
}
</style>

<?php include 'includes/admin-footer.php'; ?>