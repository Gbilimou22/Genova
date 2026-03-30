<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Tous les champs sont requis';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } else {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Nom d\'utilisateur ou email déjà utilisé';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
                $message = 'Utilisateur créé avec succès';
            } else {
                $error = 'Erreur lors de la création';
            }
        }
    }
}

// Supprimer un utilisateur
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id != $_SESSION['user_id']) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Utilisateur supprimé';
    } else {
        $error = 'Vous ne pouvez pas supprimer votre propre compte';
    }
}

// Récupérer tous les utilisateurs
$stmt = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="users-management">
    <div class="page-header">
        <h2><i class="fas fa-users"></i> Gestion des utilisateurs</h2>
        <button class="btn-add" onclick="document.getElementById('addUserModal').style.display='flex'">
            <i class="fas fa-plus"></i> Ajouter un utilisateur
        </button>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo $user['role'] == 'admin' ? 'Administrateur' : 'Éditeur'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    <td class="actions">
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=1&id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                            <span class="current-user">(Vous)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout Utilisateur -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ajouter un utilisateur</h3>
            <span class="close" onclick="document.getElementById('addUserModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label>Nom d'utilisateur *</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Mot de passe *</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="editor">Éditeur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn-submit">
                <i class="fas fa-save"></i> Créer
            </button>
        </form>
    </div>
</div>

<style>
.users-management {
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

.btn-add {
    background: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.users-table-container {
    overflow-x: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.users-table th {
    background: #f9fafb;
    font-weight: 600;
}

.role-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.role-admin {
    background: #d1fae5;
    color: #065f46;
}

.role-editor {
    background: #dbeafe;
    color: #1e40af;
}

.current-user {
    font-size: 0.75rem;
    color: #6b7280;
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
    z-index: 1000;
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
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.close {
    font-size: 1.5rem;
    cursor: pointer;
    color: #9ca3af;
}

.close:hover {
    color: #374151;
}

.btn-submit {
    width: 100%;
    background: #10b981;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    margin-top: 1rem;
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