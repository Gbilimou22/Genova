<?php
// Monitoring du site
class SiteMonitor {
    
    // Vérifier la disponibilité
    public static function checkAvailability() {
        $start = microtime(true);
        $db = Database::getInstance()->getConnection();
        $db->query("SELECT 1");
        $load_time = microtime(true) - $start;
        
        // Log si trop lent
        if ($load_time > 2) {
            logActivity('Performance', "Temps de chargement DB: {$load_time}s", 'warning');
        }
        
        return $load_time;
    }
    
    // Vérifier l'espace disque
    public static function checkDiskSpace() {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        $used_percent = (($total - $free) / $total) * 100;
        
        if ($used_percent > 90) {
            logActivity('Espace disque', "Plus que " . round(100 - $used_percent) . "% disponible", 'warning');
        }
        
        return $used_percent;
    }
    
    // Vérifier les erreurs récentes
    public static function checkRecentErrors() {
        $logFile = LOG_DIR . 'errors_' . date('Y-m-d') . '.log';
        if (file_exists($logFile)) {
            $errors = file($logFile);
            if (count($errors) > 10) {
                logActivity('Erreurs', count($errors) . " erreurs aujourd'hui", 'warning');
            }
        }
    }
}
?>