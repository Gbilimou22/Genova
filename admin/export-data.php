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

// Export CSV
if (isset($_POST['export_csv']) && isset($_POST['export_type'])) {
    $type = $_POST['export_type'];
    $filename = 'genova_export_' . $type . '_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Ajouter BOM UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    switch ($type) {
        case 'messages':
            fputcsv($output, ['ID', 'Nom', 'Email', 'Téléphone', 'Sujet', 'Message', 'Statut', 'Date']);
            $stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['phone'],
                    $row['subject'],
                    $row['message'],
                    $row['status'],
                    $row['created_at']
                ]);
            }
            break;
            
        case 'posts':
            fputcsv($output, ['ID', 'Titre', 'Slug', 'Catégorie', 'Auteur', 'Vues', 'Statut', 'Date de publication']);
            $stmt = $db->query("SELECT p.*, c.name as category_name 
                                FROM blog_posts p
                                LEFT JOIN blog_categories c ON p.category_id = c.id
                                ORDER BY p.created_at DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['id'],
                    $row['title'],
                    $row['slug'],
                    $row['category_name'],
                    $row['author'],
                    $row['views'],
                    $row['status'],
                    $row['published_at']
                ]);
            }
            break;
            
        case 'comments':
            fputcsv($output, ['ID', 'Article', 'Auteur', 'Email', 'Commentaire', 'Statut', 'Date']);
            $stmt = $db->query("SELECT c.*, p.title as post_title 
                                FROM blog_comments c
                                LEFT JOIN blog_posts p ON c.post_id = p.id
                                ORDER BY c.created_at DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['id'],
                    $row['post_title'],
                    $row['author'],
                    $row['email'],
                    $row['content'],
                    $row['status'],
                    $row['created_at']
                ]);
            }
            break;
            
        case 'subscribers':
            fputcsv($output, ['ID', 'Email', "Date d'inscription"]);
            $stmt = $db->query("SELECT * FROM newsletter ORDER BY created_at DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['id'],
                    $row['email'],
                    $row['created_at']
                ]);
            }
            break;
    }
    
    fclose($output);
    exit;
}

include 'includes/admin-header.php';
?>

<div class="export-page">
    <div class="page-header">
        <h2><i class="fas fa-file-export"></i> Export des données</h2>
        <p>Exportez vos données au format CSV pour les analyser dans Excel ou autres outils</p>
    </div>
    
    <div class="export-grid">
        <div class="export-card">
            <div class="export-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h3>Messages de contact</h3>
            <p>Exportez tous les messages reçus via le formulaire de contact</p>
            <form method="POST">
                <input type="hidden" name="export_type" value="messages">
                <button type="submit" name="export_csv" class="btn-export">
                    <i class="fas fa-download"></i> Exporter CSV
                </button>
            </form>
        </div>
        
        <div class="export-card">
            <div class="export-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3>Articles du blog</h3>
            <p>Exportez tous vos articles avec leurs métadonnées</p>
            <form method="POST">
                <input type="hidden" name="export_type" value="posts">
                <button type="submit" name="export_csv" class="btn-export">
                    <i class="fas fa-download"></i> Exporter CSV
                </button>
            </form>
        </div>
        
        <div class="export-card">
            <div class="export-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3>Commentaires</h3>
            <p>Exportez tous les commentaires des articles</p>
            <form method="POST">
                <input type="hidden" name="export_type" value="comments">
                <button type="submit" name="export_csv" class="btn-export">
                    <i class="fas fa-download"></i> Exporter CSV
                </button>
            </form>
        </div>
        
        <div class="export-card">
            <div class="export-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Newsletter</h3>
            <p>Exportez la liste des abonnés à la newsletter</p>
            <form method="POST">
                <input type="hidden" name="export_type" value="subscribers">
                <button type="submit" name="export_csv" class="btn-export">
                    <i class="fas fa-download"></i> Exporter CSV
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.export-page {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.page-header {
    margin-bottom: 2rem;
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

.export-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.export-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.export-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.export-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.export-icon i {
    font-size: 2rem;
    color: white;
}

.export-card h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
}

.export-card p {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.btn-export {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.3s;
}

.btn-export:hover {
    background: #059669;
}
</style>

<?php include 'includes/admin-footer.php'; ?>