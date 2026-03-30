<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/booking-functions.php';

$service = $_GET['service'] ?? '';
$date = $_GET['date'] ?? '';

if (!$service || !$date) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

global $booking_services;
$duration = $booking_services[$service]['duration'] ?? 30;
$slots = getAvailableSlots($date, $duration);

$result = [];
foreach ($slots as $time => $available) {
    $result['slots'][] = [
        'time' => $time,
        'available' => $available
    ];
}

echo json_encode($result);
?>