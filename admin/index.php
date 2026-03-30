<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();

// Statistiques
$stats = [];

// Nombre de messages
$stmt = $db->query("SELECT COUNT(*) FROM contacts");
$stats['messages'] = $stmt->fetchColumn();

// Messages non lus
$stmt = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'non lu'");
$stats['unread'] = $stmt->fetchColumn();

// Nombre de newsletter
$stmt = $db->query("SELECT COUNT(*) FROM newsletter");
$stats['newsletter'] = $stmt->fetchColumn();

// Nombre de projets
$stmt = $db->query("SELECT COUNT(*) FROM projects");
$stats['projects'] = $stmt->fetchColumn();

// Derniers messages
$stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
$recentMessages = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="admin-dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['messages']; ?></h3>
                <p>Messages total</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['unread']; ?></h3>
                <p>Non lus</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['newsletter']; ?></h3>
                <p>Newsletter</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['projects']; ?></h3>
                <p>Projets</p>
            </div>
        </div>
    </div>
    
    <div class="recent-messages">
        <div class="section-header">
            <h2>Derniers messages</h2>
            <a href="messages.php" class="btn-view-all">Voir tous</a>
        </div>
        
        <?php if (empty($recentMessages)): ?>
        <p class="empty-message">Aucun message pour le moment</p>
        <?php else: ?>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recentMessages as $message): ?>
                    <tr class="<?php echo $message['status'] == 'non lu' ? 'unread' : ''; ?>">
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo str_replace(' ', '-', $message['status']); ?>">
                                <?php echo $message['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-dashboard {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
    border: 1px solid #e5e7eb;
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

.recent-messages {
    background: white;
    border-radius: 0.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-header h2 {
    margin: 0;
    font-size: 1.25rem;
}

.btn-view-all {
    color: #10b981;
    text-decoration: none;
    font-size: 0.875rem;
}

.btn-view-all:hover {
    text-decoration: underline;
}

.empty-message {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
}

.table-container {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.admin-table th {
    background: #f9fafb;
    font-weight: 600;
}

.admin-table tr.unread {
    background: #fef3c7;
    font-weight: 500;
}

.admin-table tr:hover {
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

.btn-view {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #3b82f6;
    color: white;
    border-radius: 0.25rem;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-view:hover {
    background: #2563eb;
}
</style>

<?php include 'includes/admin-footer.php'; ?>