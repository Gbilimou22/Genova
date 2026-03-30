<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();

// Supprimer un abonné
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("DELETE FROM newsletter WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: newsletter.php');
    exit();
}

// Récupérer tous les abonnés
$stmt = $db->query("SELECT * FROM newsletter ORDER BY created_at DESC");
$subscribers = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="newsletter-page">
    <div class="page-header">
        <h2><i class="fas fa-newspaper"></i> Newsletter</h2>
        <p>Gérez les abonnés à la newsletter</p>
    </div>
    
    <?php if (empty($subscribers)): ?>
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <p>Aucun abonné pour le moment</p>
    </div>
    <?php else: ?>
    <div class="subscribers-stats">
        <div class="stat">
            <span class="stat-number"><?php echo count($subscribers); ?></span>
            <span class="stat-label">Abonnés total</span>
        </div>
    </div>
    
    <div class="subscribers-table-container">
        <table class="subscribers-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscribers as $subscriber): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($subscriber['created_at'])); ?></td>
                    <td class="actions">
                        <a href="mailto:<?php echo $subscriber['email']; ?>" class="btn-email" title="Envoyer un email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="?delete=1&id=<?php echo $subscriber['id']; ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer cet abonné ?')">
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
.newsletter-page {
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

.page-header h2 {
    margin-bottom: 0.25rem;
}

.page-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.page-header p {
    color: #6b7280;
}

.subscribers-stats {
    margin-bottom: 1.5rem;
}

.stat {
    background: #f9fafb;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    display: inline-block;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-left: 0.5rem;
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

.subscribers-table-container {
    overflow-x: auto;
}

.subscribers-table {
    width: 100%;
    border-collapse: collapse;
}

.subscribers-table th,
.subscribers-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.subscribers-table th {
    background: #f9fafb;
    font-weight: 600;
}

.subscribers-table tr:hover {
    background: #f3f4f6;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-email, .btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-email {
    background: #3b82f6;
    color: white;
}

.btn-email:hover {
    background: #2563eb;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
}
</style>

<?php include 'includes/admin-footer.php'; ?>