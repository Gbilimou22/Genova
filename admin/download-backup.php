<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$file = isset($_GET['file']) ? basename($_GET['file']) : '';
$backupDir = dirname(__DIR__) . '/backups/';
$filePath = $backupDir . $file;

if ($file && file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) == 'sql') {
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    header('Location: backup.php');
    exit;
}
?>