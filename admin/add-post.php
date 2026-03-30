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
$categories = getAllCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $content = trim($_POST['content']);
    $excerpt = trim($_POST['excerpt']);
    $status = $_POST['status'];
    
    if (empty($title)) {
        $error = 'Veuillez saisir un titre';
    } elseif (empty($content)) {
        $error = 'Veuillez saisir le contenu';
    } else {
        $slug = generateSlug($title);
        
        // Vérifier si le slug existe déjà
        $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Gestion de l'image
        $featured_image = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadBlogImage($_FILES['featured_image']);
            if (isset($upload['error'])) {
                $error = $upload['error'];
            } else {
                $featured_image = $upload['path'];
            }
        }
        
        if (empty($error)) {
            $published_at = $status == 'published' ? date('Y-m-d H:i:s') : null;
            
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, category_id, author, status, published_at) 
                                  VALUES (:title, :slug, :excerpt, :content, :image, :category, :author, :status, :published_at)");
            
            $result = $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':image' => $featured_image,
                ':category' => $category_id ?: null,
                ':author' => $_SESSION['username'],
                ':status' => $status,
                ':published_at' => $published_at
            ]);
            
            if ($result) {
                $success = 'Article créé avec succès !';
                // Redirection après 2 secondes
                header('refresh:2;url=posts.php');
            } else {
                $error = 'Erreur lors de la création de l\'article';
            }
        }
    }
}

include 'includes/admin-header.php';
?>

<div class="add-post">
    <div class="page-header">
        <h2><i class="fas fa-plus"></i> Nouvel article</h2>
        <a href="posts.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="post-form">
        <div class="form-row">
            <div class="form-group">
                <label>Titre de l'article *</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Catégorie</label>
                <select name="category_id">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Extrait (optionnel)</label>
            <textarea name="excerpt" rows="3" placeholder="Résumé de l'article..."></textarea>
        </div>
        
        <div class="form-group">
            <label>Contenu *</label>
            <textarea name="content" rows="15" required placeholder="Écrivez votre article ici..."></textarea>
        </div>
        
        <div class="form-group">
            <label>Image à la une</label>
            <input type="file" name="featured_image" accept="image/*">
            <small>Formats acceptés : JPG, PNG, WEBP. Max 5MB</small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Statut</label>
                <select name="status">
                    <option value="draft">Brouillon</option>
                    <option value="published">Publier</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="posts.php" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<style>
.add-post {
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

.btn-back {
    background: #6b7280;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    background: #4b5563;
}

.post-form {
    max-width: 800px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    font-family: inherit;
}

.form-group textarea {
    resize: vertical;
}

.form-group small {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-save {
    background: #10b981;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-save:hover {
    background: #059669;
}

.btn-cancel {
    background: #ef4444;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-cancel:hover {
    background: #dc2626;
}

.alert {
    padding: 1rem;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>



<?php include 'includes/admin-footer.php'; ?>