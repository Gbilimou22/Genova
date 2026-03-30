<?php
require_once __DIR__ . '/../config/booking.php';
require_once __DIR__ . '/../config/database.php';

// Créer la table des réservations
function createBookingsTable() {
    $db = Database::getInstance()->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        service VARCHAR(50) NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        date DATE NOT NULL,
        time TIME NOT NULL,
        message TEXT,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_date (date),
        INDEX idx_status (status),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
}

// Récupérer les créneaux disponibles
function getAvailableSlots($date, $service_duration = 30) {
    $db = Database::getInstance()->getConnection();
    
    // Créer tous les créneaux possibles
    $slots = [];
    $start = strtotime($date . ' ' . BOOKING_START_HOUR . ':00');
    $end = strtotime($date . ' ' . BOOKING_END_HOUR . ':00');
    
    for ($time = $start; $time < $end; $time += $service_duration * 60) {
        $time_str = date('H:i', $time);
        $slots[$time_str] = true; // disponible par défaut
    }
    
    // Récupérer les créneaux déjà réservés
    $stmt = $db->prepare("SELECT time FROM bookings WHERE date = ? AND status != 'cancelled'");
    $stmt->execute([$date]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($booked_slots as $booked) {
        $booked_time = date('H:i', strtotime($booked));
        if (isset($slots[$booked_time])) {
            $slots[$booked_time] = false; // indisponible
        }
    }
    
    return $slots;
}

// Créer une réservation
function createBooking($data) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO bookings (service, name, email, phone, date, time, message) 
                          VALUES (:service, :name, :email, :phone, :date, :time, :message)");
    return $stmt->execute($data);
}

// Envoyer un email de confirmation
function sendBookingConfirmation($booking) {
    $subject = "Confirmation de rendez-vous - " . SITE_NAME;
    $message = "
        <html>
        <body>
            <h2>Bonjour {$booking['name']},</h2>
            <p>Votre rendez-vous a été confirmé :</p>
            <ul>
                <li><strong>Service :</strong> {$booking['service']}</li>
                <li><strong>Date :</strong> " . date('d/m/Y', strtotime($booking['date'])) . "</li>
                <li><strong>Heure :</strong> {$booking['time']}</li>
            </ul>
            <p>Vous pouvez annuler ou modifier votre rendez-vous jusqu'à " . BOOKING_CANCEL_HOURS . " heures avant.</p>
            <p>Cordialement,<br>L'équipe " . SITE_NAME . "</p>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
    
    return mail($booking['email'], $subject, $message, $headers);
}
?>