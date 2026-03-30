<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/blog.php';

// Générer un slug à partir d'un titre
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Récupérer tous les articles - VERSION CORRIGÉE
function getPosts($limit = null, $offset = 0, $status = 'published') {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
            (SELECT COUNT(*) FROM blog_comments WHERE post_id = p.id AND status = 'approved') as comment_count
            FROM blog_posts p
            LEFT JOIN blog_categories c ON p.category_id = c.id
            WHERE p.status = :status
            ORDER BY p.published_at DESC";
    
    try {
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            
            // Liaison des paramètres avec les bons types
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getPosts: " . $e->getMessage());
        return [];
    }
}

// Récupérer un article par son slug
function getPostBySlug($slug) {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug,
                              (SELECT COUNT(*) FROM blog_comments WHERE post_id = p.id AND status = 'approved') as comment_count
                              FROM blog_posts p
                              LEFT JOIN blog_categories c ON p.category_id = c.id
                              WHERE p.slug = :slug AND p.status = 'published'");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Erreur getPostBySlug: " . $e->getMessage());
        return false;
    }
}

// Incrémenter les vues
function incrementViews($postId) {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id");
        $stmt->execute([':id' => $postId]);
        return true;
    } catch(PDOException $e) {
        error_log("Erreur incrementViews: " . $e->getMessage());
        return false;
    }
}

// Récupérer les articles par catégorie
function getPostsByCategory($categorySlug, $limit = null, $offset = 0) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
            (SELECT COUNT(*) FROM blog_comments WHERE post_id = p.id AND status = 'approved') as comment_count
            FROM blog_posts p
            LEFT JOIN blog_categories c ON p.category_id = c.id
            WHERE c.slug = :slug AND p.status = 'published'
            ORDER BY p.published_at DESC";
    
    try {
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':slug', $categorySlug, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':slug', $categorySlug, PDO::PARAM_STR);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getPostsByCategory: " . $e->getMessage());
        return [];
    }
}

// Récupérer toutes les catégories
function getAllCategories() {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->query("SELECT c.*, COUNT(p.id) as post_count 
                            FROM blog_categories c
                            LEFT JOIN blog_posts p ON c.id = p.category_id AND p.status = 'published'
                            GROUP BY c.id
                            ORDER BY c.name ASC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getAllCategories: " . $e->getMessage());
        return [];
    }
}

// Récupérer les articles populaires
function getPopularPosts($limit = 5) {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts 
                              WHERE status = 'published' 
                              ORDER BY views DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getPopularPosts: " . $e->getMessage());
        return [];
    }
}

// Récupérer les articles récents
function getRecentPosts($limit = 5) {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts 
                              WHERE status = 'published' 
                              ORDER BY published_at DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getRecentPosts: " . $e->getMessage());
        return [];
    }
}

// Récupérer les archives
function getArchives() {
    $db = Database::getInstance()->getConnection();
    try {
        $stmt = $db->query("SELECT DATE_FORMAT(published_at, '%Y-%m') as month, 
                                   COUNT(*) as count 
                            FROM blog_posts 
                            WHERE status = 'published' 
                            GROUP BY month 
                            ORDER BY month DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getArchives: " . $e->getMessage());
        return [];
    }
}

// Ajouter un commentaire
function addComment($postId, $author, $email, $content, $website = '') {
    $db = Database::getInstance()->getConnection();
    $status = COMMENTS_APPROVAL_REQUIRED ? 'pending' : 'approved';
    
    try {
        $stmt = $db->prepare("INSERT INTO blog_comments (post_id, author, email, website, content, status) 
                              VALUES (:post_id, :author, :email, :website, :content, :status)");
        return $stmt->execute([
            ':post_id' => $postId,
            ':author' => htmlspecialchars($author),
            ':email' => htmlspecialchars($email),
            ':website' => htmlspecialchars($website),
            ':content' => htmlspecialchars($content),
            ':status' => $status
        ]);
    } catch(PDOException $e) {
        error_log("Erreur addComment: " . $e->getMessage());
        return false;
    }
}

// Récupérer les commentaires
function getComments($postId, $limit = null, $offset = 0, $status = 'approved') {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT * FROM blog_comments 
            WHERE post_id = :post_id AND status = :status 
            ORDER BY created_at DESC";
    
    try {
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur getComments: " . $e->getMessage());
        return [];
    }
}
// Upload d'image
function uploadBlogImage($file) {
    if (!defined('UPLOAD_DIR')) {
        define('UPLOAD_DIR', __DIR__ . '/../uploads/blog/');
    }
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erreur lors du téléchargement'];
    }
    
    if ($file['size'] > 5242880) { // 5MB
        return ['error' => 'Fichier trop volumineux (max 5MB)'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Type de fichier non autorisé'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . preg_replace('/[^a-z0-9-]/', '-', strtolower(pathinfo($file['name'], PATHINFO_FILENAME))) . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => '/uploads/blog/' . $filename];
    }
    
    return ['error' => 'Erreur lors de la sauvegarde'];
}
?>