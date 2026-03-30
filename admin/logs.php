<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/security-functions.php';

if (!isset($_SESSION['user_id']) || !isAdmin()) {
    header('Location: login.php');
    exit();
}

$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : 'activity';
$days = isset($_GET['days']) ? intval($_GET['days']) : 7;

$logs = getLogs($type, $days);

// Supprimer les logs (admin uniquement)
if (isset($_POST['clear_logs']) && isAdmin()) {
    cleanOldLogs(0); // Supprimer tous
    header('Location: logs.php');
    exit();
}

include 'includes/admin-header.php';
?>

<div class="logs-page">
    <div class="page-header">
        <h2><i class="fas fa-history"></i> Logs d'activité</h2>
        <div class="log-actions">
            <form method="GET" style="display: inline;">
                <select name="type" onchange="this.form.submit()">
                    <option value="activity" <?php echo $type == 'activity' ? 'selected' : ''; ?>>Activité</option>
                    <option value="errors" <?php echo $type == 'errors' ? 'selected' : ''; ?>>Erreurs</option>
                    <option value="login_attempts" <?php echo $type == 'login_attempts' ? 'selected' : ''; ?>>Tentatives connexion</option>
                </select>
                <select name="days" onchange="this.form.submit()">
                    <option value="1" <?php echo $days == 1 ? 'selected' : ''; ?>>24h</option>
                    <option value="7" <?php echo $days == 7 ? 'selected' : ''; ?>>7 jours</option>
                    <option value="30" <?php echo $days == 30 ? 'selected' : ''; ?>>30 jours</option>
                </select>
            </form>
            <?php if (isAdmin()): ?>
            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer tous les logs ?')">
                <button type="submit" name="clear_logs" class="btn-clear">
                    <i class="fas fa-trash"></i> Effacer
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (empty($logs)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucun log pour cette période</p>
        </div>
    <?php else: ?>
        <div class="logs-container">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>IP</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                        <?php
                        // Parser la ligne de log
                        preg_match('/\[(.*?)\] \[(.*?)\] \[(.*?)\] \[(.*?)\] (.*?)(?: - (.*))?$/', trim($log), $matches);
                        if (count($matches) >= 6):
                            $date = $matches[1];
                            $level = $matches[2];
                            $ip = $matches[3];
                            $user = $matches[4];
                            $action = $matches[5];
                            $details = $matches[6] ?? '';
                            
                            $levelClass = '';
                            if ($level == 'error') $levelClass = 'log-error';
                            if ($level == 'warning') $levelClass = 'log-warning';
                        ?>
                        <tr class="<?php echo $levelClass; ?>">
                            <td><?php echo $date; ?></td>
                            <td><?php echo $ip; ?></td>
                            <td><?php echo escapeOutput($user); ?></td>
                            <td><?php echo escapeOutput($action); ?></td>
                            <td><?php echo escapeOutput($details); ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.logs-page {
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
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header h2 {
    margin: 0;
}

.page-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.log-actions {
    display: flex;
    gap: 0.5rem;
}

.log-actions select {
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-family: inherit;
}

.btn-clear {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-clear:hover {
    background: #dc2626;
}

.logs-container {
    overflow-x: auto;
}

.logs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.logs-table th,
.logs-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.logs-table th {
    background: #f9fafb;
    font-weight: 600;
}

.logs-table tr:hover {
    background: #f9fafb;
}

.log-error {
    background: #fee2e2;
}

.log-warning {
    background: #fef3c7;
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

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .logs-table {
        font-size: 0.75rem;
    }
    
    .logs-table th,
    .logs-table td {
        padding: 0.5rem;
    }
}
</style>

<?php include 'includes/admin-footer.php'; ?>