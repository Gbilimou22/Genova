<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Contact - " . SITE_NAME;
$success = false;
$error = false;
$errorMessage = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($email)) $errors[] = "L'email est requis.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
    if (empty($subject)) $errors[] = "Le sujet est requis.";
    if (empty($message)) $errors[] = "Le message est requis.";
    
    // Si pas d'erreurs, sauvegarder
    if (empty($errors)) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Insertion en base de données
            $sql = "INSERT INTO contacts (name, email, phone, subject, message, status) 
                    VALUES (:name, :email, :phone, :subject, :message, 'non lu')";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':subject' => $subject,
                ':message' => $message
            ]);
            
            if ($result) {
                $success = true;
                
                // Optionnel : envoyer un email de confirmation
                $to = $email;
                $emailSubject = "Confirmation de votre message - " . SITE_NAME;
                $emailMessage = "
                    <html>
                    <body>
                        <h2>Bonjour $name,</h2>
                        <p>Nous avons bien reçu votre message et vous recontacterons dans les plus brefs délais.</p>
                        <p>Cordialement,<br>L'équipe " . SITE_NAME . "</p>
                    </body>
                    </html>
                ";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
                mail($to, $emailSubject, $emailMessage, $headers);
            } else {
                $error = true;
                $errorMessage = "Erreur lors de l'enregistrement.";
            }
        } catch(PDOException $e) {
            $error = true;
            $errorMessage = "Erreur base de données : " . $e->getMessage();
        }
    } else {
        $error = true;
        $errorMessage = implode("<br>", $errors);
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Contactez-nous</h1>
            <p>Une question ? Un projet ? Notre équipe est à votre écoute</p>
        </div>
    </section>
    
    <?php if ($success): ?>
    <div class="alert alert-success" style="max-width: 1200px; margin: 20px auto;">
        <i class="fas fa-check-circle"></i>
        <p>Merci pour votre message ! Nous vous contacterons dans les plus brefs délais.</p>
    </div>
    <?php elseif ($error): ?>
    <div class="alert alert-error" style="max-width: 1200px; margin: 20px auto;">
        <i class="fas fa-exclamation-circle"></i>
        <p><?php echo $errorMessage; ?></p>
    </div>
    <?php endif; ?>
    
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>Informations de contact</h2>
                    <div class="info-card">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div><h3>Adresse</h3><p><?php echo $contact['address']; ?></p></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div><h3>Téléphone</h3><p><?php echo $contact['phone']; ?></p></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                            <div><h3>Email</h3><p><?php echo $contact['email']; ?></p></div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-wrapper">
                    <form class="contact-form" method="POST" action="">
                        <div class="form-group">
                            <label>Nom complet *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" name="phone">
                        </div>
                        <div class="form-group">
                            <label>Sujet *</label>
                            <select name="subject" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="Demande de devis">Demande de devis</option>
                                <option value="Nouveau projet">Nouveau projet</option>
                                <option value="Support technique">Support technique</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
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
.alert i {
    font-size: 1.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>