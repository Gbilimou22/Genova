<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/booking-functions.php';

// Créer la table si nécessaire
createBookingsTable();

$pageTitle = "Rendez-vous - " . SITE_NAME;
$success = false;
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = $_POST['service'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($date) || empty($time)) {
        $error = 'Veuillez remplir tous les champs obligatoires';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } else {
        $bookingData = [
            ':service' => $service,
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':date' => $date,
            ':time' => $time,
            ':message' => $message
        ];
        
        if (createBooking($bookingData)) {
            sendBookingConfirmation($bookingData);
            $success = true;
        } else {
            $error = 'Erreur lors de la réservation';
        }
    }
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Prenez rendez-vous</h1>
            <p>Choisissez le service qui vous intéresse et réservez un créneau</p>
        </div>
    </section>
    
    <section class="booking-section">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Votre rendez-vous a été confirmé ! Un email de confirmation vous a été envoyé.
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="booking-grid">
                <div class="booking-form-wrapper">
                    <h2>Formulaire de réservation</h2>
                    <form method="POST" class="booking-form" id="bookingForm">
                        <div class="form-group">
                            <label>Service *</label>
                            <select name="service" id="service" required onchange="updateSlots()">
                                <option value="">Sélectionnez un service</option>
                                <?php foreach($booking_services as $key => $service): ?>
                                <option value="<?php echo $key; ?>">
                                    <?php echo $service['name']; ?> 
                                    (<?php echo $service['duration']; ?> min - 
                                    <?php echo $service['price'] > 0 ? $service['price'] . '€' : 'Gratuit'; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom complet *</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="tel" name="phone">
                            </div>
                            <div class="form-group">
                                <label>Date *</label>
                                <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+' . BOOKING_ADVANCE_DAYS . ' days')); ?>" onchange="updateSlots()">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Heure *</label>
                            <select name="time" id="time" required>
                                <option value="">Sélectionnez d'abord un service et une date</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Message (optionnel)</label>
                            <textarea name="message" rows="4" placeholder="Décrivez brièvement votre projet..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check"></i> Confirmer le rendez-vous
                        </button>
                    </form>
                </div>
                
                <div class="booking-info">
                    <h2>Informations pratiques</h2>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3>Horaires</h3>
                            <p>Lundi - Vendredi: <?php echo BOOKING_START_HOUR; ?>h - <?php echo BOOKING_END_HOUR; ?>h</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h3>Besoin d'aide ?</h3>
                            <p>Appelez-nous au <?php echo SITE_PHONE; ?></p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3>Adresse</h3>
                            <p><?php echo $contact['address']; ?></p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3>Annulation</h3>
                            <p>Gratuite jusqu'à <?php echo BOOKING_CANCEL_HOURS; ?>h avant le rendez-vous</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function updateSlots() {
    const service = document.getElementById('service').value;
    const date = document.getElementById('date').value;
    
    if (!service || !date) return;
    
    fetch(`api/get-slots.php?service=${service}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            const timeSelect = document.getElementById('time');
            timeSelect.innerHTML = '<option value="">Sélectionnez un créneau</option>';
            
            data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.time;
                option.textContent = slot.time;
                if (!slot.available) {
                    option.disabled = true;
                    option.textContent += ' (indisponible)';
                }
                timeSelect.appendChild(option);
            });
        });
}
</script>

<style>
.booking-section {
    padding: 60px 0;
}

.booking-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
}

.booking-form-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.booking-form-wrapper h2 {
    margin-bottom: 1.5rem;
}

.booking-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-card {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    color: white;
    font-size: 1rem;
}

.info-card h3 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.info-card p {
    color: #6b7280;
    font-size: 0.875rem;
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .booking-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>