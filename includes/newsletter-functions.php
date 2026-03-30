<?php
require_once __DIR__ . '/../config/database.php';

// Créer la table newsletter
function createNewsletterTable() {
    $db = Database::getInstance()->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS newsletter (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE NOT NULL,
        token VARCHAR(64),
        confirmed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $db->exec($sql);
}

// S'abonner
function subscribeNewsletter($email) {
    $db = Database::getInstance()->getConnection();
    $token = bin2hex(random_bytes(32));
    
    $stmt = $db->prepare("INSERT INTO newsletter (email, token) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE token = ?");
    return $stmt->execute([$email, $token, $token]);
}

// Confirmer l'abonnement
function confirmSubscription($email, $token) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE newsletter SET confirmed = TRUE WHERE email = ? AND token = ?");
    return $stmt->execute([$email, $token]);
}

// Envoyer la newsletter
function sendNewsletter($subject, $content) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT email FROM newsletter WHERE confirmed = TRUE");
    $subscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
    
    $sent = 0;
    foreach ($subscribers as $email) {
        if (mail($email, $subject, $content, $headers)) {
            $sent++;
        }
    }
    
    return $sent;
}
?>