<?php
// Configuration du système de réservation
define('BOOKING_ENABLED', true);
define('BOOKING_SLOT_DURATION', 30); // minutes
define('BOOKING_START_HOUR', 9); // 9h
define('BOOKING_END_HOUR', 18); // 18h
define('BOOKING_ADVANCE_DAYS', 30); // réservation jusqu'à 30 jours
define('BOOKING_CANCEL_HOURS', 24); // annulation 24h avant
define('BOOKING_REMINDER_HOURS', 24); // rappel 24h avant

// Services disponibles
$booking_services = [
    'consultation' => [
        'name' => 'Consultation gratuite',
        'duration' => 30,
        'price' => 0,
        'description' => 'Découvrons ensemble votre projet'
    ],
    'audit' => [
        'name' => 'Audit SEO complet',
        'duration' => 60,
        'price' => 150,
        'description' => 'Analyse détaillée de votre site'
    ],
    'strategy' => [
        'name' => 'Stratégie digitale',
        'duration' => 90,
        'price' => 250,
        'description' => 'Élaboration de votre stratégie'
    ],
    'support' => [
        'name' => 'Support technique',
        'duration' => 60,
        'price' => 100,
        'description' => 'Assistance technique personnalisée'
    ]
];
?>