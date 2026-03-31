<?php
require_once __DIR__ . '/../config/security.php';

// Fonction de journalisation
function logActivity($action, $details = '', $level = 'info') {
    $logFile = LOG_DIR . 'activity_' . date('Y-m-d') . '.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = $_SESSION['username'] ?? 'guest';
    $timestamp = date('Y-m-d H:i:s');
    
    $logEntry = "[$timestamp] [$level] [$ip] [$user] $action";
    if ($details) {
        $logEntry .= " - $details";
    }
    $logEntry .= PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Si niveau erreur, aussi dans un fichier séparé
    if ($level == 'error') {
        $errorLog = LOG_DIR . 'errors_' . date('Y-m-d') . '.log';
        file_put_contents($errorLog, $logEntry, FILE_APPEND);
    }
}

// Vérifier si l'IP est bloquée
function isIpBlocked() {
    global $blocked_ips;
    $ip = $_SERVER['REMOTE_ADDR'];
    return in_array($ip, $blocked_ips);
}

// Vérifier les tentatives de connexion
function checkLoginAttempts($username) {
    $logFile = LOG_DIR . 'login_attempts.log';
    if (!file_exists($logFile)) {
        return true;
    }
    
    $lines = file($logFile);
    $attempts = 0;
    $timeout = time() - (LOGIN_TIMEOUT_MINUTES * 60);
    
    foreach ($lines as $line) {
        if (strpos($line, $username) !== false) {
            preg_match('/\[(.*?)\]/', $line, $matches);
            if (isset($matches[1])) {
                $attemptTime = strtotime($matches[1]);
                if ($attemptTime > $timeout) {
                    $attempts++;
                }
            }
        }
    }
    
    return $attempts < MAX_LOGIN_ATTEMPTS;
}

// Enregistrer une tentative de connexion
function logLoginAttempt($username, $success) {
    $logFile = LOG_DIR . 'login_attempts.log';
    $ip = $_SERVER['REMOTE_ADDR'];
    $status = $success ? 'SUCCESS' : 'FAILED';
    $timestamp = date('Y-m-d H:i:s');
    
    $logEntry = "[$timestamp] [$ip] $username - $status" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    logActivity('Tentative de connexion', "$username - $status", $success ? 'info' : 'warning');
}

// Générer un token CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier le token CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        logActivity('Tentative CSRF', 'Token invalide', 'warning');
        return false;
    }
    return true;
}

// Nettoyer les entrées (XSS protection)
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Valider un email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Valider une URL
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// Nettoyer les données avant affichage
function escapeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Générer un hash sécurisé
function generateSecureHash($string) {
    return password_hash($string, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Vérifier un hash
function verifySecureHash($string, $hash) {
    return password_verify($string, $hash);
}

// Nettoyer les données de session
function regenerateSession() {
    if (SESSION_REGENERATE_ID && !headers_sent()) {
        session_regenerate_id(true);
    }
}

// Vérifier la durée de la session
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Obtenir les logs
function getLogs($type = 'activity', $days = 7) {
    $logFile = LOG_DIR . $type . '_' . date('Y-m-d') . '.log';
    $logs = [];
    
    for ($i = 0; $i < $days; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $file = LOG_DIR . $type . '_' . $date . '.log';
        if (file_exists($file)) {
            $lines = file($file);
            $logs = array_merge($logs, $lines);
        }
    }
    
    return array_reverse($logs);
}

// Effacer les anciens logs
function cleanOldLogs($days = 30) {
    $files = glob(LOG_DIR . '*.log');
    $cutoff = time() - ($days * 86400);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
        }
    }
}
?>