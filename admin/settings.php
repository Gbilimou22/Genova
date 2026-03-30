<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Ici vous pouvez sauvegarder les paramètres dans un fichier ou en BDD
        // Pour l'instant, on simule la sauvegarde
        $message = "Paramètres sauvegardés avec succès !";
    }
    
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($new === $confirm) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (password_verify($current, $user['password'])) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $_SESSION['user_id']]);
                $message = "Mot de passe changé avec succès !";
            } else {
                $error = "Mot de passe actuel incorrect";
            }
        } else {
            $error = "Les nouveaux mots de passe ne correspondent pas";
        }
    }
}

include 'includes/admin-header.php';
?>

<div class="settings-page">
    <div class="page-header">
        <h2><i class="fas fa-cog"></i> Paramètres</h2>
        <p>Configurez votre site et votre compte</p>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>
    
    <div class="settings-grid">
        <!-- Informations du site -->
        <div class="settings-card">
            <h3><i class="fas fa-globe"></i> Informations du site</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nom du site</label>
                    <input type="text" value="<?php echo SITE_NAME; ?>" disabled>
                    <small>Modifiable dans config/config.php</small>
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" value="<?php echo SITE_TAGLINE; ?>" disabled>
                    <small>Modifiable dans config/config.php</small>
                </div>
                <div class="form-group">
                    <label>Email de contact</label>
                    <input type="email" value="<?php echo SITE_EMAIL; ?>" disabled>
                    <small>Modifiable dans config/config.php</small>
                </div>
            </form>
        </div>
        
        <!-- Changer le mot de passe -->
        <div class="settings-card">
            <h3><i class="fas fa-key"></i> Sécurité</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Mot de passe actuel *</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe *</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn-save">
                    <i class="fas fa-save"></i> Changer le mot de passe
                </button>
            </form>
        </div>
        
        <!-- Informations du compte -->
        <div class="settings-card">
            <h3><i class="fas fa-user"></i> Mon compte</h3>
            <div class="info-row">
                <strong>Nom d'utilisateur :</strong>
                <span><?php echo $_SESSION['username']; ?></span>
            </div>
            <div class="info-row">
                <strong>Rôle :</strong>
                <span><?php echo $_SESSION['role']; ?></span>
            </div>
            <div class="info-row">
                <strong>Dernière connexion :</strong>
                <span><?php echo date('d/m/Y H:i'); ?></span>
            </div>
        </div>
    </div>
</div>

<style>
.settings-page {
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
    margin-bottom: 0.25rem;
}

.page-header h2 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.page-header p {
    color: #6b7280;
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.settings-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.settings-card h3 {
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.settings-card h3 i {
    color: #10b981;
    margin-right: 0.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
}

.form-group small {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.btn-save {
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

.btn-save:hover {
    background: #059669;
}

.info-row {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-row strong {
    display: inline-block;
    width: 140px;
    color: #374151;
}
</style>

<?php include 'includes/admin-footer.php'; ?>