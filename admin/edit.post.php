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
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: posts.php');
    exit;
}

// Récupérer l'article
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: posts.php');
    exit;
}

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
        
        // Vérifier si le slug existe déjà pour un autre article
        $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Gestion de l'image
        $featured_image = $post['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadBlogImage($_FILES['featured_image']);
            if (isset($upload['error'])) {
                $error = $upload['error'];
            } else {
                $featured_image = $upload['path'];
            }
        }
        
        if (empty($error)) {
            $published_at = ($status == 'published' && $post['status'] != 'published') ? date('Y-m-d H:i:s') : $post['published_at'];
            
            $stmt = $db->prepare("UPDATE blog_posts 
                                  SET title = :title, slug = :slug, excerpt = :excerpt, 
                                      content = :content, featured_image = :image, 
                                      category_id = :category, status = :status, 
                                      published_at = :published_at
                                  WHERE id = :id");
            
            $result = $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':image' => $featured_image,
                ':category' => $category_id ?: null,
                ':status' => $status,
                ':published_at' => $published_at,
                ':id' => $id
            ]);
            
            if ($result) {
                $success = 'Article modifié avec succès !';
                // Recharger les données
                $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
                $stmt->execute([$id]);
                $post = $stmt->fetch();
            } else {
                $error = 'Erreur lors de la modification';
            }
        }
    }
}

include 'includes/admin-header.php';
?>

<div class="edit-post">
    <div class="page-header">
        <h2><i class="fas fa-edit"></i> Modifier l'article</h2>
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
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Catégorie</label>
                <select name="category_id">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Extrait (optionnel)</label>
            <textarea name="excerpt" rows="3" placeholder="Résumé de l'article..."><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Contenu *</label>
            <textarea name="content" rows="15" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Image à la une</label>
            <?php if($post['featured_image']): ?>
                <div class="current-image">
                    <img src="<?php echo $post['featured_image']; ?>" alt="Image actuelle" style="max-width: 200px; margin-bottom: 10px;">
                    <p>Image actuelle</p>
                </div>
            <?php endif; ?>
            <input type="file" name="featured_image" accept="image/*">
            <small>Formats acceptés : JPG, PNG, WEBP. Max 5MB. Laissez vide pour conserver l'image actuelle.</small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Statut</label>
                <select name="status">
                    <option value="draft" <?php echo $post['status'] == 'draft' ? 'selected' : ''; ?>>Brouillon</option>
                    <option value="published" <?php echo $post['status'] == 'published' ? 'selected' : ''; ?>>Publié</option>
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
.edit-post {
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

.current-image {
    margin-bottom: 10px;
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