<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();

// Approuver un commentaire
if (isset($_GET['approve']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->exec("UPDATE blog_comments SET status = 'approved' WHERE id = $id");
    header('Location: comments.php');
    exit();
}

// Supprimer un commentaire
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->exec("DELETE FROM blog_comments WHERE id = $id");
    header('Location: comments.php');
    exit();
}

// Récupérer les commentaires en attente
$stmt = $db->query("SELECT c.*, p.title as post_title 
                    FROM blog_comments c
                    LEFT JOIN blog_posts p ON c.post_id = p.id
                    ORDER BY c.created_at DESC");
$comments = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="comments-management">
    <h2>Gestion des commentaires</h2>
    
    <table class="comments-table">
        <thead>
            <tr>
                <th>Article</th>
                <th>Auteur</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($comments as $comment): ?>
            <tr>
                <td><?php echo htmlspecialchars($comment['post_title']); ?></td>
                <td><?php echo htmlspecialchars($comment['author']); ?></td>
                <td><?php echo htmlspecialchars(substr($comment['content'], 0, 100)); ?>...</td>
                <td><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></td>
                <td>
                    <?php if($comment['status'] == 'pending'): ?>
                        <span class="badge warning">En attente</span>
                    <?php else: ?>
                        <span class="badge success">Approuvé</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($comment['status'] == 'pending'): ?>
                        <a href="?approve=1&id=<?php echo $comment['id']; ?>" class="btn-approve">Approuver</a>
                    <?php endif; ?>
                    <a href="?delete=1&id=<?php echo $comment['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.comments-management {
    background: white;
    padding: 20px;
    border-radius: 8px;
}
.comments-table {
    width: 100%;
    border-collapse: collapse;
}
.comments-table th,
.comments-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
}
.badge.warning { background: #ffc107; color: #000; }
.badge.success { background: #28a745; color: #fff; }
.btn-approve {
    background: #28a745;
    color: white;
    padding: 4px 8px;
    text-decoration: none;
    border-radius: 4px;
    margin-right: 5px;
}
.btn-delete {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    text-decoration: none;
    border-radius: 4px;
}
</style>

<?php include 'includes/admin-footer.php'; ?>