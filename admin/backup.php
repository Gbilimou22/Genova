<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Créer le dossier de sauvegarde
$backupDir = dirname(__DIR__) . '/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Fonction de sauvegarde
function backupDatabase($backupDir, $db) {
    $tables = [];
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $handle = fopen($backupFile, 'w');
    
    fwrite($handle, "-- Genova Database Backup\n");
    fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
    
    foreach ($tables as $table) {
        // Structure de la table
        $stmt = $db->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
        fwrite($handle, $row['Create Table'] . ";\n\n");
        
        // Données de la table
        $stmt = $db->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $values = [];
            
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($columns as $col) {
                    $val = addslashes($row[$col]);
                    $rowValues[] = "'$val'";
                }
                $values[] = "(" . implode(',', $rowValues) . ")";
            }
            
            fwrite($handle, "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES \n");
            fwrite($handle, implode(",\n", $values) . ";\n\n");
        }
    }
    
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($handle);
    
    return $backupFile;
}

// Sauvegarde manuelle
if (isset($_POST['backup'])) {
    try {
        $backupFile = backupDatabase($backupDir, $db);
        $message = "Sauvegarde créée avec succès : " . basename($backupFile);
    } catch(Exception $e) {
        $error = "Erreur lors de la sauvegarde : " . $e->getMessage();
    }
}

// Supprimer une sauvegarde
if (isset($_GET['delete']) && isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filePath = $backupDir . $file;
    if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) == 'sql') {
        unlink($filePath);
        $message = "Sauvegarde supprimée";
    }
}

// Récupérer la liste des sauvegardes
$backups = glob($backupDir . '*.sql');
rsort($backups);

include 'includes/admin-header.php';
?>

<div class="backup-page">
    <div class="page-header">
        <h2><i class="fas fa-database"></i> Sauvegarde de la base de données</h2>
        <p>Créez et gérez les sauvegardes de votre base de données</p>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="backup-actions">
        <form method="POST" class="backup-form">
            <button type="submit" name="backup" class="btn-backup">
                <i class="fas fa-database"></i> Créer une sauvegarde maintenant
            </button>
        </form>
        
        <div class="backup-info">
            <i class="fas fa-info-circle"></i>
            <span>Les sauvegardes sont stockées dans le dossier <code>backups/</code></span>
        </div>
    </div>
    
    <?php if (!empty($backups)): ?>
        <div class="backups-list">
            <h3>Sauvegardes disponibles</h3>
            <table class="backups-table">
                <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Taille</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($backups as $backup): ?>
                        <?php $filename = basename($backup); ?>
                         <tr>
                            <td><i class="fas fa-file-archive"></i> <?php echo $filename; ?></td>
                            <td><?php echo round(filesize($backup) / 1024, 2); ?> KB</td>
                            <td><?php echo date('d/m/Y H:i:s', filemtime($backup)); ?></td>
                            <td class="actions">
                                <a href="download-backup.php?file=<?php echo urlencode($filename); ?>" class="btn-download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="?delete=1&file=<?php echo urlencode($filename); ?>" class="btn-delete" onclick="return confirm('Supprimer cette sauvegarde ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-database"></i>
            <p>Aucune sauvegarde pour le moment</p>
            <small>Cliquez sur "Créer une sauvegarde" pour commencer</small>
        </div>
    <?php endif; ?>
</div>

<style>
.backup-page {
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
    margin: 0 0 0.5rem 0;
}

.page-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.page-header p {
    color: #6b7280;
    margin: 0;
}

.backup-actions {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    text-align: center;
}

.btn-backup {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.3s;
}

.btn-backup:hover {
    background: #059669;
}

.backup-info {
    margin-top: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.backup-info code {
    background: #e5e7eb;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.backups-list h3 {
    margin: 0 0 1rem 0;
    font-size: 1.2rem;
}

.backups-table {
    width: 100%;
    border-collapse: collapse;
}

.backups-table th,
.backups-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.backups-table th {
    background: #f9fafb;
    font-weight: 600;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-download,
.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-download {
    background: #3b82f6;
    color: white;
}

.btn-download:hover {
    background: #2563eb;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
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
</style>

<?php include 'includes/admin-footer.php'; ?>