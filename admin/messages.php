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

// Marquer comme lu
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("UPDATE contacts SET status = 'lu' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

// Marquer comme répondu
if (isset($_GET['mark_responded']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("UPDATE contacts SET status = 'répondu' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

// Supprimer un message
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

// Récupérer tous les messages
$stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="messages-page">
    <div class="page-header">
        <h2><i class="fas fa-envelope"></i> Messages de contact</h2>
        <p>Gérez les messages reçus via le formulaire de contact</p>
    </div>
    
    <?php if (empty($messages)): ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>Aucun message pour le moment</p>
    </div>
    <?php else: ?>
    <div class="messages-stats">
        <div class="stat">
            <span class="stat-number"><?php echo count($messages); ?></span>
            <span class="stat-label">Total messages</span>
        </div>
        <div class="stat">
            <span class="stat-number"><?php echo count(array_filter($messages, fn($m) => $m['status'] == 'non lu')); ?></span>
            <span class="stat-label">Non lus</span>
        </div>
    </div>
    
    <div class="messages-table-container">
        <table class="messages-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Sujet</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($messages as $message): ?>
                <tr class="<?php echo $message['status'] == 'non lu' ? 'unread' : ''; ?>">
                    <td>
                        <span class="status-badge status-<?php echo str_replace(' ', '-', $message['status']); ?>">
                            <?php echo $message['status']; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                    <td class="actions">
                        <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn-view" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if($message['status'] == 'non lu'): ?>
                        <a href="?mark_read=1&id=<?php echo $message['id']; ?>" class="btn-read" title="Marquer comme lu">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <a href="?delete=1&id=<?php echo $message['id']; ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce message ?')">
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
.messages-page {
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

.messages-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat {
    background: #f9fafb;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
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

.messages-table-container {
    overflow-x: auto;
}

.messages-table {
    width: 100%;
    border-collapse: collapse;
}

.messages-table th,
.messages-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.messages-table th {
    background: #f9fafb;
    font-weight: 600;
}

.messages-table tr.unread {
    background: #fef3c7;
    font-weight: 500;
}

.messages-table tr:hover {
    background: #f3f4f6;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-non-lu {
    background: #fee2e2;
    color: #991b1b;
}

.status-lu {
    background: #d1fae5;
    color: #065f46;
}

.status-répondu {
    background: #dbeafe;
    color: #1e40af;
}

.actions {
    display: flex;
    gap: 0.5rem;
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

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-view:hover {
    background: #2563eb;
}

.btn-read {
    background: #10b981;
    color: white;
}

.btn-read:hover {
    background: #059669;
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