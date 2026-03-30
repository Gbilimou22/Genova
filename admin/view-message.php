<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: messages.php');
    exit();
}

$db = Database::getInstance()->getConnection();

// Récupérer le message
$stmt = $db->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: messages.php');
    exit();
}

// Marquer comme lu
if ($message['status'] == 'non lu') {
    $stmt = $db->prepare("UPDATE contacts SET status = 'lu' WHERE id = ?");
    $stmt->execute([$id]);
    $message['status'] = 'lu';
}

include 'includes/admin-header.php';
?>

<div class="view-message">
    <div class="message-header">
        <h2><i class="fas fa-envelope-open-text"></i> Détail du message</h2>
        <a href="messages.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <div class="message-card">
        <div class="message-info">
            <div class="info-row">
                <strong><i class="fas fa-user"></i> Nom :</strong>
                <span><?php echo htmlspecialchars($message['name']); ?></span>
            </div>
            <div class="info-row">
                <strong><i class="fas fa-envelope"></i> Email :</strong>
                <span><?php echo htmlspecialchars($message['email']); ?></span>
            </div>
            <div class="info-row">
                <strong><i class="fas fa-phone"></i> Téléphone :</strong>
                <span><?php echo $message['phone'] ? htmlspecialchars($message['phone']) : 'Non renseigné'; ?></span>
            </div>
            <div class="info-row">
                <strong><i class="fas fa-tag"></i> Sujet :</strong>
                <span><?php echo htmlspecialchars($message['subject']); ?></span>
            </div>
            <div class="info-row">
                <strong><i class="fas fa-calendar"></i> Date :</strong>
                <span><?php echo date('d/m/Y à H:i', strtotime($message['created_at'])); ?></span>
            </div>
            <div class="info-row">
                <strong><i class="fas fa-flag"></i> Status :</strong>
                <span class="status-badge status-<?php echo str_replace(' ', '-', $message['status']); ?>">
                    <?php echo $message['status']; ?>
                </span>
            </div>
        </div>
        
        <div class="message-content">
            <strong><i class="fas fa-comment"></i> Message :</strong>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
            </div>
        </div>
        
        <div class="message-actions">
            <a href="mailto:<?php echo $message['email']; ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" class="btn-reply" target="_blank">
                <i class="fas fa-reply"></i> Répondre par email
            </a>
            <?php if($message['status'] != 'répondu'): ?>
            <a href="messages.php?mark_responded=1&id=<?php echo $message['id']; ?>" class="btn-mark-responded" onclick="return confirm('Marquer ce message comme répondu ?')">
                <i class="fas fa-check-circle"></i> Marquer comme répondu
            </a>
            <?php endif; ?>
            <a href="messages.php?delete=1&id=<?php echo $message['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer ce message ?')">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
    </div>
</div>

<style>
.view-message {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.message-header h2 {
    margin: 0;
}

.message-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #6b7280;
    color: white;
    text-decoration: none;
    border-radius: 0.5rem;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #4b5563;
}

.message-card {
    background: #f9fafb;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.message-info {
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-row strong {
    width: 120px;
    color: #374151;
}

.info-row strong i {
    width: 20px;
    margin-right: 0.5rem;
}

.message-content {
    margin-bottom: 1.5rem;
}

.message-content strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
}

.message-content strong i {
    margin-right: 0.5rem;
}

.content-box {
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    line-height: 1.6;
    min-height: 150px;
}

.message-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.btn-reply,
.btn-mark-responded,
.btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-reply {
    background: #3b82f6;
    color: white;
}

.btn-reply:hover {
    background: #2563eb;
}

.btn-mark-responded {
    background: #10b981;
    color: white;
}

.btn-mark-responded:hover {
    background: #059669;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
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
</style>

<?php include 'includes/admin-footer.php'; ?>