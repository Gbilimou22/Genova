<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/blog-functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Vérifier si la table projects existe
try {
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='projects'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        // Créer la table si elle n'existe pas
        $db->exec("CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            category TEXT,
            description TEXT,
            image TEXT,
            link TEXT,
            client TEXT,
            completion_date TEXT,
            featured INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        $message = "Table 'projects' créée automatiquement";
    }
    
    // Ajouter un projet
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
        $title = trim($_POST['title']);
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $client = trim($_POST['client']);
        $link = trim($_POST['link']);
        
        $stmt = $db->prepare("INSERT INTO projects (title, category, description, client, link) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $description, $client, $link]);
        $message = 'Projet ajouté avec succès';
        header('Location: projects.php');
        exit();
    }
    
    // Supprimer un projet
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Projet supprimé';
        header('Location: projects.php');
        exit();
    }
    
    // Récupérer tous les projets
    $stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Erreur base de données : " . $e->getMessage();
    $projects = [];
}

include 'includes/admin-header.php';
?>

<div class="projects-page">
    <div class="page-header">
        <h2><i class="fas fa-folder-open"></i> Gestion des projets</h2>
        <button class="btn-add" onclick="document.getElementById('addProjectModal').style.display='flex'">
            <i class="fas fa-plus"></i> Ajouter un projet
        </button>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($projects)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>Aucun projet pour le moment</p>
        </div>
    <?php else: ?>
        <div class="projects-grid">
            <?php foreach($projects as $project): ?>
            <div class="project-card">
                <div class="project-info">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <span class="project-category"><?php echo htmlspecialchars($project['category']); ?></span>
                    <p><?php echo htmlspecialchars(substr($project['description'], 0, 100)) . '...'; ?></p>
                    <?php if($project['client']): ?>
                    <p class="project-client"><strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="project-actions">
                    <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn-edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="?delete=1&id=<?php echo $project['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer ce projet ?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Ajout Projet -->
<div id="addProjectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ajouter un projet</h3>
            <span class="close" onclick="document.getElementById('addProjectModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Catégorie *</label>
                <select name="category" required>
                    <option value="Site Web">Site Web</option>
                    <option value="Application Mobile">Application Mobile</option>
                    <option value="E-Commerce">E-Commerce</option>
                    <option value="Design">Design</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Client</label>
                <input type="text" name="client">
            </div>
            <div class="form-group">
                <label>Lien du projet</label>
                <input type="url" name="link">
            </div>
            <button type="submit" name="add_project" class="btn-submit">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </form>
    </div>
</div>

<style>
.projects-page {
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
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 1rem;
}
.btn-add {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    cursor: pointer;
}
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
}
.project-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
}
.project-card h3 {
    margin: 0 0 0.5rem 0;
}
.project-category {
    display: inline-block;
    background: #e5e7eb;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
}
.project-actions {
    display: flex;
    gap: 0.5rem;
}
.btn-edit {
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-decoration: none;
}
.btn-delete {
    background: #ef4444;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-decoration: none;
}
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: white;
    border-radius: 0.5rem;
    width: 90%;
    max-width: 500px;
    padding: 1.5rem;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}
.close {
    cursor: pointer;
    font-size: 1.5rem;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.25rem;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
}
.btn-submit {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    cursor: pointer;
    width: 100%;
}
.alert {
    padding: 0.75rem;
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}
.alert-success {
    background: #d1fae5;
    color: #065f46;
}
.alert-error {
    background: #fee2e2;
    color: #991b1b;
}
.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}
</style>

<?php include 'includes/admin-footer.php'; ?>