<?php
// Configuration de sécurité

// Protection brute force
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT_MINUTES', 15);
define('BLOCK_IP_ATTEMPTS', 10);

// Sécurité des sessions
define('SESSION_LIFETIME', 7200); // 2 heures
define('SESSION_REGENERATE_ID', true);
define('SESSION_HTTP_ONLY', true);
define('SESSION_SECURE_COOKIE', true); // Mettre à false en local

// Protection XSS
define('XSS_PROTECTION_ENABLED', true);

// Logs
define('LOG_DIR', __DIR__ . '/../logs/');
define('LOG_LEVEL', 'all'); // all, errors, none

// IP suspectes (à bloquer manuellement)
$blocked_ips = [
    // '192.168.1.100',
];

// Créer le dossier logs s'il n'existe pas
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}
?>