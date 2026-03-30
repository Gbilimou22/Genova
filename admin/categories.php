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
$message = '';
$error = '';

// Ajouter une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (empty($name)) {
        $error = 'Veuillez saisir un nom de catégorie';
    } else {
        $slug = generateSlug($name);
        
        $stmt = $db->prepare("SELECT id FROM blog_categories WHERE slug = ?");
        $stmt->execute([$slug]);
        
        if ($stmt->fetch()) {
            $error = 'Cette catégorie existe déjà';
        } else {
            $stmt = $db->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $slug, $description])) {
                $message = 'Catégorie ajoutée avec succès';
            } else {
                $error = 'Erreur lors de l\'ajout';
            }
        }
    }
}

// Supprimer une catégorie
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $error = "Impossible de supprimer cette catégorie car $count article(s) y sont associés";
    } else {
        $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Catégorie supprimée avec succès';
        } else {
            $error = 'Erreur lors de la suppression';
        }
    }
}

$categories = getAllCategories();

include 'includes/admin-header.php';
?>

<div class="categories-management">
    <div class="page-header">
        <h2><i class="fas fa-tags"></i> Gestion des catégories</h2>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="categories-layout">
        <div class="add-category-form">
            <h3>Ajouter une catégorie</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nom de la catégorie *</label>
                    <input type="text" name="name" required placeholder="Ex: Actualités">
                </div>
                <div class="form-group">
                    <label>Description (optionnel)</label>
                    <textarea name="description" rows="3" placeholder="Description de la catégorie..."></textarea>
                </div>
                <button type="submit" name="add_category" class="btn-add">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </form>
        </div>
        
        <div class="categories-list">
            <h3>Liste des catégories</h3>
            <?php if (empty($categories)): ?>
                <p class="empty-message">Aucune catégorie pour le moment</p>
            <?php else: ?>
                <table class="categories-table">
                    <thead>
                        <tr><th>Nom</th><th>Slug</th><th>Articles</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                            <td><?php echo $cat['post_count']; ?></td>
                            <td class="actions">
                                <a href="?delete=1&id=<?php echo $cat['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer cette catégorie ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.categories-management {
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
.categories-layout {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}
.add-category-form {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 0.5rem;
}
.add-category-form h3 {
    margin: 0 0 1rem 0;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 500;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
}
.btn-add {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    cursor: pointer;
}
.btn-add:hover {
    background: #059669;
}
.categories-table {
    width: 100%;
    border-collapse: collapse;
}
.categories-table th,
.categories-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.categories-table th {
    background: #f9fafb;
}
.actions {
    width: 50px;
}
.btn-delete {
    background: #ef4444;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    display: inline-block;
    text-decoration: none;
}
.alert {
    padding: 0.75rem;
    border-radius: 0.25rem;
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
@media (max-width: 768px) {
    .categories-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/admin-footer.php'; ?>