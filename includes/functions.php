<?php
// Fonctions utilitaires

// Nettoyer les données
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Valider l'email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Valider le téléphone
function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s]{10,}$/', $phone);
}

// Envoyer un email
function sendEmail($to, $subject, $message, $from = SITE_EMAIL) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <" . $from . ">\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Sauvegarder le message en base de données
function saveContactMessage($name, $email, $phone, $subject, $message) {
    try {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO contacts (name, email, phone, subject, message) 
                VALUES (:name, :email, :phone, :subject, :message)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message
        ]);
    } catch(PDOException $e) {
        error_log("Erreur sauvegarde message: " . $e->getMessage());
        return false;
    }
}

// S'inscrire à la newsletter
function subscribeNewsletter($email) {
    try {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO newsletter (email) VALUES (:email) 
                ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':email' => $email]);
    } catch(PDOException $e) {
        error_log("Erreur inscription newsletter: " . $e->getMessage());
        return false;
    }
}

// Récupérer les projets
function getProjects($limit = null, $featured = false) {
    try {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM projects WHERE 1=1";
        
        if ($featured) {
            $sql .= " AND featured = 1";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Erreur récupération projets: " . $e->getMessage());
        return [];
    }
}
?>